<?php

namespace guestbook\app\Messages;

use guestbook\engine\Core\Controller;
use guestbook\engine\Handlers\JSON;
use guestbook\engine\Handlers\Validator;

class MessagesController extends Controller
{


	/**
	 * create new message
	 */
	public function add()
	{

		//TODO: all controller methods should not know that request in json, they should get ready parsed Request object
		$request_content = $this->getJSONContent();

		$validator = new Validator();

		$request_content = array_map([$validator, 'sanitize'], $request_content);

		$text = $request_content['text'] ?? NULL;

		$parent_id = $request_content['parent_id'] ?? NULL;

		$validator->setField('text', $text);

		$validator->required();

		$validator->setField('parent_id', $parent_id);

		$validator->validateByType('integer');

		$json = new JSON();


		if ($validator->isSuccess()) {

			/**
			 * @var $new_message MessagesRecord
			 */
			$new_message = $this->model->getNewRecord();

			$request_content['user_id'] = $this->actor->getId();

			$new_message->setFields($request_content);

			$id = $new_message->create();

			$json->id = $id;

		} else {

			http_response_code(400);

			$json->errors = $validator->getErrors();

		}

		$json->send();

	}


	/**
	 * delete message
	 * @param string $id
	 */
	public function delete(string $id)
	{

		$validator = new Validator();

		$id = $validator->sanitize($id);

		$validator->setField('id', $id);

		$validator->validateByType('integer');

		$json = new JSON();

		if ($validator->isSuccess()) {

			//actor is always checked before going to methods which need authorization
			$actor_id = $this->actor->getId();

			$message = $this->model->getRecordByUnique($id);

			if (!empty($message) && ($actor_id == $message['user_id'])) {

				$this->model->deleteRecordById($id);

				$json->status = 'OK';

			} else {

				http_response_code(400);

				$json->errors = ['Record doesnt exist or User doesnt have rights to delete it'];

			}

		} else {

			http_response_code(400);

			$json->errors = $validator->getErrors();

		}

		$json->send();

	}


	/**
	 * @param string $id
	 */
	public function update(string $id)
	{

		$request_content = $this->getJSONContent();

		$validator = new Validator();

		$id = $validator->sanitize($id);

		$validator->setField('id', $id);

		$validator->validateByType('integer');

		$text = $request_content['text'] ?? NULL;

		$text = $validator->sanitize($text);

		$validator->setField('text', $text);

		$validator->required();

		$json = new JSON();

		if ($validator->isSuccess()) {

			/**
			 * @var $message MessagesRecord
			 */
			$message = $this->model->getRecordByUnique($id);

			$actor_id = $this->actor->getId();

			if(!empty($message) && ($actor_id == $message['user_id'])) {

				$message->setText($text);

				$message->save();

				$json->status = 'OK';


			} else {

				http_response_code(400);

				$json->errors = ['Record doesnt exist or User doesnt have rights to update it'];

			}

		} else {

			http_response_code(400);

			$json->errors = $validator->getErrors();

		}

		$json->send();

	}


	/**
	 * TODO: pagination
	 * Get all messages
	 */
	public function getAll()
	{

		$messages = $this->model->getRecords();

		$json = new JSON();

		$json->messages = $messages;

		$json->send();

	}


}