<?php

return [
    'roots' => [
        base_path('routes/web.php'),
        base_path('routes/api.php'),
    ],
    'except' => [
        'api/*',
        'sanctum/*',
    ],
];
