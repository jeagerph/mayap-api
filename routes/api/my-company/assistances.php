<?php

    Route::get('assistances', 'AssistanceController@index')->name('company-assistances.index');
    Route::get('live-search', 'AssistanceController@liveSearch')->name('company-assistances.index');

    Route::get('assistances/locations/list', 'AssistanceController@showAssistancesLocationsList')->name('company-assistances.index');

    Route::get('assistances/barangays/list', 'AssistanceController@showAssistancesByBarangayList')->name('company-assistances.index');

    Route::get('assistances/download', 'AssistanceController@downloadReport')->name('company-assistances.index');

    Route::get('assistances/by-barangay/download', 'AssistanceController@downloadByBarangayReport')->name('company-assistances.index');

    Route::get('assistances/by-purok/download', 'AssistanceController@downloadByPurokReport')->name('company-assistances.index');

    Route::get('assistances/by-from/download', 'AssistanceController@downloadByFromReport')->name('company-assistances.index');

    Route::group([
        'prefix' => 'assistance'
    ], function()
    {
        Route::get('beneficiary/options', 'AssistanceController@beneficiaryOptions')->name('company-assistances.index');

        Route::get('provinces', 'AssistanceController@provincesOptions')->name('company-assistances.index');

        Route::get('cities', 'AssistanceController@citiesOptions')->name('company-assistances.index');
        
        Route::get('barangays', 'AssistanceController@barangaysOptions')->name('company-assistances.index');

        Route::post('beneficiary/option', 'AssistanceController@storeBeneficiaryOption')->name('company-assistances.store');

        Route::post('', 'AssistanceController@store')->name('company-assistances.store');

        Route::get('{id}/assistances/other', 'AssistanceController@showOtherAssistances')->name('company-assistances.index');

        Route::put('{id}', 'AssistanceController@update')->name('company-assistances.update');

        Route::delete('{id}', 'AssistanceController@destroy')->name('company-assistances.destroy');
    });

?>