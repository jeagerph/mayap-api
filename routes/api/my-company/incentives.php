<?php

    Route::get('incentives', 'IncentiveController@index')->name('my-company-incentives.index');

    Route::group([
        'prefix' => 'incentive'
    ], function()
    {
        Route::put('{id}', 'IncentiveController@update')->name('my-company-incentives.update');

        Route::delete('{id}', 'IncentiveController@destroy')->name('my-company-incentives.destroy');
    });

?>