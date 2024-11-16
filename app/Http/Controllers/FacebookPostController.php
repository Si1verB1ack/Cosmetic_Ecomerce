<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookPostController extends Controller
{
    public function create($message = "Check out our new product", $image = null)
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
            'access_token' => $accessToken,
        ];

        // Set the URL for Facebook's Graph API
        $url = "https://graph.facebook.com/v21.0/{$pageId}/feed";  // Standard feed endpoint

        // If there is an image, send it to Facebook as a file
        if ($image) {
            if (is_array($image)) {
                // Initialize an array to store the media objects for the final post
                $attachedMedia = [];

                // Loop through each image in the array and upload them
                foreach ($image as $img) {
                    // Prepare the data for the image upload request
                    $uploadData = [
                        'access_token' => $accessToken,  // Make sure to pass the access token here
                        'published' => 'false',          // This ensures that the images are unpublished initially
                    ];

                    // Upload each image to Facebook
                    $response = Http::attach(
                        'file', // Field name
                        file_get_contents($img), // Image file contents
                        basename($img) // File name (e.g., image.jpg)
                    )->post("https://graph.facebook.com/v21.0/{$pageId}/photos", $uploadData); // Photo endpoint

                    // If the upload was successful, store the media ID for the attached media
                    if ($response->successful()) {
                        // Add the media object to the attachedMedia array
                        $attachedMedia[] = ['media_fbid' => $response->json()['id']]; // Store the media ID in the attachedMedia array
                    } else {
                        Log::error("Failed to upload image", ['image' => $img, 'response' => $response->body()]);
                    }
                }

                // Now that we have all the media IDs, add them to the data array for the final post
                if (!empty($attachedMedia)) {
                    // Facebook expects 'attached_media' to be an array of objects, not just the array of IDs
                    $data['attached_media'] = json_encode($attachedMedia); // Ensure it's JSON encoded
                }

                // Make the post to Facebook with the attached media
                $response = Http::post("https://graph.facebook.com/v21.0/{$pageId}/feed", $data); // Post to feed endpoint

                // Handle the response
                if ($response->successful()) {
                    Log::info("Post successfully made to Facebook!", ['response' => $response->body()]);
                } else {
                    Log::error("Failed to post to Facebook", ['response' => $response->body()]);
                }
            } else {
                // Use Http::attach to upload the image file as multipart/form-data
                $response = Http::attach(
                    'file', // Field name
                    file_get_contents($image), // Image file contents
                    basename($image) // File name (e.g., image.jpg)
                )->post("https://graph.facebook.com/v21.0/{$pageId}/photos", $data); // Photo endpoint
            }
        } else {
            // Send only the message without an image
            $response = Http::post($url, $data);
        }

        // Log the response
        Log::info("Facebook Response", ['response' => $response->body()]);

        // Check if the request was successful
        if ($response->successful()) {
            return response()->json([
                'status' => true,
                'message' => 'Post successfully made to Facebook!'
            ]);
        } else {
            // Capture Facebook error response if posting fails
            $errorMessage = $response->json()['error']['message'] ?? 'Unknown error';
            return response()->json([
                'status' => false,
                'message' => 'Failed to post to Facebook: ' . $errorMessage
            ]);
        }
    }

    public function deleteAllPosts()
    {
        $pageId = env('PAGE_ID');
        $accessToken = env('ACCESS_TOKEN');
        $batchSize = 5;  // Limit to deleting 5 posts per batch

        // Step 1: Fetch all posts from the page
        $postsUrl = "https://graph.facebook.com/v21.0/{$pageId}/posts?access_token={$accessToken}";
        $response = Http::get($postsUrl);

        if ($response->successful()) {
            $postsData = $response->json();
            $totalPosts = count($postsData['data']);

            // Step 2: Loop through each post and delete it in batches
            for ($i = 0; $i < $totalPosts; $i += $batchSize) {
                $batch = array_slice($postsData['data'], $i, $batchSize);

                // Step 3: Delete posts in the current batch
                foreach ($batch as $post) {
                    $postId = $post['id'];

                    // Send DELETE request to remove the post
                    $deleteUrl = "https://graph.facebook.com/v21.0/{$postId}?access_token={$accessToken}";
                    $deleteResponse = Http::delete($deleteUrl);

                    if ($deleteResponse->successful()) {
                        Log::info("Successfully deleted post", ['postId' => $postId]);
                    } else {
                        Log::error("Failed to delete post", ['postId' => $postId, 'response' => $deleteResponse->body()]);
                    }
                }

                // Optional: Sleep between batches to avoid hitting API rate limits
                // sleep(1);
            }

            return response()->json([
                'status' => true,
                'message' => 'All posts have been deleted successfully.'
            ]);
        } else {
            Log::error("Failed to fetch posts", ['response' => $response->body()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch posts from the page.'
            ]);
        }
    }
}

//real grapapi
//https://graph.facebook.com/v21.0/415353331671068/photos?message=hellp&url=https://imgs.search.brave.com/k7zufuVqiPom515UsuWk1TUkUq1zvthvjYI40g9Q6vA/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly8zOTY0/NjE0NS5mczEuaHVi/c3BvdHVzZXJjb250/ZW50LW5hMS5uZXQv/aHViZnMvMzk2NDYx/NDUvQ29kZU5pbmph/cyUyMC0lMjBNYXJr/ZXR0aW5nJTIwV2Vi/c2l0ZS9HcmFkaWVu/dHMvQXNzZXQlMjAy/MUAyeEAyeC5wbmc&access_token=EAAII67GFdzcBO23pBsBGI2NXAMH0LpIaajUGfEggU9MW3V1ECXKwhFKg6rL6FBYoKLszrC5XtCmbTwau8PbaQ90TBnQgLVWOejlDuG1hIdm97VQ711wMrE7PcJwkWUgBZBl7r8qrKJjSXHjAkueIVygcYO0Q8YF9CyHZAfN7mGZCP41W5eMt3tBZBzaISYTcwyUwaqFjDajmqzRJLfZC3WnHPQXjTsQcfCcmBZC5pY
