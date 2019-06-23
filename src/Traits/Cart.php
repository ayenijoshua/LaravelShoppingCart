<?php
namespace AyeniJoshua\LaravelShoppingCart\Traits;
/**
 * Trait for core cart functionalties
 */

use AyeniJoshua\LaravelShoppingCart\CartException;

trait Cart {
    public $items = null;
    public $totalQty = 0;
    public $totalPrice;
    
    // public function __construct($oldCart,$instance=null) {
    //     if($instance){
    //         $this->items = $instance?$this->items[$instance]:$this->items;
    //     }
    //    if($oldCart){
    //        $this->items = $instance?$oldCart[$instance]->items:$oldCart->items;
    //        $this->totalQty = $instance?$oldCart[$instance]->totalQty:$oldCart->totalQty;
    //        $this->totalPrice = $instance?$oldCart[$instance]->totalPrice:$oldCart->totalPrice;
    //    }
    // }

    /**
     * add to cart
     */
    public function addToCart($id,$price,$size=null){
        $storedItem = ['qty'=>0, 'price'=>$price, 'sizes'=>$size];
        $this->items = $instance?$this->items[$instance]:$this->items;
        if($this->items){
            if(array_key_exists($id,$this->items)){
                $storedItem = $this->items[$id]; 
            }     
        }
        !is_array($storedItem['sizes'])?$storedItem['sizes']=[]:'';
        if($size){
            array_push($storedItem['sizes'],$size);
            $totalSize = count($storedItem['sizes']);
            $storedItem['qty']=$totalSize;
            $storedItem['price'] = $price * $totalSize;
        }else{
            $storedItem['qty']++;
            $storedItem['price'] = $price * $storedItem['qty'];
            $this->items[$id] = $storedItem;
        }
        $this->totalQty++;
        $this->totalPrice +=  $price;
        return $this;
    }

    /**
     * update cart
     */
    public function updateCart($id,$qty,$size=null){
        try{
            if(!is_int($qty)){
                throw (new CartException())->quantity($qty);
            }
            if(in_array($size,$this->items[$id]['sizes'])){// if size exists
                $key =  array_keys($this->items[$id]['sizes'], $size); //ket size key
            }
            if($this->items[$id] && abs($qty)){// if id exists and qty is supplied
                $storedItemQty = $this->items[$id]['qty'];
                if($storedItemQty > $qty){// if stored item qty is greater than supplied qty
                    $qtyDifference = $storedItemQty - $qty;
                    $this->totalQty -= $qtyDifference;
                    $this->totalPrice -= ($this->items[$id]['price'] * $qtyDifference);
                    if(in_array($size,$this->items[$id]['sizes'])){ //if sizes exists in item's sizes
                        for($i=0;$i<($qtyDifference);$i++){
                            array_pull($this->items[$id]['sizes'], $key[0]); 
                        }
                    }
                }elseif($storedItemQty < $qty){// if stored item qty is less than supplied qty
                    $qtyDifference = $qty - $storedItemQty;
                    $this->totalQty += $qtyDifference;
                    $this->totalPrice -= ($this->items[$id]['price'] * $qtyDifference);
                    if(in_array($size,$this->items[$id]['sizes'])){//if sizes exists in item's sizes
                        for($i=0;$i<($qtyDifference);$i++){
                            array_push($this->items[$id]['sizes'], $key[0]); 
                        }
                    }
                }else{
                    $this->totalQty = $this->totalQty;
                }
                $this->items[$id]['qty']=$qty;
                return $this;
            }
        }catch(CartException $e){
            $e->getException();
        }
        
        
    }
    
 }