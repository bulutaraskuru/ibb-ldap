<?php

use Illuminate\Support\Facades\Route;
use BulutKuru\IbbLdap\Controllers\Oauth\IndexController;

Route::get('/login', [IndexController::class, 'login']);