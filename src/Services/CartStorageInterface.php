<?php
namespace AyeniJoshua\LaravelShoppingCart\Services;

/**
 * interface to be implementd by storage implementations
 */

 Interface CartStorageInterface {

    public function setName($name);
  
    public function add($id,$price,$size=null);

    public function all();

    public function get($id);

    public function remove($id);

    public function empty();

    public function update($id,$qty,$size=null);
 }

