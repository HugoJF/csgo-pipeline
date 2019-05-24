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

class GameOver extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\sGame\sOver\: (.*?)\s\s(.*?)\sscore\s(\d*?):(\d*?)\safter\s(\d*?)\smin/i";

	public $date;
	public $time;

	public $type;
	public $map;
	public $teamAScore;
	public $teamBScore;
	public $duration;

	protected static $params = [
		null, 'date', 'time',
		'type',
		'map',
		'teamAScore',
		'teamBScore',
		'duration',
	];
}