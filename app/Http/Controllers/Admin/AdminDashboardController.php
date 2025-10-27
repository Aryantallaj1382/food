<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;



class AdminDashboardController extends  Controller
{
    public function index()
    {
        $user_count = User::count();
        return view('dashboard', compact(['user_count' ]));
    }



}
