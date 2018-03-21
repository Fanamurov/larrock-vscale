<?php

Route::group(['prefix' => 'admin'], function(){
    Route::get('/vscale', 'Larrock\ComponentVscale\AdminVscaleController@index');
});

Breadcrumbs::register('admin.'. LarrockVscale::getName() .'.index', function($breadcrumbs){
    $breadcrumbs->push(LarrockVscale::getTitle(), '/admin/'. LarrockVscale::getName());
});