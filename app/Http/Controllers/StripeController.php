<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeController extends Controller
{
    //create 3 method index, create , success
    public function index()
    {
        return view('stripe');
    }

    public function create(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $products = $request->input('products', []);
        $totalShippingCharge = $request->input('totalShippingCharge', 0);
        $discountTypeAmount = $request->input('discountTypeAmount', 0);
        $discountType = $request->input('discountType', 'none'); // 'percent' or 'fixed'

        $lineItems = [];
        $subtotalAmount = 0; // To calculate the total amount before discount

        // Add the products to the line items and calculate the subtotal
        foreach ($products as $product) {
            $lineItemAmount = $product['unit_amount'] * $product['quantity'];
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product['name'],
                    ],
                    'unit_amount' => $product['unit_amount'],
                ],
                'quantity' => $product['quantity'],
            ];

            $subtotalAmount += $lineItemAmount; // Keep track of subtotal
        }

        // Create the coupon based on the discount type (percent or fixed) only if there's a discount
        $couponCode = null;
        if ($discountTypeAmount > 0) {
            if ($discountType == 'percent') {
                $coupon = $stripe->coupons->create([
                    'percent_off' => (float)$discountTypeAmount, // Percentage discount
                    'duration' => 'once', // Applies only once
                ]);
                $couponCode = $coupon->id;
            } else {
                $coupon = $stripe->coupons->create([
                    'amount_off' => (int)($discountTypeAmount * 100), // Fixed discount amount in cents
                    'currency' => 'usd',
                    'duration' => 'once',
                ]);
                $couponCode = $coupon->id;
            }
        }

        // Prepare the session data
        $sessionData = [
            'line_items' => $lineItems, // Products with original prices
            'mode' => 'payment',
            'shipping_options' => [
                [
                    'shipping_rate_data' => [
                        'display_name' => 'Standard Shipping',
                        'type' => 'fixed_amount',
                        'fixed_amount' => [
                            'amount' => (int)($totalShippingCharge * 100), // Shipping charge in cents
                            'currency' => 'usd',
                        ],
                        'delivery_estimate' => [
                            'minimum' => ['unit' => 'day', 'value' => 3],
                            'maximum' => ['unit' => 'day', 'value' => 5],
                        ],
                    ],
                ],
            ],
            'return_url' => 'http://127.0.0.1:8000/stripe-success/return?session_id={CHECKOUT_SESSION_ID}',
            'ui_mode' => 'embedded', // Ensure this is set to 'embedded'
        ];

        // Add the discount only if there is a coupon
        if ($couponCode) {
            $sessionData['discounts'] = [
                [
                    'coupon' => $couponCode, // Apply the coupon by code
                ],
            ];
        }

        // Create the Stripe checkout session
        $checkout_session = $stripe->checkout->sessions->create($sessionData);

        return response()->json(['clientSecret' => $checkout_session->client_secret]);
    }




    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        // Initialize Stripe client
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        // Retrieve the session data
        $session = $stripe->checkout->sessions->retrieve($sessionId);

        // Determine status based on the payment session
        $status = $session->payment_status === 'paid' ? 'success' : 'failed';

        // Redirect to the original route with the status in the query parameters
        return redirect()->back()->with('status', $status);
    }
}
