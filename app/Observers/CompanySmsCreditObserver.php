<?php

namespace App\Observers;

use App\Http\Repositories\Base\ActivityRepository;

trait CompanySmsCreditObserver
{
    public static function boot()
    {
        parent::boot();

        self::deleting(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->company->activities()->save(
                $activityRepository->new([
                    'description' => ' deleted PHP ' . number_format($model->amount, 2) . ' credit amount.',
                    'action' => 3,
                    'data' => null
                ])
            );
        });

        self::created(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->company->activities()->save(
                $activityRepository->new([
                    'description' => ' added PHP ' . number_format($model->amount, 2) . ' credit amount.',
                    'action' => 1,
                    'data' => [
                        'credit_date' => $model->credit_date,
                        'amount' => $model->amount,
                        'mode' => $model->creditModeOptions[$model->credit_mode],
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
                    'description' => ' updated PHP ' . number_format($model->amount, 2) . ' credit amount.',
                    'action' => 2,
                    'data' => $model->getChanges()
                ])
            );
        });
    }
}
?>