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

class SourcemodPluginsLoaded extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}): \[META\] Loaded\s(\d*?)\splugins\s\((\d*?)\salready\sloaded\)/i";

	public $date;
	public $time;

	public $loaded;
	public $alreadyLoaded;

	protected static $params = [
		null, 'date', 'time',
		'loaded',
		'alreadyLoaded',
	];
}