<?php
/**
 * custom databse storage implementation for shopping cart
 */
namespace App\CartServices;

use AyeniJoshua\LaravelShoppingCart\Contracts\CartStorageInterface;
use AyeniJoshua\LaravelShoppingCart\Services\Cart;
use AyeniJoshua\LaravelShoppingCart\Exceptions\CartException;
//use Illuminate\Session\SessionManager;
use Illuminate\Contracts\Events\Dispatcher;

class classname implements CartStorageInterface {

    //public $mode = 'default-cart';
    protected $model;
    protected $event;
    protected $cart_name = 'default';

    function __construct(Dispatcher $event, Model $model){
        //$this->model = $model;
        $this->event = $event;
    }

    /**
     * set cart instance name
     */
    private function getModel(){
        //$this->model = config('ayenicart.model_namespace','\App\Cart');
        //return $this->model;
    }

    /**
     * get cart model instance
     */
    private function modelInstance(){
        //$class = $this->getModel();
        //return new $class();
    }
    /**
     * get the cart
     */
    private function getCart(){
        // $oldCart = class_exists($this->getModel())?$this->model::where('cart_name',$this->cart_name)->get():null;  //$this->session()->has($this->instance)?$this->session()->get($this->instance):null;
        // $cart = $oldCart ? new Cart(unserialize($oldCart->cart_data)) : new Cart($oldCart);
        // return $cart;
    }


    /**
     * set cart
     */
    private function setCart($cart){
        // $model = $this->modelInstance();
        // if($prop){
        //     $model->prop = $this->prop;
        // }
        // $model->cart_data = serialize($cart);
        // $model->save();
    }

    /**
     * set the name of the cart
     */
    public function setName($name){
        //$this->cart_name = $name;
    }

    /**
     * add an item to cart
     */
    public function add($id,$price,$size=null){
        //$cart = $this->getCart()->addToCart($id,$price,$size=null);
        //$this->setCart('cart_name');
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
     * update the cart
     */
    public function update($id,$qty,$size=null){
        //$cart =  $this->getCart()->updateCart($id,$qty,$size=null);
        //$this->setCart($cart);
    }

    /**
     * remove an item from cart
     */
    public function remove($id){
        //$cart =  $this->getCart()->removeFromCart($id);
        //$this->setCart($cart);
     }

    /**
     * empty the cart
     */
    public function empty(){
       //$cart = $this->getCart()->emptyCart();
       //$this->setCart();
    }

    /**
     * restore a cart
     */
    public function restore($cart){
        // try{
        //     $unSerialize = unserialize($cart);
        //     $newCart = new $unSerialize;
        //     if($newCart instanceof Cart){
        //         $newCart = new Cart($cart);
        //         $this->setCart($newCart);
        //         return $this;
        //     }
        //     throw new CartException("Cart passed for restoration is invalid");
        // }catch(CartException $e){
        //     $e->getException();
        // }
    }

    /**
     * total price of items in the cart
     */
    public function totalPrice(){
        //return  $this->getCart()->totalPrice;
    }

    /**
     * total quantity of items in the cart
     */
    public function totalQuantity(){
        //return  $this->getCart()->totalQty;
    }
}