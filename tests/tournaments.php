<?php

namespace Tests;

require_once __DIR__ . '/../src/ChallongeAPI/loader.php';
use ChallongeAPI\ChallongeAPI;


//  Creates new instance of API wrapper
$api = new ChallongeAPI([
	'api_key' => 'YOUR_CHALLONGE_API_KEY',
]);

//  This will fetch all YOUR tournaments (does not include tournaments created by organization subdomain) and place them to TournamentList object
$list = $api->tList();

echo "<pre>";
echo "Count of your tournaments: {$list->count}<br><br>";

echo "List of Tournaments:<br>";
foreach ($list->tournaments as $t)
	echo "{$t->name}<br>";

//  You can also get exact tournament from the list by its ID or URL NAME
if ($list->count)
{
	echo "<br><br>";

	//  Temporarily get first tournament
	$temp_t = reset($list->tournaments);
	echo "First tournament is <b>{$temp_t->name}</b> with ID <b>{$temp_t->id}</b><br>";

	//  Fetch the same tournament, but by it's ID
	$t_id = $list->getTournamentById($temp_t->id);
	echo "Tournament <b>{$t_id->name}</b> selected by ID: {$t_id->id}<br>";

	//  Or URL NAME, if you wish so
	$t_url = $list->getTournamentByUrl($temp_t->url);
	echo "Tournament <b>{$t_url->name}</b> selected by URL NAME: {$t_url->url}";
}