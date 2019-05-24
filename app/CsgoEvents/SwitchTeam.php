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

class SwitchTeam extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\s\"(.*?)<(\d{1,5})><(STEAM_[01]:[01]:\d*?|BOT)>\"\sswitched\sfrom\steam\s<([A-Za-z]*?)>\sto\s<([A-Za-z]*?)>/i";

	public $date;
	public $time;

	public $playerName;
	public $playerId;
	public $playerSteamId;
	public $fromTeam;
	public $toTeam;

	protected static $params = [
		null, 'date', 'time',
		'playerName',
		'playerId',
		'playerSteamId',
		'fromTeam',
		'toTeam',
	];
}