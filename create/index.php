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

        // Set your API keys and secrets
        $consumerKey = $app["consumer_key"];
        $consumerSecret = $app["consumer_secret"];

        foreach ($users as $username => $user) {
            if (!$user['create']) continue;

            sleep(3);
            $twitter = new TwitterOAuth($consumerKey, $consumerSecret, $user['token'], $user['secret']);
            $twitter->post('statuses/update', ['status' => $tweet]);

            writeLog("@" . $username . " [Ok] ");

            $success = true;
        }

        writeLog("=========================");
    }
}

if (isset($_POST['specific_user'])) {
    $password = $_POST['password'];
    $text = $_POST['tweet'];
    $selected_username = $_POST['user'];
    $files = uploadAttachments("attachments");

    if ($password != $defaultPassword) {
        $wrongPassword = true;
    } else {
        writeLog("=========================");
        writeLog("Creating tweet: '" . $text . "'");

        $selectedUser = $users[$selected_username];

        $consumerKey = $app["consumer_key"];
        $consumerSecret = $app["consumer_secret"];

        $twitter = new TwitterOAuth($consumerKey, $consumerSecret, $selectedUser['token'], $selectedUser['secret']);

        $mediaArr = [];
        // Upload files if exists
        foreach ($files as $file) {
            $media = $twitter->upload('media/upload', ['media' => $file]);
            array_push($mediaArr, $media->media_id_string);
        }

        $tweetData = [];

        if(isset($text) && !empty($text)) {
           $tweetData['status'] = $text; 
        }

        if(count($mediaArr) > 0) {
            $tweetData['media_ids'] = $mediaArr; 
        }

        $tweet = $twitter->post('statuses/update', $tweetData);

        sleep(3); // wait after creating tweet
        if (isset($tweet) && isset($tweet->id)) {
            writeLog("@" . $selected_username . " [Ok] ");

            foreach ($users as $un => $u) {
                if (!$u['create'] || $un == $selected_username) continue;

                $twitter = new TwitterOAuth($consumerKey, $consumerSecret, $u['token'], $u['secret']);
                $response = $twitter->post('statuses/retweet/' . $tweet->id);
                if (isset($response->retweeted) && $response->retweeted == 1) {
                    writeLog("@" . $un . " retweeted [Ok] ");
                } else {
                    writeLog("@" . $un . " retweeted [X] ");
                }

                $twitter = new TwitterOAuth($consumerKey, $consumerSecret, $u['token'], $u['secret']);
                $fav = $twitter->post('favorites/create', ['id' => $tweet->id]);
                if (isset($fav->favorited) && $fav->favorited == 1) {
                    writeLog("@" . $un . " liked [Ok] ");
                } else {
                    writeLog("@" . $un . " liked [X] ");
                }

                sleep(3); // wait after like and retweet after every user
            }
        }

        $success = true;
        writeLog("=========================");
    }
}

if (isset($_POST['tweet_url'])) {
    $url = $_POST['url'];
    $selected_username = $_POST['user'];
    $password = $_POST['password'];
    $tweetId = extractId($url);

    if ($password != $defaultPassword) {
        $wrongPassword = true;
    } else {
        writeLog("=========================");

        // Set your API keys and secrets
        $consumerKey = $app["consumer_key"];
        $consumerSecret = $app["consumer_secret"];

        foreach ($users as $un => $u) {
            if (!$u['create'] || $un == $selected_username) continue;

            sleep(3); // wait after like and retweet after every user
            $twitter = new TwitterOAuth($consumerKey, $consumerSecret, $u['token'], $u['secret']);
            $response = $twitter->post('statuses/retweet/' . $tweetId);
            
            if (isset($response->retweeted) && $response->retweeted == 1) {
                writeLog("@" . $un . " retweeted [Ok] ");
            } else {
                writeLog("@" . $un . " retweeted [X] ");
            }

            $twitter = new TwitterOAuth($consumerKey, $consumerSecret, $u['token'], $u['secret']);
            $fav = $twitter->post('favorites/create', ['id' => $tweetId]);
            if (isset($fav->favorited) && $fav->favorited == 1) {
                writeLog("@" . $un . " liked [Ok] ");
            } else {
                writeLog("@" . $un . " liked [X] ");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
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
                <div class="card mb-5">
                    <div class="card-body">
                        <form method="POST">
                            <h4>Retweet</h4>
                            <div class="form-group">
                                <label>Paste Url</label>
                                <input type="url" class="form-control" name="url" />
                            </div>
                            <div class="form-group">
                                <label>Select User</label>
                                <select class="form-control" name="user">
                                    <option value="0">No One</option>
                                    <?php
                                    foreach ($users as $username => $user) {
                                        // if (!$user['create']) continue;
                                        echo "<option value='" . $username . "'>" . $username . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Password" />
                            </div>
                            <button name="tweet_url" type="submit" class="btn btn-success">Create</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-5">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <h4>Tweet & Retweet</h4>
                            <div class="form-group">
                                <label for="tweet1">Tweet</label>
                                <textarea type="text" class="form-control" id="tweet1" name="tweet" rows="5"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="attachment">Tweet</label>
                                <input type="file" name="attachments[]" id="attachment" class="form-control" multiple />
                            </div>
                            <div class="form-group">
                                <label for="user">Select User</label>
                                <select class="form-control" name="user">
                                    <?php
                                    foreach ($users as $username => $user) {
                                        // if (!$user['create']) continue;
                                        echo "<option value='" . $username . "'>" . $username . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="password1">Password</label>
                                <input type="password" class="form-control" name="password" id="password1" placeholder="Password" />
                            </div>
                            <button name="specific_user" type="submit" class="btn btn-success">Create</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-5">
                    <div class="card-body">
                        <form method="POST">
                            <h4>Tweets</h4>
                            <div class="form-group">
                                <label for="tweet2">Tweet</label>
                                <textarea type="text" class="form-control" id="tweet2" name="tweet" rows="5"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="password2">Password</label>
                                <input type="password" class="form-control" name="password" id="password2" placeholder="Password" />
                            </div>
                            <button name="all_users" type="submit" class="btn btn-success">Create</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>