<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pipe extends Model
{
	protected $fillable = ['active', 'key', 'limit', 'pop_on_limit', 'description'];

	public function lines()
	{
		return $this->hasMany(Line::class);
	}
}
