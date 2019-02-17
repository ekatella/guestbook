<?php

namespace guestbook\engine\DB;


/**
 * TODO: implement ORM instead
 * Factory for PDO connection
 * Class PDOFactory
 * @package guestbook\engine\DB
 */
class PDOFactory
{

	// TODO: Registry pattern for configs
	const DB_CONFIG_FILE = SITE_CONFIG_DIR . '/db.php';


	private function __construct()
	{
	}

	/**
	 * @return null|\PDO
	 */
	public static function getInstance() : \PDO
	{

		$result = NULL;

		$db_settings = include self::DB_CONFIG_FILE;

		$dsn= $db_settings['type'] . ':host=' . $db_settings['host'] . ';dbname=' . $db_settings['dbname'];

		try {

			$result = new \PDO($dsn, $db_settings['user'], $db_settings['password']);

			$result->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);

			$result->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE,\PDO::FETCH_ASSOC);

			$result->exec('SET NAMES UTF8');

		} catch (\PDOException $e) {

			trigger_error($e->getMessage(), E_USER_ERROR);

		}

		return $result;

	}

}