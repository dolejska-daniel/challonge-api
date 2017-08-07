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

use ChallongeAPI\Objects\Attachment;
use ChallongeAPI\Objects\AttachmentList;
use PHPUnit\Framework\TestCase;

use ChallongeAPI\ChallongeAPI;

use ChallongeAPI\Exceptions\SettingsException;


class AttachmentEndpointTest extends TestCase
{
	/** @var string */
	public static $tournament_id = 'challongeapi_dummy';

	/** @var string */
	public static $subdomain     = null;

	/** @var int */
	public static $match_id      = 94678978;

	/** @var int */
	public static $attachment1_id = null;

	/** @var int */
	public static $attachment2_id = null;

	/** @var int */
	public static $attachment3_id = null;


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
	public function testEndpoint_Create_Text( ChallongeAPI $api )
	{
		$attachment = $api->aAdd(self::$tournament_id, self::$subdomain, self::$match_id, [
			'description' => 'A text attachment.',
		]);

		$this->assertInstanceOf(Attachment::class, $attachment);
		self::$attachment1_id = $attachment->id;
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_Create_Url( ChallongeAPI $api )
	{
		$attachment = $api->aAdd(self::$tournament_id, self::$subdomain, self::$match_id, [
			'url'         => 'http://google.com',
			'description' => 'A Google link attachment.',
		]);

		$this->assertInstanceOf(Attachment::class, $attachment);
		self::$attachment2_id = $attachment->id;
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_Create_Asset( ChallongeAPI $api )
	{
		$this->markTestIncomplete('This test has not been implemented yet.');

		$attachment = $api->aAdd(self::$tournament_id, self::$subdomain, self::$match_id, [
			'asset'       => '',
			'description' => '',
		]);

		$this->assertInstanceOf(Attachment::class, $attachment);
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 *
	 * @return AttachmentList
	 */
	public function testEndpoint_Index( ChallongeAPI $api )
	{
		$attachments = $api->aList(self::$tournament_id, self::$subdomain, self::$match_id);

		$this->assertInstanceOf(AttachmentList::class, $attachments);

		return $attachments;
	}

	/**
	 * @depends testEndpoint_Index
	 *
	 * @param AttachmentList $attachments
	 */
	public function testEndpoint_Index_Count( AttachmentList $attachments )
	{
		$this->assertCount($attachments->count, $attachments->getAttachments());
	}

	/**
	 * @depends testEndpoint_Index
	 *
	 * @param AttachmentList $attachments
	 */
	public function testEndpoint_Index_getMatches( AttachmentList $attachments )
	{
		$this->assertSame($attachments->attachments, $attachments->getAttachments());
	}

	/**
	 * @depends testEndpoint_Index
	 *
	 * @param AttachmentList $attachments
	 */
	public function testEndpoint_Index_getMatchById( AttachmentList $attachments )
	{
		$this->assertSame($attachments->attachments[self::$attachment1_id], $attachments->getAttachmentById(self::$attachment1_id));
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_Show( ChallongeAPI $api )
	{
		$attachment1 = $api->aGet(self::$tournament_id, self::$subdomain, self::$match_id, self::$attachment1_id);

		$this->assertInstanceOf(Attachment::class, $attachment1);
		$this->assertSame(self::$attachment1_id, $attachment1->id);
		$this->assertSame('A text attachment.', $attachment1->description);
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_Update( ChallongeAPI $api )
	{
		$attachment1 = $api->aEdit(self::$tournament_id, self::$subdomain, self::$match_id, self::$attachment1_id, [
			'description' => 'A modified text attachment.',
		]);

		$this->assertInstanceOf(Attachment::class, $attachment1);
		$this->assertSame(self::$attachment1_id, $attachment1->id);
		$this->assertSame('A modified text attachment.', $attachment1->description);
	}

	/**
	 * @depends testInit
	 *
	 * @param ChallongeAPI $api
	 */
	public function testEndpoint_Destroy( ChallongeAPI $api )
	{
		$attachment1_del = $api->aDelete(self::$tournament_id, self::$subdomain, self::$match_id, self::$attachment1_id);
		$attachment2_del = $api->aDelete(self::$tournament_id, self::$subdomain, self::$match_id, self::$attachment2_id);
		//$attachments = $api->aDelete(self::$tournament_id, self::$subdomain, self::$match_id, self::$attachment3_id);

		$this->assertInstanceOf(Attachment::class, $attachment1_del);
		$this->assertSame(self::$attachment1_id, $attachment1_del->id);

		$this->assertInstanceOf(Attachment::class, $attachment2_del);
		$this->assertSame(self::$attachment2_id, $attachment2_del->id);

		//$this->assertInstanceOf(Attachment::class, $attachment3_del);
		//$this->assertSame(self::$attachment3_id, $attachment3_del->id);
	}
}
