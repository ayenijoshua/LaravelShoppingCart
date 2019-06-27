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

    public $cart_name = 'default';
    protected $session;
    protected $db;

    function __construct(CartDefaultSessionStorage $session, CartDefaultDatabaseStorage $db){
        $this->session = $session;
        $this->db = $db;
    }

    function getStorage($storage='session',$sessionMtd,$dbMtd){
        try{
            if($storage=='session'){
                return $sessionMtd;
              }elseif($storage=='db'){
                 return $dbMtd;
              }else{
                  throw CartException::invalidStorage("Specified storage type is invalid");
              }  
        }catch(CartException $e){
            $e->getExeption();
        }
    }

    /**
     * get cart
     */
    private function getCart($storage='session'){
       return $this->getStorage($storage,$this->session->getCart(),$this->db->getCart());
    }

    /**
     * set cart
     */
    private function setCart($storage='session',$cart){
        $this->getStorage($storage,$this->session->setCart(),$this->db->setCart());
        //$this->session()->put($this->cart_name,$cart);
    }

    /**
     * set cart instance name
     */
    public function setName($storage='session',$name){
        $this->getStorage($storage,$this->session->setName($name),$this->db->setName($name));
        //$this->cart_name = $name;
    }

    /**
     * add an item to cart
     */
    public function add($storage='session',$id,$price,$size=null){
        $cart = $this->getStorage($storage,$this->session->add($id,$price,$size=null),$this->db->add($id,$price,$size=null));
        $this->setCart($storage,$cart);
    }

    /**
     * get all items from cart
     */
    public function all($storage='session'){
       return $this->getCart($storage)->items;
    }

    /**
     * get an item from cart
     */
    public function get($storage='session',$id){
       return $this->getCart($storage)->items[$id];
    }

    /**
     * remove an item from the cart
     */
    public function update($storage='session',$id,$qty,$size=null){
        $cart = $this->getCart($storage)->updateCart($id,$qty,$size=null);
        $this->setCart($storage,$cart);
    }

    public function remove($storage='session',$id){
       $cart =  $this->getCart($storage)->removeFromCart($id);
       $this->setCart($storage,$cart);
    }

    /**
     * empty the cart
     */
    public function empty($storage='session'){
        $cart =  $this->getCart($storage)->emptyCart();
        $this->setCart($storage,$cart);
    }

}