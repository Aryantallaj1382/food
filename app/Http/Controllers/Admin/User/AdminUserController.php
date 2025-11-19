<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with('wallet');
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }
        if (!is_null($request->input('is_blocked'))) {
            $query->where('is_blocked', $request->input('is_blocked'));
        }

        if ($walletFilter = $request->input('wallet_balance')) {
            switch ($walletFilter) {
                case 'has_balance':
                    $query->whereHas('wallet', fn($q) => $q->where('balance', '>', 0));
                    break;
                case 'zero_balance':
                    $query->whereHas('wallet', fn($q) => $q->where('balance', '=', 0));
                    break;
                case 'no_wallet':
                    $query->doesntHave('wallet');
                    break;
            }
        }
        if ($request->input('sort') === 'loyal') {
            $query->withCount('orders')
            ->orderByDesc('orders_count');
        } else {
            $query->latest();
        }

        $users = $query->paginate(10)->appends($request->query());

        return view('admin.users.index', compact('users'));
    }


    public function show($id)
    {
        $user = User::findOrFail($id);
        $orders = Order::where('user_id', $id)->paginate(10);
        $address = Address::where('user_id', $id)->get();
        return view('admin.users.show', compact(['user', 'orders', 'address']));
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'کاربر با موفقیت حذف شد.');
    }

    public function create()
    {
        return view('admin.users.create');
    }

    // 💾 ذخیره کاربر جدید
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20|unique:users,mobile',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'کاربر جدید با موفقیت ایجاد شد ✅');
    }
    public function block(Request $request, User $user)
    {
        $request->validate(['reason'=>'required|string']);
        $user->update([
            'is_blocked' => true,
            'block_reason' => $request->reason,
        ]);
        return response()->json(['success'=>true]);
    }

    public function unblock(User $user)
    {
        $user->update([
            'is_blocked' => false,
            'block_reason' => null,
        ]);
        return response()->json(['success'=>true]);
    }

}
