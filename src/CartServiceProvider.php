<?php

namespace AyeniJoshua\LaravelShoppingCart;

use Illuminate\Support\ServiceProvider;
use AyeniJoshua\LaravelShoppingCart\Services\CartStorageInterface;

class CartServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
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
        $multiple_storage = $this->app['config']->get('ayenicart.multiple_storage',false);
        if($multiple_storage){
            $this->app->bind('cart',function($app){
                $class = $this->getStorageService();//$this->storageClass($this->app['session'],$this->app['events']);
                return new $class['class']($class['dependencies']); //new $class['class']($this->app['events'],$this->app['session']);
            });
        }else{
            $this->app->singleton('cart',function($app){
                $class = $this->getStorageService();//$this->storageClass($this->app['session'],$this->app['events']);
                return new $class['class']($class['dependencies']); //new $class['class']($this->app['events'],$this->app['session']);
            });
        }
        
    }

    /**
     * get cart session storage
     */
    public function getStorageService(){
        $class = $this->app['config']->get('ayenicart.storage','session');
        switch ($class) {
            case 'session':
                $dependencies = $this->app['config']->get('ayenicart.session.dependencies');
                $dep_array = []; $class = [];
                foreach ($dependencies as $key => $value) {
                    $dep_array[] = $this->app[$value];
                }
                $class['dependencies'] = implode(',',$dep_array); 
                $class['class'] = "\AyeniJoshua\LaravelShoppingCart\Services\CartSessionStorage";
                return $class;
                break;

            case 'database':
                $dependencies = $this->app['config']->get('ayenicart.database.dependencies');
                $dep_array = []; $class = [];
                foreach ($dependencies as $key => $value) {
                    $dep_array[] = $this->app[$value];
                }
                $class['dependencies'] = implode(',',$dep_array); 
                $class['class'] = $this->getDatabaseService();
                return ;
                break;
            
            default:
                $dependencies = $this->app['config']->get("ayenicart.$class.dependencies");
                $dep_array = []; $class = [];
                foreach ($dependencies as $key => $value) {
                    $dep_array[] = $this->app[$value];
                }
                $class['dependencies'] = implode(',',$dep_array);
                $class['class'] = '\App\CartServices\Cart'.ucfirst($class)."Storage";
                return $class;
                break;
        }
    }

    /**
     * get cart database storage service
     */
    public function getDatabaseService(){
        $service = $this->app['config']->get('ayenicart.database.service','eloquent');
        switch ($service) {
            case 'eloquent':
                return "\AyeniJoshua\LaravelShoppingCart\Services\CartEloquentDatabase";
                break;
            
            default:
                return '\App\CartServices\Cart'.ucfirst($service)."Database";
                break;
        }
    }

    public function provides(){
        return ['cart'];
    }
}
