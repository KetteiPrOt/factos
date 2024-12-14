<?php

namespace App\Http\Controllers\Receipts\Invoices;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\Products\Model as Product;
use App\Models\Establishments\Model as Establishment;
use App\Models\Establishments\IssuancePoint;
use App\Models\Establishments\Sequential;
use App\Models\Receipts\Type as ReceiptType;
use App\Models\Persons\IdentificationType;
use App\Models\Products\IceType;
use App\Models\Products\VatRate;
use App\Models\Receipts\Invoices\PayMethod;

class Builder
{
    private string $enviroment;

    public Establishment $establishment;

    public IssuancePoint $issuancePoint;

    public string $sequential;

    private IdentificationType $identificationType;

    private string $details;

    private string $total_without_taxation = '0.00';

    private string $total_discount = '0.00';

    private string $total = '0.00';

    /**
     * Array to save the total tax values. Keys: <tax_type>-<code>.
     * Example: ['vat-1' => 'base', 'vat-2' => 'base', 'ice-1' => ['base', 'value']]
     */
    private array $total_taxes;

    private string $total_with_taxes;

    private string $propine;

    private string $pay_methods;

    private string $additional_fields;

    public string $access_key;

    public function build(array $validated, User $user): string
    {
        $this->setAttributes($validated, $user);
        $template = Storage::disk('local')->get('invoice_template.xml');
        // Tax Information
        $invoice = str_replace('{{user_name}}', $user->name, $template);
        $invoice = str_replace('{{establishment_commercial_name}}', $this->establishment->commercial_name, $invoice);
        $invoice = str_replace('{{user_ruc}}', $user->ruc, $invoice);
        $invoice = str_replace('{{establishment_code}}', $this->establishment->code, $invoice);
        $invoice = str_replace('{{issuance_point_code}}', $this->issuancePoint->code, $invoice);
        $invoice = str_replace('{{sequential}}', $this->sequential, $invoice);
        $invoice = str_replace('{{user_matrix_address}}', $user->matrix_address, $invoice);
        // Invoice Information
        $invoice = str_replace('{{issuance_date}}', date('d/m/Y', strtotime($validated['issuance_date'])), $invoice);
        $invoice = str_replace('{{establishment_address}}', $this->establishment->address, $invoice);
        $invoice = str_replace('{{obliged_accounting}}', true ? 'SI' : 'NO', $invoice);
        $invoice = str_replace('{{identification_type_code}}', $this->identificationType->code, $invoice);
        $invoice = str_replace('{{client_name}}', $validated['social_reason'] ?? 'Consumidor Final', $invoice);
        $invoice = str_replace('{{client_identification}}', $validated['identification'] ?? '0000000000001', $invoice);
        $invoice = str_replace('{{details}}', $this->details, $invoice);
        $invoice = str_replace('{{total_without_taxation}}', $this->total_without_taxation, $invoice);
        $invoice = str_replace('{{total_discount}}', $this->total_discount, $invoice);
        $invoice = str_replace('{{total_with_taxes}}', $this->total_with_taxes, $invoice);
        $invoice = str_replace('{{propine}}', $this->propine, $invoice);
        $invoice = str_replace('{{total}}', $this->total, $invoice);
        $invoice = str_replace('{{pay_methods}}', $this->pay_methods, $invoice);
        $invoice = str_replace('{{additional_fields}}', $this->additional_fields, $invoice);
        $invoice = str_replace('{{access_key}}', $this->access_key, $invoice);
        return $invoice;
    }

    private function setAttributes(array $validated, User $user): void
    {
        $this->enviroment = config('app.env') == 'production' ? '2' : '1';
        $this->establishment = Establishment::find($validated['establishment_id']);
        $this->issuancePoint = IssuancePoint::find($validated['issuance_point_id']);
        $this->sequential = $this->sequential();
        $this->identificationType = IdentificationType::find($validated['identification_type_id']);
        $this->details = $this->details($validated['products']);
        $this->total_with_taxes = $this->taxes();
        $this->propine = $this->propine(isset($validated['tip_ten_percent']));
        $this->pay_methods = $this->payMethods($validated['payment_methods']);
        $this->additional_fields = $this->additionalFields($validated['additional_fields'] ?? null);
        $this->access_key = $this->accessKey($this->sequential, $user->ruc, $validated['issuance_date']);
    }

