<?php

class Env {
    static $timeZone = "Asia/Karachi";
    static $logFileName = "/logs/[DATE]_LOG_FILE.txt";
    
    static $twitterUsers = array(
        [
            "username" => "",
            "consumerKey" => "",
            "consumerSecret" => "",
            "accessToken" => "",
            "accessTokenSecret" => "",
        ], //Shuwarmaa
    );

    static $queryText = "follow everyone who likes this";
    static $queryTweetCount = 1;
    static $queryResultType = "recent";

    static $tweetURL = "https://twitter.com/i/web/status/[ID]";

}

?>