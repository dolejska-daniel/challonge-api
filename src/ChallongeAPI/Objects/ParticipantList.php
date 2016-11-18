<?php

namespace ChallongeAPI\Objects;

/**
 *   Class ParticipantList
 *
 * @property Participant[] $participants
 * @property int           $count
 *
 * @package ChallongeAPI\Objects
 */
class ParticipantList implements IApiObjectList
{
	/**
	 *   ParticipantList constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data )
	{
		foreach ($data as $participant_data)
		{
			$p = new Participant($participant_data);
			$this->participants[$p->id] = $p;
		}

		$this->count = count($data);
	}


	/** @var int $count */
	public $count;

	/** @var Participant[] $participants */
	public $participants;

	/**
	 *   Gets all the participants.
	 *
	 * @return Participant[]
	 */
	public function getParticipants(): array
	{
		return $this->participants;
	}

	/**
	 *   Gets tournament participant by it's unique identifier (id).
	 *
	 * @param int $participant_id
	 *
	 * @return Participant
	 */
	public function getParticipantById( int $participant_id ): Participant
	{
		return $this->participants[$participant_id];
	}
}