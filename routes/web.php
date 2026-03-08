<?php

use Illuminate\Support\Facades\Route;

use Rk\RoutingKit\Entities\RkRoute;

RkRoute::registerRoutes();

Route::get('/', function () {
    return view('welcome');
})->name('home');



require __DIR__.'/settings.php';
