<?php

    Route::get('assignatories', 'AssignatoryController@showAssignatories')->name('my-company-assignatories.index');

    Route::put('assignatories/arrange', 'AssignatoryController@arrangeAssignatories')->name('my-company-assignatories.update');

    Route::group([
        'prefix' => 'assignatory'
    ], function()
    {
        Route::post('', 'AssignatoryController@storeAssignatory')->name('my-company-assignatories.store');

        Route::put('{id}', 'AssignatoryController@updateAssignatory')->name('my-company-assignatories.update');

        Route::put('{id}/signature', 'AssignatoryController@updateAssignatorySignature')->name('my-company-assignatories.update');

        Route::put('{id}/status', 'AssignatoryController@updateAssignatoryStatus')->name('my-company-assignatories.update');

        Route::delete('{id}', 'AssignatoryController@destroyAssignatory')->name('my-company-assignatories.destroy');
    });

?>