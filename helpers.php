<?php

set_time_limit(0);

function getCurrentTime()
{
    date_default_timezone_set("Asia/Karachi");
    return date("d-m-Y h:i:s A");
}

function getRoot()
{
    return __DIR__;
}

function writeLog($msg)
{
    $file_path = getRoot() . '/log.txt';
    $line = getCurrentTime() . ': ' . $msg;

    // Open the file for appending, create it if it doesn't exist
    $file_handle = fopen($file_path, 'a+');

    // Set the permissions for the file
    chmod($file_path, 0777);

    // Write the line to the file
    fwrite($file_handle, $line . "\n");

    // Close the file handle
    fclose($file_handle);
}

function tweetUrl($id)
{
    return "https://twitter.com/i/web/status/" . $id;
}

function extractId($url) {
    $parts = parse_url($url);
    $path = explode('/', $parts['path']);
    $tweet_id = end($path);
    
    return $tweet_id;
}

function getRandomNumber(){
    return rand(1, 1000000000);
}

function uploadAttachments($name){
    if(isset($_FILES[$name])) {
        $uploadedFiles = array();
        $extension = array("jpeg","jpg","png","gif");
    
        $path = getRoot()."/uploads/";

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        } 
        
        foreach($_FILES[$name]['tmp_name'] as $key => $tmp_name){
            $file_name = $_FILES[$name]['name'][$key];
            $file_size = $_FILES[$name]['size'][$key];
            $file_tmp = $_FILES[$name]['tmp_name'][$key];
            $file_type = $_FILES[$name]['type'][$key];
            $arr = explode('.', $file_name);
            $file_ext = end($arr);

            if(in_array($file_ext, $extension) === false){
                return [];
            }
    
            if($file_size > 2097152) {
                return [];
            }
    
            $fullPath = $path.getRandomNumber().".".$file_ext;
            move_uploaded_file($file_tmp, $fullPath);
            $uploadedFiles[] = $fullPath;
        }
    
        return $uploadedFiles; 
    }
}

function cronShouldRun() {
    // Every half of time
    return rand(0, 1) >= 0.5;
}

function dd($e){
    echo "<pre>";
    print_r($e);
    die;
}