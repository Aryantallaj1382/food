<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
class AdminFeedbackController extends Controller
{

    public function index()
    {
        $feedbacks = Feedback::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.feedback.index', compact('feedbacks'));
    }

    public function update(Request $request, Feedback $feedback)
    {
        $request->validate([
            'answer' => 'required|string|max:1000'
        ]);

        $feedback->update([
            'answer' => $request->answer
        ]);

        return redirect()->route('admin.feedback.index')
            ->with('success', 'پاسخ با موفقیت ثبت شد');
    }
}
