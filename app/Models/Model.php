<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model as Eloquent;

use App\Traits\SoftDeletes;
use App\Traits\QueryBuilder;

class Model extends Eloquent
{
    use SoftDeletes, QueryBuilder;

    protected $guarded = [];

    public static function findOrDie($id)
    {
        $instance = self::find($id);

        !$instance && abort(404, 'Data not found');

        return $instance;
    }

    public static function findCodeOrDie($code)
    {
        $instance = self::where('code', $code)->first();

        !$instance && abort(404, 'Data not found');

        return $instance;
    }

    public static function findNameOrDie($name)
    {
        $instance = self::where('name', $name)->first();

        !$instance && abort(404, 'Data not found');

        return $instance;
    }

    public function createdBy()
    {
        $creator = $this->belongsTo('App\Models\User', 'created_by');

        return $creator->account->toArrayCreator();
    }
}
