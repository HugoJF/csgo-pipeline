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

class PlayerFlash extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\s\"(.*?)<(\d{1,5})><(STEAM_[01]:[01]:\d*?|BOT)><([A-Za-z]*?)>\"\sblinded\sfor\s([0-9\.]*?)\sby\s\"(.*?)<(\d{1,5})><(STEAM_[01]:[01]:\d*?)><([A-Za-z]*?)>\"\sfrom\s(.*?)\sentindex\s(\d*?)\s/i";

	public $date;
	public $time;

	public $targetName;
	public $targetId;
	public $targetSteamId;
	public $targetTeam;

	public $duration;

	public $playerName;
	public $playerId;
	public $playerSteamId;
	public $playerTeam;

	public $entity;
	public $entityId;

	protected static $params = [
		null, 'date', 'time',
		'targetName', 'targetId', 'targetSteamId', 'targetTeam',
		'duration',
		'playerName', 'playerId', 'playerSteamId', 'playerTeam',
		'entity', 'entityId',
	];
}