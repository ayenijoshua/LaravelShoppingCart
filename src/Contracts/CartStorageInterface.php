<?php
namespace AyeniJoshua\LaravelShoppingCart\Contracts;

/**
 * interface to be implementd by storage implementations
 */

 Interface CartStorageInterface {
   /**
    * set cart name
    */
    public function setName($name);
    /**
    * get cart name
    */
    public function getName();
   /**
    * add an item to the cart
    */
    public function add($id,$price,$option=null);
   /**
    * get all items from the cart
    */
    public function all();
   /**
    * get an item from the cart
    */
    public function get($id);
   /**
    * remove an item form the cart
    */
    public function remove($id);
   /**
    * empty the cart 
    */
    public function empty();
    /**
    * destroy the cart 
    */
    public function destroy();
   /**
    * update the cart
    */
    public function update($id,$qty,$option=null);
   /**
    * get total price of items in the cart
    */
    public function totalprice();
   /**
    * get total quantity of items in the cart
    */
    public function totalQuantity();
    /**
     * restore cart from archive
     */
    public function restore($oldcart);

 }

