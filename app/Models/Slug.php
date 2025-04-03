<?php

namespace App\Models;

use App\Models\Model;

class Slug extends Model
{
    public function slug()
    {
        return $this->morphTo();
	}
	
	public function toArray()
	{
		return [
            'full' => $this->full,
            'code' => $this->code,
            'name' => $this->name,
            'id'=> $this->id,
        ];
	}
}
