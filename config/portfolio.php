<?php

return [
    'upload_disk' => env(
        'PORTFOLIO_UPLOAD_DISK',
        env('FILESYSTEM_DISK') === 's3' ? 's3' : 'public'
    ),
];
