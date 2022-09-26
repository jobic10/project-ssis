<?php
if (!isset($_SESSION))
    session_start();
//Check whether the session variable SESS_MEMBER_ID is present or not
if (!isset($_SESSION['ADMIN']) || (trim($_SESSION['ADMIN']) == '')) { ?>
<script>
window.location = "index.php";
</script>
<?php
    exit;
}
$session_id = $_SESSION['ADMIN'];


$user_username = getFullNameById($session_id);
if ($user_username == -1) {
    //That means the query didn't execute
    die("Kindly Login Again");
}

sessionInactive("ADMIN");
?>
<?php
if (!isset($file_access))
    die("Direct Access To File Not Allowed");
?>