<?php

namespace ChallongeAPI\Objects;

/**
 *   Class Match
 *
 * @property int    $id
 * @property string $identifier
 * @property int    $tournament_id
 * @property int    $group_id
 * @property int    $player1_id
 * @property int    $player2_id
 * @property int    $loser_id
 * @property int    $winner_id
 *
 * @package ChallongeAPI\Objects
 */
class Match extends ApiObject
{
	/**
	 *   Match constructor.
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
	public $identifier;

	/** @var int */
	public $tournament_id;

	/** @var int */
	public $group_id;

	/** @var int */
	public $player1_id;

	/** @var int */
	public $player2_id;

	/** @var int */
	public $loser_id;

	/** @var int */
	public $winner_id;
}