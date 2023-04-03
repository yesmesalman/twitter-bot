<?php

require_once('../vendor/autoload.php');

use Abraham\TwitterOAuth\TwitterOAuth;

include("../config.php");

// Set your API keys and secrets
$consumerKey = $app["consumer_key"];
$consumerSecret = $app["consumer_secret"];
$accessToken = $app["access_token"];
$accessTokenSecret = $app["access_token_secret"];

// Set up the TwitterOAuth object with your API keys and secrets
$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

// Get the authentication URL
$requestToken = $connection->oauth('oauth/request_token', array('oauth_callback' => $defaults['signin_callback']));
$authUrl = $connection->url('oauth/authorize', array('oauth_token' => $requestToken['oauth_token']));

// Redirect the user to the authentication URL
header('Location: ' . $authUrl);
exit;
