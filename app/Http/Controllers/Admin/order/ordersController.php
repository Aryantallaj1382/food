<?php

namespace App\Http\Controllers\Admin\order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Morilog\Jalali\Jalalian;

class ordersController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $status = $request->query('status');
        $payment = $request->query('payment_status');
        $date = $request->query('date'); // فیلتر تاریخ میلادی

        $orders = Order::with(['user', 'restaurant'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('id', $q)
                        ->orWhereHas('user', fn($u) =>
                        $u->where('mobile', 'like', "%{$q}%")
                            ->orWhere('first_name', 'like', "%{$q}%")
                            ->orWhere('last_name', 'like', "%{$q}%")
                        )
                        ->orWhereHas('restaurant', fn($r) =>
                        $r->where('name', 'like', "%{$q}%")
                        );
                });
            })
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($payment, fn($query) => $query->where('payment_status', $payment))
            ->when($date, fn($query) => $query->whereDate('created_at', $date)) // تاریخ میلادی
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

// AdminOrderController.php
    public function updateAdminNote(Order $order, Request $request)
    {
        $request->validate(['admin_note' => 'nullable|string|max:1000']);

        $order->update(['admin_note' => $request->admin_note]);

        return response()->json(['message' => 'ذخیره شد']);
    }
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required',
        ]);
        if ($request->status == 'processing') {


            if ($order->sending_method == 'in_person')
            {
                $getReadyTime = $order->get_ready_time;

                $getReadyTimeJalali = $getReadyTime
                    ? Jalalian::fromDateTime($getReadyTime)->format('H:i')
                    : 'بزودی';

                $mobile = $order->user->mobile;
                $data = [
                    'readytime' => $getReadyTimeJalali,
                ];

                sms('j5ztbv1xqsaqv6x', $mobile, $data);

            }
            if ($order->sending_method == 'pike')
            {
                $mobile = $order->user->mobile;
                $data = [
                    'name' =>$order->user->name ,
                ];
                sms('0xxkazsqtxh2mc2' ,$mobile , $data );
            }
        }

        $order->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

}
