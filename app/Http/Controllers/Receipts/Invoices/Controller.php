<?php

namespace App\Http\Controllers\Receipts\Invoices;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\Receipts\Invoices\IssueRequest;

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
        return $signed;
    }
}
