<?php

/**
 * Automatic loader of Classes
 */
class Autoloader
{
	private $is_registered = false;

	/**
	 * Registered instance
	 */
	private static $instance;

	/**
	 *
	 * @return Autoloader
	 **/
	public static function getInstance()
	{

		return self::$instance = self::$instance ?? new self();

	}

	/**
	 * True — if already registered.
	 * False — if not registered.
	 * @return boolean
	 **/
	public function isRegistered()
	{
		return $this->is_registered;
	}

	/**
	 * Register autoloader in system
	 **/
	public function register()
	{
		if (self::$instance === $this) {

			$this->isRegistered() || spl_autoload_register(array($this, 'load'));

			$this->_is_registred = true;

		} else {

			trigger_error('Attempt to register an existing autoloader in the system. There can only be one ' . __CLASS__);

		}

	}

	/**
	 * Load Class
	 *
	 * @param $class_name
	 *
	 * @return boolean — True if file exists, false — we didnt find file.
	 **/
	public function load($class_name)
	{

		$is_found = FALSE;

		$path = SITE_DIR . DIRECTORY_SEPARATOR . str_replace('\\', '/', $class_name) . ".php";

		if (file_exists($path)) {

			require_once $path;

			$is_found = TRUE;

		}

		return $is_found;

	}

}