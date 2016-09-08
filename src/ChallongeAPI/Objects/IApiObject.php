<?php

namespace ChallongeAPI\Objects;

/**
 * Interface IApiObject
 *
 * @package ChallongeAPI\Objects
 */
interface IApiObject
{
	/**
	 * IApiObject constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data );

	/**
	 * @return array
	 */
	public function getData(): array;
}