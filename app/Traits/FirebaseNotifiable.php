<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

trait FirebaseNotifiable
{
   public function fcmNotify(string $title, string $body, array $data = []): bool
{
    // Fetch the latest device and check if it exists
    $lastDevice = $this->devices()->latest()->first();

    // If no device is found, log a warning and return false immediately
    if (!$lastDevice || empty($lastDevice->fcm_token)) {
        Log::channel('firebase')->warning("User {$this->id}: No FCM token");
        return false;
    }

    // Ensure all values in the $data array are strings
    $data = array_map(function ($value) {
        return (string) $value; // Convert all values to strings
    }, $data);

    try {
        // Get Firebase access token and prepare the FCM message payload
        $accessToken = $this->getFirebaseAccessToken();
        $url = "https://fcm.googleapis.com/v1/projects/" . config('firebase.project_id') . "/messages:send";

        $message = [
            'message' => [
                'token' => $lastDevice->fcm_token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ],
        ];

        // Send the request to FCM
        $client = new Client();
        $response = $client->post($url, [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ],
            'json' => $message,
        ]);

        // Check the response status
        $isSuccess = $response->getStatusCode() === 200;

        // Log success or failure
        Log::channel('firebase')->info(
            "User {$this->id}: " . ($isSuccess ? 'SUCCESS' : 'FAILURE'),
            [
                'title' => $title,
                'status' => $response->getStatusCode(),
                'response' => $response->getBody()->getContents(),
            ]
        );

        return $isSuccess;
    } catch (Exception $e) {
        // Catch and log any exceptions
        Log::channel('firebase')->error("User {$this->id}: FAILED", [
            'title' => $title,
            'error' => $e->getMessage(),
            'trace' => $e->getFile() . ':' . $e->getLine(),
        ]);

        return false;
    }
}



    protected function getFirebaseAccessToken(): string
    {
        return Cache::remember('firebase_access_token', 55 * 60, function () {
            $now = time();
            $header = ['alg' => 'RS256', 'typ' => 'JWT'];
            $payload = [
                'iss' => config('firebase.client_email'),
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ];

            $privateKey = config('firebase.private_key');
            $jwtHeader = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
            $jwtPayload = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
            openssl_sign("$jwtHeader.$jwtPayload", $signature, openssl_pkey_get_private($privateKey), 'sha256WithRSAEncryption');
            $jwt = "$jwtHeader.$jwtPayload." . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

            $client = new Client();
            $tokenResponse = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ],
            ]);

            return json_decode($tokenResponse->getBody(), true)['access_token'];
        });
    }

    // 🚀Use It Anywhere

    // Now your model-based notification call is clean and config-driven:

    // $user = User::find(1);

    // $user->fcmNotify(
    //     'DeepGrow Reminder 🌱',
    //     'Your daily mindfulness session is ready.',
    //     ['screen' => 'session_detail', 'session_id' => 24]
    // );


    // or broadcast to many:

    // User::whereNotNull('fcm_token')->each(function ($user) {
    //     $user->fcmNotify('Daily Insight', 'Take 5 minutes to reflect.');
    // });
}
