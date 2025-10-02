<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // TODO
    'allowed_origins' => ['https://tsumiage.magicgifted.com', 'https://stg-tsumiage.magicgifted.com', 'http://localhost:3000', 'http://localhost:3001', 'http://3.105.106.52', 'http://tsumiage.online', 'https://tsumiage.online'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
