<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $addresses = $user->addresses->where('is_main', true)->first();
        return api_response([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'mobile' => $user->mobile,
            'phone' => $user->phone,
            'balance' => (int)$user->wallet?->balance ?? 0,
            'address' => $addresses,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'phone' => 'nullable',
            'password' => 'nullable',
        ]);
        $user = auth()->user();
        $updateData =  [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
        ];
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        $user->update($updateData);

        return api_response([], 'اطلاعات ویرایش شد');
    }
}
