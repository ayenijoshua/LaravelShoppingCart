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
    function setStorage($storage){
        $this->storage = $storage;
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
                    throw (new CartException('InvalidStorage'));
                }
            }
            if($this->storage=='session'){
                return $sessionMtd;
              }elseif($this->storage=='db'){
                 return $dbMtd;
              }else{
                  throw (new CartException('InvalidStorage'));
              }  
        }catch(CartException $e){
            $e->getExeption();
        }
    }

    /**
     * get cart
     * @storage - cart storage service
     */
    private function getCart(){
       return $this->getStorage(null,$this->session->getCart($this->cart_name),$this->db->getCart($this->cart_name));
    }

    /**
     * set cart
     *  @storage - cart storage service
     
    private function setCart($cart){
        $this->getStorage(null,$this->session->setCart($cart),$this->db->setCart($cart));
    }
    **/

    /**
     * set cart instance name
     *  @storage - cart storage service
     * @name - cart name
     */
    public function setName($name){
        $this->cart_name = $name;
        $this->getStorage(null,$this->session->setName($this->cart_name),$this->db->setName($this->cart_name));
        return $this;
    }

    /**
     * get cart name
     */
    public function getName(){
       return $this->getStorage(null,$this->session->getCart()->name,$this->db->getCart()->name);
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
        $cart =  $this->getCart()->empty();
        return $this;
    }

    /**
     * destroy the cart
     */
    public function destroy(){
        $cart = $this->getCart()->destroy();
        //$this->setCart($cart);
    }

    /**
     * restore a cart
     */
    public function restore($cart){
        try{
            $this->getCart()->restore();
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
        return  $this->getCart()->totalQty();
    }

}