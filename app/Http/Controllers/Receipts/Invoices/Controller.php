<?php

namespace App\Http\Controllers\Receipts\Invoices;

use App\Models\Receipts\Model as Receipt;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\Invoices\Receipts\IndexRequest;
use App\Http\Requests\Receipts\Invoices\IssueRequest;
use App\Http\Resources\Receipts\Invoices\Index\PaginatedCollection;
use App\Jobs\Invoices\RequestAuthorization;
use App\Models\Receipts\Type as ReceiptType;
use Exception;
use SoapClient;

class Controller extends BaseController
{
    public function issue(IssueRequest $request, Builder $builder, Signer $signer)
    {
        $validated = $request->validated();
        $user = $this->authUser();
    
        // Check certificate is uploaded
        if( ! $user->certificate->uploaded )
            return response(['message' => 'No se ha subido la firma electrónica.'], 422);
    
        $raw = $builder->build($validated, $user);

        // Check total value
        if($builder->total_pay_methods > floatval($builder->total))
            return response(
                ['message' => 'Los pagos deben ser menor o iguales a la suma total de la factura.'],
                422
            );
    
        $signed = $signer->sign($raw, $user, $this->openssl);
    
        try {
            $client = new SoapClient('https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl');
            $response = $client->validarComprobante(['xml' => $signed]);
        } catch(Exception $e) {
            $response = null;
        }

        $status = 'No emitida.';

        if(isset($response?->RespuestaRecepcionComprobante)){
            if(isset($response?->RespuestaRecepcionComprobante?->estado)){
                $status =
                    $response?->RespuestaRecepcionComprobante?->estado
                    == 'RECIBIDA'
                    ? 'Emitida.'
                    : 'No emitida.';
            }
        }

        $invoice = Receipt::create([
            'access_key' => $builder->access_key,
            'issuance_date' => $validated['issuance_date'],
            'number' => $builder->establishment->code
                . '-' . $builder->issuancePoint->code
                . '-' . $builder->sequential, // 001-001-000000001
            'status' => $status,
            'content' => $signed,
            'user_id' => $user->id,
            'receipt_type_id' => ReceiptType::where('name', 'FACTURA')->first()->id
        ]);

        RequestAuthorization::dispatch($invoice);

        return response(['message' => $status], 200);
    }

    public function index(IndexRequest $request)
    {
        // $validated = $request->validated();
        $receipts = Receipt::where('user_id', $this->authUser()->id);
        return new PaginatedCollection(
            $receipts->orderBy('issuance_date')->paginate(10)->withQueryString()
        );
    }
}
