<?php

namespace App\Observers;

use App\Http\Repositories\Base\ActivityRepository;

trait CompanyCallTransactionObserver
{
    public static function boot()
    {
        parent::boot();

        self::deleting(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->company->activities()->save(
                $activityRepository->new([
                    'description' => ' deleted ' . $model->code . ' call transaction.',
                    'action' => 3,
                    'data' => null
                ])
            );
        });

        self::created(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->activities()->save(
                $activityRepository->new([
                    'description' => ' created ' . $model->code  . '.',
                    'action' => 1,
                    'data' => [
                        'transaction_date' => $model->transaction_date,
                        'amount' => $model->amount,
                        'recording_url' => $model->recording_url,
                        'mobile_number' => $model->mobile_number,
                        'created_at' => $model->created_at->toDateTimeString()
                    ]
                ])
            );

            $model->company->activities()->save(
                $activityRepository->new([
                    'description' => ' added call transaction (' . $model->code . ').',
                    'action' => 1,
                    'data' => [
                        'transaction_date' => $model->transaction_date,
                        'amount' => $model->amount,
                        'recording_url' => $model->recording_url,
                        'mobile_number' => $model->mobile_number,
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
                    'description' => ' updated a calltransaction (' . $model->code . ').',
                    'action' => 2,
                    'data' => $model->getChanges()
                ])
            );
        });
    }
}
?>