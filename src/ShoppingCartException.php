<?php
namespace AyeniJoshua\LaravelShoppingCart;

/**
 * Exception class for AyeniJoshua\LaravelShoppingCart
 */
class ShoppingCartException extends \Exception{

    private static $msg=null;
    /**
     * quantity exception
     */
    function quantity($qty=null){
        self::$msg =  sprintf (
            'The supplied quantity %s is invalid',$qty
        );
    }

    function getException(){
        return !self::$msg?$this->getMessage():self::$msg;
    }

    
}