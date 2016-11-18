<?php

//  PHP version check
if (PHP_VERSION_ID < 70000)
	trigger_error('This library requires PHP version 7.0.0 or newer!', E_USER_ERROR);

//  Exceptions
require_once __DIR__ . '/Exceptions/APIException.php';
require_once __DIR__ . '/Exceptions/GeneralException.php';

//  Object interfaces
require_once __DIR__ . '/Objects/IApiObject.php';
require_once __DIR__ . '/Objects/IApiObjectList.php';

//  Objects
require_once __DIR__ . '/Objects/ApiObject.php';
require_once __DIR__ . '/Objects/Attachment.php';
require_once __DIR__ . '/Objects/AttachmentList.php';
require_once __DIR__ . '/Objects/Match.php';
require_once __DIR__ . '/Objects/MatchList.php';
require_once __DIR__ . '/Objects/Participant.php';
require_once __DIR__ . '/Objects/ParticipantList.php';
require_once __DIR__ . '/Objects/Tournament.php';
require_once __DIR__ . '/Objects/TournamentList.php';

//  Core class
require_once __DIR__ . '/ChallongeAPI.php';