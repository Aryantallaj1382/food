<?php

namespace App\Http\Controllers\Admin\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class AdminTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Restaurant::select('id', 'name')
            ->withSum(['transactions as credit_sum' => function ($q) {
                $q->where('type', 'credit')->where('status', 'success');
            }], 'amount')
            ->withSum(['transactions as debit_sum' => function ($q) {
                $q->where('type', 'debit')->where('status', 'success');
            }], 'amount');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $restaurants = $query->get()->map(function ($restaurant) {
            $restaurant->balance = ($restaurant->credit_sum ?? 0) - ($restaurant->debit_sum ?? 0);
            return $restaurant;
        });

        if ($request->filled('status')) {
            if ($request->status == 'credit') {
                $restaurants = $restaurants->filter(fn($r) => $r->balance > 0);
            } elseif ($request->status == 'debit') {
                $restaurants = $restaurants->filter(fn($r) => $r->balance < 0);
            } elseif ($request->status == 'zero') {
                $restaurants = $restaurants->filter(fn($r) => $r->balance == 0);
            }
        }

        return view('admin.transation.index', compact('restaurants'))
            ->with('filters', $request->only(['search', 'status']));
    }
    public function show($id, Request $request)
    {
        // پیدا کردن رستوران
        $restaurant = Restaurant::findOrFail($id);

        // گرفتن تراکنش‌ها
        $transactions = $restaurant->transactions()->orderBy('created_at', 'desc');

        // فیلتر بر اساس نوع تراکنش
        if ($request->filled('type')) {
            $transactions->where('type', $request->type);
        }

        // فیلتر بر اساس وضعیت تراکنش
        if ($request->filled('status')) {
            $transactions->where('status', $request->status);
        }

        // امکان paginate کردن برای مدیریت تعداد رکوردها
        $transactions = $transactions->paginate(20)->withQueryString();

        // محاسبه مجموع اعتبار و بدهی برای رستوران
        $credit_sum = $restaurant->transactions()->where('type', 'credit')->where('status', 'success')->sum('amount');
        $debit_sum = $restaurant->transactions()->where('type', 'debit')->where('status', 'success')->sum('amount');
        $balance = $credit_sum - $debit_sum;

        return view('admin.transation.show', compact('restaurant', 'transactions', 'credit_sum', 'debit_sum', 'balance'))
            ->with('filters', $request->only(['type', 'status']));
    }
// نمایش فرم ثبت تراکنش بستانکار
    public function createCredit($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        return view('admin.transation.credit', compact('restaurant'));
    }

// ذخیره تراکنش بستانکار
    public function storeCredit(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
            'tracking_code' => 'nullable',
        ]);

        $restaurant->transactions()->create([
            'type' => 'credit',
            'status' => 'success',
            'amount' => $request->amount,
            'description' => $request->description,
            'tracking_code' => $request->tracking_code,
        ]);
        if ($request->filled('tracking_code')) {
            $mobile = $restaurant->user->mobile;
            $data = ['amount' => $request->amount,
                'tracking-code' => $request->tracking_code,
            ];
            sms('b29k99yczx47hsh' ,$mobile , $data );

        }

        return redirect()->route('admin.restaurants.balance', $restaurant->id)
            ->with('success', 'تراکنش بستانکار با موفقیت ثبت شد.');
    }


}
