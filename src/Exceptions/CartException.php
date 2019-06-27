<?php
namespace AyeniJoshua\LaravelShoppingCart\Exceptions;

use Exception;

/**
 * Exception class for AyeniJoshua\LaravelShoppingCart
 */
class CartException extends \Exception {

    private static $msg=null;
    /**
     * quantity exception
     */
    static function  quantity($qty=null){
       return self::$msg =  sprintf (
            'The supplied quantity {%s} is invalid, quantities should be integers',$qty
        );
    }

    static function invalidStorage($storage){
       return self::$msg =  sprintf (
            'The supplied storage {%s} is invalid, storage should be either (session or database)',$storage
        );
    }

    function getException(){
        return !self::$msg?$this->getMessage():self::$msg;
    }
    
}