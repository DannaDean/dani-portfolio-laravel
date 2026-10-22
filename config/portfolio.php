<?php

return [
    'upload_disk' => env('PORTFOLIO_UPLOAD_DISK', 'public'),

    'admin' => [
        'name' => env('PORTFOLIO_ADMIN_NAME'),
        'email' => env('PORTFOLIO_ADMIN_EMAIL'),
        'password' => env('PORTFOLIO_ADMIN_PASSWORD'),
    ],
];
