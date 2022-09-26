<?php
if (!isset($file_access)) {
    die("Direct Access To File Not Allowed");
}

?>
<?php
//Start session

if (!isset($_SESSION)) {
    session_start();
}

//Check whether the session variable SESS_MEMBER_ID is present or not
if (!isset($_SESSION['id']) || ($_SESSION['id'] == '') || $_SESSION['category'] != 'student') {
    session_destroy();
    header("location: index.php");
    exit();
}
checkMaintenance();
$session_id = $_SESSION['id'];
isActive('student');
sessionInactive("STUDENT");
$stud_log = getSettings("student_login");
if ($stud_log == 0) {

    session_destroy();
    echo script("All students access to the portal has been denied by the admin", 'index.php');
}
