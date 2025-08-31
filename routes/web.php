<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/send-invitation-email', [\App\Http\Controllers\EmailController::class, 'sendInvitationEmail']);
