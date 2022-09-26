<?php
if (!isset($file_access)) {
    die("Direct Access To File Not Allowed");
}

?><?php
//Start session

if (!isset($_SESSION)) {
    session_start();
}

//Check whether the session variable SESS_MEMBER_ID is present or not
if (!isset($_SESSION['id']) || ($_SESSION['id'] == '') || $_SESSION['category'] != 'supervisor') {
    header("location: index.php");
    exit();
}

$session_id = $_SESSION['id'];
isActive('supervisor');
sessionInactive("SUPERVISOR");

//The store varible is an array which I used to get reports from the database. I used this in view_progress.php and functions.php
$store = array(
    "Yet" => "Students in initial stage",
    "Proposal" => "Students Done With Proposal",
    "Chapter 1" => "Students Done With Chapter 1",
    "Chapter 2" => "Students Done With Chapter 2",
    "Chapter 3" => "Students Done With Chapter 3",
    "Chapter 4" => "Students Done With Chapter 4",
    "Chapter 5" => "Students Done With Chapter 5",
    "Clearance" => "Students Cleared And Ready For Defence",
    "Report" => "System Generated Report",
);
//I switched the keys in since the keys ain't what I used in the database

if (!function_exists('switchKey')) {
    function switchKey(&$key)
    {
        if ($key == "Yet") {
            return -1;
        } elseif ($key == "Proposal") {
            return 0;
        } elseif (substr($key, 0, 7) == 'Chapter') {
            return $key[-1]; //Get the last value of the key
        } elseif ($key == 'Clearance') {
            return 6;
        } else {
            return 10;
        }
    }

}

checkMaintenance();

$sup_log = getSettings("supervisor_login");
if ($sup_log == 0) {

    session_destroy();
    echo script("All supervisors access to the portal has been denied by the admin", 'index.php');
}
