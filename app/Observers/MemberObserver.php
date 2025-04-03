<?php

namespace App\Observers;

use App\Http\Repositories\Base\ActivityRepository;

trait MemberObserver
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

            // $model->relatives()->delete();

            // $model->relatedRelatives()->delete();
        });

        self::created(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->activities()->save(
                $activityRepository->new([
                    'description' => ' created ' . $model->fullName() . '.',
                    'action' => 1,
                    'data' => [
                        'date_registered' => $model->date_registered,
                        'province' => $model->province->name,
                        'city' => $model->city->name,
                        'barangay' => $model->barangay->name,
                        'house_no' => $model->house_no,

                        'first_name' => $model->first_name,
                        'middle_name' => $model->middle_name,
                        'last_name' => $model->last_name,
                        'gender' => $model->genders[$model->gender],
                        'contact_no' => $model->contact_no,
                        'email' => $model->email,
                        'address' => $model->address,
                        'place_of_birth' => $model->place_of_birth,
                        'date_of_birth' => $model->date_of_birth,
                        'civil_status' => $model->civil_status
                            ? $model->civilStatuses[$model->civil_status]
                            : null,
                        'citizenship' => $model->citizenship,
                        'religion' => $model->religion,
                        'eligibility' => $model->eligibility,
                        'blood_type' => $model->blood_type,
                        'health_history' => $model->health_history,
                        'skills' => $model->skills,
                        'pending' => $model->pending,
                        'emergency_contact_name' => $model->emergency_contact_name,
                        'emergency_contact_address' => $model->emergency_contact_address,
                        'emergency_contact_no' => $model->emergency_contact_no,
                        'precinct_no' => $model->precinct_no ?: 'NOT INDICATED',
                        'is_household' => $model->is_household ? 'YES' : 'NO',
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