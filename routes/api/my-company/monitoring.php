<?php

    Route::get('monitoring/barangays', 'MonitoringController@showBarangays')->name('company-profile.index');

    Route::get('monitoring/barangay/residents', 'MonitoringController@showBarangayResidents')->name('company-profile.index');

    Route::get('monitoring/barangay/resident', 'MonitoringController@showBarangayResident')->name('company-profile.index');

?>