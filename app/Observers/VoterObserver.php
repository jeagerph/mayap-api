<?php

namespace App\Observers;

use App\Http\Repositories\Base\ActivityRepository;

trait VoterObserver
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
                        'date_registered' => $model->date_registered,

                        'first_name' => $model->first_name,
                        'middle_name' => $model->middle_name,
                        'last_name' => $model->last_name,
                        'gender' => $model->genderOptions[$model->gender],
                        'date_of_birth' => $model->date_of_birth,
                        'precinct_no' => $model->precinct_no,
                        'application_no' => $model->application_no,
                        'application_date' => $model->application_date,
                        'application_type' => $model->application_type,

                        'province' => $model->province_id
                            ? $model->province->name
                            : 'N/a',
                        'city' => $model->city_id
                            ? $model->city->name
                            : 'N/a',
                        'barangay' => $model->barangay_id
                            ? $model->barangay->name
                            : 'N/a',

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

        self::deleting(function($model)
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