<?php
date_default_timezone_set("Asia/Karachi");
$log_file = 'file.txt';
if(!file_exists($log_file)){
	$fp = fopen($log_file, "w");
}
$fp = fopen($log_file, 'w');
fwrite($fp, date('Y-m-d H:i:s'));
fclose($fp);
require_once 'src/twitter.class.php';
    $consumerKey = 'DxaXdHVqB6OWbRIz3YLTrsW0A';
    $consumerSecret = 'SRCgtKfB2Spy1uEfmnutclgVxKnRfTluLWoLjzVkUngnYbGGSZ';
    $accessToken = '3631273638-VphJbMtknYWRJZMKO3FVTWASNuIn3YzZ8oHixUf';
    $accessTokenSecret = 'nVspYqvq1zFPlKPyXculH64fsoockl9rd0FannS7ywYp6';
    $twitter = new Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
    $results = $twitter->search(['q' => 'follow everyone who likes this','count'=>'1', 'result_type'=>'recent']);
    echo "<script>console.log(".json_encode($results, True).")</script>";
?>
<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=ibm855">
	<title>TWEETS</title>
	
	<style type="text/css">
		body{
			font-family: verdana;
			padding: 12px;
		}
		a{
			color:#3f51b5;
			font-size: 12px;
		}
		table{
			margin: 0 auto;
			text-align: center;
			border: 1px solid #ccc;
    		padding: 10px;
		}
		table tr td{
			border-top: 1px solid #ccc;
		}
	</style>
</head>
<body>
<center><h3>hey! this is result of my Job</h3></center>
<?php 
	if(count($results)){
	$count=1;
	echo "<table>
		<tr>
			<td><strong>..</strong></td>
			<td><strong>visit tweet</strong></td>
			<td><strong>tweet</strong></td>
			<td><strong>user</strong></td>
			<td><strong>created at</strong></td>
		</tr>
	";
		foreach ($results as $status){
		 $twitter->favThis($status->id);
		// $twitter->rtThis($status->id);
?>
		<tr>
			<td>
				<span><?php echo $count++; ?></span>
			</td>
			<td>
				<a href="https://twitter.com/andreasfaille/status/<?php echo $status->id ?>" target="_blank">Visit tweet</a>
			</td>
			<td>
				<a href="https://twitter.com/andreasfaille/status/<?php echo $status->id ?>" target="_blank"><?php echo $status->text; ?></a>
			</td>
			<td>
				<a href="https://twitter.com/<?php echo $status->user->screen_name ?>"><?php echo $status->user->name ?></a>
			</td>
			<td>
				<span><?php echo $status->created_at; ?></span>
			</td>
		</tr>
<?php 
		}
	echo "</table>";
	}
?>
</body>
</html>
