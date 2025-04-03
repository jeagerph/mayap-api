<?php

    Route::get('id-templates', 'IdTemplateController@index')->name('my-company-id-templates.index');

    Route::group([
        'prefix' => 'id-template',
    ], function()
    {
        Route::post('', 'IdTemplateController@store')->name('my-company-id-templates.store');

        Route::get('assignatory/options', 'IdTemplateController@assignatoryOptions')->name('my-company-id-templates.index');

        Route::group([
            'prefix' => '{id}'
        ], function()
        {
            Route::get('', 'IdTemplateController@show')->name('my-company-id-templates.index');

            Route::get('edit', 'IdTemplateController@edit')->name('my-company-id-templates.index');

            Route::put('', 'IdTemplateController@update')->name('my-company-id-templates.update');

            Route::put('status', 'IdTemplateController@updateStatus')->name('my-company-id-templates.update');

            Route::put('left-signature', 'IdTemplateController@updateLeftSignature')->name('my-company-id-templates.update');

            Route::put('right-signature', 'IdTemplateController@updateRightSignature')->name('my-company-id-templates.update');

            Route::delete('', 'IdTemplateController@destroy')->name('my-company-id-templates.destroy');
        });
    });

?>