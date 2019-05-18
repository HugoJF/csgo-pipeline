<?php
/**
 * Created by PhpStorm.
 * User: Hugo
 * Date: 5/13/2019
 * Time: 11:31 PM
 */

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

abstract class FilterBase
{
	public $model;

	/**
	 * @param Model $model
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public static function fromModel(Model $model)
	{
		$class = $model->type;
		if (!class_exists($class))
			throw new \Exception("Invalid filter type: $class");

		$instance = new $class;
		$instance->model = $model;

		return $instance;
	}

	abstract function filters($data);
}