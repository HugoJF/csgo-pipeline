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

class TeamScored extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\sTeam\s\"([A-Za-z]*?)\"\sscored\s\"(\d{1,5})\"\swith\s\"(\d{1,5})\"\splayers/i";

	public $date;
	public $time;

	public $team;
	public $teamScore;
	public $playersAlive;

	protected static $params = [
		null, 'date', 'time',
		'team',
		'teamScore',
		'playersAlive',
	];
}