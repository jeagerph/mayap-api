<?php

namespace App\Observers;

use App\Http\Repositories\Base\ActivityRepository;

trait CompanyObserver
{
    public static function boot()
    {
        parent::boot();

        self::deleting(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->activities()->save(
                $activityRepository->new([
                    'description' => ' deleted ' . $model->name . '.',
                    'action' => 3,
                    'data' => null
                ])
            );

            $model->slug()->update([
                'full' => 'DELETED',
                'code' => 'DELETED',
                'name' => 'DELETED',
                'deleted_at' => now(),
                'updated_by' => 1
            ]);

            $model->update([
                'code' => 'DELETED',
                'name' => 'DELETED',
                'updated_by' => 1
            ]);

            $model->companyAccounts()->delete();

            $model->companySmsSetting()->delete();

            $model->companyInvoiceSetting()->delete();
        });

        self::created(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->activities()->save(
                $activityRepository->new([
                    'description' => ' created ' . $model->name . ' company.',
                    'action' => 1,
                    'data' => [
                        'name' => $model->name,
                        'address' => $model->address,
                        'contact_no' => $model->contact_no,
                        'created_at' => $model->created_at->toDateTimeString(),
                    ]
                ])
            );
        });

        self::updated(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->activities()->save(
                $activityRepository->new([
                    'description' => ' updated ' . $model->name . ' company.',
                    'action' => 2,
                    'data' => $model->getChanges()
                ])
            );
        });
    }
}
?>