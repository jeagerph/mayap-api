<?php

Route::get('profile', 'DashboardController@showProfile')->name('company-profile.index');

Route::put('profile', 'DashboardController@updateProfile')->name('company-profile.update');

Route::put('logo', 'DashboardController@updateLogo')->name('company-profile.update');

Route::put('sub-logo', 'DashboardController@updateSubLogo')->name('company-profile.update');

Route::get('summary/total', 'DashboardController@summaryTotal')->name('company-profile.index');

Route::get('summary/beneficiaries/total/view', 'DashboardController@viewSummaryOfBeneficiariesTotal')->name('company-profile.index');

Route::get('summary/verified-voters/total/view', 'DashboardController@viewSummaryOfVerifiedVotersTotal')->name('company-profile.index');

Route::get('summary/cross-matched-voters/total/view', 'DashboardController@viewSummaryOfCrossMatchedVotersTotal')->name('company-profile.index');

Route::get('summary/issued-sdn-ids/total/view', 'DashboardController@viewIssuedSdnIdsTotal')->name('company-profile.index');

Route::get('summary/officers/total/view', 'DashboardController@viewSummaryOfOfficersTotal')->name('company-profile.index');

Route::get('summary/voter-types/total/view', 'DashboardController@viewSummaryOfVoterTypesTotal')->name('company-profile.index');

Route::get('summary/networks/total/view', 'DashboardController@viewSummaryOfNetworksTotal')->name('company-profile.index');

Route::get('summary/officers-networks/view', 'DashboardController@viewSummaryOfOfficerNetworksList')->name('company-profile.index');

Route::get('summary/assisted-over-assistances/total', 'DashboardController@summaryOfAssistedOverAssistancesTotal')->name('company-profile.index');

Route::get('summary/beneficiaries/monthly', 'DashboardController@summaryOfBeneficiariesPerMonthTotal')->name('company-profile.index');
Route::get('summary/beneficiaries/weekly', 'DashboardController@summaryOfBeneficiariesPerWeekTotal')->name('company-profile.index');


Route::get('summary/assistances/monthly', 'DashboardController@summaryOfAssistancesPerMonthTotal')->name('company-profile.index');
Route::get('summary/assistances/weekly', 'DashboardController@summaryOfAssistancesPerWeekTotal')->name('company-profile.index');

Route::get('summary/patients/monthly', 'DashboardController@summaryOfPatientsPerMonthTotal')->name('company-profile.index');
Route::get('summary/patients/weekly', 'DashboardController@summaryOfPatientsPerWeekTotal')->name('company-profile.index');

Route::get('summary/networks/monthly', 'DashboardController@summaryOfNetworksPerMonthTotal')->name('company-profile.index');
Route::get('summary/networks/weekly', 'DashboardController@summaryOfNetworksPerWeekTotal')->name('company-profile.index');

Route::get('summary/progress/monthly', 'DashboardController@summaryOfMonthlyProgress')->name('company-profile.index');
Route::get('summary/progress/weekly', 'DashboardController@summaryOfWeeklyProgress')->name('company-profile.index');
Route::get('summary/progress/weekly/range', 'DashboardController@summaryOfWeeklyRangeProgress')->name('company-profile.index');

Route::get('summary/assistances/type/progress/monthly', 'DashboardController@summaryOfAssistancesByTypeMonthlyProgress')->name('company-profile.index');
Route::get('summary/assistances/type/progress/weekly', 'DashboardController@summaryOfAssistancesByTypeWeeklyProgress')->name('company-profile.index');
Route::get('summary/assistances/type/progress/weekly/range', 'DashboardController@summaryOfAssistancesByTypeWeeklyRangeProgress')->name('company-profile.index');

Route::get('summary/beneficiaries/networks/top', 'DashboardController@summaryOfTopBeneficiaryNetworks')->name('company-profile.index');

Route::get('summary/report/download', 'DashboardController@downloadSummaryReport')->name('company-profile.index');

Route::get('summary/officers-networks/report/download', 'DashboardController@downloadOfficersNetworksReport')->name('company-profile.index');
Route::get('summary/weekly-networks/report/download', 'DashboardController@downloadWeeklyNetworksReport')->name('company-profile.index');

Route::group([
    'prefix' => 'dashboard',
], function () {

    Route::get('provinces', 'DashboardController@provincesOptions')->name('company-profile.index');

    Route::get('cities', 'DashboardController@citiesOptions')->name('company-profile.index');

    Route::get('barangays', 'DashboardController@barangaysOptions')->name('company-profile.index');


    // PATIENTS

    Route::get('patients/list', 'DashboardController@patientsList')->name('company-profile.index');

    Route::put('patient/{patientId}', 'DashboardController@updatePatient')->name('company-profile.update');

    Route::put('patient/{patientId}/approve', 'DashboardController@approvePatient')->name('company-profile.update');

    Route::put('patient/{patientId}/in-progress', 'DashboardController@inProgressPatient')->name('company-profile.update');

    Route::put('patient/{patientId}/complete', 'DashboardController@completePatient')->name('company-profile.update');

    Route::put('patient/{patientId}/cancel', 'DashboardController@cancelPatient')->name('company-profile.update');

    Route::delete('patient/{patientId}', 'DashboardController@destroyPatient')->name('company-profile.destroy');

    // ASSISTANCES

    Route::get('assistances/list', 'DashboardController@assistancesList')->name('company-beneficiaries.index');

    Route::put('assistance/{assistanceId}', 'DashboardController@updateAssistance')->name('company-beneficiaries.store');

    Route::delete('assistance/{assistanceId}', 'DashboardController@destroyAssistance')->name('company-beneficiaries.destroy');

    Route::post('/ask-openai', 'OpenAIController@askOpenAI')->name('company-profile.index');

});
