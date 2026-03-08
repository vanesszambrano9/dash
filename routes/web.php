<?php

use Illuminate\Support\Facades\Route;

use Rk\RoutingKit\Entities\RkRoute;

RkRoute::registerRoutes();

Route::get('/', function () {
    return view('welcome');
})->name('home');



require __DIR__.'/settings.php';
Route::get('/debug', function () {
    return [
        'app_key' => config('app.key') ? 'SET' : 'MISSING',
        'db' => DB::connection()->getPdo() ? 'CONNECTED' : 'FAILED',
        'env' => app()->environment(),
    ];
});
