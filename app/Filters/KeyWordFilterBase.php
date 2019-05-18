<?php
/**
 * Created by PhpStorm.
 * User: Hugo
 * Date: 5/13/2019
 * Time: 11:33 PM
 */

namespace App\Filters;

use App\Contracts\FilterBase;

class KeyWordFilterBase extends FilterBase
{
	public function filters($data)
	{
		return stristr($data, $this->model->value) !== false;
	}
}