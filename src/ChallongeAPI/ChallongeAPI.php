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

use ChallongeAPI\Objects\Attachment;
use ChallongeAPI\Objects\AttachmentList;
use ChallongeAPI\Objects\Match;
use ChallongeAPI\Objects\MatchList;
use ChallongeAPI\Objects\Participant;
use ChallongeAPI\Objects\ParticipantList;
use ChallongeAPI\Objects\Tournament;
use ChallongeAPI\Objects\TournamentList;

use ChallongeAPI\Exceptions\SettingsException;


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

	const
		SET_API_KEY         = 'SET_API_KEY',
		SET_VERIFY_SSL      = 'SET_VERIFY_SSL',
		SET_BASE_API_URL    = 'SET_BASE_API_URL',
		SET_RESPONSE_FORMAT = 'SET_RESPONSE_FORMAT',
		SET_USE_DUMMYDATA   = 'SET_USE_DUMMYDATA',
		SET_SAVE_DUMMYDATA  = 'SET_SAVE_DUMMYDATA';


	/** @var array */
	protected $settings = [
		self::SET_API_KEY         => null,
		self::SET_BASE_API_URL    => 'https://api.challonge.com',
		self::SET_RESPONSE_FORMAT => 'json',
	];

	/**
	 * List of settings keys that are required when
	 * initializing the library.
	 *
	 * @var array
	 */
	protected $required_settings = [
		self::SET_API_KEY,
	];

	/**
	 * List of settings keys that are allowed when
	 * initializing the library.
	 *
	 * @var array
	 */
	protected $allowed_settings = [
		self::SET_API_KEY,
		self::SET_VERIFY_SSL,
		self::SET_USE_DUMMYDATA,
		self::SET_SAVE_DUMMYDATA,
	];


	/** @var string */
	protected $endpoint;

	/** @var array */
	protected $query_data = array();

	/** @var array */
	protected $post_data = array();


	/** @var string */
	protected $result_data_raw;

	/** @var array */
	protected $result_data;

	/** @var array */
	protected $result_headers;

	/** @var int */
	protected $result_code;

	/** @var string */
	protected $request_method;


	/**
	 *  ChallongeAPI constructor.
	 *
	 * @param array $settings
	 *
	 * @throws SettingsException
	 */
	public function __construct( array $settings )
	{
		foreach ($this->required_settings as $key)
			if (isset($settings[$key]) === false)
				throw new SettingsException("Required settings parameter '$key' was not specified!");

		foreach ($this->allowed_settings as $key)
			if (isset($settings[$key]))
				$this->settings[$key] = $settings[$key];
	}


	/**
	 *   Returns vaue of requested key from settings.
	 *
	 * @param string     $name
	 * @param mixed|null $defaultValue
	 *
	 * @return mixed
	 */
	public function getSetting( string $name, $defaultValue = null )
	{
		return $this->isSettingSet($name)
			? $this->settings[$name]
			: $defaultValue;
	}

	/**
	 *   Sets new value for specified key in settings.
	 *
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return ChallongeAPI
	 *
	 */
	public function setSetting( string $name, $value ): self
	{
		$this->settings[$name] = $value;

		return $this;
	}

	/**
	 *   Sets new values for specified set of keys in settings.
	 *
	 * @param array $values
	 *
	 * @return ChallongeAPI
	 */
	public function setSettings( array $values ): self
	{
		foreach ($values as $name => $value)
			$this->setSetting($name, $value);

		return $this;
	}

	/**
	 *   Checks if specified settings key is set.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isSettingSet( string $name ): bool
	{
		return isset($this->settings[$name]) && !is_null($this->settings[$name]);
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
	 * @throws Exceptions\RequestException
	 * @throws Exceptions\ServerException
	 */
	final protected function makeCall( $method = "GET" )
	{
		$url = $this->_getUrl();

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);

		//  If you're having problems with API requests (mainly on localhost)
		//  change this cURL option to false
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getSetting(self::SET_VERIFY_SSL));

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
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($ch, CURLOPT_POSTFIELDS,
				http_build_query($this->post_data));
		}
		elseif($method == self::METHOD_DELETE)
		{
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}
		else
			throw new Exceptions\RequestException('Invalid method selected');

		$this->request_method  = $method;

		if ($this->getSetting(self::SET_USE_DUMMYDATA, false))
		{
			//  TODO: different behaviour with dummydata saving enabled
			$this->_loadDummyData($headers, $response, $response_code);
		}

		if (isset($headers) === false && isset($response) === false && isset($response_code) === false)
		{
			$raw_data = curl_exec($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

			$headers = $this->parseHeaders(substr($raw_data, 0, $header_size));
			$response = substr($raw_data, $header_size);
			$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}

		if (($curl_errno = curl_errno($ch)) !== 0)
		{
			$curl_error = curl_error($ch);
			throw new Exceptions\RequestException('cURL error ocurred: ' . $curl_error, $curl_errno);
		}

		curl_close($ch);

		$this->result_data_raw = $response;
		$this->result_data     = json_decode($response, true);
		$this->result_code     = $response_code;
		$this->result_headers  = $headers;

		if ($this->getSetting(self::SET_SAVE_DUMMYDATA, false))
			$this->_saveDummyData();

		$this->query_data      = [];
		$this->post_data       = [];

		if ($response_code == 500)
		{
			throw new Exceptions\ServerException('Internal server error.');
		}
		elseif ($response_code == 422)
		{
			$info = implode(', ', $this->result_data['errors']);
			throw new Exceptions\RequestException("Request data validation error: {$info}.");
		}
		elseif ($response_code == 406)
		{
			throw new Exceptions\RequestException('Request format not supported.');
		}
		elseif ($response_code == 404)
		{
			throw new Exceptions\RequestException('Resource not found.');
		}
		elseif ($response_code == 401)
		{
			throw new Exceptions\RequestException('Unauthorized.');
		}
		elseif ($response_code == 400)
		{
			throw new Exceptions\RequestException('Bad request.');
		}

		if (isset($this->result_data->errors) && !empty($this->result_data->errors))
			throw new Exceptions\RequestException(reset($this->result_data->errors));
	}

	public static function parseHeaders( $requestHeaders ): array
	{
		$r = array();
		foreach (explode(PHP_EOL, $requestHeaders) as $line)
		{
			if (strpos($line, ':'))
			{
				$e = explode(": ", $line);
				$r[$e[0]] = @$e[1];
			}
			elseif (strlen($line))
				$r[] = $line;
		}

		return $r;
	}

	protected function _getUrl()
	{
		$baseUrl  = $this->getSetting(self::SET_BASE_API_URL);
		$version  = self::API_VERSION;
		$endpoint = $this->endpoint;
		$format   = strtolower($this->getSetting(self::SET_RESPONSE_FORMAT));
		$query    = "?api_key={$this->getSetting(self::SET_API_KEY)}" . (!empty($this->query_data) ? '&' . http_build_query($this->query_data) : '' );

		return "$baseUrl/$version/$endpoint.$format$query";
	}

	protected function _loadDummyData( &$headers, &$response, &$response_code )
	{
		$data = @file_get_contents($this->_getDummyDataFilename());
		$data = unserialize($data);

		if (!$data || empty($data))
			throw new Exceptions\RequestException("DummyData file failed to be opened.");

		$headers = $data['headers'];
		$response = $data['response'];
		$response_code = $data['code'];
	}

	protected function _saveDummyData()
	{
		file_put_contents($this->_getDummyDataFilename(), serialize([
			'headers'  => $this->result_headers,
			'response' => $this->result_data_raw,
			'code'     => $this->result_code,
		]));
	}

	protected function _getDummyDataFilename()
	{
		$method   = $this->request_method;
		$version  = self::API_VERSION;
		$endpoint = str_replace([ '/', '.' ], [ '-', '' ], $this->endpoint);
		$format   = strtolower($this->getSetting(self::SET_RESPONSE_FORMAT));
		$query    = str_replace([ '&', '%26', '=', '%3D' ], [ '_', '_', '-', '-' ], !empty($this->query_data) ? '-' . http_build_query($this->query_data) : '' );
		$data     = !empty($this->post_data) ? '-' . md5(http_build_query($this->post_data)) : '';

		return __DIR__ . "/../../tests/DummyData/{$method}_$version-$endpoint$query$data.$format";
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
	 * @param string|null    $subdomain
	 * @param string|null    $state
	 * @param string|null    $type
	 * @param \DateTime|null $created_after
	 * @param \DateTime|null $created_before
	 *
	 * @return TournamentList
	 * @link http://api.challonge.com/v1/documents/tournaments/index
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
	 * @param array $data
	 *
	 * @return Tournament
	 * @link http://api.challonge.com/v1/documents/tournaments/create
	 */
	public function tCreate( array $data ): Tournament
	{
		$this->setEndpoint('tournaments');

		if (!isset($data['tournament']))
			$data = array( 'tournament' => $data );
		$this->setData($data);

		$this->makeCall('POST');
		return new Tournament($this->result());
	}

	/**
	 *   (Show) Retrieve a single tournament record created with your account.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param bool        $include_participants
	 * @param bool        $include_matches
	 *
	 * @return Tournament
	 * @link http://api.challonge.com/v1/documents/tournaments/show
	 */
	public function tGet( $tournament_url, string $subdomain = null, bool $include_participants = false, bool $include_matches = false ): Tournament
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url);

		$this->addQuery('include_participants', $include_participants);
		$this->addQuery('include_matches', $include_matches);

		$this->makeCall();
		return new Tournament($this->result());
	}

	/**
	 *   (Update) Update a tournament's attributes.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param array       $data
	 *
	 * @return array
	 * @link http://api.challonge.com/v1/documents/tournaments/update
	 */
	public function tEdit( $tournament_url, string $subdomain = null, array $data )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url);

		if (!isset($data['tournament']))
			$data = array( 'tournament' => $data );
		$this->setData($data);

		$this->makeCall('PUT');
		return $this->result();
	}

	/**
	 *   (Destroy) Deletes a tournament along with all its associated records. There is no undo, so use with care!
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 *
	 * @return array
	 * @link http://api.challonge.com/v1/documents/tournaments/destroy
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
	 * Marks participants who have not checked in as inactive.
	 * Moves inactive participants to bottom seeds (ordered by original seed).
	 * Transitions the tournament state from 'checking_in' to 'checked_in'
	 *
	 * NOTE: Checked in participants on the waiting list will be promoted if slots become available.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param bool        $include_participants
	 * @param bool        $include_matches
	 *
	 * @return array
	 * @link http://api.challonge.com/v1/documents/tournaments/process_check_ins
	 */
	public function tProcessCheckins( $tournament_url, string $subdomain = null, bool $include_participants = false, bool $include_matches = false )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/process_check_ins');

		$this->addQuery('include_participants', $include_participants);
		$this->addQuery('include_matches', $include_matches);

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 *   (Abort Check-in) When your tournament is in a 'checking_in' or 'checked_in' state, there's no way to edit the tournament's start time (start_at) or check-in duration (check_in_duration). You must first abort check-in, then you may edit those attributes.
	 *
	 * Makes all participants active and clears their checked_in_at times.
	 * Transitions the tournament state from 'checking_in' or 'checked_in' to 'pending'
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param bool        $include_participants
	 * @param bool        $include_matches
	 *
	 * @return array
	 * @link http://api.challonge.com/v1/documents/tournaments/abort_check_in
	 */
	public function tAbortCheckin( $tournament_url, string $subdomain = null, bool $include_participants = false, bool $include_matches = false )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/abort_check_in');

		$this->addQuery('include_participants', $include_participants);
		$this->addQuery('include_matches', $include_matches);

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 *   (Start) Start a tournament, opening up first round matches for score reporting. The tournament must have at least 2 participants.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param bool        $include_participants
	 * @param bool        $include_matches
	 *
	 * @return array
	 * @link http://api.challonge.com/v1/documents/tournaments/start
	 */
	public function tStart( $tournament_url, string $subdomain = null, bool $include_participants = false, bool $include_matches = false )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/start');

		$this->addQuery('include_participants', $include_participants);
		$this->addQuery('include_matches', $include_matches);

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 *   (Finalize) Finalize a tournament that has had all match scores submitted, rendering its results permanent.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param bool        $include_participants
	 * @param bool        $include_matches
	 *
	 * @return array
	 * @link http://api.challonge.com/v1/documents/tournaments/finalize
	 */
	public function tFinalize( $tournament_url, string $subdomain = null, bool $include_participants = false, bool $include_matches = false )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/finalize');

		$this->addQuery('include_participants', $include_participants);
		$this->addQuery('include_matches', $include_matches);

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 *   (Reset) Reset a tournament, clearing all of its scores and attachments. You can then add/remove/edit participants before starting the tournament again.
	 *
	 * @param string|int $tournament_url
	 * @param string     $subdomain
	 * @param bool       $include_participants
	 * @param bool       $include_matches
	 *
	 * @return array
	 * @link http://api.challonge.com/v1/documents/tournaments/reset
	 */
	public function tReset( $tournament_url, string $subdomain = null, bool $include_participants = false, bool $include_matches = false )
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/reset');

		$this->addQuery('include_participants', $include_participants);
		$this->addQuery('include_matches', $include_matches);

		$this->makeCall('POST');
		return $this->result();
	}

	/**
	 * @param string|int $tournament_url
	 * @param string     $subdomain
	 *
	 * @return ParticipantList
	 */
	public function tStandings( $tournament_url, string $subdomain = null ): ParticipantList
	{
		$participant_list = $this->pList($tournament_url, $subdomain);

		/**
		 * @var Participant $p1
		 * @var Participant $p2
		 */
		uasort($participant_list->participants, function( $p1, $p2 ) {
			if ($p1->final_rank == $p2->final_rank)
				return 0;

			return ($p1->final_rank < $p2->final_rank) ? -1 : 1;
		});

		return $participant_list;
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
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 *
	 * @return ParticipantList
	 * @link http://api.challonge.com/v1/documents/participants/index
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
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param array       $data
	 *
	 * @return Participant
	 * @link http://api.challonge.com/v1/documents/participants/create
	 */
	public function pAdd( $tournament_url, string $subdomain = null, array $data ): Participant
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
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param array       $data
	 *
	 * @return ParticipantList
	 * @link http://api.challonge.com/v1/documents/participants/bulk_add
	 */
	public function pBulkAdd( $tournament_url, string $subdomain = null, array $data ): ParticipantList
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants/bulk_add');

		if (!isset($data->participant))
			$data = array( 'participant' => $data );
		$this->setData($data);

		$this->makeCall('POST');
		return new ParticipantList($this->result());
	}

	/**
	 *   (Show) Retrieve a single participant record for a tournament.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $participant_id
	 * @param bool        $include_matches
	 *
	 * @return Participant
	 * @link http://api.challonge.com/v1/documents/participants/show
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
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $participant_id
	 * @param array       $data
	 *
	 * @return Participant
	 * @link http://api.challonge.com/v1/documents/participants/update
	 */
	public function pEdit( $tournament_url, string $subdomain = null, int $participant_id, array $data ): Participant
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants/' . $participant_id);

		if (!isset($data->participant))
			$data = array( 'participant' => $data );
		$this->setData($data);

		$this->makeCall('PUT');
		return new Participant($this->result());
	}

	/**
	 *   (Check In) Checks a participant in, setting checked_in_at to the current time.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $participant_id
	 *
	 * @return Participant
	 * @link http://api.challonge.com/v1/documents/participants/check_in
	 */
	public function pCheckIn( $tournament_url, string $subdomain = null, int $participant_id ): Participant
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants/' . $participant_id . '/check_in');

		$this->makeCall('POST');
		return new Participant($this->result());
	}

	/**
	 *   (Undo Check In) Marks a participant as having not checked in, setting checked_in_at to nil.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $participant_id
	 *
	 * @return Participant
	 * @link http://api.challonge.com/v1/documents/participants/undo_check_in
	 */
	public function pUndoCheckIn( $tournament_url, string $subdomain = null, int $participant_id  ): Participant
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants/' . $participant_id . '/undo_check_in');

		$this->makeCall('POST');
		return new Participant($this->result());
	}

	/**
	 *   (Destroy) If the tournament has not started, delete a participant, automatically filling in the abandoned seed number.
	 *   If tournament is underway, mark a participant inactive, automatically forfeiting his/her remaining matches.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $participant_id
	 *
	 * @return Participant
	 * @link http://api.challonge.com/v1/documents/participants/destroy
	 */
	public function pDelete( $tournament_url, string $subdomain = null, int $participant_id  ): Participant
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants/' . $participant_id);

		$this->makeCall('DELETE');
		return new Participant($this->result());
	}

	/**
	 *   (Randomize) Randomize seeds among participants.
	 *
	 * Only applicable before a tournament has started.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 *
	 * @return ParticipantList
	 * @link http://api.challonge.com/v1/documents/participants/randomize
	 */
	public function pRandomize( $tournament_url, string $subdomain = null ): ParticipantList
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/participants/randomize');

		$this->makeCall('POST');
		return new ParticipantList($this->result());
	}


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
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param string      $state all, pending, open, complete
	 * @param int         $participant_id
	 *
	 * @return MatchList
	 * @link http://api.challonge.com/v1/documents/matches/index
	 */
	public function mList( $tournament_url, string $subdomain = null, $state = null, int $participant_id = null ): MatchList
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/matches');

		$this->addQuery('state', $state);
		$this->addQuery('participant_id', $participant_id);

		$this->makeCall();
		return new MatchList($this->result());
	}

	/**
	 *   (Show) Retrieve a single match record for a tournament.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $match_id
	 * @param bool        $include_attachments
	 *
	 * @return Match
	 * @link http://api.challonge.com/v1/documents/matches/show
	 */
	public function mGet( $tournament_url, string $subdomain = null, int $match_id, bool $include_attachments = false ): Match
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . "/matches/$match_id");

		$this->addQuery('include_attachments', $include_attachments);

		$this->makeCall();

		return new Match($this->result());
	}

	/**
	 *   (Reopen) Reopens a match.
	 *
	 * Reopens a match that was marked completed, automatically resetting matches that follow it
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $match_id
	 *
	 * @return Match
	 * @link http://api.challonge.com/v1/documents/matches/reopen
	 */
	public function mReopen( $tournament_url, string $subdomain = null, int $match_id ): Match
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . "/matches/$match_id/reopen");

		$this->makeCall('POST');
		return new Match($this->result());
	}

	/**
	 *   (Update) Update/submit the score(s) for a match.
	 *
	 * If you're updating winner_id, scores_csv must also be provided.
	 * You may, however, update score_csv without providing winner_id for live score updates.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $match_id
	 * @param array       $data
	 *
	 * @return Match
	 * @link http://api.challonge.com/v1/documents/matches/update
	 */
	public function mEdit( $tournament_url, string $subdomain = null, int $match_id, array $data ): Match
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . "/matches/$match_id");

		if (!isset($data['match']))
			$data = array( 'match' => $data );
		$this->setData($data);

		$this->makeCall('PUT');
		return new Match($this->result());
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
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $match_id
	 *
	 * @return AttachmentList
	 * @link http://api.challonge.com/v1/documents/match_attachments/index
	 */
	public function aList( $tournament_url, string $subdomain = null, int $match_id ): AttachmentList
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/matches/' . $match_id . '/attachments');

		$this->makeCall();
		return new AttachmentList($this->result());
	}

	/**
	 *   (Create) Add a file, link, or text attachment to a match.
	 *
	 * NOTE: The associated tournament's "accept_attachments" attribute
	 * must be true for this action to succeed.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $match_id
	 * @param array       $data
	 *
	 * @return Attachment
	 * @link http://api.challonge.com/v1/documents/match_attachments/create
	 */
	public function aAdd( $tournament_url, string $subdomain = null, int $match_id, array $data ): Attachment
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/matches/' . $match_id . '/attachments');

		if (!isset($data->match_attachment))
			$data = array( 'match_attachment' => $data );
		$this->setData($data);

		$this->makeCall('POST');
		return new Attachment($this->result());
	}

	/**
	 *   (Show) Retrieve a single match attachment record.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $match_id
	 * @param int         $attachment_id
	 *
	 * @return Attachment
	 * @link http://api.challonge.com/v1/documents/match_attachments/show
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
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $match_id
	 * @param int         $attachment_id
	 * @param array       $data
	 *
	 * @return Attachment
	 * @link http://api.challonge.com/v1/documents/match_attachments/update
	 */
	public function aEdit( $tournament_url, string $subdomain = null, int $match_id, int $attachment_id, array $data ): Attachment
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/matches/' . $match_id . '/attachments/' . $attachment_id);

		if (!isset($data->match_attachment))
			$data = array( 'match_attachment' => $data );
		$this->setData($data);

		$this->makeCall('PUT');
		return new Attachment($this->result());
	}

	/**
	 *   (Destroy) Delete a match attachment.
	 *
	 * @param string|int  $tournament_url
	 * @param string|null $subdomain
	 * @param int         $match_id
	 * @param int         $attachment_id
	 *
	 * @return Attachment
	 * @link http://api.challonge.com/v1/documents/match_attachments/destroy
	 */
	public function aDelete( $tournament_url, string $subdomain = null, int $match_id, int $attachment_id ): Attachment
	{
		$this->setEndpoint('tournaments/' . ( !is_null($subdomain) ? "$subdomain-" : '' ) . $tournament_url . '/matches/' . $match_id . '/attachments/' . $attachment_id);

		$this->makeCall('DELETE');
		return new Attachment($this->result());
	}
}