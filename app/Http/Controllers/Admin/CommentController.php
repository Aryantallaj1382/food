<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::with(['user', 'order.restaurant', 'replies.user'])
            ->whereNull('parent_comment_id') // فقط کامنت‌های اصلی
            ->latest()
            ->paginate(10);

        return view('admin.comments.index', compact('comments'));
    }
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // اگر ریپلای دارد، حذفش کنیم
        if ($comment->replies()->count()) {
            $comment->replies()->delete();
        }

        $comment->delete();

        return back()->with('success', 'کامنت با موفقیت حذف شد.');
    }


}
