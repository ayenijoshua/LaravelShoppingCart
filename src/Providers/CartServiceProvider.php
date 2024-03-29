<?php

namespace AyeniJoshua\LaravelShoppingCart\Providers;

use Illuminate\Support\ServiceProvider;
//use AyeniJoshua\LaravelShoppingCart\Services\CartStorageInterface;
use AyeniJoshua\LaravelShoppingCart\Services\CartDefaultSessionStorage;
use AyeniJoshua\LaravelShoppingCart\Services\CartDefaultDatabaseStorage;
use AyeniJoshua\LaravelShoppingCart\Services\Cart as ShoppingCart;
use AyeniJoshua\LaravelShoppingCart\Services\CartDefaultMultipleStorage;
use AyeniJoshua\LaravelShoppingCart\Contracts\CartStorageInterface;
use Illuminate\Support\Facades\Log;

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

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/2019_06_24_104625_create_carts_table.php');

        if($this->app->runningInConsole()){
            $this->commands([
                \AyeniJoshua\LaravelShoppingCart\Commands\GenerateCartStorage::class
            ]);
        }

        // $this->publishes([
        //     __DIR__.'/../database/migrations/2019_06_24_104625_create_carts_table.php' => database_path('migrations/2019_06_24_104625_create_carts_table.php'),
        // ],'migration');

        // $this->publishes([
        //     __DIR__.'/../Commands/GenerateCartStorage.php' => app_path('Console/Commands/GenerateCartStorage.php'),
        // ],'command');

        //puslish events and listeners
        $this->publishes([
            __DIR__.'/../Events/CartDestroyed.php' => app_path('Events/CartDestroyed.php'),
            __DIR__.'/../Events/CartEmptyed.php' => app_path('Events/CartEmptied.php'),
            __DIR__.'/../Events/CartItemAdded.php' => app_path('Events/CartItemAdded.php'),
            __DIR__.'/../Events/CartItemGotten.php' => app_path('Events/CartItemGotten.php'),
            __DIR__.'/../Events/CartItemRemoved.php' => app_path('Events/CartItemRemoved.php'),
            __DIR__.'/../Events/CartItemsGotten.php' => app_path('Events/CartItemsGotten.php'),
            __DIR__.'/../Events/CartItemUpdated.php' => app_path('Events/CartItemUpdated.php'),
            __DIR__.'/../Events/CartOptionsGotten.php' => app_path('Events/CartOptionsGotten.php'),
            __DIR__.'/../Events/CartRestored.php' => app_path('Events/CartRestored.php'),
            __DIR__.'/../Events/CartSet.php' => app_path('Events/CartSet.php'),
            __DIR__.'/../Listeners/CartEventSubscriber.php' => app_path('Listeners/CartEventSubscriber.php'),
        ],'events');

        //publish listeners
        // $this->publishes([
        //     __DIR__.'/../Listeners/CartEventSubscriber.php' => app_path('Listeners/CartEventSubscriber.php'),
        // ],'listeners');
        
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
                $storage = $this->getStorageService();//$this->storageClass($this->app['session'],$this->app['events']);
                $totalDependencies = count($storage['dependencies']);
                if(version_compare(phpversion(), '7', '>=')){ // check if php version is >= 7
                    return new $storage['class'](...$storage['dependencies']);
                }
               return $this->switchDependencies($totalDependencies,$storage);
            }
    }

    /**
     * switch total dependencies 5 max (for php versions < 7.0)
     */
    public function switchDependencies($totalDependencies,$storage){
        switch ($totalDependencies) {
            case 0:
                return new $storage['class']();
                break;
            case 1:
                return new $storage['class']($storage['dependencies'][0]);
                break;
            case 2:
                return new $storage['class']($storage['dependencies'][0],$storage['dependencies'][1]);
                break;
            case 3:
                return new $storage['class']($storage['dependencies'][0],$storage['dependencies'][1],$storage['dependencies'][2]);  
                break;
            case 4:
                return new $storage['class']($storage['dependencies'][0],$storage['dependencies'][1],$storage['dependencies'][2],$storage['dependencies'][3]);
                break;
            case 5:
                return new $storage['class']($storage['dependencies'][0],$storage['dependencies'][1],$storage['dependencies'][2],$storage['dependencies'][3],$storage['dependencies'][4]);
                break;
            default:
                return new $storage['class']($storage['dependencies'][0],$storage['dependencies'][1]);
                break;
        }
    }

    /**
     * get cart storage service
     */
    public function getStorageService(){
        $storage = $this->app['config']->get('ayenicart.storage','session'); // session is the default storage
        switch ($storage) {
            case 'session':
                $dependencies = $this->app['config']->get('ayenicart.session.dependencies');
                $dep_array = []; $class = []; 
                foreach ($dependencies as $key => $value) {
                    $dep_array[] =  $this->app[$value];
                }
                $storage['dependencies'] = $dep_array; 
                $storage['class'] = $this->getSessionService();
                return $storage;
                break;
               
            case 'database':
                $dependencies = $this->app['config']->get('ayenicart.database.dependencies');
                $dep_array = []; $class = [];
                foreach ($dependencies as $key => $value) {
                    $dep_array[] = $this->app[$value];
                }
                $storage['dependencies'] = $dep_array; 
                $storage['class'] = $this->getDatabaseService();
                return $storage;
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

    /**
     * list file in a directory
     * @path - absolute path
     * @path_dir - diectory to make iteration
     * @path_to - laravel absolute path function e.g (app_path,base_path etc)
     * @return - array
     */
    //private function listFiles($path,$path_dir,$path_to){
    //     $dir = base_path($path);
    //     $hide = array('.','..','.DS_Store');
    //     $files = scandir($dir);
    //     $files_array = [];
    //     foreach($files as $file){
    //         if(!in_array($file,$hide)){
    //             $files_array[] = array(__DIR__."/../$path_dir/$file" => app_path("$path_dir/$file"));
    //         }
    //     }
    //     //Log::info(...$files_array);
    //     //Log::info(collect($files_array)->flatten(2));
    //     $col = collect($files_array)->toArray; Log::info(implode(',',$col));
    //     return collect($files_array)->values()->all();
    // }

    // public function provides(){
    //     return ['cart'];
    // }
}
