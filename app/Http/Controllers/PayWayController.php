<?php

namespace App\Http\Controllers;

use App\Services\PayWayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayWayController extends Controller
{
    protected $payWayService;

    public function __construct(PayWayService $payWayService)
    {
        $this->payWayService = $payWayService;
    }


    public function create(Request $request)
    {
        // Get data from the request instead of hardcoding
        $products = $request->input('products'); // Assuming 'products' is an array
        $item = [];

        // dd($products);
        $totalPrice = 0;

        // Map the products into the desired format
        foreach ($products as $product) {
            $item[] = [
                'name' => $product['name'],
                'quantity' => $product['quantity'],
                'price' => $product['unit_amount']
            ];
            $totalPrice += $product['unit_amount'] * $product['quantity'];
        }

        $discount = $request->input('discount');


        $totalPrice = $totalPrice - $discount;

        $req_time = now()->format('YmdHis');
        $transaction_id = $req_time;
        $shipping = $request->input('shipping');
        // $totalPrice+=$shipping;
        $amount =  $totalPrice;
        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $phone = $request->input('phone');
        $email = $request->input('abaemail');
        $return_params = $request->input('return_params');
        $type = $request->input('type');
        $currency = $request->input('currency');
        $merchant_id = config('aba.merchant_id');
        $payment_option = $request->input('payment_option'); // Dynamically set payment option
        $continue_success_url = 'http://127.0.0.1:8000/stripe-success/return?abaSuccess=true';
        $cancel_url = 'http://127.0.0.1:8000/stripe-success/return?abaSuccess=false';

        $items = base64_encode(json_encode($item));

        $hash = $this->payWayService->getHash(
            $req_time . $merchant_id . $transaction_id . $amount . $items . $shipping .
                $firstName . $lastName . $email . $phone . $type . $payment_option . $currency . $return_params
        );

        return response()->json([
            'hash' => $hash,
            'transaction_id' => $transaction_id,
            'amount' => $amount,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'phone' => $phone,
            'email' => $email,
            'items' => $items,
            'return_params' => $return_params,
            'currency' => $currency,
            'shipping' => $shipping,
            'type' => $type,
            'payment_option' => $payment_option,
            'merchant_id' => $merchant_id,
            'req_time' => $req_time,
            'continue_success_url' => $continue_success_url
        ]);
    }

    public function checkTransaction(Request $request)
    {
        // Required parameters
        $req_time = now()->format('YmdHis');  // Timestamp
        $merchant_id = config('aba.merchant_id');  // Merchant ID from config
        $tran_id = $request->input('tran_id');  // Transaction ID passed from the client
        $public_key = config('aba.api_key');  // Public API Key from config

        // Generate the hash (base64-encoded HMAC-SHA512)
        $hash = base64_encode(hash_hmac('sha512', $req_time . $merchant_id . $tran_id, $public_key, true));

        // API endpoint (sandbox for testing, change to production for live)
        $endpoint = 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/check-transaction-2';

        // Prepare the request payload
        $payload = [
            'req_time' => $req_time,
            'merchant_id' => $merchant_id,
            'tran_id' => $tran_id,
            'hash' => $hash,
        ];

        try {
            // Make the request to the ABA API using Guzzle
            $client = new \GuzzleHttp\Client();
            $response = $client->post($endpoint, ['json' => $payload]);

            // Log request payload for debugging purposes
            Log::debug('Request Payload:', [
                'req_time' => $req_time,
                'merchant_id' => $merchant_id,
                'tran_id' => $tran_id,
                'hash' => $hash,
            ]);

            // Check if the response status is 200 OK
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody()->getContents(), true);

                // Log response for debugging
                Log::debug('PayWay Response:', $data);

                // Check if the response is successful and return it
                if ($data['status']['code'] === '00') {
                    return response()->json($data);  // Return the successful status data
                } else {
                    return response()->json(['error' => 'Transaction failed: ' . $data['status']['message']], 400);
                }
            } else {
                // Handle unsuccessful response
                return response()->json(['error' => 'Failed to fetch transaction status'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Handle exceptions like network issues
            Log::error('API Request failed: ' . $e->getMessage());
            return response()->json(['error' => 'API Request failed: ' . $e->getMessage()], 500);
        }
    }
}
