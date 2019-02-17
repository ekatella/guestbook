<?php

namespace guestbook\app\Messages;


use guestbook\engine\Core\Model;

class MessagesModel extends Model
{

	//TODO: create parameter where conditions for main method
	protected function getDeleteByIdSQL($table_name) : string
	{

		return "DELETE FROM {$table_name} WHERE id = :id or parent_id = :id";

	}

}