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
        $user = auth()->user();


        $comments = Comment::whereHas('order', function ($query) use ($user) {
                $query->whereHas('restaurant', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
        })->latest()->paginate();

        $comments->getCollection()->transform(function ($item, $key) use ($user) {
            $order = $item->order->items->map(function($orderItem) {
                $foodName = $orderItem->option?->food?->name ?? '';
                $optionName = $orderItem->option?->name;

                return $optionName ? "$foodName $optionName" : $foodName;
            })->toArray();

            return [
               'id' => $item->id,
               'text' => $item->text,
               'profile' => $item->user->profile,
               'user_name' => $item->user->first_name,
               'rate' => $item->rating,
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
               'order' => $order ,

           ];
        });
        return api_response($comments);

    }
    public function reply(Request $request, $id)
    {
        if (empty($request->text)){
            return api_response([],'متن پاسخ نباید خالی باشد',422);
        }
        $parentComment = Comment::findOrFail($id);

        $text = $request->input('text', '');

        $existingReply = Comment::where('parent_comment_id', $parentComment->id)
            ->first();

        if ($existingReply) {
            $existingReply->delete();
        }
        if ($text !== '') {
            Comment::create([
                'user_id' => auth()->id(),
                'text' => $text,
                'parent_comment_id' => $parentComment->id,
            ]);
        }

        return api_response([], $text !== '' ? 'پاسخ با موفقیت ثبت شد' : 'پاسخ حذف شد');
    }


}
