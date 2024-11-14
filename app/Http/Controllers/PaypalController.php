<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PaypalController extends Controller
{
    public function index()
    {
        return view('checkout');
    }

    /**
     * @return string
     */
    private function getAccessToken(): string
    {
        $headers = [
            'Content-Type'  => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . base64_encode(config('paypal.client_id') . ':' . config('paypal.client_secret'))
        ];

        $response = Http::withHeaders($headers)
            ->withBody('grant_type=client_credentials')
            ->post(config('paypal.base_url') . '/v1/oauth2/token');

        return json_decode($response->body())->access_token;
    }

    /**
     * @return string
     */
    public function create(float $amount): string
    {
        // Generate a unique ID
        $id = uuid_create();

        $headers = [
            'Content-Type'      => 'application/json',
            'Authorization'     => 'Bearer ' . $this->getAccessToken(),
            'PayPal-Request-Id' => $id,
        ];

        $body = [
            "intent"         => "CAPTURE",
            "purchase_units" => [
                [
                    "reference_id" => $id,
                    "amount"       => [
                        "currency_code" => "USD",
                        "value"         => number_format($amount, 2, '.', ''), // Ensure it formats to 2 decimal places
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders($headers)
            ->withBody(json_encode($body))
            ->post(config('paypal.base_url') . '/v2/checkout/orders');

        // Store the order ID and request ID in session
        Session::put('request_id', $id);
        $orderId = json_decode($response->body())->id;
        Session::put('order_id', $orderId);

        return $orderId; // Return the PayPal order ID
    }


    /**
     * @return mixed
     */
    public function complete()
    {
        $url = config('paypal.base_url') . '/v2/checkout/orders/' . Session::get('order_id') . '/capture';

        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ];

        $response = Http::withHeaders($headers)
            ->post($url, null);

        return json_decode($response->body());
    }
}
