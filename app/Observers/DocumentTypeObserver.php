<?php

namespace App\Observers;

use App\Http\Repositories\Base\ActivityRepository;

trait DocumentTypeObserver
{
    public static function boot()
    {
        parent::boot();

        self::deleting(function($model)
        {
            if(request()->has('admin-document-type-deletion')):

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
            endif;
        });

        self::created(function($model)
        {
            $activityRepository = new ActivityRepository;

            $model->activities()->save(
                $activityRepository->new([
                    'description' => ' created ' . strtoupper($model->name) . ' as type of document.',
                    'action' => 1,
                    'data' => [
                        'name' => $model->name,
                        'description' => $model->description,
                        'view' => $model->view,
                        'options' => $model->options,
                        'content' => $model->content,
                        'inputs' => $model->inputs,
                        'tables' => $model->tables,
                        'enabled' => $model->enabled?'ENABLED':'DISABLED',
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
                    'description' => ' updated ' . strtoupper($model->name) . '.',
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
                    'description' => ' deleted a document type.',
                    'action' => 3,
                    'data' => null
                ])
            );
        });
    }
}
?>