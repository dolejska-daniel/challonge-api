<?php

namespace ChallongeAPI\Objects;

/**
 *   Class Match
 *
 * @package ChallongeAPI\Objects
 */
class Match extends ApiObject
{
	/** @var int */
	public $id;

	/** @var string */
	public $identifier;

	/** @var string */
	public $created_at;

	/** @var string */
	public $underway_at;

	/** @var string */
	public $started_at;

	/** @var string */
	public $updated_at;

	/** @var string */
	public $scheduled_time;

	/** @var string */
	public $state;

	/** @var bool */
	public $has_attachment;

	/** @var int */
	public $attachment_count;

	/** @var int */
	public $tournament_id;

	/** @var int */
	public $group_id;

	/** @var int */
	public $player1_id;

	/** @var bool */
	public $player1_is_prereq_match_loser;

	/** @var int */
	public $player1_prereq_match_id;

	/** @var int */
	public $player1_votes;

	/** @var int */
	public $player2_id;

	/** @var bool */
	public $player2_is_prereq_match_loser;

	/** @var int */
	public $player2_prereq_match_id;

	/** @var int */
	public $player2_votes;

	/** @var int */
	public $loser_id;

	/** @var int */
	public $winner_id;

	/** @var int */
	public $round;

	/** @var string */
	public $location;

	/** @var string */
	public $prerequisite_match_ids_csv;

	/** @var string */
	public $scores_csv;
}