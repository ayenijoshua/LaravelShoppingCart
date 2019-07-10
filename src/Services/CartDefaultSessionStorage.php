<?php
/**
 * session storage implementation for shopping cart
 */
namespace AyeniJoshua\LaravelShoppingCart\Services;

use AyeniJoshua\LaravelShoppingCart\Contracts\CartStorageInterface;
use AyeniJoshua\LaravelShoppingCart\Services\Cart;
use Illuminate\Session\SessionManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use \AyeniJoshua\LaravelShoppingCart\Exceptions\CartException;

class CartDefaultSessionStorage implements CartStorageInterface {

    public $cart_name = 'default';
    protected $session;
    protected $event;

    function __construct( Dispatcher $event, SessionManager $session){
        $this->session = $session;
        $this->event = $event;
    }

    /**
     * get cart
     * @name - cart name (if the user wants to get an instance of the cart manager)
     */
    public function getCart($name=null){
        try{
            if($name){
                if($this->session->has($name)){
                    $this->cart_name = $name;
                    return $this;
                }
                throw new CartException("Supplied Cart name $name does not exist");
            }
            $oldCart = $this->session->has($this->cart_name)?$this->session->get($this->cart_name):null;
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
        $this->session->put($this->cart_name,$cart);
        if (class_exists(\App\Events\CartSet::class)){
            event(new \App\Events\CartSet($cart));
        }
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
        $cart = $this->getCart()->addToCart($id,$price,$option);
        $this->setCart($cart);
        if (class_exists(\App\Events\CartItemAdded::class)){
            event(new \App\Events\CartItemAdded($cart->items[$id]));
        }
        return $this;
    }

    /**
     * get all items from cart
     */
    public function all(){
        if (class_exists(\App\Events\CartItemsGotten::class) && $this->getCart()->items){
            event(new \App\Events\CartItemsGotten($this->getCart()->items));
        }
      return  $this->getCart()->items;
      //return $this;
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
            if (class_exists(\App\Events\CartItemGotten::class)){
                event(new \App\Events\CartItemGotten($cart->items[$id]));
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
        $cart = $this->getCart()->updateCart($id,$qty,$option);
        $this->setCart($cart);
        if (class_exists(\App\Events\CartItemUpdated::class)){
            event(new \App\Events\CartItemUpdated($cart->items[$id]));
        }
        return $this;
    }

    public function remove($id){
       $cart =  $this->getCart()->removeFromCart($id);
       $this->setCart($cart);
       if (class_exists(\App\Events\CartItemRemoved::class)){
        event(new \App\Events\CartItemRemoved($cart->items[$id]));
    }
       return $this;
    }

    /**
     * empty the cart
     */
    public function empty(){
        $cart =  $this->getCart()->emptyCart();
        $this->setCart($cart);
        if (class_exists(\App\Events\CartEmptyed::class)){
            event(new \App\Events\CartEmptyed($cart));
        }
        return $this;
    }

    /**
     * destroy the cart
     */
    public function destroy(){
        $this->empty();  
        $this->getCart()->destroyCart();
        $this->session->forget($this->cart_name);
        unset($this);
        if (class_exists(\App\Events\CartDestroyed::class)){
            event(new \App\Events\CartDestroyed());
        }
    }

    /**
     * restore a cart
     */
    public function restore($cart){
        try{
            $unSerialize = unserialize($cart);
            $newCart = new $unSerialize;
            if($newCart instanceof Cart){
                $newCart = new Cart($unSerialize);
                $this->setCart($newCart);
                if (class_exists(\App\Events\CartRestored::class)){
                    event(new \App\Events\CartRestored($newCart));
                }
                return $this;
            }
            throw new CartException("Cart passed for restoration is invalid");
        }catch(CartException $e){
            $e->getException();
        }
    }

    /**
     * get options property in the cart
     */
    public function getOptions($id){
        $options = $this->getCart()->items[$id]['options'];
        if (class_exists(\App\Events\CartOptionsGotten::class)){
            event(new \App\Events\CartOptionsGotten($options));
        }
       return $options;
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