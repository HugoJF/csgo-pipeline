<?php
/**
 * Created by PhpStorm.
 * User: Hugo
 * Date: 11/1/2018
 * Time: 7:47 AM
 */

namespace App\CsgoEvents;

use App\Classes\CsgoEvent;
use App\Classes\SteamID;
use App\User;

class RconBadPassword extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\srcon\sfrom\s\"(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{1,5})\":\s(.*)/i";

	public $date;
	public $time;

	public $ip;

	protected static $params = [
		null, 'date', 'time',
		'ip',
	];
}