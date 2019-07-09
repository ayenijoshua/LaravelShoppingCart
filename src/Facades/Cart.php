<?php
/**
 * SHopping cart facade
 */
namespace AyeniJoshua\LaravelShoppingCart\Facades;

use Illuminate\Support\Facades\Facade;

class CartFacade extends Facade {

    protected static function getFacadeAccessor(){
        return 'cart';
    }

}