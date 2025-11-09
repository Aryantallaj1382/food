<?php

namespace App\Http\Controllers\Api\Restaurant;
use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Payment;
use Illuminate\Http\Request;


class ProductsMenuController extends controller
{
    public function index_food(Request $request){
        $query=Food::query();

        if ($foodName = $request->input('food_name')) {
            $query->where('name', $foodName);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        $foods = $query->orderBy('created_at', 'desc')->paginate(5);

        $foods->getCollection()->transform(function ($food) {
            return [
                'id' => $food->id,
                'name' => $food->name.'-'.$food->options?->name.'-'.$food->category?->name,
                'is_available'=>$food->options->is_available,


                'price' => $food->options?->price,
                'description' => $food->description,
                'created_at' => $food->created_at->format('Y-m-d H:i'),
            ];
        });

        return api_response($foods, 'فیلتر و جستجو با موفقیت انجام شد');



    }





    public function filterPayment(Request $request)
    {
        $query = Payment::query();


        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }


        $payment = $query->orderBy('created_at', 'desc')->paginate(15);


        $payment->getCollection()->transform(function ($pay) {
            return [
                'id' => $pay->id,
                'amount' => $pay->amount,
                'notes'=> $pay->notes,
                'status' => $pay->status,
                'payment_method' => $pay->payment_method,
                'created_at' => $pay->created_at?->format('d-m-Y H:i'),
            ];
        });

        return api_response($payment, 'تراکنش‌ها با موفقیت فیلتر شدند');
    }






}
