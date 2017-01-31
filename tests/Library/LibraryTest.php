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

use PHPUnit\Framework\TestCase;

use ChallongeAPI\ChallongeAPI;

use ChallongeAPI\Exceptions\SettingsException;


class LibraryTest extends TestCase
{
	public function testInit()
	{
		$api = new ChallongeAPI([
			'api_key' => getenv('API_KEY'),
		]);

		$this->assertInstanceOf(ChallongeAPI::class, $api);

		return $api;
	}

	public function testRequiredSettings()
	{
		$this->expectException(SettingsException::class);
		$this->expectExceptionMessage("Required settings parameter");

		new ChallongeAPI([]);
	}

	public function testMakeCall_InvalidMethod()
	{
		//  TODO
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function testMakeCall_500()
	{
		//  TODO
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function testMakeCall_422()
	{
		//  TODO
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function testMakeCall_406()
	{
		//  TODO
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function testMakeCall_401()
	{
		//  TODO
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function testMakeCall_400()
	{
		//  TODO
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function testMakeCall_4xx()
	{
		//  TODO
		$this->markTestIncomplete('This test has not been implemented yet.');
	}
}
