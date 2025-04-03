<?php

    Route::get('accounts', 'AccountController@showAccounts')->name('company-accounts.index');

    Route::get('accounts/summary/download', 'AccountController@downloadSummaryReport')->name('company-accounts.index');

    Route::group([
        'prefix' => 'account'
    ], function()
    {
        // OPTIONS

        Route::get('module/options', 'AccountController@moduleOptions')->name('admin-companies.index');

        Route::get('position/options', 'AccountController@positionOptions')->name('admin-companies.index');

        Route::post('', 'AccountController@storeAccount')->name('company-accounts.store');

        Route::put('{code}', 'AccountController@updateAccount')->name('company-accounts.update');

        Route::put('{code}/photo', 'AccountController@updateAccountPhoto')->name('company-accounts.update');

        Route::put('{code}/permission', 'AccountController@updateAccountPermission')->name('company-accounts.update');

        Route::delete('{code}', 'AccountController@destroyAccount')->name('company-accounts.destroy');

        Route::get('{code}/summary/total', 'AccountController@showAccountSummaryTotal')->name('company-accounts.index');

        Route::get('{code}/summary/download', 'AccountController@downloadAccountSummaryReport')->name('company-accounts.index');
    });
?>