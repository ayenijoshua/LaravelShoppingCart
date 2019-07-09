<?php
namespace AyeniJoshua\LaravelShoppingCart\Exceptions;

use \Exception;
use Illuminate\Support\Facades\Log;

/**
 * Exception class for AyeniJoshua\LaravelShoppingCart
 */
class CartException extends \Exception {

    private static $msg;
    function __construct($msg=null,$param=null){
        parent::__construct($msg);
        $this->switchExceptions($msg,$param);
    }

    /**
     * switch between exceptions
     */
    public function switchExceptions($exp,$arg){
        switch ($exp) {
            case 'IdNotFound':
                self::$msg =  sprintf (
                    'The supplied item id {%s} was not found in the cart, quantities should be integers',$arg
                );
                break;
            case 'InvalidQty':
                self::$msg =  sprintf (
                    'The supplied quantity {%s} is invalid, quantities should be integers',$arg
                );
                break;
            case 'InvalidStorage':
                self::$msg =  sprintf (
                    'The supplied storage service {%s} is invalid, storage should be either (session or database)',$arg
                );
                break;

            default:
                self::$msg = $exp;//$this->getMessage();
                break;
        }
    }
    
    /**
     * get default exception message
     */
    function getException(){
        Log::info('Error Message -'.self::$msg?self::$msg:$this->getMessage());
    }
    
}