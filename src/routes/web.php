<?php
Route::get('/login', [\App\Http\Controllers\Oauth\indexController::class, 'login']);

Route::group(['as' => 'oauth.', 'prefix' => 'oauth'], function () {
    Route::get('/logout', [\App\Http\Controllers\Oauth\indexController::class, 'logout'])->name('logout');
    Route::get('/callback', [\App\Http\Controllers\Oauth\indexController::class, 'callback'])->name('callback');
});
