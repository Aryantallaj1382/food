<?php

use App\Models\Ad;
use App\Models\Housemate\Housemate;
use App\Models\UserAttribute;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;

if (!function_exists('to_latin_digits')) {
    function to_latin_digits(string|null $str): string|null
    {
        if (blank($str))
            return $str;

        return str_replace(
            array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'),
            array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'),
            $str
        );
    }
}

if (!function_exists('persian_digits')) {
    function to_persian_digits(string|int|float|null $str): string|null
    {
        if ($str === null)
            return null;

        return str_replace(
            array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'),
            array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'),
            (string)$str
        );
    }
}
if (!function_exists('generateOrderCode')) {
    function generateOrderCode($userId)
    {
        $random = random_int(100, 999);
        return 1 . $random . str_pad($userId, 5, '0', STR_PAD_LEFT) . time();
    }
}

use Illuminate\Pagination\LengthAwarePaginator;
use IPPanel\Client;
use Morilog\Jalali\Jalalian;

function api_response(mixed $data = [], string $message = '', int $status = 200, array $append = []): JsonResponse
{
    $response = [
        'message' => $message,
    ];

    if ($data instanceof LengthAwarePaginator) {
        $response['data'] = $data->items();
        $response['total'] = $data->total();
        $response['per_page'] = $data->perPage();
        $response['last_page'] = $data->lastPage();
        $response['next_page_url'] = $data->nextPageUrl();
    } else {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $response['data'] = $data;
    }

    return response()->json(array_merge($response, $append), $status);
}

function generatePaginationLinks(LengthAwarePaginator $data)
{
    $links = [];
    $links[] = [
        'url' => $data->previousPageUrl(),
        'label' => '&laquo; Previous',
        'active' => $data->onFirstPage(),
    ];
    foreach (range(1, $data->lastPage()) as $page) {
        $links[] = [
            'url' => $data->url($page),
            'label' => (string) $page,
            'active' => $data->currentPage() === $page,
        ];
    }

    $links[] = [
        'url' => $data->nextPageUrl(),
        'label' => 'Next &raquo;',
        'active' => !$data->hasMorePages(),
    ];

    return $links;
}


function normalize_filename(string $filename): string
{
    $replacements = [
        'тАУ' => '-',
    ];

    return strtr($filename, $replacements);
}
function calculateCompatibilityPrecise($id , $user)
{
    $ad = Housemate::find($id);

    $rules = $ad->rules;
    $userValues = UserAttribute::where('user_id', $user)
        ->pluck('value')
        ->toArray();

    if (empty($rules) || empty($userValues)) return 0;
    $normalize = function($str) {
        if (!$str) return '';
        $str = mb_strtolower(trim($str), 'UTF-8');
        $str = preg_replace('/[\s\p{P}\p{S}]+/u', '', $str);
        $map = [
            'ي' => 'ی',
            'ك' => 'ک',
            'ؤ' => 'و',
            'إ' => 'ا',
            'أ' => 'ا',
            'آ' => 'ا',
            'ة' => 'ه',
            'ـ' => '',
            '‌' => '',
            '‍' => '',
        ];
        $str = strtr($str, $map);
        $str = preg_replace('/[0-9۰-۹]/u', '', $str);
        $str = preg_replace('/[^\p{L}\p{N}]/u', '', $str);
        $str = preg_replace('/(.)\1+/u', '$1', $str);
        return $str;
    };


    $rules = array_map($normalize, $rules);
    $userValues = array_map($normalize, $userValues);

    $matches = [];

    foreach ($rules as $rule) {
        foreach ($userValues as $userValue) {
            if ($rule === $userValue || levenshtein($rule, $userValue) <= 1) {
                $matches[] = $rule;
                break;
            }
        }
    }

    $matchAdPercent = count($matches) / count($rules);
    $matchUserPercent = count($matches) / count($userValues);

    $compatibility = round((($matchAdPercent + $matchUserPercent) / 2) * 100, 2);

    return $compatibility;
}
function distanceKm($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // شعاع زمین به کیلومتر

    // تبدیل درجه به رادیان
    $latFrom = deg2rad($lat1);
    $lonFrom = deg2rad($lon1);
    $latTo = deg2rad($lat2);
    $lonTo = deg2rad($lon2);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $a = sin($latDelta / 2) * sin($latDelta / 2) +
        cos($latFrom) * cos($latTo) *
        sin($lonDelta / 2) * sin($lonDelta / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $x = $earthRadius * $c;
    return (int) $x;
}
function sms($pattern , $mobile , $data )
{
            (new Client(config('app.sms_panel_apikey')))
            ->sendPattern(
                $pattern,
                '3000505',
                $mobile,
                $data
            );
}

function ui_code($orderId  , $userId)
{
    $microTime = microtime(true);
    $milliseconds = (int)($microTime * 1000);

    $uniqueId = $milliseconds . '-' . $orderId . '-' . $userId;

    return $uniqueId;

}
