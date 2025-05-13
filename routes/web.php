<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PictureController;



Route::get('/', [AuthController::class, 'layout'])->name('dashboard')->middleware('auth');
Route::get('/login', [AuthController::class, 'login_form'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout',[AuthController::class,'logout'])->name('logout');

Route::prefix('picture')->controller(PictureController::class)->name('picture.')->group(function () {
    Route::get('/add','index')->name('add');
    Route::post('/add','store');
    Route::get('/show','show')->name('show');
    Route::get('/edit/{id}','edit')->name('edit');
    Route::post('/update/{id}','update')->name('update');
    Route::get('/delete/{id}','destroy')->name('delete');
    Route::get('/permdeleted/{id}','permdeleted')->name('permdeleted');
    Route::get('/restore/{id}','restore')->name('restore');
    Route::get('/trashshow','trashshow')->name('trashshow');
    Route::get('/preview/{id}','preview')->name('preview');


});