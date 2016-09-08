# Challonge API PHP7 wrapper
This is Challonge API wrapper for PHP7 ready to be used. Easy usage and clean code.

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