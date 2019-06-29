<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
	protected $fillable = ['ip', 'port', 'priority'];

	public function getKeyAttribute()
	{
		return "$this->ip:$this->port";
	}
}
