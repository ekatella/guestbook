<?php

namespace guestbook\app\Users;


use guestbook\engine\Core\Record;

/**
 * @property \guestbook\app\Users\UsersModel model
 */
class UsersRecord extends Record
{

	protected $name;

	protected $password;

	protected $email;

	protected $salt;


	protected function initCreatingFields() : void
	{

		$this->creating_fields = ['name', 'password', 'email', 'salt'];

	}


	/**
	 * @return array
	 */
	public function jsonSerialize() : array
	{

		return [
			'id' => $this->id,
			'name' => $this->name
		];

	}


	/**
	 * @return null|string
	 */
	public function getName() : ?string
	{
		return $this->name;
	}


	/**
	 * @param string $name
	 */
	public function setName(string $name)
	{
		$this->name = $name;
	}


	/**
	 * @return null|string
	 */
	public function getPassword() : ?string
	{
		return $this->password;
	}


	/**
	 * @param string $password
	 */
	public function setPassword(string $password) : void
	{

		$salt = $this->getSalt();

		$this->password = $this->model->generatePassword($password, $salt);

	}

	/**
	 * @return null|string
	 */
	public function getEmail() : ?string
	{
		return $this->email;
	}


	/**
	 * @param string $email
	 */
	public function setEmail(string $email) : void
	{
		$this->email = $email;
	}


	/**
	 * @return string
	 */
	public function getSalt() : string
	{
		if (empty($this->salt)) {

			$this->salt = $this->model->generateRand();

		}

		return $this->salt;
	}



}