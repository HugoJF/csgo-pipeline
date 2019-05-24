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

class PlayerKilledProp extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\s\"(.*?)<(\d{1,5})><(STEAM_[01]:[01]:\d*?|BOT)><([A-Za-z]*?)>\"\s\[(-?\d{1,6})\s(-?\d{1,6})\s(-?\d{1,6})\]\skilled\sother\s\"(.*?)<(.*?)>\"\s\[(-?\d{1,6})\s(-?\d{1,6})\s(-?\d{1,6})\]\swith\s\"(.*?)\"/i";

	public $date;
	public $time;

	public $playerName;
	public $playerId;
	public $playerSteamId;
	public $playerTeam;

	public $playerX;
	public $playerY;
	public $playerZ;

	public $prop;
	public $propId;

	// TODO: not sure if this is actually the prop position
	public $propX;
	public $propY;
	public $propZ;

	public $with;

	protected static $params = [
		null, 'date', 'time',
		'playerName', 'playerId', 'playerSteamId', 'playerTeam',
		'playerX', 'playerY', 'playerZ',
		'prop', 'propId', 'propX', 'propY', 'propZ',
		'with',
	];
}