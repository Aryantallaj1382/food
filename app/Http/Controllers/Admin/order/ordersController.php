<?php

namespace App\Http\Controllers\Admin\order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
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

}
