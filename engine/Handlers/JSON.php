<?php


namespace guestbook\engine\Handlers;

/**
 * Simple Json helper
 * Class JSON
 * @package guestbook\engine\Handlers
 */
class JSON
{

	public function send($options = NULL)
	{

		header('Content-type: application/json');

		echo json_encode($this, $options);

	}


}