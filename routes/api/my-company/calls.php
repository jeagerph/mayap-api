<?php

    Route::get('call/setting', 'CallController@showSetting')->name('company-call.index');

    Route::get('call/token/generate', 'CallController@generateToken')->name('company-call.index');


    Route::get('call/transactions', 'CallController@showCallTransactions')->name('company-call.index');

    Route::post('call/transaction', 'CallController@storeCallTransaction')->name('company-call.store');

    Route::put('call/transaction/{code}', 'CallController@updateCallTransaction')->name('company-call.store');

    Route::get('call/transaction/{code}', 'CallController@showCallTransaction')->name('company-call.index');

    Route::get('call/transaction/{code}/recording', 'CallController@showCallTransactionRecording')->name('company-call.index');

?>