    private function sequential(): string
    {
        $sequential = Sequential::where(
            'issuance_point_id', $this->issuancePoint->id
        )->where(
            'receipt_type_id', ReceiptType::where('name', 'FACTURA')->first()->id
        )->first();
        $number = $sequential->next;
        // Add left zeros
        for($i = 0; $i < 8; $i++){
            if($number < (10 ** ($i + 1))){
                $number = str_repeat('0', 8 - $i) . $number;
                break;
            }
        }
        $sequential->next = $sequential->next + 1;
        $sequential->save();
        return $number;
    }

    private function details(array $products_data): string
    {
        $details = ''; $count = count($products_data);
        foreach($products_data as $key => $data){
            $details .= $this->detail($data, $key, $count);
        }
        return $details;
    }

    private function detail(array $data, int $key, int $count): string
    {
        $firstDetail = $key == 0;
        $lastDetail = $key == ($count - 1);
        $product = Product::find($data['product_id']);
        $detail = 
         ($firstDetail
                 ? "<detalle>\n"
         : "        <detalle>\n")
         . "            <codigoPrincipal>$product->code</codigoPrincipal>\n"
         . "            <descripcion>$product->name</descripcion>\n"
         . "            <cantidad>{{amount}}</cantidad>\n"
         . "            <precioUnitario>{{price}}</precioUnitario>\n"
         . "            <descuento>{{discount}}</descuento>\n"
         . "            <precioTotalSinImpuesto>{{total_without_taxation}}</precioTotalSinImpuesto>\n"
         . "            <impuestos>\n"
         . "                {{taxes}}"
         . "            </impuestos>\n"
         . "        </detalle>"
         . ($lastDetail ? '' : "\n");

        $detail = str_replace('{{amount}}', $data['amount'], $detail);
        $detail = str_replace('{{price}}', $data['price'], $detail);
        $detail = str_replace('{{discount}}', $data['discount'], $detail);
        $this->total_discount = bcadd($this->total_discount, $data['discount'], 2);

        $total_without_taxation = bcmul($data['price'], $data['amount'], 2);
        $this->total_without_taxation = bcadd($this->total_without_taxation, $total_without_taxation, 2);

        $detail = str_replace('{{total_without_taxation}}', $total_without_taxation, $detail);
        $detail = str_replace('{{taxes}}', $this->detailTaxes($product, $data), $detail);
        return $detail;
    }

    private function detailTaxes(Product $product, array $data): string
    {
        $vatRate = $product->vatRate;
        $base = bcmul($data['price'], $data['amount'], 2);
        $total = bcmul(bcdiv($base, 100, 2), $vatRate->percentaje, 2);
        $taxes = 
                           "<impuesto>\n"
         . "                    <codigo>2</codigo>\n" // Table 16, VAT
         . "                    <codigoPorcentaje>$vatRate->code</codigoPorcentaje>\n"
         . "                    <tarifa>$vatRate->percentaje.00</tarifa>\n"
         . "                    <baseImponible>$base</baseImponible>\n"
         . "                    <valor>$total</valor>\n"
         . "                </impuesto>\n";
        $this->saveTaxTotal('vat-' . $vatRate->code, $base);
        if(isset($data['ice_value']) && !is_null($product->iceType)){
            $iceType = $product->iceType;
            $total = $data['ice_value'];
            $taxes .=
           "                <impuesto>\n"
         . "                    <codigo>3</codigo>\n" // Table 16, ICE
         . "                    <codigoPorcentaje>$iceType->code</codigoPorcentaje>\n"
         . "                    <baseImponible>$base</baseImponible>\n"
         . "                    <valor>$total</valor>\n"
         . "                </impuesto>\n";
            $this->saveTaxTotal('ice-' . $iceType->code, $total);   
        }
        return $taxes;
    }

    private function saveTaxTotal(string $key, string $base): void
    {
        if(isset($this->total_taxes[$key])){
            $this->total_taxes[$key] = bcadd($this->total_taxes[$key], $base, 2);
        } else {
            $this->total_taxes[$key] = bcadd('0.00', $base, 2);
        }
    }

