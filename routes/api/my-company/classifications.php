<?php

    Route::get('classifications', 'ClassificationController@showClassifications')->name('my-company-classifications.index');

    Route::group([
        'prefix' => 'classification'
    ], function()
    {
        Route::post('', 'ClassificationController@storeClassification')->name('my-company-classifications.store');

        Route::put('{id}', 'ClassificationController@updateClassification')->name('my-company-classifications.update');

        Route::put('{id}/status', 'ClassificationController@updateClassificationStatus')->name('my-company-classifications.update');

        Route::delete('{id}', 'ClassificationController@destroyClassification')->name('my-company-classifications.destroy');
    });

?>