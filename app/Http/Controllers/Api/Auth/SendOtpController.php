<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\TempUser;
use App\Models\User;
use Illuminate\Http\Request;
use IPPanel\Client;
use Throwable;

class SendOtpController extends Controller
{
    public function sendOtp(Request $request)
    {

        $data = $request->validate([
            'mobile' => 'required|regex:/^[0-9]{10,15}$/',

        ],[], [
            'mobile' => 'موبایل ',
        ]);


        $user = User::where('mobile', $data['mobile'])->first();

        if (blank($user)) {
            $user = TempUser::firstOrCreate([
                'mobile' => $data['mobile'],
            ], [
                'sms_sent_tries' => 0,
                'sms_sent_code' => null,
            ]);
        }

        $seconds =  optional($user->sms_sent_date)->diffInSeconds(now());
        $remaining = round(120 - $seconds);
        if ($seconds && $seconds < 120) {
            $remaining = round(120 - $seconds); // گرد کردن به عدد صحیح
            return api_response([
                'remain' => $remaining],
                "لطفاً پس از گذشت " . $remaining . " ثانیه دوباره تلاش کنید."
                , 429);
        }

        $code = random_int(10000, 99999);

        $user->update([
            'sms_sent_tries' => 0,
            'sms_sent_date' => now(),
            'sms_sent_code' => $code,
        ]);

        $responseMessages = [
            'remain' => $remaining,

        ];

        try {
            if (!empty($data['mobile'])) {
                (new Client(config('app.sms_panel_apikey')))
                    ->sendPattern(
                        'hjn2b6wewed478q',
                        '3000505',
                        $data['mobile'],
                        ['code' => $code]
                    );
                $responseMessages['message'] = 'کد تایید به شماره موبایل ارسال شد.';

            }



        } catch (Throwable $e) {

            return response()->json([
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
        return api_response($responseMessages, 'کد تایید برای شما ارسال شد');
    }

}
