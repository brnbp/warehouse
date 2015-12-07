<?php

$version = '/v1';

Route::get('/', function () {
    return view('welcome');
});

Route::get($version.'/site/{ecommerce}', 'WarehouseController@site');

Route::post($version.'/log', 'WarehouseController@log');