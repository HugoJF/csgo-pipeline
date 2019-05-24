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

class TeamTriggerEvent extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}):\sTeam\s\"(.*?)\"\striggered\s\"(.*?)\"\s\((.*?)\s\"(\d{1,4})\"\)\s\((.*?)\s\"(\d{1,4})\"\)/i";

	public $date;
	public $time;

	public $team;
	public $event;
	public $teamA;
	public $teamAScore;
	public $teamB;
	public $teamBScore;

	protected static $params = [
		null, 'date', 'time',
		'team',
		'event',
		'teamA', 'teamAScore',
		'teamB', 'teamBScore',
	];
}