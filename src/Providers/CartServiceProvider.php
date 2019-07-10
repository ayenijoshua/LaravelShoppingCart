<?php

namespace AyeniJoshua\LaravelShoppingCart\Providers;

use Illuminate\Support\ServiceProvider;
//use AyeniJoshua\LaravelShoppingCart\Services\CartStorageInterface;
use AyeniJoshua\LaravelShoppingCart\Services\CartDefaultSessionStorage;
use AyeniJoshua\LaravelShoppingCart\Services\CartDefaultDatabaseStorage;
use AyeniJoshua\LaravelShoppingCart\Services\Cart as ShoppingCart;
use AyeniJoshua\LaravelShoppingCart\Services\CartDefaultMultipleStorage;
use AyeniJoshua\LaravelShoppingCart\Contracts\CartStorageInterface;

class CartServiceProvider extends ServiceProvider
{
    //protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {   
        $this->publishes([
            __DIR__.'/../config/cart.php' => config_path('ayenicart.php'),
        ],'config');

        $this->publishes([
            __DIR__.'/../database/migrations/2019_06_24_104625_create_carts_table.php' => database_path('migrations/2019_06_24_104625_create_carts_table.php'),
        ],'migration');

        $this->publishes([
            __DIR__.'/../Commands/GenerateCartStorage.php' => app_path('Console/Commands/GenerateCartStorage.php'),
        ],'command');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // binding interface to implementation
        $this->app->bind(CartStorageInterface::class, function($app){
           return  $this->checkMultipleConnections($app);
        });

        //facade static binding
        $this->app->singleton('cart',function($app){
           return $this->checkMultipleConnections($app);
        });
        
        // binding CartDefaultSessionStorage instance
        $this->app->instance(CartDefaultSessionStorage::class,function($app){
            return new CartDefaultSessionStorage($app['events'],$app['session']);
        });

        // binding CartDefaultDatabaseStorage instance
        $this->app->instance(CartDefaultDatabaseStorage::class,function($app){
            return new CartDefaultDatabaseStorage($app['events']);
        });

        //binding cart object for event handling
        $this->app->instance(ShoppingCart::class, function(){
            return new ShoppingCart();
        });
    }

    /**
     * check user's connection (multiple or single)
     */
    private function checkMultipleConnections($app){
        $multiple_storage = $this->app['config']->get('ayenicart.multiple_storage.activate',false);
            $default = $this->app['config']->get('ayenicart.multiple_storage.default',false);
            if($multiple_storage){
                if($default){
                    return new CartDefaultMultipleStorage(new CartDefaultSessionStorage($app['events'],$app['session']),new CartDefaultDatabaseStorage($app['events']));
                }else{
                    $dependencies = $this->app['config']->get('ayenicart.multiple_storage.dependencies');
                    $class= $this->app['config']->get('ayenicart.multiple_storage.class');
                    $ses_dep = $this->app['config']->get('ayenicart.session.dependencies');
                    $db_dep = $this->app['config']->get('ayenicart.database.dependencies');
                    $ses_dep_array = []; $db_dep_array = []; 
                    foreach ($ses_dep as $key => $value) {
                        $ses_dep_array[] =  $this->app[$value];
                    }
                    foreach ($db_dep as $key => $value) {
                        $db_dep_array[] =  $this->app[$value];
                    }
                    //please use php >= 7.0
                    return new $class(new $dependencies[0](...$ses_dep_array),$dependencies[1](...$db_dep_array));
                }
            }else{
                $class = $this->getStorageService();//$this->storageClass($this->app['session'],$this->app['events']);
                $totalDependencies = count($class['dependencies']);
                if(version_compare(phpversion(), '7', '>=')){ // check if php version is >= 7
                    return new $class['class'](...$class['dependencies']);
                }
               return $this->switchDependencies($totalDependencies,$class);
            }
    }

    /**
     * switch total depencies 5 max (for php versions < 7.0)
     */
    public function switchDependencies($totalDependencies,$class){
        switch ($totalDependencies) {
            case 0:
                return new $class['class']();
                break;
            case 1:
                return new $class['class']($class['dependencies'][0]);
                break;
            case 2:
                return new $class['class']($class['dependencies'][0],$class['dependencies'][1]);
                break;
            case 3:
                return new $class['class']($class['dependencies'][0],$class['dependencies'][1],$class['dependencies'][2]);  
                break;
            case 4:
                return new $class['class']($class['dependencies'][0],$class['dependencies'][1],$class['dependencies'][2],$class['dependencies'][3]);
                break;
            case 5:
                return new $class['class']($class['dependencies'][0],$class['dependencies'][1],$class['dependencies'][2],$class['dependencies'][3],$class['dependencies'][4]);
                break;
            default:
                return new $class['class']($class['dependencies'][0],$class['dependencies'][1]);
                break;
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
                    $dep_array[] =  $this->app[$value];
                }
                $class['dependencies'] = $dep_array; 
                $class['class'] = $this->getSessionService();
                return $class;
                break;
               
            case 'database':
                $dependencies = $this->app['config']->get('ayenicart.database.dependencies');
                $dep_array = []; $class = [];
                foreach ($dependencies as $key => $value) {
                    $dep_array[] = $this->app[$value];
                }
                $class['dependencies'] = $dep_array; 
                $class['class'] = $this->getDatabaseService();
                return $class;
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

    // public function provides(){
    //     return ['cart'];
    // }
}
