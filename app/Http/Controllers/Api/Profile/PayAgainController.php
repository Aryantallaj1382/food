<?php

namespace App\Http\Controllers\Api\Profile;

use App\Helpers\ParsianPayment;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PayAgainController extends Controller
{
    public function index(Request $request)
    {
        $order = Order::find($request->id);
        if ($order->restaurant->is_open == false) {
            return api_response([],'با عرض پوزش رستوران بسته شده سات', 422);
        }

        $inactiveFoods = [];

        foreach ($order->items as $orderItem) {
            $food = $orderItem->option; // یا $orderItem->meal یا هر اسمی که داری

            if (!$food || !$food->is_available) { // فیلد فعال بودن غذا معمولاً is_active یا status هست
                $inactiveFoods[] = $food ? $food->food->name : 'غذای ناموجود شده';
            }
        }

        if (!empty($inactiveFoods)) {
            return api_response(
                [],
                "متأسفانه برخی از غذاهای انتخابی شما غیرفعال شده‌اند . لطفاً سبد خرید خود را بررسی و مجدداً اقدام به پرداخت نمایید.",
                422
            );
        }

        Payment::create([
            'user_id' => $order->user->id,
            'order_id' => $order->id,
            'amount' =>$order->total_amount,
            'payment_method' => 'both',
            'gateway' => 'pars',
        ]);
        $x = ui_code($order->id , $order->user->id);
        $a = new ParsianPayment();
        $invoiceNo =  $this->generateInvoiceNo();
        $order->invoice_no = $invoiceNo;
        $order->save();

        $b = $a->pay($invoiceNo , (int)$order->total_amount , 'https://api.testghazaresan.ir/api/order/callback?order_id=' . $order->id. '&uni=' . $x );
        return api_response($b,'سفارش با موفقیت ثبت شد.');
    }
    protected $payment;

    public function __construct(ParsianPayment $payment)
    {
        $this->payment = $payment;
    }



    public function generateInvoiceNo()
    {
        do {
            $invoice = time() . rand(1000,9999); // یا از DB یک شماره sequential بگیرید
        } while (Order::where('invoice_no', $invoice)->exists());

        return $invoice;
    }
}
