<?php

use Illuminate\Support\Facades\Route;

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

Route::get('/', function () {
    return view('welcome');
});
