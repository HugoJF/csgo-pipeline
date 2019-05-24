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

class PlayerThrewGrenade extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\s\"(.*?)<(\d{1,5})><(STEAM_[01]:[01]:\d*?|BOT)><([A-Za-z]*?)>\"\sthrew\s(.*?)\s\[(-?\d{1,6})\s(-?\d{1,6})\s(-?\d{1,6})\]/i";

	public $date;
	public $time;

	public $playerName;
	public $playerId;
	public $playerSteamId;
	public $playerTeam;

	public $grenade;

	public $playerX;
	public $playerY;
	public $playerZ;


	protected static $params = [
		null, 'date', 'time',
		'playerName',
		'playerId',
		'playerSteamId',
		'playerTeam',
		'grenade',
		'playerX',
		'playerY',
		'playerZ',
	];
}