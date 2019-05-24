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

class PlayerTriggerEvent extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\s\"(.*?)<(\d{1,6})><(STEAM_[01]:[01]:\d*?|BOT)><([A-Za-z]*)>\"\striggered\s\"(.*?)\"/i";

	public $date;
	public $time;

	public $playerName;
	public $playerId;
	public $playerSteamId;
	public $playerTeam;

	public $event;

	protected static $params = [
		null, 'date', 'time',
		'playerName',
		'playerId',
		'playerSteamId',
		'playerTeam',
		'event',
	];
}