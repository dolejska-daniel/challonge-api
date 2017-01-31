# Challonge API PHP7 wrapper

> Version 0.2

[![build status](https://gitlab.dolejska.me/dolejskad/challonge-api/badges/master/build.svg)](https://gitlab.dolejska.me/dolejskad/challonge-api/commits/master)
[![coverage report](https://gitlab.dolejska.me/dolejskad/challonge-api/badges/master/coverage.svg)](https://gitlab.dolejska.me/dolejskad/challonge-api/commits/master)

This is Challonge API wrapper for PHP7! Just ready to be used. With easy usage and clean code.

```php
$api = new ChallongeAPI([ 'api_key' => 'YOUR_CHALLONGE_API_KEY' ]);
```

Working with Challonge API was never easier!
```php
// Fetches all tournaments created on your account
$api->tList();

// Fetches all tournaments created by organization 'csgo' (csgo.challonge.com)
$api->tList('csgo');
```