<?php
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    throw new Exception(sprintf('Please run "composer require google/apiclient:~2.0" in "%s"', __DIR__));
}
require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'base.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig(APP_CREDENTIAL_CLIENT_JSON);
$client->setApplicationName(APP_NAME);
$client->setScopes([
    'https://www.googleapis.com/auth/youtube.readonly',
    'https://www.googleapis.com/auth/youtube',
    'https://www.googleapis.com/auth/youtube.force-ssl'
]);
$client->setAccessType('offline');
$client->setPrompt("none");
$client->setIncludeGrantedScopes(true);
$redirect = filter_var(get_web_protocol() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'], FILTER_SANITIZE_URL);
$client->setRedirectUri($redirect);

$tokenSessionKey = 'accessToken';
if (isset($_GET['code'])) {
    if(isset($_SESSION['state'])) {
        if (strval($_SESSION['state']) !== strval($_GET['state'])) {
            die('The session state did not match.');
        }
    }

    $_SESSION[$tokenSessionKey] = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    header('Location: ' . $redirect);
    // $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    // $_SESSION['access_token'] = $accessToken;
}

// if (isset($_SESSION[$tokenSessionKey]) && $_SESSION[$tokenSessionKey]) {
//     $client->setAccessToken($_SESSION[$tokenSessionKey]);
//     // $client->setAccessToken($_SESSION['access_token']);
//     // $accessToken = $_SESSION['access_token'];
// }

// Request authorization from the user.
$state = mt_rand();
echo '<h1>Youtube Live Demo - Viewer Page</h1>';
// var_dump($_SESSION);
// var_dump($_SERVER);

// Define service object for making API requests.
$service = new Google_Service_YouTube($client);

print_r($service );
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
    echo '<h2>SESSION</h2>'.print_r($_SESSION);
}