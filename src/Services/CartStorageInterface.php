<?php
namespace AyeniJoshua\LaravelShoppingCart\Services;

/**
 * interface to be implementd by storage implementations
 */

 Interface CartStorageInterface {

    public function add();

    public function all();

    public function get();

    public function remove();

    public function empty();

    public function update();
 }

