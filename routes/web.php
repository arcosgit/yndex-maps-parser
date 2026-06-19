<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', fn() => inertia()->render('Auth/Index'));
Route::get('/', fn() => inertia()->render('Index/Index'))->middleware('auth')->name('index');

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('auth.login');
