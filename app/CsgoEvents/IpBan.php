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

class IpBan extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}): Addip:\s\"(.*?)\"\swas\sbanned\sby\sIP\s\"(.*?)\"\sby\s\"(.*?)\"\s\(IP\s\"(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\"\)/i";

	public $date;
	public $time;

	// Not sure what this is "<><><>"
	public $player;
	public $duration;
	public $issuer;
	public $ip;

	protected static $params = [
		null, 'date', 'time',
		'player',
		'duration',
		'issuer',
		'ip',
	];
}