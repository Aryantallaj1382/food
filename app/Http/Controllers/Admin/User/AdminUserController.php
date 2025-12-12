<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\Transaction;
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
        $orders = Order::where('user_id', $id)->where('payment_status', 'paid')->orWhere('payment_status', 'cash')->paginate(10);
        $address = Address::where('user_id', $id)->get();
        return view('admin.users.show', compact(['user', 'orders', 'address']));
    }




    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'operation_type' => 'required|in:deposit,withdraw',
            'amount' => 'required|numeric|min:1',
        ]);

        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = $user->wallet()->create(['balance' => 0]);
        }

        if ($data['operation_type'] === 'deposit') {
            $wallet->balance += $data['amount'];
        } else {
            if ($wallet->balance < $data['amount']) {
                return back()->with('error', 'موجودی کافی نیست.');
            }
            $wallet->balance -= $data['amount'];
        }
        $wallet->save();



        return back()->with('success', 'تراکنش با موفقیت ثبت شد.');
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
    public function edit_user($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }
    public function update_user(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'mobile'     => 'required|digits:11|unique:users,mobile,' . $user->id,
            'phone'      => 'nullable|string|max:20',
            'password'   => 'nullable|confirmed',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->mobile     = $request->mobile;
        $user->phone      = $request->phone;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'کاربر با موفقیت ویرایش شد');
    }


}
