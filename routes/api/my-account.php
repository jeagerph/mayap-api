<?php

    Route::group([
        'prefix' => 'my-account',
    ], function()
    {
        Route::get('', 'MyAccountController@index')->name('my-account.index');

        Route::get('edit', 'MyAccountController@edit')->name('my-account.index');

        Route::put('', 'MyAccountController@update')->name('my-account.update');

        Route::put('password', 'MyAccountController@updatePassword')->name('my-account.update');

        Route::put('photo', 'MyAccountController@updatePhoto')->name('my-account.update');

        Route::get('activities', 'MyAccountController@showActivities')->name('my-account.index');
        
        Route::get('logout', 'MyAccountController@logout')->name('my-account.index');
    });

?>