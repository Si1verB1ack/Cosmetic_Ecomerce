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

        $lineItems = [];
        foreach ($products as $product) {
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
        }

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'ui_mode' => 'embedded',
            'return_url' => 'http://127.0.0.1:8000/stripe-success/return?session_id={CHECKOUT_SESSION_ID}',
        ]);

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
        return redirect()->route('front.checkout')->with('status', $status);
    }
}
