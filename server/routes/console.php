<?php

use Illuminate\Support\Facades\Artisan;

// Helpers
Artisan::command('prod', function () {
    Artisan::call('optimize');
    Artisan::call('view:cache');
})->purpose('Cache stuff for production');

Artisan::command('websockets:dev', function () {
    system('nodemon -w app -e php --exec php artisan websockets:serve');
})->purpose('Run the WebSockets server with dev live refresh');
