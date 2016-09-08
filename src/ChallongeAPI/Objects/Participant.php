<?php

namespace ChallongeAPI\Objects;


/**
 * Class Participant
 *
 * @property int    $id
 * @property string $name
 * @property int    $tournament_id
 * @property bool   $active
 * @property bool   $checked_in
 * @property string $checked_in_at
 * @property int    $seed
 * @property int    $group_id
 * @property string $misc
 *
 * @package ChallongeAPI\Objects
 */
class Participant extends ApiObject
{
	/**
	 * Participant constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data )
	{
		// Trait initialization
		parent::__construct($data);

		// Assigns data to class properties
		foreach ($this->getData() as $property => $value)
			if (property_exists(self::class, $property))
				$this->$property = $value;
	}


	/** @var int */
	public $id;

	/** @var string */
	public $name;

	/** @var int */
	public $tournament_id;

	/** @var bool */
	public $active;

	/** @var bool */
	public $checked_in;

	/** @var string */
	public $checked_in_at;

	/** @var int */
	public $seed;

	/** @var int */
	public $group_id;

	/** @var string */
	public $misc;
}