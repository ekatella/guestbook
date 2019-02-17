<?php
/**
 * Created by PhpStorm.
 * User: evgeniya
 * Date: 11.01.19
 * Time: 21:43
 */

namespace guestbook\engine\Handlers;


use guestbook\engine\App;

/**
 * Check access to actions of controller through access map for roles
 * Class AccessChecker
 * @package guestbook\engine\Handlers
 */
class AccessChecker
{

	// TODO: Registry pattern for configs
	const ACCESS_PATH = SITE_CONFIG_DIR . '/access/';

	private $action;

	private $actor;

	private $access_rules;

	public function __construct(string $action, string $controller)
	{

		$this->action = $action;

		$module_name =  App::getModuleName('controller', $controller);

		$access_file = self::ACCESS_PATH . $module_name . '.php';

		//if rules dont exist then its full access for controller
		if (file_exists($access_file)) {

			$this->access_rules = include $access_file;

			$this->actor = App::getActor();

		}

	}


	/**
	 * if empty errors - its allowed
	 * @return array
	 */
	public function check()
	{

		$errors = [];

		if ($this->actor && !$this->isAllowed($this->action, 'authorized')) {

			http_response_code(400);

			$errors[] = 'This action for authorized users is not found';

		} elseif (!$this->actor && !$this->isAllowed($this->action, 'unauthorized')) {

			http_response_code(401);

			$errors[] = 'Unauthorized error';

		}

		return $errors;

	}


	/**
	 * Check access on access config from const ACCESS_PATH
	 * @param string $action_name
	 * @param string $role_name
	 * @return bool
	 */
	protected function isAllowed(string $action_name, string $role_name)
	{

		return empty($this->access_rules)
		|| (!empty($this->access_rules[$role_name])
		&& in_array($action_name, $this->access_rules[$role_name])
		);

	}

}