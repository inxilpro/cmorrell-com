<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home');
Route::view('/php-fpm', 'pages.php-fpm');
Route::view('/laravel-relationships', 'pages.laravel-relationships');
Route::view('/laravel-typehint-directive', 'pages.laravel-typehint-directive');
Route::view('/a-tale-of-two-methodologies', 'pages.a-tale-of-two-methodologies');
Route::view('/one-billion', 'pages.one-billion');

if (App::isLocal()) {
	Route::view('opengraph', 'opengraph');
}

Route::feeds();
