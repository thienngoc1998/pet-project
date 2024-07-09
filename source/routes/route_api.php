<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Api','prefix' => 'api-ptp'], function(){
    Route::get('','ApiDashboardController@index');
});
