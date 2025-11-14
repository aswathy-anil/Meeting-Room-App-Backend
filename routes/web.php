<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-mail', function () {
    Mail::raw('This is a test email from Laravel', function ($message) {
        $message->to('achuappu1412@gmail.com')->subject('Test Mail');
    });
    return 'Mail sent!';
});
