<?php

    Route::get('document-templates', 'DocumentTemplateController@index')->name('my-company-document-templates.index');

    Route::group([
        'prefix' => 'document-template',
    ], function()
    {
        Route::post('', 'DocumentTemplateController@store')->name('my-company-document-templates.store');

        Route::get('assignatory/options', 'DocumentTemplateController@assignatoryOptions')->name('my-company-document-templates.index');

        Route::group([
            'prefix' => '{id}'
        ], function()
        {
            Route::get('', 'DocumentTemplateController@show')->name('my-company-document-templates.index');

            Route::get('edit', 'DocumentTemplateController@edit')->name('my-company-document-templates.index');

            Route::put('', 'DocumentTemplateController@update')->name('my-company-document-templates.update');

            Route::put('status', 'DocumentTemplateController@updateStatus')->name('my-company-document-templates.update');

            Route::put('border', 'DocumentTemplateController@updateBorder')->name('my-company-document-templates.update');

            Route::put('left-signature', 'DocumentTemplateController@updateLeftSignature')->name('my-company-document-templates.update');

            Route::put('right-signature', 'DocumentTemplateController@updateRightSignature')->name('my-company-document-templates.update');

            Route::delete('', 'DocumentTemplateController@destroy')->name('my-company-document-templates.destroy');
        });
    });

?>