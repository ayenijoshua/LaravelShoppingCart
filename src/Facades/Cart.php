<?php
/**
 * SHopping cart facade
 */
namespace AyeniJoshua\LaravelShoppingCart\Facades;

use Illuminate\Support\Facades\Facade;

class CartFacade extends Facade {

    public function getFacadeAccessor(){
        return 'cart';
    }

}