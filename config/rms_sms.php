<?php

return [
    'username' => env('RMS_SMS_USERNAME', 'RMSLTD'),
    'api_key' => env('RMS_SMS_API_KEY', 'e30b9d345a783aa65d6e4d67c01b3aa229f81f3bd7a8416be429715e00e41a2ff97e3e24085bbef59171c820c46b4788f28b94b6c90f809bf1dd9338612ae97c'),
    'sender_id' => env('RMS_SMS_SENDER_ID', 'RMSLTD'),
    'api_endpoint' => env('RMS_SMS_API_ENDPOINT', 'https://api.mspace.co.ke/smsapi/v2/sendtext'),
    'timeout' => env('RMS_SMS_TIMEOUT', 30),
];
