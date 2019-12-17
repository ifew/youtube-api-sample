<?php
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    throw new Exception(sprintf('Please run "composer require google/apiclient:~2.0" in "%s"', __DIR__));
}
require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'base.php';

putenv('GOOGLE_APPLICATION_CREDENTIALS='.APP_CREDENTIAL_SERVICE_ACCOUNT_JSON);
$client = new Google_Client();
$client->setScopes([
    'https://www.googleapis.com/auth/youtube.readonly',
    'https://www.googleapis.com/auth/youtube',
    'https://www.googleapis.com/auth/youtube.force-ssl'
]);
$client->useApplicationDefaultCredentials();

$service = new Google_Service_YouTube($client);

    $queryParams = [
        'broadcastStatus' => 'active',
        'broadcastType' => 'all'
    ];

    $response = $service->liveBroadcasts->listLiveBroadcasts('snippet', $queryParams);
echo json_encode($response) . "\n";