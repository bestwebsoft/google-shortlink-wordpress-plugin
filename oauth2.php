<?php
require_once  __DIR__ . '/vendor/autoload.php';


$session = session_id();
if ( empty( $session ) ){
    session_start();
}


$config = array(
  'client_id' => $_SESSION['client_id'],
  'client_secret' => $_SESSION['client_secret'],
  'redirect_uri' => $_SESSION['redirect_uri'],
);

$client = new Google_Client();
$client->setAuthConfig($config);

$client->addScope(Google_Service_FirebaseDynamicLinksAPI::FIREBASE);
$client->setAccessType('offline');
$client->setIncludeGrantedScopes(true);
$client->setRedirectUri($config['redirect_uri']);
$client->setApprovalPrompt("force");

if( ! isset($_GET['code'] ) ){
    $auth_url = $client->createAuthUrl();
    header( 'Location: ' . filter_var( $auth_url, FILTER_SANITIZE_URL ) );
} else {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();

    $redirect_uri =  $_SESSION['redirect_to'];
    header( 'Location: '. filter_var( $redirect_uri, FILTER_SANITIZE_URL ) );
}
