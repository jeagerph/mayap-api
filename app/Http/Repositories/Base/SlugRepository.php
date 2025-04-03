<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use Illuminate\Support\Str;

class SlugRepository
{
    public function new($string)
	{
        $code = Str::random(12);
        $name = Str::slug($string, '-');

        $duplicates = Slug::where('name', 'like',  '%'.$name.'%')->get();

        if($duplicates->count()):
            $name = $name . '-' . $duplicates->count();
        endif;

		return new Slug([
            'full' => $code . '-' . $name,
            'code' => $code,
            'name' => $name,
            'created_by' => Auth::id() ?: 1
        ]);
	}

	public function update($data)
	{
        $name = Str::slug($data['name'], '-');

        $duplicates = Slug::where('name', 'like',  '%'.$name.'%')->get();

        if($duplicates->count()):
            $name = $name . '-' . $duplicates->count();
        endif;

		return [
            'full' => $data['code'] . '-' . $name,
            'code' => $data['code'],
            'name' => $name,
            'updated_by' => Auth::id() ?: 1
        ];
    }
}
?>