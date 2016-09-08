<?php

namespace ChallongeAPI\Objects;

/**
 * Class ApiObject
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
		if ($data instanceof \Traversable)
			$data = iterator_to_array($data);

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
	}


	/** @var array */
	private $_data;

	/**
	 * @return array
	 */
	public function getData(): array
	{
		return $this->_data;
	}
}