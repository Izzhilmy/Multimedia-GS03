<?php

use App\Http\Controllers\DetectionController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('detection.form'));

Route::get('/detection',        [DetectionController::class, 'showForm'])->name('detection.form');
Route::post('/detection',       [DetectionController::class, 'analyze'])->name('detection.analyze');
Route::get('/detection/result', [DetectionController::class, 'showResult'])->name('detection.result');
Route::get('/history',          [HistoryController::class,   'index'])->name('history.index');
Route::get('/search',           [SearchController::class,    'index'])->name('search.index');
