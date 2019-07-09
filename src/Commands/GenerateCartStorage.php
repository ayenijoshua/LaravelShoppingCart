<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateCartStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:cartstorage';

            //{driver=session : The cart storage driver} 
            //{--s|storage=session : The type of storage (session/database)}'

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to generate shopping cart storge service';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $driver;
    protected $storage;

    public function __construct()
    {
        parent::__construct();
        //$this->getInput();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->getInput();
    }

    public function getInput(){
        $this->info('Please not that this package already offers session, database and multiple (non-mutually exclusive)storage, 
        hence you should continue if your requirement needs to extend the implementation');
        if($this->confirm("Do you want to continue?")){
            $this->driver = $this->anticipate('What driver do you want to use?', ['redis','mongo','memcache']);
            $this->storage = $this->choice('What storage service do you want to use?',['session','database'],0);
            if($this->driver && $this->storage){
                $this->generate();
            }
            
        }
        $this->info("Thank you for your response, build something great");
    }

    public function getStubType($type){
        return file_get_contents(base_path("packages/ayenijoshua/LaravelShoppingCart/src/stubs/$type.php"));
    }

    public function generate(){
        $driver = ucfirst($this->driver);
        $storage = ucfirst($this->storage);
        $template = str_replace('classname',"Cart".$driver.$storage."Storage",$this->getStubType($storage));
        if(mkdir(app_path("CartServices"))){
            $file = file_put_contents(app_path("CartServices/Cart".$driver.$storage."Storage.php"),$template);
            if($file){
                $this->info("Process completed, build something great");
            }
        }
    }
}
