# Challonge API PHP7 wrapper

> Version 0.3

[![build status](https://gitlab.dolejska.me/dolejskad/challonge-api/badges/master/build.svg)](https://gitlab.dolejska.me/dolejskad/challonge-api/commits/master)
[![coverage report](https://gitlab.dolejska.me/dolejskad/challonge-api/badges/master/coverage.svg)](https://gitlab.dolejska.me/dolejskad/challonge-api/commits/master)
[![GitHub release](https://img.shields.io/github/release/dolejska-daniel/challonge-api.svg)](https://github.com/dolejska-daniel/riot-api)
[![GitHub pre release](https://img.shields.io/github/release/dolejska-daniel/challonge-api/all.svg?label=pre%20release)](https://github.com/dolejska-daniel/riot-api)
[![Packagist](https://img.shields.io/packagist/v/dolejska-daniel/challonge-api.svg)](https://packagist.org/packages/dolejska-daniel/riot-api)
[![Packagist](https://img.shields.io/packagist/l/dolejska-daniel/challonge-api.svg)](https://packagist.org/packages/dolejska-daniel/riot-api)

# Table of Contents

1. [Introduction](#introduction)
2. [ChallongeAPI](#challongeapi)
	1. [Initializing the library](#initializing-the-library)
	2. [Using the library](#using-the-library)
	3. [Taking advantage of objects](#taking-advantage-of-objects)

# Introduction

This is Challonge API wrapper for PHP7! Just ready to be used. With easy usage and clean code.

# ChallongeAPI

## Initializing the library

Initializing the library is easy, it just needs `array` of settings. Mainly, your `SET_API_KEY`. Take a look:

```php
use ChallongeAPI\ChallongeAPI;

$api = new ChallongeAPI([
	//  Your Challonge API key, you can get one at https://challonge.com/settings/developer
	ChallongeAPI::SET_API_KEY => 'YOUR_CHALLONGE_API_KEY'
]);
```

**Available library settings**:

| Name | Value | Description |
| ---- | ----- | ----------- |
| `SET_API_KEY` | `string` | ___Required___. Your Challonge API key, you can get one at https://challonge.com/settings/developer |
| `SET_VERIFY_SSL` | `bool` | Useful when debuging on localhost, cURL might throw SSL verification errors. _Should not be used in production_.

## Using the library

Working with Challonge API was never easier!

```php
// Fetches all tournaments created on your account
$api->tList();

// Fetches all tournaments created by organization 'csgo' (csgo.challonge.com)
$api->tList('csgo');
```

## Taking advantage of objects

```php
// Fetches all tournaments created on your account
$list = $api->tList();

//  Outputs name of all tournaments on your account
foreach ($list->getTournaments() as $tournament)
	echo $tournament->name . "<br>";

//  Finds tournament by it's ID in the list
$tournament = $list->getTournamentById(123456789);
echo $tournament->name . "<br>";

//  Finds tournament by it's URL name in the list
$tournament = $list->getTournamentByUrl('best_tournament');
echo $tournament->name . "<br>";
```
