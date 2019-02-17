<?php

namespace guestbook\engine\Core;


use guestbook\engine\App;
use guestbook\engine\DB\PDOFactory;

abstract class Record implements \JsonSerializable
{

	/**
	 * current model of module
	 * @var Model|null
	 */
	protected $model;

	/**
	 * @var null|\PDO
	 */
	protected $db;

	/**
	 * @var array
	 */
	protected $creating_fields = [];

	protected $id;


	/**
	 * Record constructor.
	 * @param array $fields
	 */
	 public function __construct(array $fields = [])
	 {

		 $this->initCreatingFields();

		 $this->db = PDOFactory::getInstance();

		 $model_name = App::getModuleName('record', get_class($this));

		 $this->model = App::getModel($model_name);

		foreach ($fields as $name => $value) {

			$this->setFieldValue($name, $value);

		}

	}


	/**
	 * Need to set property $creating_fields to determine required for creating Record field
	 */
	abstract protected function initCreatingFields();


	/**
	 * @param $name
	 * @param $value
	 */
	protected function setFieldValue($name, $value) : void
	{

		if (property_exists($this, $name)) {

			$this->$name = $value;

		}

	}


	/**
	 * Set fields by Setters
	 * @param array $fields
	 */
	public function setFields(array $fields) : void
	{

		foreach ($fields as $name => $value) {

			$field = str_replace('_', '', ucwords($name, '_'));

			$method = 'set' . ucfirst($field);

			if(method_exists($this, $method)) {

				$this->$method($value);

			}

		}

	}


	/**
	 * @return null|string
	 */
	public function create() : ?string
	{

		$insert_id = NULL;

		//TODO: here also would be correct check fields if they required
		if (!empty($this->creating_fields)) {

			$table_name = $this->model->getTableName();

			$fields = $this->getFieldsString($this->creating_fields);

			$statement_fields = $this->getStatementString($this->creating_fields);

			$input_parameters = $this->getInputParameters($this->creating_fields);

			$sql_string = "INSERT INTO {$table_name} ({$fields}) VALUES ({$statement_fields})";

			$sql = $this->db->prepare($sql_string);

			$sql->execute($input_parameters);

			$insert_id = $this->db->lastInsertId();

		} else {

			trigger_error('Attempt to create Record without declaration of fields for creation', E_USER_ERROR);

		}


		return $insert_id;

	}



	/**
	 * This method return string with fields for queries
	 * TODO: its supposed to be in extender or smth, not here
	 * @param array $fields_array
	 * @return string
	 */
	protected function getFieldsString (array $fields_array) : string
	{

		return implode(', ', $fields_array);

	}

	/**
	 * This method return string with Statement fields for PDO
	 * TODO: its supposed to be in extender or smth, not here
	 * @param array $fields_array
	 * @return string
	 */
	protected function getStatementString (array $fields_array) : string
	{

		return ':' . implode(', :', $fields_array);

	}


	/**
	 * This method return array with input parameters for PDO Statement
	 * TODO: its supposed to be in extender or smth, not here
	 * @param array $fields_array
	 * @return array
	 */
	protected function getInputParameters (array $fields_array) : array
	{

		$input_parameters = [];

		foreach ($fields_array as $name) {

			$input_parameters[$name] = $this->$name;

		}

		return $input_parameters;

	}


	/**
	 * @return null|string
	 */
	public function getId() : ?string
	{
		return $this->id;
	}


}