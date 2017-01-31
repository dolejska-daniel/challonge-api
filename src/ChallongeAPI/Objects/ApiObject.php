<?php

namespace ChallongeAPI\Objects;

/**
 *   Class ApiObject
 *
 * @property $_data
 *
 * @package ChallongeAPI\Objects
 */
abstract class ApiObject implements IApiObject
{
	/**
	 *   ApiObject constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data )
	{
		$object_keys = [
			'tournament',
			'participant',
			'match',
			'match_attachment',
		];

		// Parses the real data from the array
		foreach ($object_keys as $key)
			if (isset($data[$key]))
				$data = $data[$key];

		$this->_data = $data;

		// Assigns data to class properties
		foreach ($data as $property => $value)
			if (property_exists($this, $property))
				$this->$property = $value;
	}


	/** @var array */
	private $_data;

	/**
	 *   Gets all the original data fetched from ChallongeAPI.
	 *
	 * @return array
	 */
	public function getData(): array
	{
		return $this->_data;
	}
}