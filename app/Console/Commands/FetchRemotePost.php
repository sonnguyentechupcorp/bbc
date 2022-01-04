<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchRemotePost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:fetch-remote-post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use Laravel HTTP Client feature to fetch post from remote API';

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
        $response = Http::get('https://jsonplaceholder.typicode.com/posts');

        return $response->toArra();

    }
}
