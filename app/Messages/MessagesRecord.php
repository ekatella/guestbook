<?php

namespace guestbook\app\Messages;


use guestbook\app\Users\UsersRecord;
use guestbook\engine\App;
use guestbook\engine\Core\Record;


class MessagesRecord extends Record
{

	protected $text;

	protected $user;

	protected $user_id;

	protected $parent_id;

	protected $created;


	/**
	 * MessagesRecord constructor.
	 * @param array $fields
	 */
	public function __construct(array $fields = [])
	{

		parent::__construct($fields);

		if (isset($fields['user_id'])) {

			$this->user = App::getModel('Users')->getRecordByUnique($fields['user_id']);

		}

	}


	/**
	 * init array with fields for creating records in DB
	 */
	protected function initCreatingFields() : void
	{

		$this->creating_fields = ['text', 'user_id', 'parent_id'];

	}

	/**
	 * @return array
	 */
	public function jsonSerialize() : array
	{
		return [
			'id' => $this->id,
			'user' => $this->user,
			'text' => $this->text,
			'created' => $this->created,
			'parent_id' => $this->parent_id,
		];
	}


	/**
	 *
	 * TODO: tranfer to parent and make suitable for any record
	 * @return int
	 */
	public function save() : int
	{

		$table_name = $this->model->getTableName();

		$sql_string = "UPDATE {$table_name} SET text=:text WHERE id=:id";

		$sql= $this->db->prepare($sql_string);

		$sql->execute([
			'text' => $this->text,
			'id' => $this->id
		]);

		return $sql->rowCount();

	}


	/**
	 * @param string $user_id
	 */
	public function setUserId(string $user_id) : void
	{
		$this->user_id = $user_id;
	}

	/**
	 * @return null|string
	 */
	public function getParentId() : ?string
	{
		return $this->parent_id;
	}


	/**
	 * @param string $parent_id
	 */
	public function setParentId(string $parent_id) : void
	{
		$this->parent_id = $parent_id;
	}


	/**
	 * @return null|string
	 */
	public function getText() : ?string
	{
		return $this->text;
	}


	/**
	 * @param string $text
	 */
	public function setText(string $text) : void
	{
		$this->text = $text;
	}


	/**
	 * @return UsersRecord|null
	 */
	public function getUser() : ?UsersRecord
	{
		return $this->user;
	}


}