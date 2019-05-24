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

class SuicideEvent extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\s\"(.*?)<(\d{1,5})><(STEAM_[01]:[01]:\d*?)><([A-Za-z]*?)>\"\s\[(-?\d{1,6})\s(-?\d{1,6})\s(-?\d{1,6})\]\scommitted\ssuicide\swith\s\"(.*?)\"/i";

	public $date;
	public $time;

	public $playerName;
	public $playerId;
	public $playerSteamId;
	public $playerTeam;

	public $playerX;
	public $playerY;
	public $playerZ;

	public $suicideReason;

	protected static $params = [
		null, 'date', 'time',
		'playerName', 'playerId', 'playerSteamId', 'playerTeam',
		'playerX', 'playerY', 'playerZ',
		'suicideReason',
	];
}