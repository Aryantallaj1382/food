<?php

namespace App\Http\Controllers\Api\Profile;

use App\Helpers\ParsianPayment;
use App\Helpers\ParsinForWallet;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $wallet = $user->wallet->balance;
        return api_response($wallet);
    }
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required',
            'gateway' => 'required',
        ]);
        $user = auth()->user();
        if ($request->gateway == 'pars') {
            $a = new ParsinForWallet();
              $id =   Payment::create([
                    'amount'       => $request->amount,
                    'transaction_id'    =>null,
                    'status'       => 'pending',
                    'gateway' => 'pars',
                    'user_id'      => $user->id,
                ]);
            $b = $a->pay($request->amount , $user->id , 'https://api.testghazaresan.ir/api/wallet/callback?id=' . $id->id );
            $id->update([
                'transaction_id' => $b['token']
            ]);
            return api_response($b['url']);
        }
        else{
            return api_response([],'درگاه پرداخت تعریف نشده');
        }



    }
    protected $payment;

    public function __construct(ParsianPayment $payment)
    {
        $this->payment = $payment;
    }
    public function callback(Request $request)
    {
        $order_id = $request->input('id');

        if (!$order_id ) {
            return $this->errorResponse('پارامترهای نامعتبر.');
        }
        $order = Payment::where('id', $order_id)->first();
        $token = $order->transaction_id;
        if (!$order) {
            return $this->errorResponse('پرداخت یافت نشد.');
        }

        $confirmResult = $this->payment->confirm($token);

        if (!$confirmResult['success']) {
            $order->update([
                'status' => 'failed',
            ]);
            return $this->errorResponse(
                'پرداخت ناموفق بود. کد خطا: ' . ($confirmResult['code'] ?? 'نامشخص')
            );
        }
        $order->update([
            'status' => 'paid',
        ]);
        $user = $order->user;

        $wallet = $user->wallet()->firstOrCreate(
            [],
            ['balance' => 0]
        );
        $wallet->balance += $order->amount;
        $wallet->save();


        return $this->successResponse($order, 'پرداخت با موفقیت انجام شد.');
    }
    private function successResponse($order, $message)
    {
        return redirect('https://testghazaresan.ir/profile/wallet/success');
//        return $message ;
    }

    // پاسخ خطا
    private function errorResponse($message)
    {
        return redirect('https://testghazaresan.ir/profile/wallet/failed');
//        return $message ;



    }
}
