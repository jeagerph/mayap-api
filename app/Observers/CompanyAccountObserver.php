<?php

namespace App\Observers;

use App\Http\Repositories\Base\ActivityRepository;

trait CompanyAccountObserver
{
    public static function boot()
    {
        parent::boot();

        self::deleting(function($model)
        {
            $model->account->slug()->update([
                'full' => 'DELETED',
                'code' => 'DELETED',
                'name' => 'DELETED',
                'deleted_at' => now(),
                'updated_by' => 1
            ]);

            $model->account()->update([
                'full_name' => 'DELETED',
                'deleted_at' => now(),
                'updated_by' => 1
            ]);

            $model->account->user()->update([
                'username' => 'DELETED',
                'code' => 'DELETED',
                'updated_by' => 1,
                'deleted_at' => now(),
                'deleted_by' => 1,
            ]);

            $model->account->permissions()->delete();
        });

        self::created(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->company->activities()->save(
                $activityRepository->new([
                    'description' => ' added new account.',
                    'action' => 1,
                    'data' => [
                        'full_name' => $model->account->full_name,
                        'company_position' => $model->companyPosition->name,
                        'created_at' => $model->created_at->toDateTimeString()
                    ]
                ])
            );
        });

        self::updated(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->company->activities()->save(
                $activityRepository->new([
                    'description' => ' updated account.',
                    'action' => 2,
                    'data' => $model->getChanges()
                ])
            );
        });

        self::deleted(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->company->activities()->save(
                $activityRepository->new([
                    'description' => ' deleted account.',
                    'action' => 3,
                    'data' => null
                ])
            );
        });
    }
}
?>