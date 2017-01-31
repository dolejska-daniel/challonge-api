<?php

namespace ChallongeAPI\Objects;

/**
 *   Class Participant
 *
 * @property int    $id
 * @property string $name
 * @property int    $tournament_id
 * @property int    $final_rank
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
	/** @var int */
	public $id;

	/** @var string */
	public $name;

	/** @var int */
	public $tournament_id;

	/** @var int */
	public $final_rank;

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