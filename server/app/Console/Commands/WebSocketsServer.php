<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebSocketsController;
use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server;

class WebSocketsServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websockets:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the websockets server';

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
        $loop = Factory::create();

        // Start websockets server
        echo 'Starting websockets server at: ws://' . config('websockets.host') . ':' . config('websockets.port') . '/' . PHP_EOL;
        $websocketsController = new WebSocketsController();
        $websocketServer = new IoServer(
            new HttpServer(new WsServer($websocketsController)),
            new Server(config('websockets.host') . ':' . config('websockets.port'), $loop),
            $loop
        );

       $loop->run();
    }
}
