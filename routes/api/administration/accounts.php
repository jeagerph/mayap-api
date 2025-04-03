<?php

    Route::get('accounts', 'AccountController@index')->name('admin-accounts.index');

    Route::group([
        'prefix' => 'account',
    ], function()
    {   
        Route::get('module/options', 'AccountController@moduleOptions')->name('admin-accounts.index');

        Route::post('', 'AccountController@store')->name('admin-accounts.store');

        Route::group([
            'prefix' => '{code}'
        ], function()
        {
            Route::get('', 'AccountController@show')->name('admin-accounts.index');

            Route::get('edit', 'AccountController@edit')->name('admin-accounts.index');

            Route::put('', 'AccountController@update')->name('admin-accounts.update');

            Route::put('password', 'AccountController@updatePassword')->name('admin-accounts.update');

            Route::put('status', 'AccountController@updateStatus')->name('admin-accounts.update');

            Route::put('photo', 'AccountController@updatePhoto')->name('admin-accounts.update');

            Route::delete('', 'AccountController@destroy')->name('admin-accounts.destroy');

            
            Route::get('permissions', 'AccountController@showPermissions')->name('admin-accounts.index');
            
            Route::put('permission', 'AccountController@updatePermission')->name('admin-accounts.update');
            
            Route::get('modules', 'AccountController@showAccountModules')->name('admin-accounts.index');

            Route::get('user-pin/latest', 'AccountController@showLatestUserPin')->name('admin-accounts.index');

            Route::get('activities', 'AccountController@showActivities')->name('admin-accounts.index');

        });
    });

?>