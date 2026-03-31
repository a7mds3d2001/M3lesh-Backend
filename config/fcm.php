<?php

return [
    'server_key' => env('FCM_SERVER_KEY'),
    'api_url' => env('FCM_API_URL', 'https://fcm.googleapis.com/fcm/send'),
];
