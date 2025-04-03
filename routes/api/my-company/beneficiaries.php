<?php

    Route::get('beneficiaries', 'BeneficiaryController@index')->name('company-beneficiaries.index');

    Route::get('beneficiaries/locations/list', 'BeneficiaryController@showBeneficiariesLocationsList')->name('company-beneficiaries.index');

    Route::get('beneficiaries/barangays/summary', 'BeneficiaryController@showBeneficiariesSummaryByBarangay')->name('company-beneficiaries.index');

    Route::get('beneficiaries/voter-type/list', 'BeneficiaryController@showBeneficiariesVoterTypeList')->name('company-beneficiaries.index');

    Route::post('beneficiaries/import', 'BeneficiaryController@import')->name('company-beneficiaries.store');

    Route::get('beneficiaries/download', 'BeneficiaryController@downloadReport')->name('company-beneficiaries.index');

    Route::get('beneficiaries/by-barangay/download', 'BeneficiaryController@downloadByBarangayReport')->name('company-beneficiaries.index');

    Route::get('beneficiaries/by-purok/download', 'BeneficiaryController@downloadByPurokReport')->name('company-beneficiaries.index');

    Route::get('beneficiaries/household/by-barangay/download', 'BeneficiaryController@downloadHouseholdByBarangayReport')->name('company-beneficiaries.index');

    Route::get('beneficiaries/household/by-purok/download', 'BeneficiaryController@downloadHouseholdByPurokReport')->name('company-beneficiaries.index');

    Route::get('beneficiaries/print/download', 'BeneficiaryController@downloadPrintReport')->name('company-beneficiaries.index');

    Route::group([
        'prefix' => 'beneficiary',
    ], function()
    {
        Route::post('check', 'BeneficiaryController@check')->name('company-beneficiaries.store');

        Route::post('', 'BeneficiaryController@store')->name('company-beneficiaries.store');

        Route::get('provinces', 'BeneficiaryController@provincesOptions')->name('company-beneficiaries.index');

        Route::get('cities', 'BeneficiaryController@citiesOptions')->name('company-beneficiaries.index');
        
        Route::get('barangays', 'BeneficiaryController@barangaysOptions')->name('company-beneficiaries.index');

        Route::get('classification/options', 'BeneficiaryController@classificationOptions')->name('company-beneficiaries.index');

        Route::get('officer-classification/options', 'BeneficiaryController@officerClassificationOptions')->name('company-beneficiaries.index');

        Route::get('questionnaire/options', 'BeneficiaryController@questionnaireOptions')->name('company-beneficiaries.index');

        Route::get('report/field/options', 'BeneficiaryController@reportFieldOptions')->name('company-beneficiaries.index');

        Route::get('relationship/options', 'BeneficiaryController@relationshipOptions')->name('company-beneficiaries.index');

        Route::get('options', 'BeneficiaryController@beneficiaryOptions')->name('company-beneficiaries.index');

        Route::get('network/options', 'BeneficiaryController@beneficiaryNetworkOptions')->name('company-beneficiaries.index');

        Route::get('id-template/options', 'BeneficiaryController@idTemplateOptions')->name('company-beneficiaries.index');

        Route::get('document-template/options', 'BeneficiaryController@documentTemplateOptions')->name('company-beneficiaries.index');

        Route::post('option', 'BeneficiaryController@storeBeneficiaryOption')->name('company-beneficiaries.store');

        Route::get('relationships', 'BeneficiaryController@relationshipsOptions')->name('company-beneficiaries.relationships');


        Route::group([
            'prefix' => '{code}'
        ], function()
        {
            Route::get('', 'BeneficiaryController@show')->name('company-beneficiaries.index');

            Route::get('edit', 'BeneficiaryController@edit')->name('company-beneficiaries.index');

            Route::put('', 'BeneficiaryController@update')->name('company-beneficiaries.update');
            
            Route::put('photo', 'BeneficiaryController@updatePhoto')->name('company-beneficiaries.update');

            Route::put('officer', 'BeneficiaryController@updateOfficer')->name('company-beneficiaries.update');

            Route::get('voters/check', 'BeneficiaryController@checkVoters')->name('company-beneficiaries.index');

            Route::put('voter', 'BeneficiaryController@updateVoter')->name('company-beneficiaries.update');

            Route::get('profile', 'BeneficiaryController@showProfile')->name('company-beneficiaries.index');

            Route::get('mobile-no', 'BeneficiaryController@showMobileNo')->name('company-beneficiaries.index');

            Route::delete('', 'BeneficiaryController@destroy')->name('company-beneficiaries.destroy');

            Route::get('activities', 'BeneficiaryController@showActivities')->name('company-beneficiaries.index');


            // RELATIVES

            Route::get('barangay/beneficiaries', 'BeneficiaryController@beneficiaryOptions')->name('company-beneficiaries.index');

            Route::get('relatives', 'BeneficiaryController@showRelatives')->name('company-beneficiaries.index');

            Route::put('relatives/arrange', 'BeneficiaryController@arrangeRelatives')->name('company-beneficiaries.index');

            Route::post('relative', 'BeneficiaryController@storeRelative')->name('company-beneficiaries.store');

            Route::delete('relative/{id}', 'BeneficiaryController@destroyRelative')->name('company-beneficiaries.destroy');


            // FAMILIES

            Route::get('families', 'BeneficiaryController@showFamilies')->name('company-beneficiaries.index');

            Route::put('families/arrange', 'BeneficiaryController@arrangeFamilies')->name('company-beneficiaries.index');

            Route::post('family', 'BeneficiaryController@storeFamily')->name('company-beneficiaries.store');

            Route::put('family/{id}', 'BeneficiaryController@updateFamily')->name('company-beneficiaries.update');

            Route::delete('family/{id}', 'BeneficiaryController@destroyFamily')->name('company-beneficiaries.destroy');


            // NETWORKS

            Route::get('network/list', 'BeneficiaryController@showNetworkByList')->name('company-beneficiaries.index');

            Route::get('network/chart', 'BeneficiaryController@showNetworkByChart')->name('company-beneficiaries.index');

            Route::post('network', 'BeneficiaryController@storeNetwork')->name('company-beneficiaries.store');

            Route::delete('network/{networkId}', 'BeneficiaryController@destroyNetwork')->name('company-beneficiaries.destroy');

            Route::get('download/network/list', 'BeneficiaryController@downloadNetworkByList')->name('company-beneficiaries.index');
         
            // INCENTIVES

            Route::get('incentives', 'BeneficiaryController@showIncentives')->name('company-beneficiaries.index');

            Route::post('incentive', 'BeneficiaryController@storeIncentive')->name('company-beneficiaries.store');

            Route::delete('incentive/{id}', 'BeneficiaryController@destroyIncentive')->name('company-beneficiaries.destroy');

            
            // ASSISTANCES

            Route::get('assistances', 'BeneficiaryController@showAssistances')->name('company-beneficiaries.index');

            Route::post('assistance', 'BeneficiaryController@storeAssistance')->name('company-beneficiaries.store');

            Route::put('assistance/{assistanceId}', 'BeneficiaryController@updateAssistance')->name('company-beneficiaries.store');

            Route::delete('assistance/{assistanceId}', 'BeneficiaryController@destroyAssistance')->name('company-beneficiaries.destroy');
            
            
            // PATIENTS

            Route::get('patients', 'BeneficiaryController@showPatients')->name('company-beneficiaries.index');

            Route::post('patient', 'BeneficiaryController@storePatient')->name('company-beneficiaries.store');

            Route::put('patient/{patientId}', 'BeneficiaryController@updatePatient')->name('company-beneficiaries.update');

            Route::put('patient/{patientId}/approve', 'BeneficiaryController@approvePatient')->name('company-beneficiaries.update');

            Route::put('patient/{patientId}/in-progress', 'BeneficiaryController@inProgressPatient')->name('company-beneficiaries.update');

            Route::put('patient/{patientId}/complete', 'BeneficiaryController@completePatient')->name('company-beneficiaries.update');

            Route::put('patient/{patientId}/cancel', 'BeneficiaryController@cancelPatient')->name('company-beneficiaries.update');

            Route::delete('patient/{patientId}', 'BeneficiaryController@destroyPatient')->name('company-beneficiaries.destroy');
            

            // MESSAGES

            Route::get('messages', 'BeneficiaryController@showMessages')->name('company-beneficiaries.index');

            Route::post('message', 'BeneficiaryController@storeMessage')->name('company-beneficiaries.store');


            // CALLS

            Route::get('calls', 'BeneficiaryController@showCalls')->name('company-beneficiaries.index');

            Route::post('call', 'BeneficiaryController@storeCall')->name('company-beneficiaries.store');

            Route::put('call/{callId}', 'BeneficiaryController@updateCall')->name('company-beneficiaries.update');


            // IDENTIFICATIONS

            Route::get('identifications', 'BeneficiaryController@showIdentifications')->name('company-beneficiaries.index');

            Route::post('identification', 'BeneficiaryController@storeIdentification')->name('company-beneficiaries.store');

            Route::get('identification/{identificationId}/download', 'BeneficiaryController@downloadIdentification')->name('company-beneficiaries.index');

            Route::delete('identification/{identificationId}', 'BeneficiaryController@destroyIdentification')->name('company-beneficiaries.destroy');

            // DOCUMENTS

            Route::get('documents', 'BeneficiaryController@showDocuments')->name('company-beneficiaries.index');

            Route::post('document', 'BeneficiaryController@storeDocument')->name('company-beneficiaries.store');

            Route::get('document/{documentId}/download', 'BeneficiaryController@downloadDocument')->name('company-beneficiaries.index');

            Route::delete('document/{documentId}', 'BeneficiaryController@destroyDocument')->name('company-beneficiaries.destroy');
        });
    });

?>