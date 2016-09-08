<?php

namespace ChallongeAPI\Objects;


/**
 * Class Attachment
 *
 * @property int    $id
 * @property int    $match_id
 * @property int    $user_id
 * @property string $url
 * @property string $asset_url
 * @property string $description
 *
 * @package ChallongeAPI\Objects
 */
class Attachment extends ApiObject
{
	/**
	 * Attachment constructor.
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

	/** @var int */
	public $id;

	/** @var int */
	public $match_id;

	/** @var int */
	public $user_id;

	/** @var string */
	public $url;

	/** @var mixed */
	public $asset_url;

	/** @var string */
	public $description;
}