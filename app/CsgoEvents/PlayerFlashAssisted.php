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

class PlayerFlashAssisted extends CsgoEvent implements \JsonSerializable
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}): \"(.*?)<(\d{1,5})><(STEAM_[01]:[01]:\d*?)><([A-Za-z]*?)>\"\sflash-assisted\skilling\s\"(.*?)<(\d{1,5})><(STEAM_[01]:[01]:\d*?)><([A-Za-z]*?)>\"/i";

	public $date;
	public $time;

	public $assisterName;
	public $assisterId;
	public $assisterSteam;
	public $assisterTeam;

	public $targetName;
	public $targetId;
	public $targetSteam;
	public $targetTeam;

	protected static $params = [
		null, 'date', 'time',
		'assisterName', 'assisterId', 'assisterSteam', 'assisterTeam',
		'targetName', 'targetId', 'targetSteam', 'targetTeam',
	];
}