<?php

    Route::get('network/setting', 'SettingController@showNetworkSetting')->name('company-profile.index');

    Route::put('network/setting', 'SettingController@updateNetworkSetting')->name('company-profile.index');

?>