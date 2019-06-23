<?php

namespace AyeniJoshua\LaravelShoppingCart;

use Illuminate\Support\ServiceProvider;
use AyeniJoshua\LaravelShoppingCart\Services\CartStorageInterface;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/cart.php' => config_path('ayenicart.php')
        ],'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $class = $this->getStorageService();
        $this->app->singleton('cart',function($app){
            return new $class();
        });
    }

    public function getStorageService(){
        $class = $this->app['config']->get('cart.storage','session');
        switch ($class) {
            case 'session':
                return 'CartSessionStorage';
                break;
            case 'database':
                return 'CartDatabaseStorage';
                break;
            
            default:
                return 'Cart'.ucfirst($class).'Storage';
                break;
        }
    }
}
