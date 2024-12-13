<?php

namespace App\Http\Controllers\Receipts\Invoices;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\Receipts\Invoices\IssueRequest;

class Controller extends BaseController
{
    public function issue(IssueRequest $request, Builder $builder)
    {
        $validated = $request->validated();
        $user = $this->authUser();
        // Check certificate is uploaded
        if( ! $user->certificate->uploaded )
            return response(['message' => 'No se ha subido la firma electrónica.'], 422);
        // Build Invoice
        return $builder->build($validated, $user);
        return $validated;
    }
}
