<?php

namespace ChallongeAPI\Objects;

/**
 *   Interface IApiObject
 *
 * @package ChallongeAPI\Objects
 */
interface IApiObject
{
	/**
	 *   IApiObject constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data );

	/**
	 *   Gets all the original data fetched from ChallongeAPI.
	 *
	 * @return array
	 */
	public function getData(): array;
}