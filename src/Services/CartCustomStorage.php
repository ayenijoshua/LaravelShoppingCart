<?php
/**
 * custom storage implementation for shopping cart
 */
namespace App\CartServices;

use AyeniJoshua\LaravelShoppingCart\Contracts\CartStorageInterface;
use AyeniJoshua\LaravelShoppingCart\Services\Cart;
//use Illuminate\Session\SessionManager;
use Illuminate\Contracts\Events\Dispatcher;

class CartCustomStorage implements CartStorageInterface {

    public $cart_name = 'default';
    //protected $session;
    protected $event;

    function __construct(Dispatcher $event, $custom=null){
        //$this->session = $session;
        $this->event = $event;
    }

    /**
     * get cart
     */
    private function getCart(){
        // $oldCart = $this->session()->has($this->cart_name)?$this->session()->get($this->cart_name):null;
        // $cart = new Cart($oldCart);
        // return $cart;
    }

    /**
     * set cart
     */
    private function setCart($cart){
        //$this->session()->put($this->cart_name,$cart);
    }

    /**
     * set cart instance name
     */
    public function setName($name){
        //$this->instance = $name;
    }

    /**
     * add an item to cart
     */
    public function add($id,$price,$size=null){
        //$cart = $this->getCart()->addToCart($id,$price,$size=null);
        //$this->setCart($cart);
    }

    /**
     * get all items from cart
     */
    public function all(){
        //$this->getCart()->items;
    }

    /**
     * get an item from cart
     */
    public function get($id){
        //$this->getCart()->items[$id];
    }

    /**
     * remove an item from the cart
     */
    public function update($id,$qty,$size=null){
        //$cart = $this->getCart()->updateCart($id,$qty,$size=null);
        //$this->setCart($cart);
    }

    public function remove($id){
       //$cart =  $this->getCart()->removeFromCart($id);
       //$this->setCart($cart);
    }

    /**
     * empty the cart
     */
    public function empty(){
        //$cart =  $this->getCart()->emptyCart();
        //$this->setCart($cart);
    }
}