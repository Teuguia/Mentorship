<?php

use App\Http\Controllers\DomainController;
use App\Http\Controllers\MenteeController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('mentors', MentorController::class);
Route::apiResource('mentees', MenteeController::class);
Route::apiResource('domains', DomainController::class);
Route::get('domains/{domain}/mentors', [DomainController::class, 'mentors']);
Route::apiResource('sessions', SessionController::class);
Route::apiResource('reviews', ReviewController::class);
