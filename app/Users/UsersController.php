<?php

namespace guestbook\app\Users;


use guestbook\engine\Core\Controller;
use guestbook\engine\Handlers\JSON;
use guestbook\engine\Handlers\Validator;

/**
 * @property \guestbook\app\Users\UsersModel model
 */
class UsersController extends Controller
{


	public function login ()
	{
		$json = new JSON();

		//TODO: all controller methods should not know that request in json, they should get ready parsed Request object
		$request_content = $this->getJSONContent();

		$validator = new Validator();

		$request_content = array_map([$validator, 'sanitize'], $request_content);

		$email = $request_content['email'] ?? NULL;

		$password = $request_content['password'] ?? NULL;

		$validator->setField('email', $email);

		$validator->required();

		$validator->validateByType('email');

		$validator->setField('password', $password);

		$validator->required();

		if ($validator->isSuccess()) {

			/**
			 * @var $user UsersRecord
			 */
			$user = $this->model->getRecordByUnique($email, 'email');

			if ($user && $this->model->checkPasswords($user, $password)) {

				$this->model->setTokens($user);

				$json->id = $user->getId();

				$json->name = $user->getName();


			} else {

				http_response_code(401);

				$json->errors = ['Wrong password or login'];

			}

		} else {

			http_response_code(400);

			$json->errors = $validator->getErrors();

		}

		$json->send();


	}


	public function register ()
	{

		$request_content = $this->getJSONContent();

		$validator = new Validator();

		$request_content = array_map([$validator, 'sanitize'], $request_content);

		$email = $request_content['email'] ?? NULL;

		$password = $request_content['password'] ?? NULL;

		$name = $request_content['name'] ?? NULL;

		$validator->setField('email', $email);

		$validator->required();

		$validator->validateByType('email');

		$validator->setField('password', $password);

		$validator->validateByType('password');

		$validator->required();

		$validator->setField('name', $name);

		$validator->validateByType('name');

		$json = new JSON();

		if ($validator->isSuccess()) {

			/**
			 * @var $new_user UsersRecord
			 */

			$user = $this->model->getRecordByUnique($email, 'email');

			if (!$user) {

				$new_user = $this->model->getNewRecord();

				$new_user->setFields($request_content);

				$id = $new_user->create();

				$json->id = $id;

			} else {

				$json->errors = ['User with this email already exists'];

			}



		} else {

			http_response_code(400);

			$json->errors = $validator->getErrors();

		}

		$json->send();

	}


	public function logout ()
	{

		$this->model->unsetAuthTokens();

		$json = new JSON();

		$json->status = 'OK';

		$json->send();

	}


}