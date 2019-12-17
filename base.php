<?php

function insert_youtuber($client){
  $oauth_service = new Google_Service_Oauth2($client);
  $userData = $oauth_service->userinfo->get();
  
  // Create connection
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }


  $sql = 'INSERT INTO youtuber (gid, firstname, lastname, gender, email, picture) VALUES ("'.$userData->id.'", "'.$userData->givenName.'", "'.$userData->familyName.'", "'.$userData->gender.'", "'.$userData->email.'", "'.$userData->picture.'") ON DUPLICATE KEY UPDATE gid = "'.$userData->id.'"';

  if ($conn->query($sql) === FALSE) {
      echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $id = $conn->insert_id;

  $conn->close();

  return array("id" => $id, "gid" => $userData->id);
}

function insert_youtuber_live($gid, $live){
  // Create connection
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }


  $sql = 'INSERT INTO youtuber_live (gid, channel_id) VALUES ("'.$gid.'", "'.$live->items[0]->id.'") ON DUPLICATE KEY UPDATE gid = "'.$gid.'"';

  if ($conn->query($sql) === FALSE) {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $conn->close();
}

function delete_youtuber_live($gid){
  // Create connection
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }


  $sql = 'DELETE FROM youtuber_live WHERE gid = "'.$gid.'"';

  if ($conn->query($sql) === FALSE) {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $conn->close();
}

function list_youtuber_live(){
  // Create connection
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }


  $sql = 'SELECT id,gid,channel_id FROM youtuber_live ORDER BY id DESC';
  $result = $conn->query($sql);
  if ($result === FALSE) {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $return_results = $result->fetch_all(MYSQLI_ASSOC);
  $result->free_result();

  $conn->close();

  return $return_results;
}

function get_web_protocol() {
	if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
		return 'https'; 
	}
	return 'http';
}

/* Ad hoc functions to make the examples marginally prettier.*/
function isWebRequest()
{
  return isset($_SERVER['HTTP_USER_AGENT']);
}

function pageHeader($title)
{
  $ret = "<!doctype html>
  <html>
  <head>
    <title>" . $title . "</title>
    <link href='styles/style.css' rel='stylesheet' type='text/css' />
  </head>
  <body>\n";
  if ($_SERVER['PHP_SELF'] != "/index.php") {
    $ret .= "<p><a href='index.php'>Back</a></p>";
  }
  $ret .= "<header><h1>" . $title . "</h1></header>";

 // Start the session (for storing access tokens and things)
  if (!headers_sent()) {
    session_start();
  }

  return $ret;
}


function pageFooter($file = null)
{
  $ret = "";
  if ($file) {
    $ret .= "<h3>Code:</h3>";
    $ret .= "<pre class='code'>";
    $ret .= htmlspecialchars(file_get_contents($file));
    $ret .= "</pre>";
  }
  $ret .= "</html>";

  return $ret;
}

function missingApiKeyWarning()
{
  $ret = "
    <h3 class='warn'>
      Warning: You need to set a Simple API Access key from the
      <a href='http://developers.google.com/console'>Google API console</a>
    </h3>";

  return $ret;
}

function missingClientSecretsWarning()
{
  $ret = "
    <h3 class='warn'>
      Warning: You need to set Client ID, Client Secret and Redirect URI from the
      <a href='http://developers.google.com/console'>Google API console</a>
    </h3>";

  return $ret;
}

function missingServiceAccountDetailsWarning()
{
  $ret = "
    <h3 class='warn'>
      Warning: You need download your Service Account Credentials JSON from the
      <a href='http://developers.google.com/console'>Google API console</a>.
    </h3>
    <p>
      Once downloaded, move them into the root directory of this repository and
      rename them 'service-account-credentials.json'.
    </p>
    <p>
      In your application, you should set the GOOGLE_APPLICATION_CREDENTIALS environment variable
      as the path to this file, but in the context of this example we will do this for you.
    </p>";

  return $ret;
}

function missingOAuth2CredentialsWarning()
{
  $ret = "
    <h3 class='warn'>
      Warning: You need to set the location of your OAuth2 Client Credentials from the
      <a href='http://developers.google.com/console'>Google API console</a>.
    </h3>
    <p>
      Once downloaded, move them into the root directory of this repository and
      rename them 'oauth-credentials.json'.
    </p>";

  return $ret;
}

function invalidCsrfTokenWarning()
{
  $ret = "
    <h3 class='warn'>
      The CSRF token is invalid, your session probably expired. Please refresh the page.
    </h3>";

  return $ret;
}

function checkServiceAccountCredentialsFile($credential_file_path)
{
  // service account creds
  $application_creds = __DIR__ . $credential_file_path;

  return file_exists($application_creds) ? $application_creds : false;
}

function getCsrfToken()
{
  if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
  }

  return $_SESSION['csrf_token'];
}

function validateCsrfToken()
{
  return isset($_REQUEST['csrf_token'])
      && isset($_SESSION['csrf_token'])
      && $_REQUEST['csrf_token'] === $_SESSION['csrf_token'];
}

function getOAuthCredentialsFile()
{
  // oauth2 creds
  $oauth_creds = __DIR__ . '/../../oauth-credentials.json';

  if (file_exists($oauth_creds)) {
    return $oauth_creds;
  }

  return false;
}

function setClientCredentialsFile($apiKey)
{
  $file = __DIR__ . '/../../tests/.apiKey';
  file_put_contents($file, $apiKey);
}


function getApiKey()
{
  $file = __DIR__ . '/../../tests/.apiKey';
  if (file_exists($file)) {
    return file_get_contents($file);
  }
}

function setApiKey($apiKey)
{
  $file = __DIR__ . '/../../tests/.apiKey';
  file_put_contents($file, $apiKey);
}