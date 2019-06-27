<?php

namespace AyeniJoshua\LaravelShoppingCart\Providers;

use Illuminate\Support\ServiceProvider;
//use AyeniJoshua\LaravelShoppingCart\Services\CartStorageInterface;
use AyeniJoshua\LaravelShoppingCart\Services\CartDefaultSessionStorage;
use AyeniJoshua\LaravelShoppingCart\Services\CartDefaultDatabaseStorage;
use AyeniJoshua\LaravelShoppingCart\Services\CartMultipleStorage;

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

        $this->publishes([
            __DIR__.'/Commands/create_carts_table.php' => database_path('migrations/create_carts_table.php'),
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
        $multiple_storage = $this->app['config']->get('ayenicart.multiple_storage.activate',false);
        $default = $this->app['config']->get('ayenicart.multiple_storage.default',false);
        $class= $this->app['config']->get('ayenicart.multiple_storage.class');
        $pendencies = $this->app['config']->get('ayenicart.multiple_storage.dependencies');
        if($multiple_storage){
            if($default){
                $this->app->singleton('cart',function($app){
                    return new CartMultipleStorage(CartDefaultSessionStorage::class,CartDefaultDatabaseStorage::class);
                     // $class = $this->getStorageService();//$this->storageClass($this->app['session'],$this->app['events']);
                     // return new $class['class']($class['dependencies']); //new $class['class']($this->app['events'],$this->app['session']);
                 });
            }else{
                $this->app->singleton('cart',function($app){
                    return new $class($app[$dependencies[0]],$app[$dependencies[1]]);
                });
               
                //return new CartMultipleStorage(CartDefaultSessionStorage::class,CartDefaultDatabaseStorage::class);
            }
           
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
                $class['class'] = $this->getSessionService();
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
            
        }
    }

    /**
     * get cart database storage service
     */
    public function getDatabaseService(){
        $service = $this->app['config']->get('ayenicart.database.driver','default');
        switch ($service) {
            case 'default':
                return "\AyeniJoshua\LaravelShoppingCart\Services\CartDefaultDatabaseStorage";
                break;
            
            default:
                return '\App\CartServices\Cart'.ucfirst($service)."DatabaseStorage";
                break;
        }
    }

    /**
     * get cart session storage service
     */
    public function getSessionService(){
        $service = $this->app['config']->get('ayenicart.session.driver','default');
        switch ($service) {
            case 'default':
                return "\AyeniJoshua\LaravelShoppingCart\Services\CartDefaultSessionStorage";
                break;
            
            default:
                return '\App\CartServices\Cart'.ucfirst($service)."SessionStorage";
                break;
        }
    }

    public function provides(){
        return ['cart'];
    }
}
