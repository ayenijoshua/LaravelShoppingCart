<?php
//namespace AyeniJoshua\LaravelShoppingCart\config;

/**
 * cart configurations
 */

return [
    'storage'=>'database', //session, database (only)

    /**
     * incase you want to use multiple storage services (e.g session and database together)
     * this default setting is good for most applications
     */
    'multiple_storage'=>[
        'activate'=>true,
        'default'=>true,
        'class'=>'\AyeniJoshua\LaravelShoppingCart\Services\CartDefaultMultipleStorage', //default
        'dependencies' => [  //'service container binding key for your CartSessionStorage','service container binding key for your CartDatabaseStorage' 
           ' \AyeniJoshua\LaravelShoppingCart\Services\CartDefaultSessionStorage','\AyeniJoshua\LaravelShoppingCart\Services\CartDefaultDatabaseStorage' 
        ]
    ],

    /**
     * if storage is session configure array below (OR USE DEFAULT)
     * this default setting is good for most applications
     */
    'session'=>[
        'driver'=>'default', //you could configure this to e.g(database, redis etc)
        'dependencies' => [  //Get the registered name of the component you want to use as dependencies.
            'events','session' 
        ]
    ],

    /**
     * if storage is database, configure array below (OR USE DEFAULT)
     * this default setting is good for most applications
     */
    'database'=>[
        'model_namespace' => '\AyeniJoshua\LaravelShoppingCart\Models\App\Cart', //default (you could update this to suite your needs)
        'driver'=>'default', //redis,memcache etc (you could update this to suite your needs)
        'dependencies' => [ //Get the registered name of the component you want to use as dependencies. (you could update this to suite your needs)
            'events'
        ]
    ],
 
];