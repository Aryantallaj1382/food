<?php

namespace App\Helpers;
use App\Models\Order;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Log;
use SoapClient;
class ParsinForWallet{
    private $pin;
    private $callbackUrl;
    private $wsdl = 'https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?WSDL';

    public function __construct()
    {
        $this->pin = config('parsian.pin');
        $this->callbackUrl = config('parsian.callback_url');
    }

    /**
     * شروع پرداخت
     * فقط مبلغ را می‌گیرد
     */
    public function pay(int $amount,int $user, $callback, string $additionalData = '', string $originator = '')
    {
        if ($amount <= 0) {
            abort(422, 'مبلغ باید بزرگ‌تر از صفر باشد.');
        }
        $orderId = time() . rand(1000, 9999);

        $result = $this->requestPayment($orderId, $amount, $additionalData, $originator, $callback);

        return $result;
    }

    private function requestPayment($orderId, $amount, $additionalData, $originator, $callback)
    {
        $params = [
            "LoginAccount"   => $this->pin,
            "Amount"         => (int)$amount,
            "OrderId"        => $orderId,
            "CallBackUrl"    => $callback,
            "AdditionalData" => $additionalData,
            "Originator"     => $originator
        ];

        try {
            $client = new SoapClient($this->wsdl, ['cache_wsdl' => WSDL_CACHE_NONE]);
            $result = $client->SalePaymentRequest(['requestData' => $params]);
            $res = $result->SalePaymentRequestResult;

            if ($res->Status == 0 && !empty($res->Token)) {
                return [
                    'success' => true,
                    'token'   => $res->Token,
                    'url'     => "https://pec.shaparak.ir/NewIPG/?Token={$res->Token}"
                ];
            }

            return [
                'success' => false,
                'code'    => $res->Status,
                'error'   => $res->Message ?? 'خطای ناشناخته'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error'   => 'خطای ارتباط با درگاه: ' . $e->getMessage()
            ];
        }
    }

    /**
     * تأیید پرداخت و بروزرسانی رکورد payment
     */
    public function confirm($token)
    {
        $confirmWsdl = 'https://pec.shaparak.ir/NewIPGServices/Confirm/ConfirmService.asmx?WSDL';
        $params = [
            "LoginAccount" => $this->pin,
            "Token"        => $token
        ];

        try {
            $client = new SoapClient($confirmWsdl, [
                'cache_wsdl' => WSDL_CACHE_NONE,
                'trace'      => true,
                'exceptions' => true
            ]);

            $result = $client->ConfirmPayment(['requestData' => $params]);
            $res = $result->ConfirmPaymentResult;

            $payment = Payment::where('authority', $token)->first();

            if ($res->Status == 0) {
                // موفقیت
                if ($payment) {
                    $payment->update([
                        'status' => 'paid',
                        'rrn'    => $res->RRN ?? null,
                        'card'   => $res->CardNumberMasked ?? null,
                        'amount' => $res->Amount ?? $payment->amount,
                    ]);
                }

                return [
                    'success' => true,
                    'rrn'     => $res->RRN ?? null,
                    'card'    => $res->CardNumberMasked ?? null,
                    'amount'  => $res->Amount ?? null
                ];
            } else {
                if ($payment) {
                    $payment->update([
                        'status' => 'failed',
                        'error'  => $res->Message ?? 'خطای ناشناخته',
                    ]);
                }

                return [
                    'success' => false,
                    'code'    => $res->Status,
                    'error'   => $res->Message ?? 'خطای ناشناخته'
                ];
            }

        } catch (Exception $e) {
            if (isset($payment)) {
                $payment->update(['status' => 'failed', 'error' => 'خطای ارتباط با درگاه: ' . $e->getMessage()]);
            }

            return [
                'success' => false,
                'error'   => 'خطای ارتباط با درگاه: ' . $e->getMessage()
            ];
        }
    }
}
