<?php

return [
    /*
    | Sandbox keys below are for demo only. Replace via MIDTRANS_* env on production.
    */
    'server_key' => env(
        'MIDTRANS_SERVER_KEY',
        'SB-Mid-server-t8MFom-L-ZMiJYA0lPvUX8hc'
    ),
    'client_key' => env(
        'MIDTRANS_CLIENT_KEY',
        'SB-Mid-client-07PMj5xBlweORohB'
    ),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => (bool) env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => (bool) env('MIDTRANS_IS_3DS', true),
];
