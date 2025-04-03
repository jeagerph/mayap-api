<?php

namespace App\Observers;

use App\Http\Repositories\Base\ActivityRepository;

trait CompanySmsTransactionObserver
{
    public static function boot()
    {
        parent::boot();

        self::deleting(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->company->activities()->save(
                $activityRepository->new([
                    'description' => ' deleted ' . $model->code . ' SMS transaction.',
                    'action' => 3,
                    'data' => null
                ])
            );

            $model->smsRecipients()->delete();
        });

        self::created(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->activities()->save(
                $activityRepository->new([
                    'description' => ' created ' . $model->code  . '.',
                    'action' => 1,
                    'data' => [
                        'message' => $model->message,
                        'sms_type' => $model->smsTypes[$model->sms_type],
                        'transaction_date' => $model->transaction_date,
                        'transaction_type' => $model->transactionTypes[$model->transaction_type],
                        'credit_per_sent' => $model->credit_per_sent,
                        'scheduled_date' => $model->scheduled_date,
                        'scheduled_time' => $model->scheduled_time,
                        'status' => $model->statuses[$model->status],
                        'created_at' => $model->created_at->toDateTimeString()
                    ]
                ])
            );

            $model->company->activities()->save(
                $activityRepository->new([
                    'description' => ' added an SMS transaction (' . $model->code . ').',
                    'action' => 1,
                    'data' => [
                        'sms_type' => $model->smsTypes[$model->sms_type],
                        'transaction_date' => $model->transaction_date,
                        'transaction_type' => $model->transactionTypes[$model->transaction_type],
                        'credit_per_sent' => $model->credit_per_sent,
                        'scheduled_date' => $model->scheduled_date,
                        'scheduled_time' => $model->scheduled_time,
                        'status' => $model->statuses[$model->status],
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
                    'description' => ' updated an SMS transaction (' . $model->code . ').',
                    'action' => 2,
                    'data' => $model->getChanges()
                ])
            );
        });
    }
}
?>