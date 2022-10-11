<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PlaceToPayService
{
    const IP_ADDRESS = '127.0.0.1';

    const USER_AGENT = 'PlacetoPay Sandbox';

    const CREATE_SESSION_OK = 'OK';

    const CURRENCY = 'COP';

    private function getUrlPlaceToPaySession(int $requestId = null)
    {
        $url = env('PLACETOPAY_URL').env('PLACETOPAY_API_SESSION');

        if ($requestId) {
            $url .= DIRECTORY_SEPARATOR.$requestId;
        }

        return $url;
    }

    public function getAuth(Carbon $time)
    {
        $nonce = Str::random(20);
        $seed = $time->format('c');
        $secretKey = env('PLACETOPAY_SECRET_KEY');

        return [
            'login' => env('PLACETOPAY_LOGIN'),
            'tranKey' => base64_encode(sha1($nonce.$seed.$secretKey, true)),
            'nonce' => base64_encode($nonce),
            'seed' => $seed,
        ];
    }

    public function getPaymentData(Order $order)
    {
        $now = now();

        return [
            'auth' => self::getAuth($now),
            'payment' => [
                'reference' => str_pad($order->id, 10, '0', STR_PAD_LEFT),
                'description' => $order->product->name,
                'amount' => [
                    'currency' => self::CURRENCY,
                    'total' => $order->amount,
                ],
            ],
            'expiration' => $now->addMinutes(30)->format('c'),
            'returnUrl' => env('APP_URL').':'.env('APP_PORT').'/orders/'.$order->id,
            'ipAddress' => self::IP_ADDRESS,
            'userAgent' => self::USER_AGENT,
        ];
    }

    public function sendRequestPayment(Order $order)
    {
        $url = self::getUrlPlaceToPaySession();
        $data = $this->getPaymentData($order);

        return Http::post($url, $data);
    }

    public function getRequestInformation(int $requestId)
    {
        $url = self::getUrlPlaceToPaySession($requestId);
        $data = ['auth' => self::getAuth(now())];

        return Http::post($url, $data);
    }
}
