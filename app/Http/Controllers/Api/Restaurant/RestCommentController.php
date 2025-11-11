<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class RestCommentController extends Controller
{
    public function index()
    {
//        $user = auth()->user();
        $user = User::find(6);

        $comments = Comment::whereHas('order', function ($query) use ($user) {
                $query->whereHas('restaurant', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
        })->paginate();
        $comments->getCollection()->transform(function ($item, $key) use ($user) {
           return [
               'id' => $item->id,
               'text' => $item->text,
               'profile' => $item->user->profile,
               'user_name' => $item->user->name,
               'rate' => $item->rate,
               'is_liked' => $user
                   ? $item->likes->where('user_id', $user->id)->where('is_like', true)->isNotEmpty()
                   : false,
               'is_disliked' => $user
                   ? $item->likes->where('user_id', $user->id)->where('is_like', false)->isNotEmpty()
                   : false,
               'order_id' => $item->order_id,
               'created' => $item->created_at ? Jalalian::fromCarbon($item->created_at)->format('Y/m/d') : null,
               'replies' => $item->replies->map(function ($item, $key) {
                   return [
                     'id' => $item->id,
                     'text' => $item->text,
                   ];
               }),

               'likesCount' => $item->likesCount(),
               'dislikesCount' => $item->dislikesCount(),
               'order' => $item->order->items->pluck('option.food.name')->toArray(),

           ];
        });
        return api_response($comments);

    }
    public function reply(Request $request, $id)
    {
        $request->validate([
            'text' => 'required|string|max:1000',
        ]);

        $parentComment = Comment::findOrFail($id);

         Comment::create([
            'user_id' => auth()->id(),          // کاربر فعلی
            'text' => $request->input('text'),
            'parent_comment_id' => $parentComment->id,  // کامنت پدر
        ]);

        return api_response([], 'پاسخ با موفقیت ثبت شد');
    }

}
