<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Models\feedback;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class FeedBackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required',
            'rating' => 'required',
        ]);
        $user = auth()->user();
        $feed  = FeedBack::create([
            'user_id' => $user->id,
            'text' => $request->text,
            'rating' => $request->rating,
        ]);
        return api_response([],'موفقیت ثبت شد');

    }
    public function index()
    {
        $user = auth()->user();
        $feed  = FeedBack::where('user_id', $user->id)->latest()->get();
        $a = $feed->map(function ($item) {
            return [
                'id' => $item->id,
                'commentText' => $item->text,
                'userName' => $item->user->name,
                'rating' => $item->rating,
                'answer' => $item->answer,
                'createdDate' => Jalalian::fromCarbon($item->created_at)->format('Y/m/d'),

            ];
        });
        return api_response($a);

    }

}
