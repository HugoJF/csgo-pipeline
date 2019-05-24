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

class LogFileOpened extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}): Log\sfile\sstarted\s\(file\s\"(.*?)\"\)\s\(game\s\"(.*?)\"\)\s\(version\s\"(.*?)\"\)/i";

	public $date;
	public $time;

	public $file;
	public $path;
	public $version;

	protected static $params = [
		null, 'date', 'time',
		'file',
		'path',
		'version',
	];
}