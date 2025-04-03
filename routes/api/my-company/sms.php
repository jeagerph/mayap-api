<?php

    Route::post('sms/send', 'SmsController@sendSms')->name('company-sms.store');


    Route::get('sms-transactions', 'SmsController@showSmsTransactions')->name('company-sms.index');

    Route::group([
        'prefix' => 'sms-transaction',
    ], function()
    {
        Route::group([
            'prefix' => '{code}'
        ], function()
        {
            Route::get('', 'SmsController@showSmsTransaction')->name('company-sms.index');

            Route::put('message', 'SmsController@updateSmsTransactionMessage')->name('company-sms.update');

            Route::put('status/cancel', 'SmsController@cancelSmsTransaction')->name('company-sms.update');

            Route::delete('', 'SmsController@destroySmsTransaction')->name('company-sms.destroy');


            Route::get('recipients', 'SmsController@showSmsRecipients')->name('company-sms.index');

            Route::group([
                'prefix' => 'recipient/{recipientId}'
            ], function()
            {
                Route::put('', 'SmsController@updateSmsRecipient')->name('company-sms.update');

                Route::put('send', 'SmsController@sendSmsRecipient')->name('company-sms.update');

                Route::delete('', 'SmsController@destroySmsRecipient')->name('company-sms.destroy');
            });

        });
    });

?>