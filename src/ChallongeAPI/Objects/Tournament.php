<?php

namespace ChallongeAPI\Objects;

/**
 *   Class Tournament
 *
 * @property int    $id
 * @property string $name
 * @property string $url
 * @property string $full_challonge_url
 *
 * @package ChallongeAPI\Objects
 */
class Tournament extends ApiObject
{
	/** @var int $id */
	public $id;

	/** @var string $name */
	public $name;

	/** @var string $url */
	public $url;

	/** @var string $full_challonge_url */
	public $full_challonge_url;
}