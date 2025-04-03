<?php

namespace App\Observers;

use App\Http\Repositories\Base\ActivityRepository;

trait CompanySmsRecipientObserver
{
    public static function boot()
    {
        parent::boot();

        self::created(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->smsTransaction->activities()->save(
                $activityRepository->new([
                    'description' => ' sent an SMS to ' . $model->mobile_number  . '.',
                    'action' => 1,
                    'data' => [
                        'mobile_number' => $model->mobile_number,
                        'message' => $model->message,
                        'status' => $model->status ? 'SENT':'NOT SENT',
                        'status_code' => $model->status_code,
                        'created_at' => $model->created_at->toDateTimeString()
                    ]
                ])
            );
        });

        self::updated(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->smsTransaction->activities()->save(
                $activityRepository->new([
                    'description' => ' updated an SMS (' . $model->mobile_number . ').',
                    'action' => 2,
                    'data' => $model->getChanges()
                ])
            );
        });

        self::deleted(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->smsTransaction->activities()->save(
                $activityRepository->new([
                    'description' => ' deleted an SMS.',
                    'action' => 3,
                    'data' => null
                ])
            );
        });
    }
}
?>