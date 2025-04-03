<?php

    Route::get('questionnaires', 'QuestionnaireController@showQuestionnaires')->name('my-company-questionnaires.index');

    Route::group([
        'prefix' => 'questionnaire'
    ], function()
    {
        Route::post('', 'QuestionnaireController@storeQuestionnaire')->name('my-company-questionnaires.store');

        Route::put('{id}', 'QuestionnaireController@updateQuestionnaire')->name('my-company-questionnaires.update');

        Route::put('{id}/status', 'QuestionnaireController@updateQuestionnaireStatus')->name('my-company-questionnaires.update');

        Route::delete('{id}', 'QuestionnaireController@destroyQuestionnaire')->name('my-company-questionnaires.destroy');
    });

?>