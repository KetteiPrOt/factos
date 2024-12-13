<?php

namespace App\Http\Controllers\Receipts\Invoices;

use App\Http\Controllers\Controller;
use App\Http\Resources\Receipts\PayMethods\Resource;
use App\Models\Receipts\Invoices\PayMethod;
use Illuminate\Http\Request;

class PayMethodController extends Controller
{
    public function index()
    {
        return Resource::collection(PayMethod::all());
    }
}
