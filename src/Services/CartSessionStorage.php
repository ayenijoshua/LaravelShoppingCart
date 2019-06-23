<?php
/**
 * session storage implementation for shopping cart
 */
namespace AyeniJoshua\LaravelShoppingCart\Services;

use AyeniJoshua\LaravelShoppingCart\CartStorageInterface;
use AyeniJoshua\LaravelShoppingCart\Traits\Cart;
use Illuminate\Session\SessionManager;
use Illuminate\Contracts\Events\Dispatcher;

class CartSessionStorage implements CartStorageInterface {

    use Cart;

    public $instance = 'default';
    protected $shoppingCart ;
    protected $session;
    protected $event;

    function __construct(SessionManager $session, Dispatcher $event){
        $this->session = $session;
        $this->event = $event;
    }

    /**
     * set cart instance name
     */
    public function name($instance){
        $this->instance = $name;
        return $this;
    }

    /**
     * add an item to cart
     */
    public function add($id,$price,$size=null){
        $cart = $this->addToCart($id,$price,$size=null);
        $this->shoppingCart = $this->session()->set($this->instance,$cart);
         //$this->shoppingCart = $this->all();
         return $this;
    }

    /**
     * get all items from cart
     */
    public function all(){
        $this->session()->get($this->instance)->items;
    }

    /**
     * get an item from cart
     */
    public function get($id){
        $this->session()->get($this->instance)->items[$id];
    }

    /**
     * remove an item from the cart
     */
    public function update($id,$qty,$size=null){
        $this->shoppingCart =  $this->shoppingCart->updateCart($id,$qty,$size=null);
        $this->session()->set($this->instance,$this->shoppingCart);
        $this->shoppingCart = $this->all();
        return $this;
    }

    /**
     * empty the cart
     */
    public function empty(){
        $this->session()->set($this->instance,null);
    }

}