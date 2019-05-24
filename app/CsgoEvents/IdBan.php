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

class IdBan extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}): Banid:\s\"(.*?)<(\d{1,5})><(STEAM_[01]:[01]:\d*?)><([A-Za-z]*?)>\"\swas\sbanned\s\"for\s([\d\.]*?)\sminutes\"\sby\s\"(.*?)\"/i";

	public $date;
	public $time;

	public $playerName;
	public $playerId;
	public $playerSteam;
	public $playerTeam;
	public $duration;
	public $issuer;

	protected static $params = [
		null, 'date', 'time',
		'playerName', 'playerId', 'playerSteam', 'playerTeam',
		'duration', 'issuer',
	];
}