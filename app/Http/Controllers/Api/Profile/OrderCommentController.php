<?php

namespace App\Http\Controllers\Api\Profile;

use App\Models\Order;
use App\Models\Comment;
use Illuminate\Http\Request;
class OrderCommentController
{
    public function store(Request $request, $orderId)
    {
        $request->validate([
            'text' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $user = auth()->user();
        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->with('restaurant')
            ->first();

        if (!$order) {
            return api_response([],'سفارش یافت نشد یا متعلق به شما نیست');
        }

        if ($order->comment) {
            return api_response([], 'برای این سفارش قبلاً کامنت ثبت شده است');
        }

        $comment = Comment::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'text' => $request->text,
            'rating' => $request->rating,
        ]);

        return api_response([],'کامنت با موفقیت ثبت شد');
    }
}
