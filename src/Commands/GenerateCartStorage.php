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
    protected $signature = 'cart:storage 
                            {file_name*} : The name of the file} 
                            {--type= : The typ of the file (session/database)}';

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fileName = $this->argument('file_name');
        //$fileType = $this->op
    }
}
