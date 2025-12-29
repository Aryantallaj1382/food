<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $mobile1 = SystemSetting::where('kay', 'mobile1')->value('value');
        $mobile2 = SystemSetting::where('kay', 'mobile2')->value('value');
        $mobile3 = SystemSetting::where('kay', 'mobile3')->value('value');
        return view('admin.support.index' , compact('mobile1', 'mobile2', 'mobile3'));
    }
    public function update(Request $request)
    {
        // اعتبارسنجی ورودی‌ها
        $request->validate([
            'mobile1' => 'required|string|max:20',
            'mobile2' => 'nullable|string|max:20',
            'mobile3' => 'nullable|string|max:20',
        ]);

        // آپدیت یا ایجاد تنظیمات (اگر وجود نداشته باشد، ایجاد می‌شود)
        SystemSetting::updateOrCreate(
            ['kay' => 'mobile1'],
            ['value' => $request->mobile1]
        );

        SystemSetting::updateOrCreate(
            ['kay' => 'mobile2'],
            ['value' => $request->mobile2 ?? '']
        );

        SystemSetting::updateOrCreate(
            ['kay' => 'mobile3'],
            ['value' => $request->mobile3 ?? '']
        );

        return redirect()
            ->route('admin.support.index') // نام روت صفحه index
            ->with('success', 'شماره‌های پشتیبانی با موفقیت به‌روزرسانی شدند.');
    }

}
