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
    private $storageClass;

    public function boot()
    {   
        $this->publishes([
            __DIR__.'/config/cart.php' => config_path('ayenicart.php'),
        ]);

        $this->publishes([
            __DIR__.'/Services/CartCustomStorage.php' => app_path('CartServices/CartCustomStorage.php'),
            __DIR__.'/Services/CartCustomDatabase.php' => app_path('CartServices/CartCustomDatabase.php'),
        ],'service');

        $this->publishes([
            __DIR__.'/database/migrations/create_carts_table.php' => database_path('migrations/create_carts_table.php'),
            __DIR__.'/Models/Cart.php' => app_path('Cart.php'),
        ],'migration');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->storageClass = $this->getStorageService();
        $this->app->singleton('cart',function($app){
            $class = $this->getStorageService();//$this->storageClass($this->app['session'],$this->app['events']);
            return new $class($this->app['session'],$this->app['events']);
        });
    }

    /**
     * get cart session storage
     */
    public function getStorageService(){
        $class = $this->app['config']->get('ayenicart.storage','session');
        switch ($class) {
            case 'session':
                return "\App\AyeniJoshua\LaravelShoppingCart\Services\CartSessionStorage";
                break;
            case 'database':
                return $this->getDatabaseService();;
                break;
            
            default:
                return '\App\CartServices\Cart'.ucfirst($class)."Storage";
                break;
        }
    }

    /**
     * get cart database storage service
     */
    public function getDatabaseService(){
        $class = $this->app['config']->get('ayenicart.database_service','eloquent');
        switch ($class) {
            case 'eloquent':
                return "\App\AyeniJoshua\LaravelShoppingCart\Services\CartEloquentDatabase";
                break;
            
            default:
                return '\App\CartServices\Cart'.ucfirst($class)."Database";
                break;
        }
    }
}
