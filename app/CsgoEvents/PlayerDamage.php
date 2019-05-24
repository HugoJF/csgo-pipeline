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

class PlayerDamage extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\s\"(.+?)<(\d*)><(STEAM_\d:\d:\d+?|BOT)><(.+?)>\"\s\[(-?\d+?)\s(-?\d+?)\s(-?\d+?)\]\sattacked\s\"(.+?)<(\d+?)><(STEAM_\d:\d:\d+?|BOT)><(.+?)>\"\s\[(-?\d+?)\s(-?\d+?)\s(-?\d+?)\]\swith\s\"(.+?)\"\s\(damage\s\"(\d+?)\"\)\s\(damage_armor\s\"(\d+?)\"\)\s\(health\s\"(\d+?)\"\)\s\(armor\s\"(\d+?)\"\)\s\(hitgroup\s\"(.+?)\"\)/i";

	public $date;
	public $time;

	public $attackerName;
	public $attackerSteam;
	public $attackerTeam;
	public $attackerId;
	public $attackerPosition;
	public $weapon;

	public $damage;
	public $armorDamage;
	public $hitgroup;

	public $targetName;
	public $targetId;
	public $targetSteam;
	public $targetTeam;
	public $targetPosition;
	public $targetHealth;
	public $targetArmor;

	protected static $params = [
		null, 'date', 'time',
		'attackerName', 'attackerId', 'attackerSteam', 'attackerTeam', null, null, null,
		'targetName', 'targetId', 'targetSteam', 'targetTeam', null, null, null,
		'weapon', 'damage', 'armorDamage', 'targetHealth', 'targetArmor', 'hitgroup',
	];
}