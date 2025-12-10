<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;
use App\Http\Controllers\PredictionController;

// 1. Map Page (Home)
Route::get('/', function () {
    return view('map');
})->name('map');

Route::get('/map', function () {
    return view('map');
});

// 2. Prediction API
Route::post('/predict', [PredictionController::class, 'predict'])->name('predict');

// 3. Trip History & Saving (Open to public/guests for now)
Route::resource('trips', TripController::class);