<?php
/**
 * datbase storage implementation for shopping cart
 */
namespace AyeniJoshua\LaravelShoppingCart\Services;

use AyeniJoshua\LaravelShoppingCart\Services\CartStorageInterface;
use AyeniJoshua\LaravelShoppingCart\Services\Cart;
//use Illuminate\Session\SessionManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;

class CartEloquentDatabasee  implements CartStorageInterface {


    //public $mode = 'default-cart';
    protected $model;
    protected $event;
    protected $cart_name = 'default';

    function __construct(Dispatcher $event, Model $model){
        $this->model = $model;
        $this->event = $event;
    }

    /**
     * set cart instance name
     */
    private function getModel(){
        $this->model = config('ayenicart.model_namespace','\App\Cart');
        return $this->model;
    }

    /**
     * get cart model instance
     */
    private function modelInstance(){
        return new $this->model;
    }
    /**
     * get the cart
     */
    private function getCart(){
        $oldCart = class_exists($this->getModel())?$this->model::where('cart_name',$this->cart_name)->get():null;  //$this->session()->has($this->instance)?$this->session()->get($this->instance):null;
        $cart = $oldCart ? new Cart(unserialize($oldCart->cart_data)) : new Cart($oldCart);
        return $cart;
    }

    /**
     * set cart
     * set a cart_name if non exists
     */
    private function setCart($cart,$prop=null){
        $model = $this->modelInstance();
        if($prop){
            $model->prop = $this->prop;
        }
        $model->cart_data = serialize($cart);
        $model->save();
    }


    /**
     * set the name of the cart
     */
    public function setName($name){
        $this->cart_name = $name;
    }

    /**
     * add an item to cart
     */
    public function add($id,$price,$size=null){
        $cart = $this->getCart()->addToCart($id,$price,$size=null);
        $this->setCart('cart_name');
    }

    /**
     * get all items from cart
     */
    public function all(){
        $this->getCart()->items;
    }

    /**
     * get an item from cart
     */
    public function get($id){
        $this->getCart()->items[$id];
    }

    /**
     * update the cart
     */
    public function update($id,$qty,$size=null){
        $cart =  $this->getCart()->updateCart($id,$qty,$size=null);
        $this->setCart($cart);
    }

    /**
     * remove an item from cart
     */
    public function remove($id){
        $cart =  $this->getCart()->removeFromCart($id);
        $this->setCart($cart);
     }

    /**
     * empty the cart
     */
    public function empty(){
       $cart = $this->getCart()->emptyCart();
       $this->setCart();
    }

}