<?php
require_once "Env.php";
require_once 'src/twitter.class.php';

class TwitterBot extends Twitter{

	function __construct($index) {
		parent::__construct(Env::$twitterUsers[$index]['consumerKey'], Env::$twitterUsers[$index]['consumerSecret'], Env::$twitterUsers[$index]['accessToken'], Env::$twitterUsers[$index]['accessTokenSecret']);
	}

	function logEvent($text){
		$filePath = dirname(__FILE__). Env::$logFileName;
		$filePath = str_replace("[DATE]", date("d_M_Y"), $filePath);

		date_default_timezone_set(Env::$timeZone);
		if(!file_exists($filePath))
			$fp = fopen($filePath, "w");
		
		file_put_contents($filePath, $text, FILE_APPEND);
	}

}


foreach (Env::$twitterUsers as $index => $user) {
	$twitterBot = new TwitterBot($index);
	$results = $twitterBot->search(['q' => Env::$queryText, 'count' => Env::$queryTweetCount, 'result_type' => Env::$queryResultType]);

	foreach ($results as $result){

		//Fav
		$liked = $twitterBot->favThis($result->id);
		if($liked[0]){
			$text = Env::$twitterUsers[$index]["username"]." Like Success ".date("h:i:s A d-M-Y"). " ".str_replace("[ID]", $result->id, Env::$tweetURL)." \n";
			$twitterBot->logEvent($text);
		}else{
			$text = Env::$twitterUsers[$index]["username"]." Like Failed ".date("h:i:s A d-M-Y"). " ".str_replace("[ID]", $result->id, Env::$tweetURL)." (".$liked[1].") \n";
			$twitterBot->logEvent($text);
		}
	}
}

die;
?>