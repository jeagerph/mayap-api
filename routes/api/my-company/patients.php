<?php

    Route::get('patients', 'PatientController@index')->name('company-patients.index');

    Route::get('patients/download', 'PatientController@downloadReport')->name('company-patients.index');

    Route::get('patients/by-barangay/download', 'PatientController@downloadByBarangayReport')->name('company-patients.index');

    Route::get('patients/by-purok/download', 'PatientController@downloadByPurokReport')->name('company-patients.index');

    Route::group([
        'prefix' => 'patient'
    ], function()
    {
        Route::get('beneficiary/options', 'PatientController@beneficiaryOptions')->name('company-patients.index');
        
        Route::get('provinces', 'PatientController@provincesOptions')->name('company-patients.index');

        Route::get('cities', 'PatientController@citiesOptions')->name('company-patients.index');
        
        Route::get('barangays', 'PatientController@barangaysOptions')->name('company-patients.index');

        Route::post('beneficiary/option', 'PatientController@storeBeneficiaryOption')->name('company-patients.store');


        Route::post('', 'PatientController@store')->name('company-patients.store');

        Route::put('{id}', 'PatientController@update')->name('company-patients.update');

        Route::put('{id}/approve', 'PatientController@approve')->name('company-patients.update');

        Route::put('{id}/in-progress', 'PatientController@inProgress')->name('company-patients.update');

        Route::put('{id}/complete', 'PatientController@complete')->name('company-patients.update');

        Route::put('{id}/cancel', 'PatientController@cancel')->name('company-patients.update');

        Route::delete('{id}', 'PatientController@destroy')->name('company-patients.destroy');
    });

?>