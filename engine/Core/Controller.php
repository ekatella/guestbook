<?php

namespace guestbook\engine\Core;


use guestbook\app\Users\UsersRecord;
use guestbook\engine\App;

abstract class Controller
{

	// TODO:  Registry pattern for configs
	const CORS_HEADER = "Cache-Control, Host, Cookie, Connection, Content-Type, CSRF-Token, Keep-Alive, User-Agent, X-Requested-With";


	/**
	 * @var Model|null
	 */
	public $model;

	/**
	 * If module is without model then we dont need to create it. Set value on FALSE
	 * @var bool
	 */
	protected $use_model = TRUE;

	/**
	 * @var UsersRecord|null
	 */
	protected $actor;

	// Unauthorized controller doesnt need user - then set value FALSE
	protected $use_actor = TRUE;



	/**
	 * Controller constructor.
	 */
	public function __construct($matched_route)
	{

		$this->actor = $this->use_actor ? App::getActor() : NULL;

		//not all modules have models
		if ($this->use_model) {

			$module_name =  App::getModuleName('controller', get_class($this));

			$this->model =  App::getModel($module_name);

		}

		$this->setDefaultHeaders();

	}

	/**
	 * @return string
	 */
	public function getJSONContent() : array
	{

		$content = file_get_contents('php://input');

		return json_decode($content, TRUE);

	}

	protected function setDefaultHeaders()
	{

		header("X-Frame-Options: deny");
		header("Access-Control-Allow-Origin: " . BASE_DOMAIN);
		header("Access-Control-Allow-Headers: " . self::CORS_HEADER);
		header("Access-Control-Allow-Credentials: true");
		header("Content-Security-Policy: default-src 'self' style-src: 'unsafe-inline'");

	}

}