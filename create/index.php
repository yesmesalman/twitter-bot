<?php

require_once('../vendor/autoload.php');

use Abraham\TwitterOAuth\TwitterOAuth;

include("../config.php");
include("../helpers.php");

set_time_limit(100);

$wrongPassword = false;
$success = false;
$defaultPassword = "admin!";

if (isset($_POST['all_users'])) {
    $password = $_POST['password'];
    $tweet = $_POST['tweet'];

    if ($password != $defaultPassword) {
        $wrongPassword = true;
    } else {
        writeLog("=========================");
        writeLog("Creating tweet: '" . $tweet . "'");

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

            writeLog("@" . $username . " [Ok] ");

            $success = true;
            sleep(1);
        }

        writeLog("=========================");
    }
}


if (isset($_POST['specific_user'])) {
    $password = $_POST['password'];
    $tweet = $_POST['tweet'];
    $selected_username = $_POST['user'];

    if ($password != $defaultPassword) {
        $wrongPassword = true;
    } else {
        writeLog("=========================");
        writeLog("Creating tweet: '" . $tweet . "'");

        $selectedUser = $users[$selected_username];

        $consumerKey = $app["consumer_key"];
        $consumerSecret = $app["consumer_secret"];

        $twitter = new TwitterOAuth($consumerKey, $consumerSecret, $selectedUser['token'], $selectedUser['secret']);
        $tweet = $twitter->post('statuses/update', ['status' => $tweet]);

        if (isset($tweet) && isset($tweet->id)) {
            writeLog("@" . $selected_username . " [Ok] ");

            foreach ($users as $un => $u) {
                if (!$u['create'] || $un == $selected_username) continue;

                $twitter = new TwitterOAuth($consumerKey, $consumerSecret, $u['token'], $u['secret']);
                $response = $twitter->post('statuses/retweet/' . $tweet->id);
                if (isset($response->retweeted) && $response->retweeted == 1) {
                    writeLog("@" . $un . "retweeted [Ok] ");
                } else {
                    writeLog("@" . $un . "retweeted [X] ");
                }

                $twitter = new TwitterOAuth($consumerKey, $consumerSecret, $u['token'], $u['secret']);
                $tweet = $twitter->post('favorites/create', ['id' => $tweet->id]);
                if (isset($tweet->favorited) && $tweet->favorited == 1) {
                    writeLog("@" . $un . "liked [Ok] ");
                } else {
                    writeLog("@" . $un . "liked [X] ");
                }

                sleep(2);
            }
        }

        $success = true;
        writeLog("=========================");
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
    <div class="container" style="padding-top: 60px">
        <div class="row">
            <div class="col-md-12">
                <?php
                if ($wrongPassword) {
                    echo '<div class="alert alert-danger"><span>Wrong Password</span></div>';
                }
                ?>
                <?php
                if ($success) {
                    echo '<div class="alert alert-success"><span>Tweet has been posted!</span></div>';
                }
                ?>
            </div>
            <div class="col-md-6">
                <form method="POST">
                    <h4>Tweets for one person & retweet from other accounts</h4>
                    <div class="form-group">
                        <label for="tweet">Tweet</label>
                        <textarea type="text" class="form-control" id="tweet" name="tweet"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="user">Select User</label>
                        <select class="form-control" name="user">
                            <?php
                            foreach ($users as $username => $user) {
                                if (!$user['create']) continue;
                                echo "<option value='" . $username . "'>" . $username . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" />
                    </div>
                    <button name="specific_user" type="submit" class="btn btn-success">Create</button>
                </form>
            </div>
            <div class="col-md-6">
                <form method="POST">
                    <h4>Tweets for all</h4>
                    <div class="form-group">
                        <label for="tweet">Tweet</label>
                        <textarea type="text" class="form-control" id="tweet" name="tweet"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" />
                    </div>
                    <button name="all_users" type="submit" class="btn btn-success">Create</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>