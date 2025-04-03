<?php

namespace App\Observers;

trait AccountObserver
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

            $model->user()->update([
                'username' => 'DELETED',
                'code' => 'DELETED',
                'updated_by' => 1,
                'deleted_at' => now(),
                'deleted_by' => 1,
            ]);
        });
    }
}
?>