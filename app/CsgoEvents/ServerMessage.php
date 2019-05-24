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

class ServerMessage extends CsgoEvent
{
	protected const PATTERN = "/(\d{1,2}\/\d{1,2}\/\d{1,4})\s-\s(\d{1,2}:\d{1,2}:\d{1,2}): server_message:\s\"(.*?)\"/i";

	public $date;
	public $time;

	public $message;

	protected static $params = [
		null, 'date', 'time',
		'message',
	];
}