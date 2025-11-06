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

        $orders = Order::with(['user']) // eager load روابط مفید
        ->when($q, fn($qry) => $qry->where(function($sub){
            $term = request('q');
            $sub->where('id', $term)
                ->orWhere('tracking_number', 'like', "%{$term}%")
                ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
        }))
            ->when($status, fn($qry) => $qry->where('status', $status))
            ->when($payment, fn($qry) => $qry->where('payment_status', $payment))
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

}
