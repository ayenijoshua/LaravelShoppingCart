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
                if($storage==='session' || $storage==='db'){
                    return $this;
                }
                throw (new CartException('InvalidStorage'));
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
    private function getCart($name=null){
       return $this->getStorage(null,$this->session->getCart($name),$this->db->getCart($name));
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
        $this->getStorage(null,$this->session->setName($name),$this->db->setName($name));
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
        $cart = $this->getStorage(null,$this->session->add($id,$price,$option=null),$this->db->add($id,$price,$option=null));
        //$this->setCart($cart);
        return $this;
    }

    /**
     * get all items from cart
     *  @storage - cart storage service
     */
    public function all(){
       return $this->getCart()->items;
    }

    /**
     * get an item from cart
     *  @storage - cart storage service
     * @id - cart item id
     */
    public function get($id){
       return $this->getCart()->items[$id];
    }

    /**
     * remove an item from the cart
     *  @storage - cart storage service
     * @option - cart property
     * @id - cart item id
     */
    public function update($id,$qty,$option=null){
        $cart = $this->getCart()->updateCart($id,$qty,$option=null);
        //$this->setCart($cart);
        return $this;
    }

    /**
     * remove an item from the cart
     *  @storage - cart storage service
     * @id - cart item id
     */
    public function remove($id){
       $cart =  $this->getCart()->removeFromCart($id);
       //$this->setCart($cart);
       return $this;
    }

    /**
     * empty the cart
     *  @storage - cart storage service
     */
    public function empty(){
        $cart =  $this->getCart()->emptyCart();
        //$this->setCart($cart);
        return $this;
    }

    /**
     * destroy the cart
     */
    public function destroy(){
        $cart = $this->getCart()->destroyCart();
        //$this->setCart($cart);
    }

    /**
     * restore a cart
     */
    public function restore($cart){
        try{
            $unSerialize = unserialize($cart);
            $newCart = new $unSerialize;
            if($newCart instanceof Cart){
                $newCart = new Cart($cart);
                $this->setCart($newCart);
                return $this;
            }
            throw new CartException("Cart passed for restoration is invalid");
        }catch(CartException $e){
            $e->getException();
        }
    }

    /**
     * get cart total price
     */
    public function totalPrice(){
        return  $this->getCart()->totalPrice;
    }

    /**
     * get cart total price
     */
    public function totalQuantity(){
        return  $this->getCart()->totalQty;
    }

}