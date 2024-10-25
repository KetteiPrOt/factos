<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\Certificate\UpdateRequest;
use App\Http\Resources\Certificates\ShowResource;
use DateTime;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    private string $signature;

    public function show()
    {
        return new ShowResource(
            $this->authUser()->certificate
        );
    }

    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();
        $user = $this->authUser();
        $user->certificate->update(['password' => $validated['password']]);
        Storage::putFileAs('/signatures', $request->file('signature'), name: "$user->id");
        return $this->unlock($validated['password']);
    }

    private function unlock(string $password): Response
    {
        $status = 422;
        $unknow_details = [
            'uploaded' => false, 'effective_date' => null, 'owner' => 'Desconocido.'
        ];
        $data = [
            'message' => 'El certificado de firma digital es inválido o la contraseña no es correcta.',
            'errors' => [
                'signature' => ['El certificado podría estar corrompido.'],
                'password' => ['La contraseña podría ser incorrecta.']
            ]
        ];
        $user = $this->authUser();
        $openssl = $this->openssl; $storage_path = $this->storage_path;
        $user_id = $user->id; $password = escapeshellarg($password);
        $command = "$openssl pkcs12 -in $storage_path/signatures/$user_id -nodes -passin pass:$password";
        $output = shell_exec($command);
        if($output){
            $this->signature = $output;
            $data = $this->extractDetails();
            $status = 200;
        } else {
            $user->certificate->update($unknow_details);
        }
        return response($data, $status);
    }

    private function extractDetails(): array
    {
        $user = $this->authUser();
        $openssl = $this->openssl;
        $storage_path = $this->storage_path; $user_id = $user->id;
        $file = "$storage_path/signatures/$user_id.pem";
        File::put($file, $this->signature);
        // Extract subject
        $command = "$openssl x509 -in $file -noout -subject";
        $owner = shell_exec($command) ?? 'Desconocido.';
        // Extract effective date
        $command = "$openssl x509 -in $file -noout -enddate";
        $effective_date = shell_exec($command);
        $effective_date = $this->formatDate($effective_date);
        File::delete($file);
        $user->certificate->update([
            'uploaded' => true, 'effective_date' => $effective_date, 'owner' => $owner
        ]);
        return ['message' => 'Guardado con éxito.'];
    }

    private function formatDate(?string $date): ?string
    {
        if(is_null($date)) return null;
        $date = Str::betweenFirst($date, 'notAfter=', '\n');
        return (new DateTime($date))->format('Y-m-d');
    }
}
