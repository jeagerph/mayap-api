<?php

    Route::get('officer-classifications', 'OfficerClassificationController@index')->name('my-company-officer-classifications.index');

    Route::group([
        'prefix' => 'officer-classification'
    ], function()
    {
        Route::post('', 'OfficerClassificationController@store')->name('my-company-officer-classifications.store');

        Route::put('{id}', 'OfficerClassificationController@update')->name('my-company-officer-classifications.update');

        Route::put('{id}/status', 'OfficerClassificationController@updateStatus')->name('my-company-officer-classifications.update');

        Route::delete('{id}', 'OfficerClassificationController@destroy')->name('my-company-officer-classifications.destroy');
    });

?>