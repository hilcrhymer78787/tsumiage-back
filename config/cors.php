<?php

return [

    // 対象のパス
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // 許可するHTTPメソッド
    'allowed_methods' => ['*'],

    // 許可するオリジン（ワイルドカード使用不可 if credentials=true）
    'allowed_origins' => [
        'https://tsumiage.magicgifted.com',
        'https://stg-tsumiage.magicgifted.com',
        'http://localhost:3000',
        'http://localhost:3001',
        'http://localhost:3002',
        'http://3.105.106.52',
        'http://tsumiage.online',
        'https://tsumiage.online',
    ],

    // ポート付きや動的ドメイン対応
    'allowed_origins_patterns' => [
        '/^https:\/\/tsumiage\.magicgifted\.com(:[0-9]+)?$/',
        '/^https:\/\/stg-tsumiage\.magicgifted\.com(:[0-9]+)?$/',
        '/^https:\/\/tsumiage\.online(:[0-9]+)?$/',
        '/^http:\/\/localhost(:[0-9]+)?$/',
    ],

    // 許可するリクエストヘッダ
    'allowed_headers' => ['*'],

    // フロントに返す追加ヘッダ
    'exposed_headers' => [],

    // プリフライトキャッシュの有効期限（秒）
    'max_age' => 0,

    // Cookieや認証情報を送信する場合はtrue
    'supports_credentials' => true,

];
