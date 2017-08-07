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

declare(strict_types=1);

use ChallongeAPI\Objects\Participant;
use ChallongeAPI\Objects\ParticipantList;
use PHPUnit\Framework\TestCase;

use ChallongeAPI\ChallongeAPI;

use ChallongeAPI\Exceptions\SettingsException;


class ParticipantEndpointTest extends TestCase
{

	/** @var string */
	public static $tournament_id  = 'challongeapi_dummy';

	/** @var string */
	public static $subdomain      = null;

	/** @var int */
	public static $match_id       = 94562401;

	/** @var int */
	public static $participant_id = null;

	/** @var ParticipantList */
	public static $participants   = null;


	public function testInit()
	{
		$api = new ChallongeAPI([
			ChallongeAPI::SET_API_KEY        => getenv('API_KEY'),
			ChallongeAPI::SET_USE_DUMMYDATA  => true,
			ChallongeAPI::SET_SAVE_DUMMYDATA => false,
		]);

		$this->assertInstanceOf(ChallongeAPI::class, $api);

		return $api;
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_Create( ChallongeAPI $api )
	{
		$participant = $api->pAdd(self::$tournament_id, self::$subdomain, [
			'name' => 'Dummy 4',
			'misc' => 'participant 4',
		]);

		$this->assertInstanceOf(Participant::class, $participant);
		$this->assertSame('Dummy 4', $participant->name);
		$this->assertSame('participant 4', $participant->misc);

		self::$participant_id = $participant->id;
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 *
	 * @return ParticipantList
	 */
	public function testEndpoint_Index( ChallongeAPI $api )
	{
		$participants = $api->pList(self::$tournament_id, self::$subdomain);

		$this->assertInstanceOf(ParticipantList::class, $participants);

		return $participants;
	}

	/**
	 * @depends testEndpoint_Index
	 *
	 * @param ParticipantList $participants
	 */
	public function testEndpoint_Index_Count( ParticipantList $participants )
	{
		$this->assertCount($participants->count, $participants->getParticipants());
	}

	/**
	 * @depends testEndpoint_Index
	 *
	 * @param ParticipantList $participants
	 */
	public function testEndpoint_Index_getMatches( ParticipantList $participants )
	{
		$this->assertSame($participants->participants, $participants->getParticipants());
	}

	/**
	 * @depends testEndpoint_Index
	 *
	 * @param ParticipantList $participants
	 */
	public function testEndpoint_Index_getMatchById( ParticipantList $participants )
	{
		$this->assertSame($participants->participants[self::$participant_id], $participants->getParticipantById(self::$participant_id));
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_BulkAdd( ChallongeAPI $api )
	{
		$this->markTestIncomplete('This test has not been implemented yet.');

		$participants = $api->pBulkAdd(self::$tournament_id, self::$subdomain, [
			[
				'name' => 'Bulk Dummy 1',
				'misc' => 'participant 5',
			],
			[
				'name' => 'Bulk Dummy 2',
				'misc' => 'participant 6',
			],
			[
				'name' => 'Bulk Dummy 3',
				'misc' => 'participant 7',
			],
		]);

		$this->assertInstanceOf(ParticipantList::class, $participants);
		$this->assertCount(3, $participants->getParticipants());

		self::$participants = $participants;
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_Show( ChallongeAPI $api )
	{
		$participants = $api->pGet(self::$tournament_id, self::$subdomain, self::$participant_id);

		$this->assertInstanceOf(Participant::class, $participants);
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_Update( ChallongeAPI $api )
	{
		$participants = $api->pEdit(self::$tournament_id, self::$subdomain, self::$participant_id, [
			'name' => 'Dummy 4, edited',
		]);

		$this->assertInstanceOf(Participant::class, $participants);
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_CheckIn( ChallongeAPI $api )
	{
		$participants = $api->pCheckIn(self::$tournament_id, self::$subdomain, self::$participant_id);

		$this->assertInstanceOf(Participant::class, $participants);
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_UndoCheckIn( ChallongeAPI $api )
	{
		$participants = $api->pUndoCheckIn(self::$tournament_id, self::$subdomain, self::$participant_id);

		$this->assertInstanceOf(Participant::class, $participants);
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_Destroy( ChallongeAPI $api )
	{
		$participants = $api->pDelete(self::$tournament_id, self::$subdomain, self::$participant_id);

		$this->assertInstanceOf(Participant::class, $participants);

		/*
		foreach (self::$participants->getParticipants() as $p)
			$api->pDelete(self::$tournament_id, self::$subdomain, $p->id);
		*/
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_Randomize( ChallongeAPI $api )
	{
		$participants = $api->pRandomize(self::$tournament_id, self::$subdomain);

		$this->assertInstanceOf(ParticipantList::class, $participants);
	}
}
