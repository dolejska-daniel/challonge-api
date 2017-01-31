<?php

namespace ChallongeAPI\Objects;

/**
 *   Class Attachment
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