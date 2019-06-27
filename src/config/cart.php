<?php
namespace AyeniJoshua\LaravelShoppingCart\config;

/**
 * cart configurations
 */

return [
    'storage'=>'session', //session, database, redis etc

    /**
     * inscase you want to use multiple storage services (e.g session and database together)
     */
    'multiple_storage'=>[
        'activate'=>false,
        'default'=>true,
        'class'=>'\AyeniJoshua\LaravelShoppingCart\Services\CartMultipleStorage', //replace with yours
        'dependencies' => [  
            'service container binding key for your CartSessionStorage','service container binding key for your CartDatabaseStorage' 
        ]
    ],

    'session'=>[
        'driver'=>'default',
        'dependencies' => [  //Get the registered name of the component you want to use as dependencies.
            'events','session' 
        ]
    ],

    'session'=>[
        'dependencies' => [  //Get the registered name of the component you want to use as dependencies.
            'events','session' 
        ]
    ],

    //if storage is database, configure array below
    'database'=>[
        'model_namespace' => '\App\Cart',
        'driver'=>'default', //redis,memcache etc
        'dependencies' => [ //Get the registered name of the component you want to use as dependencies.
            'events'
        ]
    ],
 
];