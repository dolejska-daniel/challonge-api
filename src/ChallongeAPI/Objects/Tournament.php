<?php

namespace ChallongeAPI\Objects;


/**
 * Class Tournament
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
	/**
	 * Tournament constructor.
	 *
	 * @param array $settings
	 */
	public function __construct( array $settings )
	{
		// Trait initialization
		parent::__construct($settings);

		// Assigns data to class properties
		foreach ($this->getData() as $property => $value)
			if (property_exists(self::class, $property))
				$this->$property = $value;
	}

	/** @var int $id */
	public $id;

	/** @var string $name */
	public $name;

	/** @var string $url */
	public $url;

	/** @var string $full_challonge_url */
	public $full_challonge_url;
}