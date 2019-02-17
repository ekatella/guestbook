<?php

namespace guestbook\engine\Core;

use guestbook\engine\App;
use guestbook\engine\DB\PDOFactory;


abstract class Model
{


	protected $table_name;

	protected $db;


	/**
	 * Model constructor.
	 */
	public function __construct()
	{
		$this->db = PDOFactory::getInstance();

	}

	/**
	 * @return string
	 */
	public function getTableName() : string
	{

		if (empty($this->table_name)) {

			// TODO: again hardcode - its supposed to have schema of module, parsed and registered in system
			$this->table_name = strtolower(App::getModuleName('model', get_class($this)));

		}

		return $this->table_name;
	}

	/**
	 * @return Record|null
	 */
	public function getNewRecord() : ?Record
	{

		return $this->createRecordObject([]);

	}


	/**
	 * Return created records from query result
	 * @param \PDOStatement $sql
	 * @return array
	 */
	protected function getRows(\PDOStatement $sql) : array
	{

		$records = [];

		while ($fields = $sql->fetch()) {

			$record_object = $this->createRecordObject($fields);

			if (!empty($record_object)) {

				$records[] = $record_object;

			}

		}

		return $records;

	}


	/**
	 * @param array $fields
	 * @return Record|null
	 */
	protected function createRecordObject(array $fields) : ?Record
	{

		$record = NULL;

		$class_name = $this->getRecordClassName();

		if (class_exists($class_name)) {

			$record = new $class_name($fields);

		} else {

			trigger_error('Attempt to create non-existent Record ' . $class_name, E_USER_ERROR);

		}

		return $record;

	}

	/**
	 * TODO: implemented taking records by parts, 'where' conditions, fields array, properly change ordering default
	 * Of course in production its unacceptable
	 * @param string $order_by
	 * @param string $type
	 * @return array
	 */
	public function getRecords(string $order_by = "id", string $type = "DESC") : array
	{
		$table_name = $this->getTableName();

		$sql_string = "SELECT * FROM " . $table_name . " ORDER BY " . $order_by . " " . $type;

		$sql = $this->db->query($sql_string);

		$records = $this->getRows($sql);

		return $records;

	}


	/**
	 * @param string $id
	 * @return int
	 */
	public function deleteRecordById(string $id) : int
	{

		$table_name = $this->getTableName();

		// TODO: temporary for deleting parent_id for messages, need separate logic with Where Conditions
		$sql_string = $this->getDeleteByIdSQL($table_name);

		$sql = $this->db->prepare($sql_string);

		$sql->execute(['id' => $id]);

		return $sql->rowCount();

	}


	//TODO: create parameter where conditions for main method
	protected function getDeleteByIdSQL($table_name) : string
	{

		return "DELETE FROM {$table_name} WHERE id = :id";

	}


	/**
	 * TODO: separate getByID and getFirstRecord methods
	 * @param string $value
	 * @param string $field_name
	 * @return Record|null
	 */
	public function getRecordByUnique( string $value, string $field_name = 'id') : ?Record
	{

		$record = NULL;

		$table_name = $this->getTableName();

		$sql_string = "SELECT * FROM {$table_name} WHERE {$field_name} = ?";

		$sql = $this->db->prepare($sql_string);

		$sql->execute([$value]);

		$row = $sql->fetch();

		if (!empty($row)) {

			$record = $this->createRecordObject($row);

		}

		return $record;

	}


	/**
	 * @return string
	 */
	protected function getRecordClassName() : string
	{

		return str_replace('Model', 'Record', get_class($this));

	}


}