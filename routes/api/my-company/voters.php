<?php

Route::get('voters', 'VoterController@index')->name('company-voters.index');
Route::post('voters/import', 'VoterController@import')->name('company-voters.store');

Route::group([
    'prefix' => 'voter',
], function () {
    Route::post('check', 'VoterController@check')->name('company-voters.store');

    Route::post('', 'VoterController@store')->name('company-voters.store');

    Route::get('provinces', 'VoterController@provincesOptions')->name('company-voters.index');

    Route::get('cities', 'VoterController@citiesOptions')->name('company-voters.index');

    Route::get('barangays', 'VoterController@barangaysOptions')->name('company-voters.index');


    Route::group([
        'prefix' => '{code}'
    ], function () {
        Route::get('', 'VoterController@show')->name('company-voters.index');

        Route::get('edit', 'VoterController@edit')->name('company-voters.index');

        Route::get('profile', 'VoterController@showProfile')->name('company-voters.index');

        Route::put('', 'VoterController@update')->name('company-voters.update');

        Route::delete('', 'VoterController@destroy')->name('company-voters.destroy');

        Route::get('activities', 'VoterController@showActivities')->name('company-voters.index');

        Route::put('photo', 'VoterController@updatePhoto')->name('company-voters.updatePhoto');
    });
});

?>