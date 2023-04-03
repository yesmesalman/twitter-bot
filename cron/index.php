<?php

require_once('../vendor/autoload.php');

use Abraham\TwitterOAuth\TwitterOAuth;

include("../config.php");
include("../helpers.php");

set_time_limit(100);

// record log
writeLog("=========================");

// Set your API keys and secrets
$consumerKey = $app["consumer_key"];
$consumerSecret = $app["consumer_secret"];
$accessToken = $app["access_token"];
$accessTokenSecret = $app["access_token_secret"];

// Set up the TwitterOAuth object with your API keys and secrets
$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

foreach ($searches as $s) {
    $search_results = $connection->get('search/tweets', ['q' => $s['search'], 'count' => $defaults['count'], 'exclude_replies' => true, 'filter_retweets' => true]);
    $tweets = $search_results->statuses;

    // record log
    writeLog("Searching '" . $s['search'] . "'");
    writeLog("Found " . count($tweets) . " tweets");

    foreach ($tweets as $tweet) {
        if ($s['type'] == "likes_tweet") {
            writeLog("liking " . tweetUrl($tweet->id));
        }
        if ($s['type'] == "hashtag_group") {
            writeLog("retweeting " . tweetUrl($tweet->id));
        }

        foreach ($users as $username => $user) {
            $twitter = new TwitterOAuth($consumerKey, $consumerSecret, $user['token'], $user['secret']);

            if ($s['type'] == "likes_tweet" && $user['likes_tweet']) { // If search is type like and user is allowed to like
                $response = $twitter->post('favorites/create', ['id' => $tweet->id]);
                if (isset($response->favorited) && $response->favorited == 1) {
                    writeLog("@" . $username . " Y ");
                } else {
                    writeLog("@" . $username . " X ");
                    break;
                }
            }

            if ($s['type'] == "hashtag_group" && $user['hashtag_group']) { // If search is type hashtag and user is allowed for it
                $response = $twitter->post('statuses/retweet/' . $tweet->id);
                if (isset($response->retweeted) && $response->retweeted == 1) {
                    writeLog("@" . $username . " Y ");
                } else {
                    writeLog("@" . $username . " X ");
                }
            }
        }
    }

    sleep(1);
}



// record log
writeLog("=========================");

die;
