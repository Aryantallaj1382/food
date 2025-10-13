<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $addresses = $user->addresses;
        return api_response($addresses);
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'address' => 'required',
            'lat' => 'required',
            'lng' => 'required',
        ]);
        $address = Address::create([
            'address' => $request->address,
            'latitude' => $request->lat,
            'longitude' => $request->lng,
            'is_main' => false,
            'user_id' => $user->id,

        ]);
        return api_response([], 'با موفقیت ایجاد شد');
    }
    public function delete(Request $request, $id)
    {
        $user = auth()->user();
        $address = Address::where('user_id' , $user->id)->find($id);
        $address->delete();
        return api_response([], 'با موفقیت حذف شد');

    }
    public function update(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'address' => 'nullable',
            'id' => 'required',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ]);
        $address = Address::where('user_id' , $user->id)->find($request->id);

        $address->update([
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_main' => false,

        ]);
        return api_response([], 'با موفقیت ویراسش شد');
    }
    public function update_is_main(Request $request)
    {
        $user = auth()->user();

        Address::where('user_id', $user->id)->update([
            'is_main' => false,
        ]);
        $address = Address::where('user_id', $user->id)->findOrFail($request->id);
        $address->update([
            'is_main' => true,
        ]);
        return api_response([], 'با موفقیت ویراسش شد');
    }

}
