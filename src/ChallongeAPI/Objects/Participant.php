<?php

namespace ChallongeAPI\Objects;

/**
 *   Class Participant
 *
 * @package ChallongeAPI\Objects
 */
class Participant extends ApiObject
{
	/** @var int */
	public $id;

	/** @var bool */
	public $active;

	/** @var string */
	public $checked_in_at;

	/** @var string */
	public $created_at;

	/** @var int */
	public $final_rank;

	/** @var int */
	public $group_id;

	/** @var string */
	public $icon;

	/** @var int */
	public $invitation_id;

	/** @var string */
	public $invite_email;

	/** @var string */
	public $misc;

	/** @var string */
	public $name;

	/** @var bool */
	public $on_waiting_list;

	/** @var int */
	public $seed;

	/** @var int */
	public $tournament_id;

	/** @var string */
	public $updated_at;

	/** @var string */
	public $challonge_username;

	/** @var bool */
	public $challonge_email_address_verified;

	/** @var bool */
	public $removable;

	/** @var bool */
	public $participatable_or_invitation_attached;

	/** @var bool */
	public $confirm_remove;

	/** @var bool */
	public $invitation_pending;

	/** @var string */
	public $display_name_with_invitation_email_address;

	/** @var string */
	public $email_hash;

	/** @var string */
	public $username;

	/** @var string */
	public $attached_participatable_portrait_url;

	/** @var bool */
	public $can_check_in;

	/** @var bool */
	public $checked_in;

	/** @var bool */
	public $reactivatable;
}