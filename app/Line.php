<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
	public function pipe()
	{
		return $this->belongsTo(Pipe::class);
	}
}
