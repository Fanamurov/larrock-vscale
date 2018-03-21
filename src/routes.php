<?php

Route::group(['prefix' => 'admin'], function(){
    Route::get('/vscale', 'Larrock\ComponentVscale\AdminVscaleController@index')->name('vscale.index');
    Route::get('/vscale/rebuild/{ctid}', 'Larrock\ComponentVscale\AdminVscaleController@rebuild')->name('vscale.rebuild');
    Route::get('/vscale/backup/{ctid}', 'Larrock\ComponentVscale\AdminVscaleController@backup')->name('vscale.backup');
});

Breadcrumbs::register('admin.'. LarrockVscale::getName() .'.index', function($breadcrumbs){
    $breadcrumbs->push(LarrockVscale::getTitle(), '/admin/'. LarrockVscale::getName());
});