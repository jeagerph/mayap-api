<?php

namespace App\Observers;

use App\Http\Repositories\Base\ActivityRepository;

trait BeneficiaryObserver
{
    public static function boot()
    {
        parent::boot();

        self::deleting(function($model)
        {
            $model->slug()->update([
                'full' => 'DELETED',
                'code' => 'DELETED',
                'name' => 'DELETED',
                'deleted_at' => now(),
                'updated_by' => 1
            ]);

            $model->update([
                'code' => 'DELETED',
                'first_name' => 'DELETED',
                'last_name' => 'DELETED',
                'updated_by' => 1
            ]);
        });

        self::created(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->activities()->save(
                $activityRepository->new([
                    'description' => ' created ' . $model->fullName() . '.',
                    'action' => 1,
                    'data' => [
                        'code' => $model->code,
                        'date_registered' => $model->date_registered,

                        'province' => $model->province->name,
                        'city' => $model->city->name,
                        'barangay' => $model->barangay->name,
                        'house_no' => $model->house_no,

                        'first_name' => $model->first_name,
                        'middle_name' => $model->middle_name,
                        'last_name' => $model->last_name,
                        'gender' => $model->genderOptions[$model->gender],
                        'mobile_no' => $model->mobile_no,
                        'email' => $model->email,
                        'place_of_birth' => $model->place_of_birth,
                        'date_of_birth' => $model->date_of_birth,
                        // 'civil_status' => $model->civil_status
                        //     ? $model->civilStatusOptions[$model->civil_status]
                        //     : null,
                        'citizenship' => $model->citizenship,
                        'religion' => $model->religion,

                        'educational_attainment' => $model->educational_attainment,
                        'occupation' => $model->occupation,
                        'monthly_income' => $model->monthly_income,
                        'classification' => $model->classification,
                        'is_household' => $model->is_household ? 'YES' : 'NO',
                        
                        'emergency_contact_name' => $model->emergency_contact_name,
                        'emergency_contact_address' => $model->emergency_contact_address,
                        'emergency_contact_no' => $model->emergency_contact_no,

                        'created_at' => $model->created_at->toDateTimeString()
                    ]
                ])
            );
        });

        self::updated(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->activities()->save(
                $activityRepository->new([
                    'description' => ' updated ' . $model->fullName() . '.',
                    'action' => 2,
                    'data' => $model->getChanges()
                ])
            );
        });

        self::deleted(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->activities()->save(
                $activityRepository->new([
                    'description' => ' deleted ' . $model->fullName() . '.',
                    'action' => 3,
                    'data' => null
                ])
            );
        });
    }
}
?>