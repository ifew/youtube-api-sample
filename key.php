<?php
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    throw new Exception(sprintf('Please run "composer require google/apiclient:~2.0" in "%s"', __DIR__));
}
require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'base.php';

session_start();

$client = new Google_Client();
$client->setApplicationName(APP_NAME);
$client->setDeveloperKey(APP_API_KEY);

$service = new Google_Service_YouTube($client);

    $queryParams = [
        'broadcastStatus' => 'active',
        'broadcastType' => 'all'
    ];

    $response = $service->liveBroadcasts->listLiveBroadcasts('snippet', $queryParams);

    if(empty($response->items[0])) {  
        echo '<img src="maxresdefault.jpg" />';
    } else {
        echo '<h2>Live</h2><iframe width="560" height="315" src="https://www.youtube.com/embed/'.$response->items[0]->id.'?autoplay=1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        echo '<h2>Respond Code</h2>'.json_encode($response);
    }