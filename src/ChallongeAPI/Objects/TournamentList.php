<?php

namespace ChallongeAPI\Objects;

/**
 *   Class TournamentList
 *
 * @property Tournament[] $tournaments
 * @property int          $count
 *
 * @package ChallongeAPI\Objects
 */
class TournamentList implements IApiObjectList
{
	/**
	 *   TournamentList constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data )
	{
		foreach ($data as $tournament_data)
		{
			$t = new Tournament($tournament_data);
			$this->tournaments[$t->id] = $t;

			$this->tournaments_urls_list[$t->url] = $t->id;
		}

		$this->count = count($data);
	}


	/** @var int $count */
	public $count;

	/** @var Tournament[] $tournaments */
	public $tournaments;

	/**
	 *   Gets all the tournaments.
	 *
	 * @return Tournament[]
	 */
	public function getTournaments(): array
	{
		return $this->tournaments;
	}

	/**
	 *   Gets tournament by it's unique identifier (id).
	 *
	 * @param int $tournament_id
	 *
	 * @return Tournament
	 */
	public function getTournamentById( int $tournament_id ):  Tournament
	{
		return $this->tournaments[$tournament_id];
	}

	/** @var array $tournaments_urls_list */
	protected $tournaments_urls_list = [];

	/**
	 *   Gets tournament by its unique url (url).
	 *
	 * @param string $tournament_url
	 *
	 * @return Tournament
	 */
	public function getTournamentByUrl( string $tournament_url ): Tournament
	{
		return $this->tournaments[$this->tournaments_urls_list[$tournament_url]];
	}
}