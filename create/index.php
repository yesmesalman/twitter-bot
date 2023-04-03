<?php

require_once('../vendor/autoload.php');

use Abraham\TwitterOAuth\TwitterOAuth;

include("../config.php");
include("../helpers.php");

set_time_limit(100);

$wrongPassword = false;
$success = false;
if (isset($_POST['submit'])) {
    $password = $_POST['password'];
    $tweet = $_POST['tweet'];

    if ($password == "admin!") {
        // record log
        writeLog("==================================================================================");

        // Set your API keys and secrets
        $consumerKey = $app["consumer_key"];
        $consumerSecret = $app["consumer_secret"];
        $accessToken = $app["access_token"];
        $accessTokenSecret = $app["access_token_secret"];

        // Set up the TwitterOAuth object with your API keys and secrets
        $connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

        foreach ($users as $username => $user) {
            if (!$user['create']) continue;

            $twitter = new TwitterOAuth($consumerKey, $consumerSecret, $user['token'], $user['secret']);
            $twitter->post('statuses/update', ['status' => $tweet]);
            $success = true;

            sleep(1);
        }

        // record log
        writeLog("==================================================================================");
    } else {
        $wrongPassword = true;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Create Tweets</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container" style="padding-top: 100px">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <?php
                if ($wrongPassword) {
                ?>
                    <div class="alert alert-danger">
                        <span>Wrong Password</span>
                    </div>
                <?php
                }
                ?>
                <?php
                if ($success) {
                ?>
                    <div class="alert alert-success">
                        <span>Tweet has been posted!</span>
                    </div>
                <?php
                }
                ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="tweet">Tweet</label>
                        <textarea type="text" class="form-control" id="tweet" name="tweet"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                    </div>
                    <button name="submit" type="submit" class="btn btn-success">Create</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>