<?php


namespace guestbook\engine\Core;


class View extends \Smarty
{

	private static $instance;

	public function __construct()
	{

		parent::__construct();

		$this->template_dir = SITE_TEMPLATES_DIR;
		$this->compile_dir = SITE_COMPILE_DIR;

	}

	public static function getInstance()
	{

		return self::$instance = self::$instance ?? new self();

	}

}