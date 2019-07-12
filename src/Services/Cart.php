<?php
namespace AyeniJoshua\LaravelShoppingCart\Services;
/**
 * Trait for core cart functionalties
 */

use AyeniJoshua\LaravelShoppingCart\Exceptions\CartException;

class Cart {
    public $items = null;
    public $totalQty = 0;
    public $totalPrice = 0;
    public $name = 'default';
    public $storage = 'session';
    
    /**
     * initialise the class
     */
    public function __construct($oldCart=null) {
       if($oldCart){
           $this->items = $oldCart->items;
           $this->totalQty = $oldCart->totalQty;
           $this->totalPrice = $oldCart->totalPrice;
           $this->name = $oldCart->name;
           $this->storage = $oldCart->storage;
       }
    }

    /**
     * set cart name
     */
    public function setName($name){
        $this->name = $name ?? $this->name;
        return $this;
    }

    /**
     * set storage for multiple cart storage
     */
    public function setStorage($storage,$name=null){
        $this->storage = $storage ?? $this->storage;
        $this->setName($name);
        return $this;
    }

    /**
     * add to cart
     * @id - product id
     * @price - product price
     * @option - product property (e.g size or color etc)
     */
    public function addToCart($id,$price,$option=null){
        try{
            $storedItem = ['qty'=>0, 'price'=>$price, 'totalPrice'=>0, 'options'=>[]];
            if($this->items){
                if(array_key_exists($id,$this->items)){
                    $storedItem = $this->items[$id]; 
                }     
            }
            if(!is_null($option)){
                array_push($storedItem['options'],$option);
                $totalOptions = count($storedItem['options']);
                $storedItem['qty']=$totalOptions;
                $storedItem['price'] = $price;
                $storedItem['totalPrice'] = $price * $totalOptions;
            }else{
                $storedItem['qty']++;
                $storedItem['price'] = $price;
                $storedItem['totalPrice'] = $price * $storedItem['qty'];
            }
            $this->items[$id] = $storedItem;
            $this->totalQty++;
            $this->totalPrice +=  $price;
        }catch(CartException $e){
            $e->getException();
        }finally{
            return $this;
        }
    }

    /**
     * update cart
     * @id - product id
     * @qty - producnt quantity
     * @option - product property (e.g size or color etc)
     */
    public function updateCart($id,$qty,$option=null){
        try{
            if(!is_int($qty)){
                throw (new CartException('InvalidQty',$qty));
            }
            if(count($this->items)>0 && array_key_exists($id,$this->items)){// if id exists and qty is supplied
                if(in_array($option,$this->items[$id]['options'])){// if size exists
                    $key =  array_keys($this->items[$id]['options'], $option); //ket size key
                }
                $storedItemQty = $this->items[$id]['qty'];
                if($storedItemQty > $qty){// if stored item qty is greater than supplied qty
                    $qtyDifference = $storedItemQty - $qty;
                    $this->totalQty -= $qtyDifference;
                    $this->totalPrice -= ($this->items[$id]['price'] * $qtyDifference);
                    if(in_array($option,$this->items[$id]['options'])){ //if sizes exists in item's sizes
                        for($i=0;$i<($qtyDifference);$i++){
                            array_pull($this->items[$id]['options'], $key[0]); 
                        }
                    }
                }elseif($storedItemQty < $qty){// if stored item qty is less than supplied qty
                    $qtyDifference = $qty - $storedItemQty;
                    $this->totalQty += $qtyDifference;
                    $this->totalPrice += ($this->items[$id]['price'] * $qtyDifference); //$this->items[$id]['totalPrice'];
                    if(in_array($option,$this->items[$id]['options'])){//if sizes exists in item's sizes
                        for($i=0;$i<($qtyDifference);$i++){
                            array_push($this->items[$id]['options'], $key[0]); 
                        }
                    }
                }else{
                    $this->totalQty = $this->totalQty;
                }
                $this->items[$id]['qty']=$qty;
                $this->items[$id]['totalPrice'] = $this->items[$id]['price'] * $qty;
            }
        }catch(CartException $e){
            $e->getException();
        }finally{
            return $this;
        }
    }

    /**
     * remove an Item from the cart
     * @id - product id
     */
    public function removeFromCart($id){
        try{
            if($this->items && array_key_exists($id,$this->items)){
                $this->totalPrice -= $this->items[$id]['totalPrice'];
                $this->totalQty -= $this->items[$id]['qty'];
                unset($this->items[$id]);
            }
        }catch(CartException $e){
            $e->getException();
        }finally{
            return $this;
        }
    }

    /**
     * empty the cart
     */
    public function emptyCart(){
        try{
            if(count($this->items)>0){
                $this->items = null;
                $this->totalQty = 0;
                $this->totalPrice =0;
                //$this->name = '';
            }
        }catch(CartExeption $e){
            $e->getException();
        }finally{
            return $this;
        }
    }

    /**
     * destroy the cart
     */
    public function destroyCart(){
        unset($this);
        return null;
    }
    
 }