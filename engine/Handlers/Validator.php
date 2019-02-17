<?php

namespace guestbook\engine\Handlers;


class Validator

{

	const PATTERNS  = [

		'password'  => '/^\S*(?=\S{6,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/',
		'integer'   => '/^[0-9]+$/',
		//it can be more strict pattern. I used this one because its better from SEO side to attract more users
		'email'     => '/^.+@.+\..+$/i',
		'name'      => '/^[a-z\d_]{2,25}$/i',

	];

	const VALIDATION_ERROR = 'Invalid field value ';

	const REQUIRED_ERROR = ' field is requiered';


	private $errors = [];

	private $name;

	private $value;


	/**
	 * Set current field to validation
	 * @param string $name
	 * @param string $value
	 */
	public function setField(string $name, string $value = NULL) :void
	{

		$this->name = $name;
		$this->value = $value;

	}


	/**
	 * Check required field
	 */
	public function required() : void
	{

		if(is_null($this->value) || $this->value === '') {

			$this->errors[] = $this->name . self::REQUIRED_ERROR;

		}

	}


	/**
	 * Validate by pattern name from const
	 * @param string $pattern_type
	 */
	public function validateByType(string $pattern_type) : void
	{

		$pattern = self::PATTERNS[$pattern_type];

		if (!empty($pattern)) {


			if (!is_null($this->value) && $this->value != '' && !preg_match($pattern, $this->value)) {

				$this->errors[] = self::VALIDATION_ERROR . $this->name;
			}

		} else {

			trigger_error('Attempt to use non-existent validation pattern ' . $pattern_type, E_USER_ERROR);

		}

	}


	/**
	 * Validate by external pattern
	 * @param string $pattern
	 */
	public function validateByPattern(string $pattern) : void
	{

		if (!is_null($this->value) && $this->value != '' && !preg_match($pattern, $this->value)) {

			$this->errors[] = self::VALIDATION_ERROR . $this->name;

		}

	}


	/**
	 * sanitize field from spaces and special characters
	 * @param string $field
	 * @return string
	 */
	public function sanitize(string $field) : string
	{

		return htmlspecialchars(strip_tags(trim($field)));

	}

	/**
	 * Check if empty array errors
	 * @return bool
	 */
	public function isSuccess() : bool
	{

		return empty($this->errors);

	}

	/**
	 * Get errors from all validations
	 * @return array
	 */
	public function getErrors()
	{

		return $this->errors;

	}


}