<?php

    Route::get('companies', 'CompanyController@index')->name('admin-companies.index');

    Route::group([
        'prefix' => 'company',
    ], function()
    {
        // OPTIONS

        Route::get('module/options', 'CompanyController@moduleOptions')->name('admin-companies.index');

        Route::get('position/options', 'CompanyController@positionOptions')->name('admin-companies.index');

        Route::get('provinces', 'CompanyController@provincesOptions')->name('admin-companies.index');

        Route::get('cities', 'CompanyController@citiesOptions')->name('admin-companies.index');
        
        Route::get('barangays', 'CompanyController@barangaysOptions')->name('admin-companies.index');

        
        Route::post('', 'CompanyController@store')->name('admin-companies.store');

        Route::group([
            'prefix' => '{code}'
        ], function()
        {
            Route::get('', 'CompanyController@show')->name('admin-companies.index');

            Route::get('edit', 'CompanyController@edit')->name('admin-companies.index');

            Route::put('', 'CompanyController@update')->name('admin-companies.update');

            Route::put('logo', 'CompanyController@updateLogo')->name('admin-companies.update');

            Route::put('sub-logo', 'CompanyController@updateSubLogo')->name('admin-companies.update');

            Route::put('status', 'CompanyController@updateStatus')->name('admin-companies.update');

            Route::delete('', 'CompanyController@destroy')->name('admin-companies.destroy');

            Route::get('activities', 'CompanyController@showActivities')->name('admin-companies.index');

            Route::get('accounts', 'CompanyController@showAccounts')->name('admin-companies.index');

            Route::group([
                'prefix' => 'account'
            ], function()
            {
                Route::post('', 'CompanyController@storeAccount')->name('admin-companies.store');

                Route::put('{accountCode}', 'CompanyController@updateAccount')->name('admin-companies.index');

                Route::put('{accountCode}/permission', 'CompanyController@updateAccountPermission')->name('admin-companies.update');

                Route::put('{accountCode}/photo', 'CompanyController@updateAccountPhoto')->name('admin-companies.update');

                Route::delete('{accountCode}', 'CompanyController@destroyAccount')->name('admin-companies.destroy');
            });


            Route::get('barangays', 'CompanyController@showBarangays')->name('admin-companies.index');

            Route::group([
                'prefix' => 'barangay'
            ], function()
            {
                Route::post('', 'CompanyController@storeBarangay')->name('admin-companies.store');

                Route::put('{barangayId}', 'CompanyController@updateBarangay')->name('admin-companies.update');

                Route::put('{barangayId}/status', 'CompanyController@updateBarangayStatus')->name('admin-companies.update');

                Route::put('{barangayId}/city/logo', 'CompanyController@updateCityLogo')->name('admin-companies.update');

                Route::put('{barangayId}/barangay/logo', 'CompanyController@updateBarangayLogo')->name('admin-companies.update');

                Route::delete('{barangayId}', 'CompanyController@destroyBarangay')->name('admin-companies.destroy');
            });

            Route::group([
                'prefix' => 'sms/setting'
            ], function()
            {
                Route::get('', 'CompanyController@showSmsSetting')->name('admin-companies.index');

                Route::put('', 'CompanyController@updateSmsSetting')->name('admin-companies.update');
            });

            Route::group([
                'prefix' => 'call/setting'
            ], function()
            {
                Route::get('', 'CompanyController@showCallSetting')->name('admin-companies.index');

                Route::put('', 'CompanyController@updateCallSetting')->name('admin-companies.update');
            });

            Route::group([
                'prefix' => 'network/setting'
            ], function()
            {
                Route::get('', 'CompanyController@showNetworkSetting')->name('admin-companies.index');

                Route::put('', 'CompanyController@updateNetworkSetting')->name('admin-companies.update');
            });

            Route::group([
                'prefix' => 'id/setting'
            ], function()
            {
                Route::get('', 'CompanyController@showIdSetting')->name('admin-companies.index');

                Route::put('', 'CompanyController@updateIdSetting')->name('admin-companies.update');
            });

            Route::group([
                'prefix' => 'map/setting'
            ], function()
            {
                Route::get('', 'CompanyController@showMapSetting')->name('admin-companies.index');

                Route::put('', 'CompanyController@updateMapSetting')->name('admin-companies.update');
            });


            Route::get('sms/credits', 'CompanyController@showSmsCredits')->name('admin-companies.index');

            Route::group([
                'prefix' => 'sms/credit'
            ], function()
            {   
                Route::post('', 'CompanyController@storeSmsCredit')->name('admin-companies.store');

                Route::put('{id}', 'CompanyController@updateSmsCredit')->name('admin-companies.update');

                Route::put('{id}', 'CompanyController@destroySmsCredit')->name('admin-companies.destroy');
            });

            Route::get('call/credits', 'CompanyController@showCallCredits')->name('admin-companies.index');

            Route::group([
                'prefix' => 'call/credit'
            ], function()
            {   
                Route::post('', 'CompanyController@storeCallCredit')->name('admin-companies.store');

                Route::put('{id}', 'CompanyController@updateCallCredit')->name('admin-companies.update');

                Route::put('{id}', 'CompanyController@destroyCallCredit')->name('admin-companies.destroy');
            });

            Route::get('summary/sms/credits', 'CompanyController@showSummaryOfSmsCredits')->name('admin-companies.index');

            Route::get('summary/call/credits', 'CompanyController@showSummaryOfCallCredits')->name('admin-companies.index');

        });
    });

?>