<?php
/**
 * Copyright (C) 2016  Daniel Dolejška
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace ChallongeAPI;

use ChallongeAPI\Exceptions;
use ChallongeAPI\Objects\Attachment;
use ChallongeAPI\Objects\AttachmentList;
use ChallongeAPI\Objects\Match;
use ChallongeAPI\Objects\MatchList;
use ChallongeAPI\Objects\Participant;
use ChallongeAPI\Objects\ParticipantList;
use ChallongeAPI\Objects\Tournament;
use ChallongeAPI\Objects\TournamentList;

/**
 *   Class ChallongeAPI
 *
 * @package ChallongeAPI
 */
class ChallongeAPI
{
	/** API used version constant. */
	const
		API_VERSION = 'v1';

	/** Constants for cURL requests. */
	const
		METHOD_GET      = 'GET',
		METHOD_POST     = 'POST',
		METHOD_PUT      = 'PUT',
		METHOD_DELETE   = 'DELETE';


	/** @var array */
	protected $settings = array(
		'api_key'   => null,
		'api_url'   => 'https://api.challonge.com',
		'format'    => 'json',
	);

	/** @var string */
	protected $endpoint;

	/** @var array */
	protected $query_data = array();

	/** @var array */
	protected $post_data = array();

	/** @var array */
	protected $result_data;


	/**
	 *  ChallongeAPI constructor.
	 *
	 * @param array $settings
	 *
	 * @throws Exceptions\GeneralException
	 */
	public function __construct( array $settings )
	{
		$required_settings = [
			'api_key',
		];

		foreach ($required_settings as $key)
			if (array_search($key, array_keys($settings), true) === false)
				throw new Exceptions\GeneralException("Required settings parameter '$key' was not specified!");

		$allowed_settings = [
			'api_key',
		];

		foreach ($allowed_settings as $key)
			if (array_search($key, array_keys($settings), true) !== false)
				$this->settings[$key] = $settings[$key];
	}


	/**
	 *   Sets call target for script.
	 *
	 * @param $endpoint
	 *
	 * @return $this
	 */
	protected function setEndpoint( $endpoint )
	{
		$this->endpoint = $endpoint;
		return $this;
	}

	/**
	 *   Adds GET parameter to called URL.
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return $this
	 */
	protected function addQuery( $name, $value )
	{
		if ($value !== null)
			$this->query_data[$name] = $value;

		return $this;
	}

	/**
	 *   Sets POST/PUT data.
	 *
	 * @param array|\Traversable $data
	 *
	 * @return $this
	 */
	protected function setData( $data )
	{
		$this->post_data = $data;
		return $this;
	}

	/**
	 *   Adds POST/PUT data to array.
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return $this
	 */
	protected function addData( $name, $value )
	{
		$this->post_data[$name] = $value;
		return $this;
	}

