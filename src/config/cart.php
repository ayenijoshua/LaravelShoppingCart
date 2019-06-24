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
    'multiple_storage'=>false,

    'session'=>[
        'dependencies' => [  //Get the registered name of the component you want to use as dependencies.
            'events','session' 
        ]
    ],

    //if storage is database, configure array below
    'database'=>[
        'model_namespace' => '\App\Cart',
        'service'=>'eloquent', //redis,memcache etc
        'dependencies' => [ //Get the registered name of the component you want to use as dependencies.
            'events'
        ]
    ],

    // 'redis'=>[
    //     'dependencies' => [  //Get the registered name of the component you want to use as dependencies.
    //         'events','session' 
    //     ]
    // ],
];