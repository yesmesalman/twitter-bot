<?php

$app = [
    "consumer_key" => 'consumer_key',
    "consumer_secret" => 'consumer_secret',
    "access_token" => 'access_token',
    "access_token_secret" => 'access_token_secret',
];

$defaults = [
    "count" => 1,
    "signin_callback" => 'http://localhost/twitter-system/signin/callback.php'
];

$searches = [
    [
        "type" => "likes_tweet",
        "search" => "follow everyone",
    ],
];

$users = [
    "username" => [
        "token" => "token",
        "secret" => "secret",
        "hashtag_group" => true,
        "likes_tweet" => true,
        "create" => true,
    ],
];
