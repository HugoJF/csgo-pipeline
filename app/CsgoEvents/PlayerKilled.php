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

class PlayerKilled extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\s\"(.*?)<(\d{1,5})><(STEAM_[01]:[01]:\d*?|BOT)><([A-Za-z]*?)>\"\s\[(-?\d{1,6})\s(-?\d{1,6})\s(-?\d{1,6})\]\skilled\s\"(.*?)<(\d{1,5})><(STEAM_[01]:[01]:\d*?|BOT)><([A-Za-z]*?)>\"\s\[(-?\d{1,6})\s(-?\d{1,6})\s(-?\d{1,6})\]\swith\s\"(.*?)\"/i";

	public $date;
	public $time;

	public $attackerName;
	public $attackerId;
	public $attackerSteam;
	public $attackerTeam;
	public $attackerPositionX;
	public $attackerPositionY;
	public $attackerPositionZ;

	public $targetName;
	public $targetId;
	public $targetSteam;
	public $targetTeam;
	public $targetPositionX;
	public $targetPositionY;
	public $targetPositionZ;

	public $weapon;

	protected static $params = [
		null, 'date', 'time',
		'attackerName', 'attackerId', 'attackerSteam', 'attackerTeam',
		'attackerPositionX', 'attackerPositionY', 'attackerPositionZ',
		'targetName', 'targetId', 'targetSteam', 'targetTeam',
		'targetPositionX', 'targetPositionY', 'targetPositionZ',
		'weapon',
	];
}