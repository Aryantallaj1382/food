<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class AdminReportRestaurantController extends Controller
{
    public function toggleAll(Request $request)
    {
        $status = $request->input('status'); // همیشه 0 یا 1

        if (!in_array($status, [0, 1])) {
            return back()->with('error', 'وضعیت نامعتبر است.');
        }

        \App\Models\Restaurant::query()->update(['is_open' => $status]);

        $message = $status == 1 ? 'همه رستوران‌ها فعال شدند.' : 'همه رستوران‌ها غیرفعال شدند.';
        return back()->with('success', $message);
    }

    public function reports(Restaurant $restaurant)
    {
        return view('admin.restaurants.reports', compact('restaurant'));
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $restaurants = Restaurant::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.report.index', compact('restaurants', 'search'));
    }

    public function salesReport(Request $request, Restaurant $restaurant)
    {
        $query = Order::where('restaurant_id', $restaurant->id);

        // فیلتر تاریخ
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // گرفتن سفارش‌ها
        $orders = $query->with('items')->latest()->get();

        // محاسبه مجموع فروش
        $totalSales = 0;

        foreach ($orders as $order) {
            $order->sum_items = $order->items->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $totalSales += $order->sum_items;
        }

        return view('admin.report.sales-report', compact('restaurant', 'orders', 'totalSales'));
    }

    public function payouts(Request $request, $id)
    {
        $query = \App\Models\Transaction::where('restaurant_id', $id)->where('type', 'credit')->whereNotNull('tracking_code');

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(20);

        $totalAmount = $query->sum('amount');

        return view('admin.report.payouts', compact('transactions', 'totalAmount', 'id'));
    }
    public function ordersCount(Request $request, $id)
    {
        $query = \App\Models\Order::where('restaurant_id', $id);

        // فیلتر تاریخ — میلادی
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // فیلتر نوع پرداخت
        if ($request->payment_method && in_array($request->payment_method, ['online', 'cash'])) {
            $query->where('payment_method', $request->payment_method);
        }

        $orders = $query->orderBy('id', 'desc')->paginate(20);
        $ordersCount = $query->count();

        return view('admin.report.orders_count', compact('orders','ordersCount','id'));
    }

}
