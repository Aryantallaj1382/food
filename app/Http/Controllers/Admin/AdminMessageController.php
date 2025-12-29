<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    public function show()
    {
        $message = SystemSetting::where('kay', 'admin_message')->value('value');
        return view('admin.message.show', compact('message'));
    }

    public function update(Request $request)
    {

        $message = SystemSetting::where('kay', 'admin_message')->first();
        $message->value = $request->message;
        $message->save();
        User::query()->update([
            'admin_message' => false
        ]);
        return redirect()->route('admin.message.show');
    }


}
