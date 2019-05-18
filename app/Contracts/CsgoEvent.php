<?php
/**
 * Created by PhpStorm.
 * User: Hugo
 * Date: 1/3/2019
 * Time: 4:08 AM
 */

namespace App\Classes;


abstract class CsgoEvent
{
	public abstract static function build($raw);
}