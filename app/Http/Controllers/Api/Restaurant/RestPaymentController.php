<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class RestPaymentController extends Controller
{
    public function index(Request $request)
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
        $payment = $query->latest()->paginate(15);


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
