<?php

namespace App\Jobs\Invoices;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Receipts\Model as Invoice;
use Exception;
use SoapClient;

class RequestAuthorization implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Invoice $invoice
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $invoice = $this->invoice;
        try{
            $client = new SoapClient('https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl');
            $response = $client->autorizacionComprobante([
                'claveAccesoComprobante' => $invoice->access_key
            ]);
            dump($response);
            $status = $this->extractStatus($response);
            if($status == 'AUTORIZADO'){
                $invoice->status = 'Autorizada';
            } else if($status == 'NO AUTORIZADO') {
                $invoice->status = 'No autorizada';
            } else if($status == 'EN PROCESAMIENTO') {
                $invoice->status = 'En procesamiento';
            } else {
                $invoice->status = 'Sin respuesta';
            }
        }catch(Exception $e){}
        $invoice->save();
    }
    
    private function extractStatus($response)
    {
        if($response){
            if($response?->RespuestaAutorizacionComprobante){
                $response = $response->RespuestaAutorizacionComprobante;
                if($response?->autorizaciones){
                    $response = $response?->autorizaciones;
                    if($response?->autorizacion){
                        $response = $response?->autorizacion;
                        if($response?->estado){
                            $response = $response?->estado;
                            return $response;
                        }
                    }
                }
            }
        }
        return null;
    }
}
