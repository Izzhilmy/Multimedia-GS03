<?php

use Illuminate\Support\Facades\Route;
Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', fn() => view('auth.login'))->name('login');
