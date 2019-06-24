<?php
namespace AyeniJoshua\LaravelShoppingCart\config;

/**
 * cart configurations
 */

return [
    'storage'=>'session', //session, database, redis etc

    //if storage is database, configure array below
    'database_service'=>[
        'model_namespace' => '\App\Cart',
        'service'=>'eloquent' //redis,memcache etc
    ]
];