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

declare(strict_types = 1);

use ChallongeAPI\Objects\Match;
use ChallongeAPI\Objects\MatchList;
use PHPUnit\Framework\TestCase;

use ChallongeAPI\ChallongeAPI;

use ChallongeAPI\Exceptions\SettingsException;


class MatchEndpointTest extends TestCase
{
	public static $tournament_id = 'challongeapi_dummy';

	public static $subdomain     = null;

	public static $match_id      = 94562401;

	public static $participant1  = 59610274;

	public static $participant2  = 59610275;


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
	public function testEndpoint_Index( ChallongeAPI $api )
	{
		$matches = $api->mList(self::$tournament_id, self::$subdomain);

		$this->assertInstanceOf(MatchList::class, $matches);
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_Show( ChallongeAPI $api )
	{
		$match = $api->mGet(self::$tournament_id, self::$subdomain, self::$match_id);

		$this->assertInstanceOf(Match::class, $match);
		$this->assertSame(self::$match_id, $match->id);
		$this->assertSame(self::$match_id, $match->getData()['id']);
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_Update( ChallongeAPI $api )
	{
		$match = $api->mEdit(self::$tournament_id, self::$subdomain, self::$match_id, [
			'scores_csv' => '1-0,0-1,1-0',
			'winner_id'  => self::$participant1,
		]);

		$this->assertInstanceOf(Match::class, $match);
		$this->assertSame(self::$match_id, $match->id);
		$this->assertSame(self::$match_id, $match->getData()['id']);
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_Reopen( ChallongeAPI $api )
	{
		$match = $api->mReopen(self::$tournament_id, self::$subdomain, self::$match_id);

		$this->assertInstanceOf(Match::class, $match);
		$this->assertSame(self::$match_id, $match->id);
		$this->assertSame(self::$match_id, $match->getData()['id']);
	}
}
