<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyPosition;

class CompanyPositionRepository
{
    public function store($data)
    {
        $model = new CompanyPosition;
        $model->name = strtoupper($data['name']);
        $model->description = $data['description'];
        $model->enabled = 1;
        $model->created_by = Auth::id() ?: 1;
        $model->save();

        return $model;
    }

    public function update($data)
    {
        return [
            'name' => strtoupper($data['name']),
            'description' => $data['description'],
            'updated_by' => Auth::id() ?: 1
        ];
    }
}
?>