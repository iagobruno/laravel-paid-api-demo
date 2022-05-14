<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', LandingController::class)->name('home');

Route::get('/auth/github/redirect', [AuthController::class, 'redirect'])->name('auth.github');
Route::get('/auth/github/callback', [AuthController::class, 'callback']);
Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');
