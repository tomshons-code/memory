<?php
use App\Http\Controllers\FrontController;

Route::get('verify/{id}/{hash}', [FrontController::class, 'app'])->middleware('signed')->name('verification.verify');
Route::get('sopanel/{page?}/{subpage?}/{subpage1?}/{subpage2?}', [FrontController::class, 'panel']);
Route::get('{page?}/{subpage?}/{subpage1?}/{subpage2?}', [FrontController::class, 'app']);
