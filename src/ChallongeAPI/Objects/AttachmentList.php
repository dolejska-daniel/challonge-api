<?php

namespace ChallongeAPI\Objects;

/**
 *   Class AttachmentList
 *
 * @property Attachment[] $attachments
 * @property int          $count
 *
 * @package ChallongeAPI\Objects
 */
class AttachmentList implements IApiObjectList
{
	/**
	 *   AttachmentList constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data )
	{
		foreach ($data as $attachment_data)
		{
			$a = new Attachment($attachment_data);
			$this->attachments[$a->id] = $a;
		}

		$this->count = count($data);
	}


	/** @var int $count */
	public $count;

	/** @var Attachment[] $attachments */
	public $attachments;

	/**
	 *   Gets all the attachments.
	 *
	 * @return Attachment[]
	 */
	public function getAttachments(): array
	{
		return $this->attachments;
	}

	/**
	 *   Gets attachment by it's unique identifier (id).
	 *
	 * @param int $attachment_id
	 *
	 * @return Attachment
	 */
	public function getAttachmentById( int $attachment_id ): Attachment
	{
		return $this->attachments[$attachment_id];
	}
}