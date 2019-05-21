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

class StartingFreezePeriodEvent extends CsgoEvent implements \JsonSerializable
{
	private const PATTERN = "(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\sStarting\sFreeze\speriod";

	public $date;
	public $time;

	private static $params = [
		null, 'date', 'time',
	];

	protected function fill($matches)
	{
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

		return $serialization;
	}
}