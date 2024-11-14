<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookPostController extends Controller
{
    // For verification challenge when setting up the webhoo    public function create($message = "Check out our new product", $image = null)
    {
        $pageId = env('PAGE_ID');
        $accessToken = env('ACCESS_TOKEN');

        // Log the request data
        Log::info("Posting to Facebook", ['message' => $message, 'image' => $image]);

        // Validate the input data
        if (empty($message) && empty($image)) {
            return response()->json([
                'status' => false,
                'message' => 'Message or image must be provided.'
            ]);
        }

        // Prepare the data for the request
        $data = [
            'message' => $message,
            'access_token' => $accessToken
        ];

        if ($image) {
            $data['url'] = $image;
            $url = "https://graph.facebook.com/v21.0/{$pageId}/photos";
        } else {
            $url = "https://graph.facebook.com/v21.0/{$pageId}/feed";
        }

        // Send the request to Facebook's Graph API
        $response = Http::post($url, $data);

        // Log the response
        Log::info("Facebook Response", ['response' => $response->body()]);

        // Check if the request was successful
        if ($response->successful()) {
            return response()->json([
                'status' => true,
                'message' => 'Post successfully made to Facebook!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Failed to post to Facebook: ' . $response->body()
            ]);
        }
    }
}

//real grapapi
//https://graph.facebook.com/v21.0/415353331671068/photos?message=hellp&url=https://imgs.search.brave.com/k7zufuVqiPom515UsuWk1TUkUq1zvthvjYI40g9Q6vA/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly8zOTY0/NjE0NS5mczEuaHVi/c3BvdHVzZXJjb250/ZW50LW5hMS5uZXQv/aHViZnMvMzk2NDYx/NDUvQ29kZU5pbmph/cyUyMC0lMjBNYXJr/ZXR0aW5nJTIwV2Vi/c2l0ZS9HcmFkaWVu/dHMvQXNzZXQlMjAy/MUAyeEAyeC5wbmc&access_token=EAAII67GFdzcBO23pBsBGI2NXAMH0LpIaajUGfEggU9MW3V1ECXKwhFKg6rL6FBYoKLszrC5XtCmbTwau8PbaQ90TBnQgLVWOejlDuG1hIdm97VQ711wMrE7PcJwkWUgBZBl7r8qrKJjSXHjAkueIVygcYO0Q8YF9CyHZAfN7mGZCP41W5eMt3tBZBzaISYTcwyUwaqFjDajmqzRJLfZC3WnHPQXjTsQcfCcmBZC5pY