    private function taxes(): string
    {
        $this->total = $this->total_without_taxation;
        $total_with_taxes = '';
        $i = 0;
        $count = count($this->total_taxes);
        foreach($this->total_taxes as $key => $value){
            $first = $i == 0;
            $last = $i == ($count - 1);
            $i++;
            if(str_starts_with($key, 'vat-')){
                $base = $value;
                $code = substr($key, mb_strpos($key, '-') + 1);
                $vatRate = VatRate::where('code', $code)->first();
                $total = bcmul(bcdiv($base, 100, 2), $vatRate->percentaje, 2);
                $total_with_taxes .=
                        ($first
                            ? "<totalImpuesto>\n"
                : "            <totalImpuesto>\n")
                . "                <codigo>2</codigo>\n" // Table 16, IVA
                . "                <codigoPorcentaje>$vatRate->code</codigoPorcentaje>\n"
                . "                <baseImponible>$base</baseImponible>\n"
                . "                <tarifa>$vatRate->percentaje.00</tarifa>\n"
                . "                <valor>$total</valor>\n"
                . "            </totalImpuesto>"
                . ($last ? '' : "\n");
            } else if(str_starts_with($key, 'ice-')){
                $base = $value[0];
                $total = $value[1];
                $code = substr($key, mb_strpos($key, '-') + 1);
                $iceType = IceType::where('code', $code)->first();
                $total_with_taxes .=
                  "                <totalImpuesto>\n"
                . "                    <codigo>3</codigo>\n" // Table 16, ICE
                . "                    <codigoPorcentaje>$iceType->code</codigoPorcentaje>\n"
                . "                    <baseImponible>$base</baseImponible>\n"
                . "                    <valor>$total</valor>\n"
                . "                </totalImpuesto>\n";
            }
            $this->total = bcadd($this->total, $total, 2);
        }
        return $total_with_taxes;
    }

    private function propine(bool $tip_ten_percent): string
    {
        if($tip_ten_percent){
            $tip_ten_percent = bcmul(bcdiv($this->total_without_taxation, 100, 2), 10, 2);
            $this->total = bcadd($this->total, $tip_ten_percent, 2);
        } else {
            $tip_ten_percent = '0.00';
        }
        return $tip_ten_percent;
    }

    private function payMethods(array $pay_methods_data): string
    {
        $pay_methods = '';
        $i = 0;
        $count = count($pay_methods_data);
        foreach($pay_methods_data as $pay_method_data){
            $first = $i == 0; $last = $i == ($count - 1); $i++;
            $payMethod = PayMethod::find($pay_method_data['pay_method_id']);
            $total = $pay_method_data['value'];
            $pay_method =
                    ($first ? "<pago>\n"
            : "                <pago>\n")
            . "                <formaPago>$payMethod->code</formaPago>\n"
            . "                <total>$total</total>\n";
            // if(isset($pay_method_data['term'])){
            //     $term = $pay_method_data['term'];
            //     $pay_method .=
            //   "                <plazo>$term<plazo>\n";
            // }
            // if(isset($pay_method_data['time'])){
            //     $time = $pay_method_data['time'];
            //     $pay_method .=
            //   "                <unidadTiempo>$time</unidadTiempo>\n";
            // }
            $pay_method .=
              "            </pago>" . ($last ? '' : "\n");;

            $pay_methods .= $pay_method;
        }
        return $pay_methods;
    }

    private function additionalFields(?array $additional_fields): string
    {
        if(is_null($additional_fields)) return '';
        $fields = "<infoAdicional>\n        ";
        $i = 0;
        $count = count($additional_fields);
        foreach($additional_fields as $additional_field){
            $first = $i == 0; $last = $i == ($count - 1); $i++;
            $name = $additional_field['name'];
            $description = $additional_field['description'];
            $fields .= $first
                        ? "<campoAdicional nombre=\"$name\">$description</campoAdicional>\n"
                : "        <campoAdicional nombre=\"$name\">$description</campoAdicional>" . ($last ? '' : "\n");
        }
        $fields .= "    </infoAdicional>";
        return $fields;
    }

    private function accessKey(
        string $sequential,
        string $identification,
        string $issuance_date
    ): string
    {
        $access_key = 
            date('dnY', strtotime($issuance_date))
            . '01' // document type: 01 => invoice
            . $identification
            . $this->enviroment // enviroment type: 1 => test, 2 => production
            . $this->establishment->code . $this->issuancePoint->code // series: 001001
            . $sequential
            . '12345678' // random number code (8 length)
            . '1' // issuance type: 1 => normal
            ;
        $access_key .= $this->calcVerifierDigit($access_key);
        return $access_key;
    }

    private function calcVerifierDigit(string $access_key): string
    {
        $digits = str_split($access_key);
        $factors = [7, 6, 5, 4, 3, 2];
        $factors_pointer = 0;

        $summation = 0;

        for($i = 0; $i < count($digits); $i++){
            $summation += $digits[$i] * $factors[$factors_pointer];
            $factors_pointer = ($factors_pointer == 5) ? 0 : $factors_pointer + 1;
        }

        $module =  $summation % 11;
        $subtract = 11 - $module;
        if($subtract == 11){
            $verifierDigit = 0;
        } else if($subtract == 10){
            $verifierDigit = 1;
        } else {
            $verifierDigit = $subtract;
        }
        return $verifierDigit;
    }
}