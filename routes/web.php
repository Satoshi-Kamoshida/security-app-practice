<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/contact',[ContactController::class,'form'])->name('contact.form');
Route::post("/contact",[ContactController::class,"store"])->name("contact.store");
Route::get('/contact/thanks',[ContactController::class,'thanks'])->name('contact.thanks');
