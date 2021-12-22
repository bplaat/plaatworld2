<?php

use Illuminate\Support\Facades\Artisan;

// Helpers
Artisan::command('websockets:dev', function () {
    system('nodemon -w app -e php --exec php artisan websockets:serve');
})->purpose('Run the WebSocket server with dev live refresh');
