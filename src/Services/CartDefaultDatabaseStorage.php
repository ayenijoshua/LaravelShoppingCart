<?php
/**
 * datbase storage implementation for shopping cart
 */
namespace AyeniJoshua\LaravelShoppingCart\Services;

use AyeniJoshua\LaravelShoppingCart\Contracts\CartStorageInterface;
use AyeniJoshua\LaravelShoppingCart\Services\Cart;
use AyeniJoshua\LaravelShoppingCart\Exceptions\CartException;
//use Illuminate\Session\SessionManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;

class CartDefaultDatabaseStorage  implements CartStorageInterface {


    //public $mode = 'default-cart';
    protected $model;
    protected $event;
    protected $cart_name = 'default';

    function __construct(Dispatcher $event){
        //$this->model = $model;
        $this->event = $event;
    }

    /**
     * set cart instance name
     */
    private function getModel(){
        $model = config('ayenicart.model_namespace','\App\Cart');
        return $model;
    }

    /**
     * get cart model instance
     */
    private function modelInstance(){
        try{
            $class = $this->getModel();
            return new $class();
        }catch(CartException $e){
            $e->getException();
        }
    }

    /**
     * get the cart
     * @name - cart name (if the user wants to get an instance of the cart manager)
     */
    public function getCart($name=null){
        try{
            if($name){
                $oldCart = class_exists($this->getModel())?$this->getModel()::where('cart_name',$name)->first():null;
                if($oldCart){
                    $this->cart_name = $name;
                    return $this;
                }
                throw new CartException("Supplied Cart name $name does not exist"); 
            }
            $oldCart = class_exists($this->getModel())?$this->getModel()::where('cart_name',$this->cart_name)->first():null;
            $cart = $oldCart ? new Cart(unserialize($oldCart->cart_data)) : new Cart($oldCart);
            return $cart;
        }catch(CartException $e){
            $e->getException();
        }
    }

    /**
     * set cart
     * set a cart_name if non exists
     */
    public function setCart($cart){
        try{
            $model_id = $this->getModel()::where('cart_name',$this->cart_name)->value('id');//findOrFail(1); //$this->modelInstance();
            //dd($model_id);
            $model = $this->getModel()::find($model_id);
            if($model){
                $model->cart_data = serialize($cart);
                $model->update();
            }else{
                $model = $this->modelInstance();
                $model->cart_name = $this->cart_name;
                $model->cart_data = serialize($cart);
                $model->save();
            }
        }catch(CartException $e){
            $e->getException();
        }
    }

    /**
     * set the name of the cart
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
     */
    public function add($id,$price,$size=null){
        $cart = $this->getCart()->addToCart($id,$price,$size=null);
        $this->setCart($cart);
        return $this;
    }

    /**
     * get all items from cart
     */
    public function all(){
       return $this->getCart()->items;
    }

    /**
     * get an item from cart
     */
    public function get($id){
      return  $this->getCart()->items[$id];
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
       $this->setCart($cart);
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
     * get total price
     */
    public function totalPrice(){
        return  $this->getCart()->totalPrice;
    }

    /**
     * get total quantity
     */
    public function totalQuantity(){
        return  $this->getCart()->totalQty;
    }

}