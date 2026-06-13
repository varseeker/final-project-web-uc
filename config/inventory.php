<?php

return [

    'enabled' => env('INVENTORY_SERVICE_ENABLED', false),

    'base_url' => rtrim(env('INVENTORY_SERVICE_URL', 'http://127.0.0.1:8001'), '/'),

    'api_token' => env('INVENTORY_API_TOKEN'),

    'sync_ttl_seconds' => (int) env('INVENTORY_SYNC_TTL', 120),

    'default_price' => (int) env('INVENTORY_DEFAULT_PRICE', 18000),

    'source' => env('INVENTORY_ORDER_SOURCE', 'pos-warkop-kayu'),

    'order_id_prefix' => env('INVENTORY_ORDER_ID_PREFIX', 'pos-warkop-kayu'),

];
