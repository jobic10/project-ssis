<?php
error_reporting(0);
//File created automatically on Friday, 28-August-2020 02:08 AM;
date_default_timezone_set('Africa/Lagos');
function connect()
{
    @session_start();
    $con = mysqli_connect('localhost', 'just', 'guess', 'ssis');
    // $con = new mysqli('localhost', 'just', 'guess', 'experiment');
    if ($con->connect_errno || mysqli_connect_errno()) die('Unknown Error Occured...');
    return $con;
}