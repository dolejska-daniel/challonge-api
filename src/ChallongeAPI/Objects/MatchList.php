<?php

namespace ChallongeAPI\Objects;


/**
 * Class MatchList
 *
 * @property Match[] $matches;
 * @property int          $count
 *
 * @package ChallongeAPI\Objects
 */
class MatchList implements IApiObjectList
{
	/**
	 * MatchList constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data )
	{
		foreach ($data as $match_data)
		{
			$m = new Participant($match_data);
			$this->matches[$m->id] = $m;
		}

		$this->count = count($data);
	}


	/** @var int $count */
	public $count;

	/** @var Match[] $matches */
	public $matches;

	/**
	 * @return Match[]
	 */
	public function getMatches(): array
	{
		return $this->matches;
	}

	/**
	 * @param int $match_id
	 *
	 * @return Match
	 */
	public function getMatchById( int $match_id ): Match
	{
		return $this->matches[$match_id];
	}
}