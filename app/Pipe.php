<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Pipe extends Model
{
	protected $fillable = ['active', 'key', 'limit', 'pop_on_limit', 'description'];

	public function lines()
	{
		return $this->hasMany(Line::class);
	}

	public function getPendingEventsAttribute()
	{
		return Redis::command('llen', [$this->key]);
	}
}
