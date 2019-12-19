<?php
/**
 * session storage implementation for shopping cart
 */
namespace AyeniJoshua\LaravelShoppingCart\Services;

use AyeniJoshua\LaravelShoppingCart\Contracts\CartStorageInterface;
use AyeniJoshua\LaravelShoppingCart\Services\Cart;
use AyeniJoshua\LaravelShoppingCart\Services\CartDefaultSessionStorage;
use AyeniJoshua\LaravelShoppingCart\Services\CartDefaultDatabaseStorage;
use AyeniJoshua\LaravelShoppingCart\Exceptions\CartException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

class CartDefaultMultipleStorage implements CartStorageInterface {

    public $storage = 'session';
    public $cart_name = 'default';
    protected $session;
    protected $db;

    function __construct(CartDefaultSessionStorage $session, CartDefaultDatabaseStorage $db){
        $this->session = $session;
        $this->db = $db;
    }

    /**
     * set cart storage service
     */
    function setStorage($storage,$name=null){
        $this->cart_name = $name ?? $this->cart_name;
        if($storage=='session'){
            $this->storage = 'session';
            $this->session->setName($this->cart_name);
            //$this->session->setStorage();
        }elseif($storage=='db'){
            $this->storage = 'db';
            //$this->session->setName($this->cart_name);
            $this->db->setStorage($this->cart_name);
        }else{
            $this->storage = $this->storage;
        }
        return $this;
    }

    /**
     * get the cart storage
     * @storage - cart storage service
     * @sessionMtd - cart session method
     * @dbMtd - cart dtabase method
     */
    function getStorage($storage=null,$sessionMtd,$dbMtd){
        try{
            if($storage){
                if($storage==='session'){
                    $this->storage = 'session';
                    return $this;
                }elseif($storage=='db'){
                    $this->storage = 'db';
                    return $this;
                }else{
                   $this->storage = $this->storage;
                   Log::info("The storage supplied is Invalid. Hence, the system chose default session Storage");
                   return $this; 
                }
            }
            if($this->storage=='session'){
                return $sessionMtd;
              }elseif($this->storage=='db'){
                 return $dbMtd;
              }else{
                Log::info("The storage supplied is Invalid. Hence, the system chose default session Storage");
                return $sessionMtd;
              }  
        }catch(CartException $e){
            $e->getException();
        }
    }

    /**
     * get cart
     * @storage - cart storage service
     */
    public function getCart($name=null){
        $this->cart_name = $name ?? $this->cart_name;
       return $this->getStorage(null,$this->session->getCart($this->cart_name),$this->db->getCart($this->cart_name));
    }

    /**
     * set cart instance name
     *  @storage - cart storage service
     * @name - cart name
     */
    public function setName($name){
        $this->cart_name = $name;
        //$this->storage = ($storage =='session')?'session':'db';
        $this->getCart()->setName($this->cart_name);
        //$this->setStorage($this->storage);
        return $this;
    }

    /**
     * get cart name
     */
    public function getName(){
       return $this->getCart()->getName(); //$this->getStorage(null,$this->session->getCart()->name,$this->db->getCart()->name);
    }

    /**
     * add an item to cart
     */
    public function add($id,$price,$option=null){
         $this->getCart()->add($id,$price,$option); //$this->getStorage(null,$this->session->add($id,$price,$option),$this->db->add($id,$price,$option));
        return $this;
    }

    /**
     * get all items from cart
     *  @storage - cart storage service
     */
    public function all(){
       return $this->getCart()->all();
    }

    /**
     * get an item from cart
     *  @storage - cart storage service
     * @id - cart item id
     */
    public function get($id){
       return $this->getCart()->get($id);
    }

    /**
     * remove an item from the cart
     *  @storage - cart storage service
     * @option - cart property
     * @id - cart item id
     */
    public function update($id,$qty,$option=null){
        $cart = $this->getCart()->update($id,$qty,$option);
        return $this;
    }

    /**
     * remove an item from the cart
     *  @storage - cart storage service
     * @id - cart item id
     */
    public function remove($id){
       $cart =  $this->getCart()->remove($id);
       return $this;
    }

    /**
     * empty the cart
     *  @storage - cart storage service
     */
    public function empty(){
        $this->getCart()->empty();
        return $this;
    }

    /**
     * destroy the cart
     */
    public function destroy(){
        $this->getCart()->destroy();
    }

    /**
     * restore a cart
     */
    public function restore($cart){
        try{
            $this->getCart()->restore($cart);
        }catch(CartException $e){
            $e->getException();
        }
    }

    /**
     * get options property of cart
     */
    public function getOptions(){
        return $this->getCart()->getOptions();
    }

    /**
     * get cart total price
     */
    public function totalPrice(){
        return  $this->getCart()->totalPrice();
    }

    /**
     * get cart total price
     */
    public function totalQuantity(){
        return  $this->getCart()->totalQuantity();
    }

}