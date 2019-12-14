<?php 
namespace AyeniJoshua\LaravelShoppingCart\Services;
/**
 * redis implementation of cart storage
 */
use AyeniJoshua\LaravelShoppingCart\Contacts\CartStorageInterface;
use AyeniJoshua\LaravelShoppingCart\Exceptions\CartException;
use Illuminate\Contracts\Events\Dispatcher;
use Iluminate\Support\Facades\Redis;

 class CartRedisStorage implements CartStorageInterface{
     
    private $event;
    private $cart_name;

    function __construct(Dispatcher $event){
        $this->event = $event;
    }

    /**
     * get cart
     * @name - cart name (if the user wants to get an instance of the cart manager)
     */
    public function getCart($name=null){
        try{
            if($name){
                if(Redis::get($name) && Redis::get($name)!='nil'){
                    $this->cart_name = $name;
                    return $this;
                }
                throw new CartException("Supplied Cart name $name does not exist");
            }
            $oldCart = Redis::get($this->cart_name) && Redis::get($name)!='nil'?Redis::get($this->cart_name):null;
            $cart = new Cart($oldCart);
            return $cart;
        }catch(CartException $e){
            $e->getException();
        }
    }

    /**
     * set cart
     * @cart - shopping cart
     */
    public function setCart($cart){
        Redis::set($this->cart_name,$cart);
    }

    /**
     * set cart instance name
     * @name - cart name
     */
    public function setName($name){
        $this->cart_name = $name;
        $cart = $this->getCart()->setName($name);
        $this->setCart($cart);
        return $this;
    }

    /**
     * get cart name
     */
    public function getName(){
        return $this->getCart()->name;
    }

    /**
     * add an item to cart
     * @id - item id
     * @price - item price
     * @option - product property (e.g size or color etc)
     */
    public function add($id,$price,$option=null){
        $cart = $this->getCart()->addToCart($id,$price,$option=null);
        $this->setCart($cart);
        return $this;
    }

    /**
     * get all items from cart
     */
    public function all(){
      return  $this->getCart()->items;
      return $this;
    }

    /**
     * get an item from cart
     * @id - item id
     */
    public function get($id){
        try{
            if(!array_key_exists($id,$this->getCart()->items)){
                throw (new CartException('IdNotFound',$id));
            }
            return $this->getCart()->items[$id];
        }catch(CartException $e){
            $e->getException();
        }
    }

    /**
     * remove an item from the cart
     * @id - item id
     * @qty - item quantity
     * @option - product property (e.g size or color etc)
     */
    public function update($id,$qty,$option=null){
        $cart = $this->getCart()->updateCart($id,$qty,$option=null);
        $this->setCart($cart);
        return $this;
    }

    public function remove($id){
       $cart =  $this->getCart()->removeFromCart($id);
       $this->setCart($cart);
       return $this;
    }

    /**
     * empty the cart
     */
    public function empty(){
        $cart =  $this->getCart()->emptyCart();
        $this->setCart($cart);
        return $this;
    }

    /**
     * destroy the cart
     */
    public function destroy(){
        $cart =  $this->getCart()->destroyCart();
        $this->setCart($cart);
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
     * total price of items in the cart
     */
    public function totalPrice(){
        return  $this->getCart()->totalPrice;
    }

    /**
     * total quantity of items in the cart
     */
    public function totalQuantity(){
        return  $this->getCart()->totalQty;
    }

 }