	/**
	 *   Makes call to ChallongeAPI.
	 *
	 * @param string $method
	 *
	 * @throws Exceptions\APIException
	 * @throws Exceptions\GeneralException
	 */
	final protected function makeCall( $method = "GET" )
	{
		$url = $this->settings['api_url'] . '/' . self::API_VERSION . "/" . $this->endpoint . '.' . strtolower($this->settings['format'])
			. "?api_key={$this->settings['api_key']}" . (!empty($this->query_data) ? '&' . http_build_query($this->query_data) : '' );

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		//  If you're having problems with API requests (mainly on localhost)
		//  change this cURL option to false
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

		if ($method == self::METHOD_GET)
		{
			curl_setopt($ch, CURLOPT_URL, $url);
		}
		elseif($method == self::METHOD_POST)
		{
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
				http_build_query($this->post_data));
		}
		elseif($method == self::METHOD_PUT)
		{
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_PUT, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
				http_build_query($this->post_data));
		}
		elseif($method == self::METHOD_DELETE)
		{
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}
		else
			throw new Exceptions\GeneralException('Invalid method selected');

		$response = curl_exec($ch);
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($response_code == 500)
		{
			throw new Exceptions\APIException('Internal server error');
		}
		elseif ($response_code == 422)
		{
			throw new Exceptions\APIException('Validation error');
		}
		elseif ($response_code == 406)
		{
			throw new Exceptions\APIException('Request not supported');
		}
		elseif ($response_code == 404)
		{
			throw new Exceptions\APIException('Resource not found');
		}
		elseif ($response_code == 401)
		{
			throw new Exceptions\APIException('Forbidden');
		}
		elseif ($response_code == 400)
		{
			throw new Exceptions\APIException('Bad request');
		}

		$this->result_data  = json_decode($response, true);
		$this->query_data   = array();

		curl_close ($ch);

		if (isset($this->result_data->errors) && !empty($this->result_data->errors))
			throw new Exceptions\APIException(reset($this->result_data->errors));
	}

	/**
	 *   Returns result data from call.
	 *
	 * @return array
	 */
	protected function result(): array
	{
		return $this->result_data;
	}


	/**
	 *  #################################
	 *  ##
	 *  ##   TOURNAMENTS
	 *  ##
	 *  #################################
	 */

	/**
	 *    (Index) Retrieve a set of tournaments created with your account.
	 *
	 * @link http://api.challonge.com/v1/documents/tournaments/index
	 *
	 * @param string|null    $subdomain
	 * @param string|null    $state
	 * @param string|null    $type
	 * @param \DateTime|null $created_after
	 * @param \DateTime|null $created_before
	 *
	 * @return TournamentList
	 */
	public function tList( string $subdomain = null, string $state = null, string $type = null, \DateTime $created_after = null, \DateTime $created_before = null ): TournamentList
	{
		$this->setEndpoint('tournaments');

		if (!is_null($state))
			$this->addQuery('state', $state);

		if (!is_null($type))
			$this->addQuery('type', $type);

		if (!is_null($created_after))
			$this->addQuery('created_after', $created_after->format('Y-m-d'));

		if (!is_null($created_before))
			$this->addQuery('created_before', $created_before->format('Y-m-d'));

		if (!is_null($subdomain))
			$this->addQuery('subdomain', $subdomain);

		$this->makeCall();
		return new TournamentList($this->result());
	}

	/**
	 *   (Create) Create a new tournament.
	 *
	 * @link http://api.challonge.com/v1/documents/tournaments/create
	 *
	 * @param array|\Traversable $data
	 *
	 * @return Tournament
	 */
	public function tCreate( $data ): Tournament
	{
		$this->setEndpoint('tournaments');

		if (!isset($data->tournament))
			$data = array( 'tournament' => $data );
		$this->setData($data);

		$this->makeCall('POST');
		return new Tournament($this->result());
	}

	/**
	 *   (Show) Retrieve a single tournament record created with your account.
	 *
	 * @link http://api.challonge.com/v1/documents/tournaments/show
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param bool        $include_participants
	 * @param bool        $include_matches
	 *
	 * @return Tournament
	 */
	public function tGet( $tournament_url, string $subdomain = null, bool $include_participants = false, bool $include_matches = false ): Tournament
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url);

		$this->addQuery('include_participants', $include_participants);
		$this->addQuery('include_maatches', $include_matches);

		$this->makeCall();
		return new Tournament($this->result());
	}

	/**
	 *   (Update) Update a tournament's attributes.
	 *
	 * @link http://api.challonge.com/v1/documents/tournaments/update
	 *
	 * @param string|int         $tournament_url
	 * @param string|null        $subdomain
	 * @param array|\Traversable $data
	 *
	 * @return array
	 */
	public function tEdit( $tournament_url, string $subdomain = null, $data )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url);

		if (!isset($data->tournament))
			$data = array( 'tournament' => $data );
		$this->setData($data);

		$this->makeCall('PUT');
		return $this->result();
	}

	/**
	 *   (Destroy) Deletes a tournament along with all its associated records. There is no undo, so use with care!
	 *
	 * @link http://api.challonge.com/v1/documents/tournaments/destroy
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 *
	 * @return array
	 */
	public function tDelete( $tournament_url, string $subdomain = null )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url);

		$this->makeCall('DELETE');
		return $this->result();
	}

	/**
	 *   (Process Check-ins) This should be invoked after a tournament's check-in window closes before the tournament is started.
	 *
	 *      Marks participants who have not checked in as inactive.
	 *      Moves inactive participants to bottom seeds (ordered by original seed).
	 *      Transitions the tournament state from 'checking_in' to 'checked_in'
	 *
	 *   NOTE: Checked in participants on the waiting list will be promoted if slots become available.
	 *
	 * @link http://api.challonge.com/v1/documents/tournaments/process_check_ins
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param bool        $include_participants
	 * @param bool        $include_matches
	 *
	 * @return array
	 */
	public function tProcessCheckins( $tournament_url, string $subdomain = null, bool $include_participants = false, bool $include_matches = false )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/process_check_ins');

		$this->addQuery('include_participants', $include_participants);
		$this->addQuery('include_maatches', $include_matches);

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 *   (Abort Check-in) When your tournament is in a 'checking_in' or 'checked_in' state, there's no way to edit the tournament's start time (start_at) or check-in duration (check_in_duration). You must first abort check-in, then you may edit those attributes.
	 *
	 *      Makes all participants active and clears their checked_in_at times.
	 *      Transitions the tournament state from 'checking_in' or 'checked_in' to 'pending'
	 *
	 * @link http://api.challonge.com/v1/documents/tournaments/abort_check_in
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param bool        $include_participants
	 * @param bool        $include_matches
	 *
	 * @return array
	 */
	public function tAbortCheckin( $tournament_url, string $subdomain = null, bool $include_participants = false, bool $include_matches = false )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/abort_check_in');

		$this->addQuery('include_participants', $include_participants);
		$this->addQuery('include_maatches', $include_matches);

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 *   (Start) Start a tournament, opening up first round matches for score reporting. The tournament must have at least 2 participants.
	 *
	 * @link http://api.challonge.com/v1/documents/tournaments/start
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param bool        $include_participants
	 * @param bool        $include_matches
	 *
	 * @return array
	 */
	public function tStart( $tournament_url, string $subdomain = null, bool $include_participants = false, bool $include_matches = false )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/start');

		$this->addQuery('include_participants', $include_participants);
		$this->addQuery('include_maatches', $include_matches);

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 *   (Finalize) Finalize a tournament that has had all match scores submitted, rendering its results permanent.
	 *
	 * @link http://api.challonge.com/v1/documents/tournaments/finalize
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param bool        $include_participants
	 * @param bool        $include_matches
	 *
	 * @return array
	 */
	public function tFinalize( $tournament_url, string $subdomain = null, bool $include_participants = false, bool $include_matches = false )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/finalize');

		$this->addQuery('include_participants', $include_participants);
		$this->addQuery('include_maatches', $include_matches);

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 *   (Reset) Reset a tournament, clearing all of its scores and attachments. You can then add/remove/edit participants before starting the tournament again.
	 *
	 * @link http://api.challonge.com/v1/documents/tournaments/reset
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param bool        $include_participants
	 * @param bool        $include_matches
	 *
	 * @return array
	 */
	public function tReset( $tournament_url, string $subdomain = null, bool $include_participants = false, bool $include_matches = false )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/reset');

		$this->addQuery('include_participants', $include_participants);
		$this->addQuery('include_maatches', $include_matches);

		$this->makeCall('POST');
		return $this->result();
	}


	/**
	 *  #################################
	 *  ##
	 *  ##   PARTICIPANTS
	 *  ##
	 *  #################################
	 */

	/**
	 *   (Index) Retrieve a tournament's participant list.
	 *
	 * @link http://api.challonge.com/v1/documents/participants/index
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 *
	 * @return ParticipantList
	 */
	public function pList( $tournament_url, string $subdomain = null ): ParticipantList
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants');

		$this->makeCall();
		return new ParticipantList($this->result());
	}

	/**
	 *   (Create) Add a participant to a tournament (up until it is started).
	 *
	 * @link http://api.challonge.com/v1/documents/participants/create
	 *
	 * @param string|int         $tournament_url
	 * @param string|null        $subdomain
	 * @param array|\Traversable $data
	 *
	 * @return Participant
	 */
	public function pAdd( $tournament_url, string $subdomain = null, $data ): Participant
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants');

		if (!isset($data->participant))
			$data = array( 'participant' => $data );
		$this->setData($data);

		$this->makeCall('POST');
		return new Participant($this->result());
	}

	/**
	 *   (Bulk Add) Bulk add participants to a tournament (up until it is started). If an invalid participant is detected,
	 *   bulk participant creation will halt and any previously added participants (from this API request) will be rolled back.
	 *
	 * @link http://api.challonge.com/v1/documents/participants/bulk_add
	 *
	 * @param string|int         $tournament_url
	 * @param string|null        $subdomain
	 * @param array|\Traversable $data
	 *
	 * @return array
	 */
	public function pBulkAdd( $tournament_url, string $subdomain = null, $data )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants/bulk_add');

		if (!isset($data->participant))
			$data = array( 'participant' => $data );
		$this->setData($data);

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 *   (Show) Retrieve a single participant record for a tournament.
	 *
	 * @link http://api.challonge.com/v1/documents/participants/show
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $participant_id
	 * @param bool        $include_matches
	 *
	 * @return Participant
	 */
	public function pGet( $tournament_url, string $subdomain = null, int $participant_id, bool $include_matches = false ): Participant
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants/' . $participant_id);

		$this->addQuery('include_matches', $include_matches);

		$this->makeCall();
		return new Participant($this->result());
	}

	/**
	 *   (Update) Update the attributes of a tournament participant.
	 *
	 * @link http://api.challonge.com/v1/documents/participants/update
	 *
	 * @param string|int         $tournament_url
	 * @param string|null        $subdomain
	 * @param int                $participant_id
	 * @param array|\Traversable $data
	 *
	 * @return array
	 */
	public function pEdit( $tournament_url, string $subdomain = null, int $participant_id, $data )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants/' . $participant_id);

		if (!isset($data->participant))
			$data = array( 'participant' => $data );
		$this->setData($data);

		$this->makeCall('PUT');
		return $this->result();
	}

	/**
	 *   (Check In) Checks a participant in, setting checked_in_at to the current time.
	 *
	 * @link http://api.challonge.com/v1/documents/participants/check_in
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $participant_id
	 *
	 * @return array
	 */
	public function pCheckIn( $tournament_url, string $subdomain = null, int $participant_id )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants/' . $participant_id . '/check_in');

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 *   (Undo Check In) Marks a participant as having not checked in, setting checked_in_at to nil.
	 *
	 * @link http://api.challonge.com/v1/documents/participants/undo_check_in
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $participant_id
	 *
	 * @return array
	 */
	public function pUndoCheckIn( $tournament_url, string $subdomain = null, int $participant_id  )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants/' . $participant_id . '/undo_check_in');

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 *   (Destroy) If the tournament has not started, delete a participant, automatically filling in the abandoned seed number.
	 *   If tournament is underway, mark a participant inactive, automatically forfeiting his/her remaining matches.
	 *
	 * @link http://api.challonge.com/v1/documents/participants/destroy
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $participant_id
	 *
	 * @return array
	 */
	public function pDelete( $tournament_url, string $subdomain = null, int $participant_id  )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants/' . $participant_id);

		$this->makeCall('DELETE');
		return $this->result();
	}

	/**
	 *   (Randomize) Randomize seeds among participants. Only applicable before a tournament has started.
	 *
	 * @link http://api.challonge.com/v1/documents/participants/randomize
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 *
	 * @return array
	 */
	public function pRandomize( $tournament_url, string $subdomain = null )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants/randomize');

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 *   (Randomize) Randomize seeds among participants. Only applicable before a tournament has started. Alias for pRandomize.
	 *
	 * @link http://api.challonge.com/v1/documents/participants/randomize
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 *
	 * @return array
	 */
	public function pShuffle( $tournament_url, string $subdomain = null ) { return $this->pRandomize(func_get_args()); }


	/**
	 *  #################################
	 *  ##
	 *  ##   MATCHES
	 *  ##
	 *  #################################
	 */

	/**
	 *   (Index) Retrieve a tournament's match list.
	 *
	 * @link http://api.challonge.com/v1/documents/matches/index
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param string      $state all, pending, open, complete
	 * @param int         $participant_id
	 *
	 * @return MatchList
	 */
	public function mList( $tournament_url, string $subdomain = null, $state = null, int $participant_id = null ): MatchList
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/matches');

		if (!is_null($state))
			$this->addQuery('state', $state);

		if (!is_null($participant_id))
			$this->addQuery('participant_id', $participant_id);

		$this->makeCall();
		return new MatchList($this->result());
	}

	/**
	 *   (Show) Retrieve a single match record for a tournament.
	 *
	 * @link http://api.challonge.com/v1/documents/matches/show
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $match_id
	 * @param bool        $include_attachments
	 *
	 * @return Match
	 */
	public function mGet( $tournament_url, string $subdomain = null, int $match_id, bool $include_attachments = false ): Match
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/matches/' . $match_id);

		$this->addQuery('include_attachments', $include_attachments);

		$this->makeCall();
		return new Match($this->result());
	}

	/**
	 *   (Update) Update/submit the score(s) for a match.
	 *
	 *   * If you're updating winner_id, scores_csv must also be provided. You may, however, update score_csv without providing winner_id for live score updates.
	 *
	 * @link http://api.challonge.com/v1/documents/matches/update
	 *
	 * @param string|int         $tournament_url
	 * @param string|null        $subdomain
	 * @param int                $match_id
	 * @param array|\Traversable $data
	 *
	 * @return array
	 */
	public function mEdit( $tournament_url, string $subdomain = null, int $match_id, $data )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/matches/' . $match_id);

		if (!isset($data->match))
			$data = array( 'match' => $data );
		$this->setData($data);

		$this->makeCall('PUT');
		return $this->result();
	}


	/**
	 *  #################################
	 *  ##
	 *  ##   ATTACHMENTS
	 *  ##
	 *  #################################
	 */

	/**
	 *   (Index) Retrieve a match's attachments.
	 *
	 * @link http://api.challonge.com/v1/documents/match_attachments/index
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $match_id
	 *
	 * @return AttachmentList
	 */
	public function aList( $tournament_url, string $subdomain = null, int $match_id ): AttachmentList
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/matches/' . $match_id . '/attachments');

		$this->makeCall();
		return new AttachmentList($this->result());
	}

	/**
	 *   (Create) Add a file, link, or text attachment to a match. NOTE: The associated tournament's "accept_attachments"
	 *   attribute must be true for this action to succeed.
	 *
	 * @link http://api.challonge.com/v1/documents/match_attachments/create
	 *
	 * @param string|int         $tournament_url
	 * @param string|null        $subdomain
	 * @param int                $match_id
	 * @param array|\Traversable $data
	 *
	 * @return array
	 */
	public function aAdd( $tournament_url, string $subdomain = null, int $match_id, $data )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/matches/' . $match_id . '/attachments');

		if (!isset($data->match_attachment))
			$data = array( 'match_attachment' => $data );
		$this->setData($data);

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 *   (Show) Retrieve a single match attachment record.
	 *
	 * @link http://api.challonge.com/v1/documents/match_attachments/show
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $match_id
	 * @param int         $attachment_id
	 *
	 * @return Attachment
	 */
	public function aGet( $tournament_url, string $subdomain = null, int $match_id, int $attachment_id ): Attachment
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/matches/' . $match_id . '/attachments/' . $attachment_id);

		$this->makeCall();
		return new Attachment($this->result());
	}

	/**
	 *   (Update) Update the attributes of a match attachment.
	 *
	 * @link http://api.challonge.com/v1/documents/match_attachments/update
	 *
	 * @param string|int         $tournament_url
	 * @param string|null        $subdomain
	 * @param int                $match_id
	 * @param int                $attachment_id
	 * @param array|\Traversable $data
	 *
	 * @return array
	 */
	public function aEdit( $tournament_url, string $subdomain = null, int $match_id, int $attachment_id, $data )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/matches/' . $match_id . '/attachments/' . $attachment_id);

		if (!isset($data->match_attachment))
			$data = array( 'match_attachment' => $data );
		$this->setData($data);

		$this->makeCall('PUT');
		return $this->result();
	}

	/**
	 *   (Destroy) Delete a match attachment.
	 *
	 * @link http://api.challonge.com/v1/documents/match_attachments/destroy
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $match_id
	 * @param int         $attachment_id
	 *
	 * @return array
	 */
	public function aDelete( $tournament_url, string $subdomain = null, int $match_id, int $attachment_id )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/matches/' . $match_id . '/attachments/' . $attachment_id);

		$this->makeCall('DELETE');
		return $this->result();
	}
}