<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected string $openssl = '';

    protected string $storage_path = '../storage/app/private';

    public function __construct()
    {
        $this->openssl = config('app.openssl-cli', 'openssl');
    }

    protected function authUser(): ?User
    {
        return Auth::user();
    }

    protected function numberToChar(int $n): string
    {
        // 100 - 999
        if($n > 99) return "$n";
        // 10 - 99
        if($n > 9) return "0$n";
        // 1 - 9
        return "00$n";
    }
}
