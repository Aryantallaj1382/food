<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $wallet = $user->wallet->balance;
        return api_response($wallet);
    }
}
