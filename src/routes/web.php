<?php

Route::get('install', function(){
	return view('indoc::install');
});

Route::group(['namespace' => 'Debugsolver\Bappe\Controller'], function () {
	Route::group(['middleware' => 'web-check'],function(){
	    Route::get('install', 'PackageController@index')->name('installer');
	    Route::get('check-requirement', 'PackageController@requirement')->name('requirement');
	    Route::get('check-permissions', 'PackageController@permissionsCheck')->name('permissions');
	    Route::get('purchased-code/verification', 'PackageController@purchasedCode')->name('purchased.code');
	    Route::post('purchased-code/store', 'PackageController@purchasedCodeStore')->name('purchased.code.store');
	});
});