<?php

namespace App\Http\Controllers\Receipts\Invoices;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\Receipts\Invoices\IssueRequest;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    public function issue(IssueRequest $request)
    {
        $validated = $request->validated();
        return $validated;
    }
}
