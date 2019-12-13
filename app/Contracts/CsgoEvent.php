<?php
/**
 * Created by PhpStorm.
 * User: Hugo
 * Date: 1/3/2019
 * Time: 4:08 AM
 */

namespace App\Classes;

abstract class CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "//i";

	protected static $params = [];

	// Global event parameters
	public $server;
	public $type;

	protected function fill($server, $matches)
	{
		$this->type = class_basename(static::class);
		$this->server = $server;

		foreach (static::$params as $key => $param) {
			if ($param !== null) {
				$this->$param = $matches[ $key ];
			}
		}
	}

	public static function build($server, $raw)
	{
		if (preg_match(static::PATTERN, $raw, $matches)) {
			$event = new static();

			$event->fill($server, $matches);

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

		foreach (static::$params as $param) {
			if ($param !== null)
				$serialization[ $param ] = $this->$param;
		}

		$serialization['server'] = $this->server;
		$serialization['type'] = $this->type;

		return $serialization;
	}
}