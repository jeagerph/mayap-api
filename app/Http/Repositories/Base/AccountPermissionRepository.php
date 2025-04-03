<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\AccountPermission;

class AccountPermissionRepository
{
    public function store($data)
    {
        $model = new AccountPermission;
        $model->account_id = $data['account_id'];
        $model->module_id = $data['module_id'];
        $model->access = $data['access'];
        $model->index = $data['index'];
        $model->store = $data['store'];
        $model->update = $data['update'];
        $model->destroy = $data['destroy'];
        $model->created_by = Auth::id() ?: 1;
        $model->save();

        return $model;
    }

    public function new($data)
    {
        return new AccountPermission([
            'module_id' => $data['module_id'],
            'access' => $data['access'],
            'index' => $data['index'],
            'store' => $data['store'],
            'update' => $data['update'],
            'destroy' => $data['destroy'],
            'created_by' => Auth::id() ?: 1
        ]);
    }
}
?>