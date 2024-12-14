<?php

namespace App\Http\Controllers\Receipts\Invoices;

use App\Models\Receipts\Model as Receipt;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\Receipts\Invoices\IssueRequest;
use App\Models\Receipts\Type as ReceiptType;
use Exception;
use SoapClient;
use stdClass;

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

        Receipt::create([
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

        return response(['message' => $status], 200);
    }
}
