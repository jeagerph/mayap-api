<?php

    Route::get('members', 'MemberController@index')->name('company-members.index');

    Route::post('members/import', 'MemberController@import')->name('company-members.store');

    Route::group([
        'prefix' => 'member',
    ], function()
    {
        Route::post('check', 'MemberController@check')->name('company-members.store');

        Route::post('', 'MemberController@store')->name('company-members.store');

        Route::get('provinces', 'MemberController@provincesOptions')->name('company-members.index');

        Route::get('cities', 'MemberController@citiesOptions')->name('company-members.index');
        
        Route::get('barangays', 'MemberController@barangaysOptions')->name('company-members.index');

        Route::get('classification/options', 'MemberController@classificationOptions')->name('company-members.index');

        Route::get('report/field/options', 'MemberController@reportFieldOptions')->name('company-members.index');

        Route::get('relationships', 'MemberController@relationshipsOptions')->name('company-members.index');

        Route::get('barangay/profiles/filters', 'MemberController@barangayProfilesFilters')->name('company-members.index');

        Route::group([
            'prefix' => '{code}'
        ], function()
        {
            Route::get('', 'MemberController@show')->name('company-members.index');

            Route::get('edit', 'MemberController@edit')->name('company-members.index');

            Route::put('', 'MemberController@update')->name('company-members.update');
            
            Route::put('photo', 'MemberController@updatePhoto')->name('company-members.update');

            Route::put('thumbmark/left', 'MemberController@updateLeftThumbmark')->name('company-members.update');

            Route::put('thumbmark/right', 'MemberController@updateRightThumbmark')->name('company-members.update');

            Route::get('profile', 'MemberController@showProfile')->name('company-members.index');

            Route::get('contact', 'MemberController@showContact')->name('company-members.index');

            Route::delete('', 'MemberController@destroy')->name('company-members.destroy');

            Route::get('identification/download', 'MemberController@customDownloadIdentification')->name('company-members.index');

            // Route::get('documents', 'MemberController@showDocuments')->name('company-members.index');

            // Route::get('identification/store', 'MemberController@storeIdentification')->name('company-members.index');
            
            // Route::get('identification/{idCode}/download', 'MemberController@downloadIdentification')->name('company-members.index');

            // Route::get('relatives', 'MemberController@showRelatives')->name('company-members.index');

            // Route::put('relatives/arrange', 'MemberController@arrangeRelatives')->name('company-members.index');

            // Route::post('relative', 'MemberController@storeRelative')->name('company-members.store');

            // Route::delete('relative/{id}', 'MemberController@destroyRelative')->name('company-members.destroy');

            // Route::get('attachments', 'MemberController@showAttachments')->name('company-members.index');

            // Route::group([
            //     'prefix' => 'attachment'
            // ], function()
            // {
            //     Route::post('', 'MemberController@storeAttachment')->name('company-members.store');

            //     Route::put('{id}', 'MemberController@updateAttachment')->name('company-members.update');

            //     Route::delete('{id}', 'MemberController@destroyAttachment')->name('company-members.update');
            // });

            Route::get('activities', 'MemberController@showActivities')->name('company-members.index');
        });
    });

?>