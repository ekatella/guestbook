<?php


namespace guestbook\engine;

use guestbook\engine\Core\ActorModel;
use guestbook\engine\Core\Model;

/**
 * Provide connection between app modules and engine classes through static methods
 * Probably provides extra tight coupling which needs to be refactored for avoid TODO: Refactor coupling
 * Class App
 * @package guestbook\engine
 */
final class App
{

	/**
	 * Set model name of Users model on app
	 */
	// TODO: better to implement Registry pattern to init all configs on project
	const ACTOR_MODEL = 'Users';


	/**
	* cached models
	* @var array
	*/
	private static $models = [];


	public static function getActor()
	{

		/** @var ActorModel $actor_model */
		$actor_model = self::getModel(self::ACTOR_MODEL);

		return $actor_model->getAuthorizedActor();

	}


	/**
	 * Get Model by Name
	 * @param string $name
	 * @return Model|null
	 */
	public static function getModel(string $name) : ?Model
	{

		$model = NULL;

		// check if we have already get this Model
		if (isset(self::$models[$name]) ) {

			$model = self::$models[$name];

		} else {

			// directory of module and module classes always contain module name
			$class_name = BASE_NAMESPASE . '\\' . $name . '\\' . $name . 'Model';

			if (class_exists($class_name)) {

				$model = self::$models[$name] = new $class_name();


			} else {

				trigger_error('Attempt to use non-existent model ' . $name,	E_USER_NOTICE);

			}

		}

		return $model;

	}

	/**
	 * module classes always contain module name
	 * @param string $class_type - class type of this module (e.g. controller, record, model)
	 * @param string $class_name - class name for extract name
	 * @return string
	 */
	public static  function getModuleName(string $class_type, string $class_name) : string
	{

		$model_name = '';

		// Get from such pattern - /{model_name} . {class_type}
		$pattern = '/.*\\\(\w+)' . ucfirst($class_type) . '$/';

		preg_match($pattern, $class_name, $matches);

		if (!empty($matches[1])) {

			$model_name = $matches[1];

		} else {

			trigger_error('Model name has not been found for ' . $class_name);

		}

		return $model_name;

	}


}