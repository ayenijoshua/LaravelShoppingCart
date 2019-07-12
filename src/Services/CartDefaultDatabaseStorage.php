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
    protected $storage='database';

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
                return $this; 
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
    public function setCart($cart,$name=null){
        try{
            //dd($cart);
            $this->cart_name = $name ?? $this->cart_name;
            $model_id = $this->getModel()::where('cart_name',$this->cart_name)->value('id');
            $model = $this->getModel()::find($model_id);
            if($model){
                $model->cart_data = serialize($cart);
                $model->update();
            }else{
                $model = $this->modelInstance();
                $model->cart_name = $cart->name;
                $model->cart_data = serialize($cart);
                $model->save();
            }
            if (class_exists(\App\Events\CartSet::class) && $cart){
                event(new \App\Events\CartSet($cart));
            }
        }catch(CartException $e){
            $e->getException();
        }
    }

    /**
     * set the name of the cart
     */
    public function setName($name){
        $oldCart = class_exists($this->getModel())?$this->getModel()::where('cart_name',$name)->first():null;
        if(!$oldCart){
            $this->cart_name = $name;
            $cart = $this->getCart()->setName($name);
            $this->setCart($cart);
        }
        return $this;
    }

    /**
     * set cart storage and name (for multiple cart storage)
     */
    public function setStorage($name){
        $oldCart = class_exists($this->getModel())?$this->getModel()::where('cart_name',$name)->first():null;
        if(!$oldCart){
            $cart = $this->getCart()->setStorage($this->storage,$name);
            $this->cart_name = $name;
            $this->setCart($cart);
        }
        return $this;
    }

    /**
     * get car storage
     */
    public function getStorage(){
        return $this->getCart()->storage;
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
    public function add($id,$price,$option=null){
        $cart = $this->getCart()->addToCart($id,$price,$option);
        //dd($cart);
        $this->setCart($cart);
        if (class_exists(\App\Events\CartItemAdded::class) && $cart->items[$id]){
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
       return $this->getCart()->items;
    }

    /**
     * get an item from cart
     */
    public function get($id){
        try{
            if(!array_key_exists($id,$this->getCart()->items)){
                throw (new CartException('IdNotFound',$id));
            }
            $item = $this->getCart()->items[$id];
            if (class_exists(\App\Events\CartItemGotten::class) && $cart->items[$id]){
                event(new \App\Events\CartItemGotten($cart->items[$id]));
            }
          return $item;
        }catch(CartException $e){
            $e->getException();
        }
    }

    /**
     * update the cart
     */
    public function update($id,$qty,$option=null){
        try{
            $cart =  $this->getCart()->updateCart($id,$qty,$option);
            $this->setCart($cart);
            if (class_exists(\App\Events\CartItemUpdated::class) && $cart->items[$id]){
                event(new \App\Events\CartItemUpdated($cart->items[$id]));
            }
        }catch(CartException $e){
            $e->getException();
        }
    }

    /**
     * remove an item from cart
     */
    public function remove($id){
        $cart =  $this->getCart()->removeFromCart($id);
        $this->setCart($cart);
        if (class_exists(\App\Events\CartItemRemoved::class) && $cart->items[$id]){
            event(new \App\Events\CartItemRemoved($cart->items[$id]));
        }
     }

    /**
     * empty the cart
     */
    public function empty(){
       $cart = $this->getCart()->emptyCart();
       $this->setCart($cart);
       if(class_exists(\App\Events\CartEmptyed::class) && $cart){
            event(new \App\Events\CartEmptyed($cart));
        }
    }

    /**
     * destroy the cart
     */
    public function destroy(){
        try{
            $cart =  $this->getCart()->destroyCart();
            $model_id = $this->getModel()::where('cart_name',$this->cart_name)->value('id');
            $model = $this->getModel()::find($model_id);
            if($model){
                $model->delete();
                if (class_exists(\App\Events\CartDestroyed::class)){
                    event(new \App\Events\CartDestroyed());
                }
            }else{
                throw new CartException("Unable to delete cart from database");
            }
        }catch(CartException $e){
            $e->getException();
        }
    }

    /**
     * get cart options property
     */
    public function getOptions($id){
        $options = $this->getCart()->items[$id]['options'];
        if (class_exists(\App\Events\CartOptionsGotten::class) && $options){
            event(new \App\Events\CartOptionsGotten($options));
        }
       return $options;
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
                $this->setStorage($newCart->name ?? $this->cart_name)->setCart($newCart);
                //$this->setCart($newCart,$newCart->name);
                if (class_exists(\App\Events\CartRestored::class) && $newCart){
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