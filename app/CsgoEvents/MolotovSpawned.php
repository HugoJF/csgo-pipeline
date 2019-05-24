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

class MolotovSpawned extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\sMolotov\sprojectile\sspawned\sat\s(-?[0-9\.]*?)\s(-?[0-9\.]*?)\s(-?[0-9\.]*?),\svelocity\s(-?[0-9\.]*?)\s(-?[0-9\.]*?)\s(-?[0-9\.]*?)$/i";

	public $date;
	public $time;

	public $positionX;
	public $positionY;
	public $positionZ;

	public $velocityX;
	public $velocityY;
	public $velocityZ;

	protected static $params = [
		null, 'date', 'time',
		'positionX', 'positionY', 'positionZ',
		'velocityX', 'velocityY', 'velocityZ',
	];
}