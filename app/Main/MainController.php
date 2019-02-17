<?php

namespace guestbook\app\Main;

use guestbook\engine\Core\Controller;
use guestbook\engine\Core\View;

class MainController extends Controller
{

	protected $use_model = FALSE;

	protected $use_actor = FALSE;


	/**
	 * Set default template
	 */
	public function index()
	{

		$smarty = View::getInstance();

		$smarty->display('index.tpl');

	}
}