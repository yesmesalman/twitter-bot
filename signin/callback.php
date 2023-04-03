<?php
// Load the TwitterOAuth library
require_once('../vendor/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;

// Set your API keys and secrets
$consumerKey = 'YOUR_CONSUMER_KEY';
$consumerSecret = 'YOUR_CONSUMER_SECRET';

// Get the OAuth token and verifier from the callback URL
$oauthToken = $_GET['oauth_token'];
$oauthVerifier = $_GET['oauth_verifier'];

// Set up the TwitterOAuth object with your API keys and secrets
$connection = new TwitterOAuth($consumerKey, $consumerSecret);

// Get the access token using the OAuth token and verifier
$accessToken = $connection->oauth('oauth/access_token', array('oauth_token' => $oauthToken, 'oauth_verifier' => $oauthVerifier));

// Save the access token for future use
// You may want to store the access token in a database or session variable
// Here, we'll just print it out for demonstration purposes
echo "Access Token: " . $accessToken['oauth_token'] . "<br>";
echo "Access Token Secret: " . $accessToken['oauth_token_secret'];
?>
