<?php

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
