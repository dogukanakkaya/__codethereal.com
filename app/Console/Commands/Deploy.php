<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Deploy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'All commands that needs to be run after setup';

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
     * @return int
     */
    public function handle()
    {
        $this->call('key:generate');
        $this->call('dev:reset');
        $this->call('storage:link');
        $this->call('test');
        if (!file_exists(base_path('storage/app/public/thumbs'))){
            mkdir(base_path('storage/app/public/thumbs'));
        }
        return 0;
    }
}
