<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::latest()->paginate(12);
        Notification::query()->update(['is_seen' => 1]);

        return view('admin.notifications.index', compact('notifications'));
    }
    public function clear()
    {
        Notification::truncate(); // همه رکوردها حذف می‌شود

        return back()->with('success', 'همه نوتیف‌ها حذف شدند.');
    }


}
