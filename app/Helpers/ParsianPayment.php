<?php
// helpers/ParsianPayment.php
namespace App\Helpers;

use Exception;
use SoapClient;

class ParsianPayment
{
    private $pin;
    private $callbackUrl;
    private $wsdl = 'https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?WSDL';

    public function __construct()
    {
        // از config یا .env
        $this->pin = config('parsian.pin');
        $this->callbackUrl = config('parsian.callback_url');
    }

    /**
     * شروع پرداخت
     *
     * @param string $orderId
     * @param int $amount       // به ریال
     * @param string $callbackUrl // اختیاری
     * @param string $additionalData
     * @param string $originator
     * @return void
     */
    public function pay($orderId, $amount, $callbackUrl = null, $additionalData = '', $originator = '') {
        $this->validate($orderId, $amount);
        $callback = $callbackUrl ?: $this->callbackUrl;
//        $this->savePendingTransaction($orderId, $amount, $callback);
        return $this->requestPayment($orderId, $amount, $callback, $additionalData, $originator);
    }

    private function validate($orderId, $amount)
    {
        if (empty($orderId)) {
            die('شماره سفارش الزامی است.');
        }
        if (!is_numeric($amount) || $amount <= 0) {
            die('مبلغ باید عدد مثبت باشد.');
        }
        if (!preg_match('/^\d+$/', $amount)) {
            die('مبلغ باید عدد صحیح باشد.');
        }
    }

    private function savePendingTransaction($orderId, $amount, $callback)
    {
        // مثال با MySQL
        // $pdo->prepare("INSERT INTO payments ...")->execute([...]);
        // یا با فایل، یا لاگ
    }

    private function requestPayment($orderId, $amount, $callback, $additionalData, $originator)
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
                $this->updateToken($orderId, $res->Token);
                // برگرداندن URL
                return "https://pec.shaparak.ir/NewIPG/?Token=" . $res->Token;
            } else {
                $this->showError($res->Status, $res->Message ?? 'خطای ناشناخته');
            }

        } catch (Exception $e) {
            $this->showError(-1, 'خطا در ارتباط با درگاه: ' . $e->getMessage());
        }
    }

    private function updateToken($orderId, $token)
    {
        // ذخیره توکن در دیتابیس
    }

    private function redirectToBank($token)
    {
        $url = "https://pec.shaparak.ir/NewIPG/?Token=" . $token;
        header("Location: $url");
        exit;
    }

    private function showError($code, $message)
    {
        echo "<div style='direction:rtl; font-family:tahoma; color:red;'>
                <strong>خطا ($code):</strong> $message
              </div>";
        exit;
    }

    // متد تأیید پرداخت (در callback)
    public function confirm($token)
    {
        $confirmWsdl = 'https://pec.shaparak.ir/NewIPGServices/Confirm/ConfirmService.asmx?WSDL';
        $params = [
            "LoginAccount" => $this->pin,
            "Token"        => $token
        ];

        try {
            $client = new SoapClient($confirmWsdl);
            $result = $client->ConfirmPayment(['requestData' => $params]);
            $res = $result->ConfirmPaymentResult;

            if ($res->Status == 0) {
                return [
                    'success' => true,
                    'rrn'     => $res->RRN ?? null,
                    'card'    => $res->CardNumberMasked ?? null,
                    'amount'  => $res->Amount ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'error'   => $res->Message ?? 'خطا',
                    'code'    => $res->Status
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error'   => 'خطای ارتباط: ' . $e->getMessage()
            ];
        }
    }
}
