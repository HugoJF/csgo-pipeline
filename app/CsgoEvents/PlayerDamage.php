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
	private const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\s\"(.+?)<(\d*)><(STEAM_\d:\d:\d+?|BOT)><(.+?)>\"\s\[(-?\d+?)\s(-?\d+?)\s(-?\d+?)\]\sattacked\s\"(.+?)<(\d+?)><(STEAM_\d:\d:\d+?|BOT)><(.+?)>\"\s\[(-?\d+?)\s(-?\d+?)\s(-?\d+?)\]\swith\s\"(.+?)\"\s\(damage\s\"(\d+?)\"\)\s\(damage_armor\s\"(\d+?)\"\)\s\(health\s\"(\d+?)\"\)\s\(armor\s\"(\d+?)\"\)\s\(hitgroup\s\"(.+?)\"\)/i";

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

	private static $params = [
		null, 'date', 'time',
		'attackerName', 'attackerId', 'attackerSteam', 'attackerTeam', null, null, null,
		'targetName', 'targetId', 'targetSteam', 'targetTeam', null, null, null,
		'weapon', 'damage', 'armorDamage', 'targetHealth', 'targetArmor', 'hitgroup',
	];

	protected function fill($matches)
	{
		$this->attackerPosition = [
			'x' => intval($matches[7]),
			'y' => intval($matches[8]),
			'z' => intval($matches[9]),
		];

		$this->targetPosition = [
			'x' => intval($matches[14]),
			'y' => intval($matches[15]),
			'z' => intval($matches[16]),
		];

		foreach (static::$params as $key => $param) {
			if ($param !== null) {
				$this->$param = $matches[ $key ];
			}
		}
	}

	public static function build($raw)
	{
		if (preg_match(static::PATTERN, $raw, $matches)) {
			$event = new static();

			$event->fill($matches);

			return $event;
		} else {
			return false;
		}
	}

	/**
	 * Specify data which should be serialized to JSON
	 *
	 * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize()
	{
		$serialization = [];

		foreach (static::$params as $key => $param) {
			if ($param !== null) {
				$serialization[ $param ] = $this->$param;
			}
		}

		$serialization['attackerPosition'] = $this->attackerPosition;
		$serialization['targetPosition'] = $this->targetPosition;

		return $serialization;
	}
}