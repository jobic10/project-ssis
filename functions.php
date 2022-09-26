<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

include_once 'constants.php';

define('PROJECT_TITLE', 'Student-Supervisor Allocation and Supervision System');
define('DEVELOPER_NAME', 'Owonubi Job Sunday');
define('DEVELOPER_MATRIC', '17/52HA127');
define('SUPERVISOR', 'Mr. H. A. Mojeed');
define('IMAGE_SIZE', 300);
define('FILE_SIZE', 300);
define('MAX_TIME', 30); //Max. time (minutes)  before destroying inactive sessions

if (!isset($file_access) && !isset($_GET['tiny'])); //die('Direct File Access Disabled');
if (!isset($_SESSION)) {
    session_start();
}
//Next line should include special_db file but since it is auto-generated, we check before doing that
if (!file_exists("includes/special_db.php") && !file_exists("../includes/special_db.php")) {
    if (stripos(getcwd(), "admin") !== false) {
        echo
        '<!DOCTYPE html>
<html lang="en">
<head>
<title>Admin not yet built</title>
<style>
body{
background: rgb(30,30,30);
color:gold;
}
</style>
</head>
<body>
<h1 align="center">Admin Page Not Yet Generated!</h1>
<h1 align="center"><font size="+5"> &#128679; </font></h1>
<p align="center">Install This System First By Going To The Homepage</p>
<h1 align="center">&#128281; &#128284;</h1>
</body>
</html>
';
        exit;
    }
    $access = 1; //? To ensure that we can directly access
    include 'validated/index.php';
    exit;
}

require 'includes/special_db.php';
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
    "Allocation History" => "System allocation history",
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
        } elseif ($key == 'Allocation History') {
            return 100;
        } else {
            return 10;
        }
    }
}
function getIP()
{
    return $_SERVER['REMOTE_ADDR'];
}

function sessionInactive($type)
{
    //Function to ensure any user gets logged out if they are inactive for > MAX_TIME minutes
    if (!isset($_SESSION['CREATED_' . $type])) {
        $_SESSION['CREATED_' . $type] = time();
    } else if (time() - $_SESSION['CREATED_' . $type] > MAX_TIME * 60) {
        // session started more than 30 minutes ago
        session_regenerate_id(true); // change session ID for the current session and invalidate old session ID
        $_SESSION['CREATED_' . $type] = time(); // update creation time
    }

    if (isset($_SESSION['LAST_ACTIVITY_' . $type]) && (time() - $_SESSION['LAST_ACTIVITY_' . $type] > MAX_TIME * 60)) {
        // last request was more than 30 minutes ago

        if (@$_SESSION['category'] == 'supervisor') {
            $user = 2;
        } elseif (@$_SESSION['category'] == 'student') {
            $user = 1;
        } else {
            $user = 3;
        }
        logDown(@$_SESSION['id'], "Session timeout", 1, $user);
        echo script("You have been inactive for quite a while, kindly login again", 1);
        session_unset(); // unset $_SESSION variable for the run-time
        session_destroy(); // destroy session data in storage
    }
    $_SESSION['LAST_ACTIVITY_' . $type] = time(); // update last activity time stamp
}


function verify_login($username, $password, $answer, $access)
{
    $con = connect();
    if ($answer != $_SESSION['real_captcha']) {
        echo script('Captcha Verification Failed', 1);
    } elseif (strlen($username) < 3) {
        echo script('Username is required');
    } elseif (strlen($password) < 3) {
        echo script('Password is required');
    } elseif ($access != date('Y')) {
        echo script('Access Denied');
    } else {
        $con = connect();
        $password = md5($password);
        // $password = md5($password . $_SERVER['HTTP_HOST']);
        $stmt = $con->prepare("SELECT user_id as id FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = $result->num_rows;
        $ip = getIP();
        $type = 'ADMIN';
        if ($count == 1) {
            @session_start();
            @session_regenerate_id(true);
            logDown($row['id'], "Admin Login Successful with IP $ip", 3, 3);
            $_SESSION["ADMIN"] = $row['id'];
            $_SESSION['LAST_ACTIVITY_' . $type] = time();
            //The session will be managed by the session.php file located in the pro/ folder
            echo "<script>window.location = 'dashboard.php'; </script>";
        } else {
            logDown(0, "Someone with IP $ip tried to login but provided invalid login", 3, 3);
            echo "<script>alert('Access Denied'); </script>";
        }
    }
    ($con->close());
}

function isActive($who = 'student')
{
    $table = 'students';
    if ($who != 'student') {
        $table = 'supervisors';
    }
    $id = getIdFromSession($_SESSION['id'], $who);

    $query = connect()->query("SELECT status FROM $table WHERE status = '1' AND id = '$id'");
    if (!$query->num_rows) {
        @session_destroy();
        echo script("You are not an active user", 'index.php');
    }
}

function sendMail($to, $subject, $msg)
{ //error_reporting(E_ALL);
    //die("<script>alert('".$_SERVER['PHP_SELF']."')</script>");
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    // require 'vendor/autoload.php';
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 0; // Enable verbose debug output
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = SMTP_MAIL; // SMTP username
        $mail->Password = SMTP_PASS; // SMTP password
        $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587; // TCP port to connect to
        //Recipients
        $from_name = 'UNIVERSITY OF ILORIN - Student Project Portal ';
        $mail->setFrom($mail->Username, $from_name);
        $mail->addAddress($to); // Name is optional
        $mail->addReplyTo("jobowonubi@gmail.com", "Owonubi Job Sunday");
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        // Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = '<!DOCTYPE html>
    <html lang="en">

    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title>Our Response</title>
      <style type="text/css">
      body {margin: 0; padding: 0; min-width: 100%!important;}
      img {height: auto;}
      .content {width: 100%; max-width: 600px;}
      .header {padding: 40px 30px 20px 30px;}
      .innerpadding {padding: 30px 30px 30px 30px;}
      .borderbottom {border-bottom: 1px solid #f2eeed;}
      .subhead {font-size: 15px; color: #ffffff; font-family: sans-serif; letter-spacing: 10px;}
      .h1, .h2, .bodycopy {color: #153643; font-family: sans-serif;}
      .h1 {font-size: 33px; line-height: 38px; font-weight: bold;}
      .h2 {padding: 0 0 15px 0; font-size: 24px; line-height: 28px; font-weight: bold;}
      .bodycopy {font-size: 16px; line-height: 22px;}
      .button {text-align: center; font-size: 18px; font-family: sans-serif; font-weight: bold; padding: 0 30px 0 30px;}
      .button a {color: #ffffff; text-decoration: none;}
      .footer {padding: 20px 30px 15px 30px;}
      .footercopy {font-family: sans-serif; font-size: 14px; color: #ffffff;}
      .footercopy a {color: #ffffff; text-decoration: underline;}

      @media only screen and (max-width: 550px), screen and (max-device-width: 550px) {
      body[yahoo] .hide {display: none!important;}
      body[yahoo] .buttonwrapper {background-color: transparent!important;}
      body[yahoo] .button {padding: 0px!important;}
      body[yahoo] .button a {background-color: #e05443; padding: 15px 15px 13px!important;}
      body[yahoo] .unsubscribe {display: block; margin-top: 20px; padding: 10px 50px; background: #2f3942; border-radius: 5px; text-decoration: none!important; font-weight: bold;}
      }

      /*@media only screen and (min-device-width: 601px) {
        .content {width: 600px !important;}
        .col425 {width: 425px!important;}
        .col380 {width: 380px!important;}
        }*/

      </style>
    </head>

    <body yahoo bgcolor="#f6f8f1">
    <table width="100%" bgcolor="#f6f8f1" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td>
        <!--[if (gte mso 9)|(IE)]>
          <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td>
        <![endif]-->
        <table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td bgcolor="#c7d8a7" class="header">
              <table width="70" align="left" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="70" style="padding: 0 20px 20px 0;">
                    <img class="fix" src="http://www.unilorin.edu.ng/imageshm/logo.png" width="70" height="90" border="0" alt="" />
                  </td>
                </tr>
              </table>
              <!--[if (gte mso 9)|(IE)]>
                <table width="425" align="left" cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td>
              <![endif]-->
              <table class="col425" align="left" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 425px;">
                <tr>
                  <td height="70">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td class="h2" style="padding: 0 0 0 3px;">
                          UNIVERSITY OF ILORIN
                        </td>
                      </tr>
                      <tr>
                        <td class="h3" style="padding: 5px 0 0 0;">
                          COMPUTER SCIENCE - STUDENT PROJECT PORTAL
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <!--[if (gte mso 9)|(IE)]>
                    </td>
                  </tr>
              </table>
              <![endif]-->
            </td>
          </tr>
          <tr>
            <td class="innerpadding borderbottom">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="h2">
                   Howdy, How are you doing?
                  </td>
                </tr>
                <tr>
                  <td class="bodycopy">
                    You have an urgent message</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td class="innerpadding borderbottom">
              <table width="115" align="left" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="115" style="padding: 0 20px 20px 0;">
                    <img class="fix" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/210284/article1.png" width="60" height="60" border="0" alt="" />
                  </td>
                </tr>
              </table>
              <!--[if (gte mso 9)|(IE)]>
                <table width="380" align="left" cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td>
              <![endif]-->
              <table class="col380" align="left" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 380px;">
                <tr>
                  <td>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td class="bodycopy" align="justify">
                          ' . $msg . '
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 20px 0 0 0;">
                          <table class="buttonwrapper" bgcolor="#e05443" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td class="button" height="45">
                                <a href="' . $_SERVER["HTTP_HOST"] . '">Visit Us!</a>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <!--[if (gte mso 9)|(IE)]>
                    </td>
                  </tr>
              </table>
              <![endif]-->
            </td>
          </tr>

          <tr>
            <td class="innerpadding bodycopy">
            If you would like to reach out to us, talk to us any time you like via +234(0)8100134741.<br/>Thank You!
            </td>
          </tr>
          <tr>
            <td class="footer" bgcolor="#44525f">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" class="footercopy">
                    University of Ilorin, Ilorin, Kwara State.<br/>

                  </td>
                </tr>
                <tr>
                  <td align="center" style="padding: 20px 0 0 0;">

                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        <!--[if (gte mso 9)|(IE)]>
              </td>
            </tr>
        </table>
        <![endif]-->
        </td>
      </tr>
    </table>
    </body>
    </html>

    ';

        $mail->AltBody = $msg;

        $mail->send();
        return 1;
    } catch (Exception $e) {
        // return 0;
        // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return 0;
    }
    return 0;
}

function getFullNameById($session_id)
{
    $con = connect();
    $query = $con->prepare("SELECT firstname, lastname FROM users WHERE user_id = ?");
    $query->bind_param("i", $session_id);
    if (!$query->execute()) {
        return -1;
    }

    $result = $query->get_result();
    $row = $result->fetch_assoc();
    $fullname = $row['lastname'] . ", " . $row['firstname'];
    $fullname = ucwords(strtolower($fullname));
    return $fullname;
}

function getRegnoFromId($id)
{
    $con = connect();
    $query = $con->prepare("SELECT regno FROM students WHERE id = ?");
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        return 0;
    } else {
        $result = $query->get_result();
        if ($result->num_rows != 1) {
            return 0;
        } else {
            $row = $result->fetch_assoc();
            return $row['regno'];
        }
        return 0;
    }
}

function getStudentDetailsById($id)
{
    $con = connect();
    $query = $con->prepare("SELECT firstname, lastname, regno FROM students WHERE id = ?");
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        return -1;
    }

    $result = $query->get_result();
    $row = $result->fetch_assoc();
    $fullname = $row['lastname'] . ", " . $row['firstname'] . " (" . $row['regno'] . ")";
    $fullname = ucwords(strtolower($fullname));
    return $fullname;
}

function countSupervisors()
{
    $con = connect();
    $query = $con->query("SELECT * FROM supervisors");
    return $query->num_rows;
}

function countRegSupervisors()
{
    $con = connect();
    $query = $con->query("SELECT * FROM supervisors WHERE status != 0");
    return $query->num_rows;
}

function countUnRegSupervisors()
{
    $con = connect();
    $query = $con->query("SELECT * FROM supervisors WHERE status = 0");
    return $query->num_rows;
}

function countFields()
{
    $con = connect();
    $query = $con->query("SELECT * FROM field_of_interests ");
    return $query->num_rows;
}

function countStudents()
{
    $con = connect();
    $query = $con->query("SELECT * FROM students");
    return $query->num_rows;
}

function countRegStudents()
{
    $con = connect();
    $query = $con->query("SELECT * FROM students WHERE status != 0");
    return $query->num_rows;
}

function countUnRegStudents()
{
    $con = connect();
    $query = $con->query("SELECT * FROM students WHERE status = 0");
    return $query->num_rows;
}

function countAdmins()
{
    $con = connect();
    $query = $con->query("SELECT * FROM users ");
    return $query->num_rows;
}

function getAllFields()
{
    $con = connect();
    $query = $con->query("SELECT * FROM field_of_interests ORDER BY RAND()");
    if ($query->num_rows < 1) {
        return 0;
    }

    return $query;
}

function getAllSupervisor($type = 1)
{
    $con = connect();
    if ($type == 1) {
        $query = $con->query("SELECT count(cpu.id) as no, supervisors.id as id, supervisors.max as myMax, concat(supervisors.lastname, ' ', supervisors.firstname, ' (',supervisors.fileno,')' )
  as bio , SUM(cpu.full) as current, SUM(cpu.no) AS expected FROM supervisors INNER JOIN cpu ON cpu.supervisor_id = supervisors.id GROUP BY supervisors.id ORDER BY RAND()");
    } else {
        $query = $con->query("SELECT supervisors.id as id, supervisors.max as myMax, concat(supervisors.lastname, ' ', supervisors.firstname, ' (',supervisors.fileno,')' )
  as bio   FROM supervisors WHERE supervisors.id NOT IN (SELECT cpu.supervisor_id FROM cpu) AND supervisors.title_id != 0 AND supervisors.password != '' GROUP BY  supervisors.id  ORDER BY RAND()");
    }

    if ($query->num_rows < 1) {
        return 0;
    }

    return $query;
}

function getFields()
{
    $output = "";
    $con = connect();
    $query = $con->query("SELECT * FROM field_of_interests");
    $sn = 1;
    while ($row = $query->fetch_assoc()) {
        $id = $row['id'];

        $output .= '
    <tr>
        <td width="30">
        <input id="optionsCheckbox" class="uniform_on" name="selector[]" type="checkbox"
        value="' . $id . '">
        </td>
        <td>' . $sn++ . '</td>
        <td>' . $row['name'] . '</td>

        <td width="30"><a href="edit_subject.php?id=' . base64_encode($id) . '" class="btn btn-success"><i class="icon-pencil"></i> </a></td>
  </tr>';
    }
    return $output;
}

function cleanArray($val)
{ //Function called only when supervisor selects fields(s) of interest
    if ($val < 1) {
        return false;
    }

    return true;
}

function getFieldsToStudents()
{
    $con = connect();
    //$query = $con->query("SELECT * FROM `field_of_interests` WHERE id IN (SELECT field_id FROM cpu) ORDER BY RAND()");
    $query = $con->query("SELECT * FROM  `field_of_interests` ");
    $output = "";
    while ($row = $query->fetch_assoc()) {
        $id = $row['id'];
        $name = $row['name'];
        $output .= '
<option value="' . $id . '">' . ucwords(strtolower($name)) . '</option>';
    }
    return $output . "<option value='0'> None</option>";
}

function getFieldsToUsers()
{
    $output = "";
    $con = connect();
    $query = $con->query("SELECT * FROM field_of_interests");
    while ($row = $query->fetch_assoc()) {
        $id = $row['id'];
        $name = $row['name'];
        $output .= '
<tr>
<td width="30">
<input id="optionsCheckbox" class="uniform_on" name="selector[]" type="checkbox"
value="' . $id . '">
</td>
<td>' . $name . '</td>
<td><input type="number" required value ="0" name="no[]" class="uniform_on" placeholder="Enter number of students"/></td>

</tr>';
    }
    return $output;
}

function delField($id)
{
    $N = count($id);
    $assigned = 0;
    if ($N < 1) {
        return 0;
    }

    $con = connect();
    for ($i = 0; $i < $N; $i++) {
        $checkSQL = $con->prepare("SELECT id FROM cpu  WHERE field_id = ?");
        $checkSQL->bind_param("i", $id[$i]);
        if (!$checkSQL->execute()) {
            return 0;
        }

        $result = $checkSQL->get_result();
        if ($result->num_rows > 0) {
            $assigned = 1;
            continue;
        }
        $query = $con->prepare("DELETE FROM field_of_interests where id = ? ");
        $query->bind_param("i", $id[$i]);
        if (!$query->execute()) {
            return 0;
        }
    }
    logDown(0, "Admin deleted field of interest", 2, 3);
    if ($assigned) {
        return 2;
    } else {
        return 1;
    }
}

function getAllStudentInChange()
{
    $con = connect();
    $query = $con->query("SELECT * FROM `change_request` ");
    return $query;
}

function getFieldNameFromCpuID($get_id)
{

    $con = connect();
    $query = $con->query("SELECT field_of_interests.name as name FROM `field_of_interests` INNER JOIN cpu ON cpu.field_id = field_of_interests.id WHERE cpu.id = '$get_id'");
    $result = $query->fetch_assoc();
    return $result['name'];
}

function getListOfStudentsByCpuId($id)
{
    $con = connect();
    $query = $con->query("SELECT * FROM students WHERE cpu_id = '$id'");
    $output = '';
    $sn = 0;

    while ($row = $query->fetch_assoc()) {
        $output .= '<tr>
    <td>' . ++$sn . '</td>
    <td>' . $row['regno'] . '</td>
    <td>' . $row['lastname'] . '</td>
    <td>' . $row['firstname'] . '</td>
    </tr>';
    }
    return $output;
}

function getFullProgress($id)
{
    $con = connect();
    $data = "";
    $table_start = "<table class='table'><tr><th>SN</th><th>Chapter</th><th>Status</th><th>Action</th></tr>";
    $table_end = "</table>";
    $query = $con->query("SELECT * FROM progress WHERE student_id = '$id' ORDER BY chapter");
    if ($query->num_rows < 1) {
        $data = '<tr><td colspan="4" class="text-error">Nothing from this student yet!</td></tr>';
    } else {
        $sn = 0;
        while ($row = $query->fetch_assoc()) {
            $sn++;
            $thisId = $row['id'];
            $chap = $row['chapter'];
            $status = $row['status'];
            if ($chap == 0) {
                $chap = 'Project Proposal';
            } elseif ($chap == 6) {
                $chap = 'Project Clearance';
            } else {
                $chap = "Chapter $chap";
            }

            if ($status == 0) {
                $status = 'Pending Response From You';
                $link = "<a href='view_progress.php'><button class='btn btn-success'>Reply</button></a>";
            } elseif ($status == -1) {
                $status = 'Rejected, awaiting fresh upload from student';
                $link = "";
            } else {
                $status = 'Approved On ' . $row['date_accepted'];
                $link = " <a href='uploads/" . $row['link'] . "'><button class='btn btn-info'>Download</button></a>";
            }
            $data .= "<tr><td>$sn</td><td>$chap</td><td>$status</td><td>$link</td></tr>";
        }
    }

    return $table_start . $data . $table_end;
}

function getMyStudents($id)
{
    $start = '	<ul	 id="da-thumbs" class="da-thumbs">';
    $content = '';
    $end = '</ul>';
    $id = intval($id);

    if (canThisIdAccess($id) == -1) {
        return $start . "No Way Through There" . $end;
    }

    $con = connect();
    $query = $con->query("SELECT * FROM students WHERE cpu_id = '$id' ");
    if ($query->num_rows > 0) {
        while ($row = $query->fetch_assoc()) {
            $avatar = getStudentAvatarById($row['id']);
            $response = getFullProgress($row['id']);
            $email = $row['email'];
            $phone = $row['phone'];
            $content .= '<li>
				<a  data-toggle="modal" href="#viewStudent' . $row['id'] . '">
<img id="student_avatar_class" src ="uploads/' . (($row['location'] == null) ? 'no.jpg' : $row['location']) . '" width="124" height="140" class="img-polaroid">
					<div>
					<span>
					<p>' . substr(($row['lastname'] . ' ' . $row['firstname']), 0, 20) . '</p>

					</span>
					</div>
				</a>
				<p class="class"></p>
        <p class="subject"></p>
        <p></p>
				<a  href="#viewStudent' . $row['id'] . '" data-toggle="modal"><i class="icon-bookmark-empty"></i> View</a>
    </li>
    <!-- modal starts -->
    <div id="viewStudent' . $row['id'] . '" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
      <h3 id="myModalLabel">Viewing  ' . ucwords(strtolower(substr(($row['lastname'] . ' ' . $row['firstname']), 0, 20))) . ' Progress Status</h3>

    </div>
      <div class="modal-body">
      <p align="center"><em><table align="center" style="width:100%"><tr><td style="width:33%"><font color="blue" size="+1">' . $phone . '</font></td><td  style="width:33%"><img src="uploads/' . ((strlen($avatar) < 5) ? "no.jpg" : $avatar) . '" class="img img-polaroid" width="200" height="100" /></td><td style="width:33%"><font color="blue" size="+1">' . $email . '</font></td></tr></table></em></p>
    <h4>' . $response . '</h4>
          </div>
    <div class="modal-footer">
    <a href="' . $_SERVER['PHP_SELF'] . '?zip=-1&std=' . $row['id'] . '"><button class="btn btn-danger"><i class="icon-remove icon-large"></i> Download All Materials (Rejected) </button></a>
    <a href="' . $_SERVER['PHP_SELF'] . '?zip=0&std=' . $row['id'] . '"><button class="btn btn-warning"><i class="icon-remove icon-large"></i> Download All Materials (Pending)</button></a>
    <a href="' . $_SERVER['PHP_SELF'] . '?zip=1&std=' . $row['id'] . '"> <button class="btn btn-success"><i class="icon-remove icon-large"></i> Download All Materials (Approved)</button></a>
      <button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-large"></i> Close</button>

    </div>
  </div>
    <!-- Modal ends -->
    ';
        }
    } else {
        $content = '<li class="alert alert-danger">
  <p class="class">No student(s) currently enrolled</p>
  <p class="subject">Kindly check back later.</p>
</li>';
    }
    return $start . $content . $end;
}

function getFieldById($id)
{
    $con = connect();
    $query = $con->prepare("select * from field_of_interests where id = ?");
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        return -1;
    }

    $result = $query->get_result();
    return $result->fetch_assoc();
}

function updateFieldById($id, $name)
{
    if (strlen($name) < 2) {
        return -1;
    }

    $con = connect();
    $query = $con->prepare("select * from field_of_interests where id = ?");
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        return -1;
    }

    $result = $query->get_result();
    if ($result->num_rows != 1) {
        return -1;
    }

    $updateSQL = $con->prepare("UPDATE field_of_interests SET name = ? WHERE id = ? LIMIT 1");
    $updateSQL->bind_param("si", $name, $id);
    if (!$updateSQL->execute()) {
        return -1;
    }

    logDown(0, "Field of interest was renamed", 1, 3);
    return 1;
}

function saveField($name)
{
    if (strlen($name) < 2) {
        return -1;
    }

    $con = connect();
    $checkSQL = $con->prepare("SELECT id FROM field_of_interests WHERE name = ?");
    $checkSQL->bind_param("s", $name);
    if (!$checkSQL->execute()) {
        return -1;
    }

    $result = $checkSQL->get_result();
    if ($result->num_rows > 0) {
        return -2;
    }

    $query = $con->prepare("INSERT INTO field_of_interests (name) VALUES (?)");
    $query->bind_param("s", $name);
    if (!$query->execute()) {
        return -1;
    }

    logDown(0, "Admin inserted new field of interest ($name)", 1, 3);
    return 1;
}

function saveFieldInBulk($file, $user = "csv")
{
    if ($_FILES[$file]['size'] && ($_FILES[$file]['size'] / 1024) > FILE_SIZE) {
        return -1;
    }

    $valid_extension = array("csv");
    if (($_FILES[$file]['size'] && !in_array(strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION)), $valid_extension)) || ($_FILES[$file]['error']) > 0 || !($_FILES[$file]['size'])) {
        return 0;
    } else
    if (($handle = fopen($_FILES[$file]['tmp_name'], 'r')) !== false) {
        // necessary if a large csv file
        set_time_limit(0);
        $row = 0; //Insert Counter
        $duplicate = $skipped = 0;
        // $con = connect();
        $col_count = 0;
        if ($user == "csv") {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // number of fields in the csv
                // $col_count = count($data) - 1;
                $col_count++;
                // exit($col_count);
                $field = $data[0];
                if (strtoupper($field) == 'FIELDS') {
                    $skipped++;
                    continue;
                }
                $add = saveField($field);
                if ($add == -2) {
                    $duplicate++;
                } elseif ($add == 1) {
                    $row++;
                }
            }
        } elseif ($user == "supervisor") {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // $col_count = count($data) - 1;
                $col_count++;
                // exit($col_count);
                $fileno = strtoupper($data[0]); // 0 -> File Number
                $max = intval($data[1]); //1 is Maximum number
                if (($fileno) == 'FILE NO') {
                    $skipped++;
                    continue;
                }
                $insert = addSupervisor($fileno, $max);
                if ($insert == 1) {
                    $row++;
                } elseif ($insert == -2) {
                    $duplicate++;
                } elseif ($insert == -3 || $insert == -1) {
                    $skipped++;
                }
            }
        } else {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // $col_count = count($data) - 1;
                $col_count++;
                // exit($col_count);
                $regno = strtoupper($data[0]);
                if (($regno) == 'MATRIC NO') {
                    $skipped++;
                    continue;
                }
                $insert = addStudent($regno);
                if ($insert == 1) {
                    $row++;
                } elseif ($insert == -2) {
                    $duplicate++;
                } elseif ($insert == -3 || $insert == -1) {
                    $skipped++;
                }
            }
        }
        logDown(0, "Field In Bulk Insert: Total Rows = $col_count, Skipped Records = $skipped, Duplicates = $duplicate And $row Were Inserted", 1, 3);
        return "<h3 class='alert alert-info'>Total Rows = $col_count, Skipped Records = $skipped, Duplicates = $duplicate And $row Were Inserted</h1>";
    }
    return 0;
}

function getMyMax($id)
{
    $con = connect();
    $query = $con->prepare("SELECT max FROM supervisors WHERE id = ?");
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        return -1;
    }

    $result = $query->get_result();
    $row = $result->fetch_assoc();
    return $row['max'];
}

function getCPUMaxById($id)
{
    $con = connect();
    $query = $con->prepare("SELECT SUM(no) as no FROM cpu WHERE supervisor_id = ?");
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        return -1;
    }

    $result = $query->get_result();
    $row = $result->fetch_assoc();
    return $row['no'];
}

function getSupervisors()
{
    $con = connect();
    $query = $con->query("select * from supervisors");
    $sn = 0;
    $output = "";
    while ($row = $query->fetch_assoc()) {
        $passport = "../uploads/";

        $id = $row['id'];
        $stat = $row['status'];
        $passport .= (strlen($row['location']) < 7) ? "no.jpg" : $row["location"];
        $output .= '<tr>
<td width="30">
<input id="optionsCheckbox" class="uniform_on" name="selector[]" type="checkbox" value="' . $id . '">
</td>
  <td width="40"><img class="img-circle" src="' . $passport . '" height="50" width="50"></td>
  <td>' . $row["firstname"] . '  ' . $row["lastname"] . '</td>
  <td>' . $row["fileno"] . '</td>
<td width="50"><a href="edit_supervisor.php?id=' . base64_encode($id) . '" class="btn btn-success"><i class="icon-pencil"></i></a></td>' . (($stat == 1) ? '<td width="120"><a href="' . basename($_SERVER['PHP_SELF']) . '?id=' . base64_encode($id) . '&status=0" class="btn btn-danger"><i class="icon-remove"></i> Deactivate</a></td></tr>' : '<td width="120"><a href="' . basename($_SERVER['PHP_SELF']) . '?id=' . base64_encode($id) . '&status=1" class="btn btn-success"><i class="icon-check"></i> Activate</a></td></tr>');
    }
    return $output;
}

function getSettings($type = 0)
{
    $con = connect();
    if ($type != '0') {
        $row = ($con->query("select value from settings where category = '$type'"))->fetch_assoc();
        return $row['value'];
        exit;
    }
    $query = $con->query("select * from settings");
    $sn = 0;
    $output = "";
    while ($row = $query->fetch_assoc()) {

        $id = $row['id'];
        $cat = $row['category'];
        $cat = str_replace("_", " ", $cat);
        $cat = str_replace("reg", "registration", $cat);
        $cat = ucwords(strtolower($cat));
        $stat = $row['value'];
        $output .= '<tr>

  <td>' . ++$sn . '</td>
  <td>' . $cat . '</td>
  <td>' . (($stat == 0) ? "Denied" : "Granted") . '</td>
' . (($stat == 1) ? '<td width="120"><a href="' . basename($_SERVER['PHP_SELF']) . '?id=' . base64_encode($id) . '&status=0" class="btn btn-danger"><i class="icon-remove"></i> Deactivate</a></td></tr>' : '<td width="120"><a href="' . basename($_SERVER['PHP_SELF']) . '?id=' . base64_encode($id) . '&status=1" class="btn btn-success"><i class="icon-check"></i> Activate</a></td></tr>');
    }
    return $output;
}

function getStudents()
{
    $con = connect();
    $query = $con->query("select * from students");
    $sn = 0;
    $output = "";
    while ($row = $query->fetch_assoc()) {
        $passport = "../uploads/";

        $id = $row['id'];
        $stat = $row['status'];
        $passport .= (strlen($row['location']) < 7) ? "no.jpg" : $row["location"];
        $output .= '<tr>
<td width="30">
<input id="optionsCheckbox" class="uniform_on" name="selector[]" type="checkbox" value="' . $id . '">
</td>
  <td width="40"><img class="img-circle" src="' . $passport . '" height="50" width="50"></td>
  <td>' . $row["firstname"] . '  ' . $row["lastname"] . '</td>
  <td>' . $row["regno"] . '</td>
<td width="50"><a href="edit_student.php?id=' . base64_encode($id) . '" class="btn btn-success"><i class="icon-pencil"></i></a></td>' . (($stat == 1) ? '<td width="120"><a href="' . basename($_SERVER['PHP_SELF']) . '?id=' . base64_encode($id) . '&status=0" class="btn btn-danger"><i class="icon-remove"></i> Deactivate</a></td></tr>' : '<td width="120"><a href="' . basename($_SERVER['PHP_SELF']) . '?id=' . base64_encode($id) . '&status=1" class="btn btn-success"><i class="icon-check"></i> Activate</a></td></tr>');
    }
    return $output;
}

function addSupervisor($fileno, $max)
{
    if (strlen($fileno) != 5 || strlen($max) > 2) {
        return -1;
    }

    $fileno = strtoupper($fileno);
    $con = connect();
    $checkSQL = $con->prepare("SELECT id FROM `supervisors` WHERE fileno = ?");
    $checkSQL->bind_param("s", $fileno);
    if (!$checkSQL->execute()) {
        return -1;
    }

    $result = $checkSQL->get_result();
    if ($result->num_rows > 0) {
        return -2;
    }

    $insertSQL = $con->prepare("INSERT INTO supervisors (fileno,max) VALUES  (?,?)");
    $insertSQL->bind_param("si", $fileno, $max);
    if (!$insertSQL->execute()) {
        return -3;
    }

    logDown(0, "New staff account was created with file no: $fileno and max of $max", 2, 3);
    return 1;
}

function addStudent($regno)
{
    if (strlen($regno) != 10) {
        return -1;
    }
    $regno = strtoupper($regno);
    $con = connect();
    $checkSQL = $con->prepare("SELECT id FROM `students` WHERE regno = ?");
    $checkSQL->bind_param("s", $regno);
    if (!$checkSQL->execute()) {
        return -1;
    }

    $result = $checkSQL->get_result();
    if ($result->num_rows > 0) {
        return -2;
    }

    $insertSQL = $con->prepare("INSERT INTO students (regno) VALUES (?)");
    $insertSQL->bind_param("s", $regno);
    if (!$insertSQL->execute()) {
        return -3;
    }

    logDown(0, "New student account was created with regno no: $regno", 2, 3);
    return 1;
}

function searchStudent($regno)
{
    if (strlen($regno) != 10) {
        return array(-1, 1);
    }
    $regno = strtoupper($regno);
    $con = connect();
    $checkSQL = $con->prepare("SELECT id FROM `students` WHERE regno = ?");
    $checkSQL->bind_param("s", $regno);
    if (!$checkSQL->execute()) {
        return array(0, 1);
    }

    $result = $checkSQL->get_result();
    if ($result->num_rows != 1) {
        return array(0, 1);
    }

    $row = $result->fetch_assoc();
    $id = $row['id'];
    logDown(0, "Searched for student on progress: $regno", 1, 3);
    return (array(1, (connect()->query("SELECT * FROM progress WHERE student_id = '$id' "))));
}

function changeStatusById($id, $status, $user = 'supervisors')
{
    if (($status != 0 && $status != 1) || strlen($id) < 1) {
        return -1;
    }
    $con = connect();
    if ($user == 'supervisors') {
        $table = $user;
    } else {
        $table = 'students';
    }
    $checkSQL = $con->prepare("SELECT * FROM $table WHERE id = ?");
    $checkSQL->bind_param("i", $id);
    if (!$checkSQL->execute()) {
        return -1;
    }
    $result = $checkSQL->get_result();
    if ($result->num_rows != 1) {
        return -2;
    }
    $updateSQL = $con->prepare("UPDATE $table SET status = ? WHERE id = ?");
    $updateSQL->bind_param("ii", $status, $id);
    if (!$updateSQL->execute()) {
        return -1;
    }

    logDown(0, "Account activate set for $user with $status ", 2, 3);
    return 1;
}

function changeSettingsById($id, $status)
{
    if (($status != 0 && $status != 1) || strlen($id) < 1) {
        return -1;
    }
    $con = connect();

    $checkSQL = $con->prepare("SELECT * FROM settings WHERE id = ?");
    $checkSQL->bind_param("i", $id);
    if (!$checkSQL->execute()) {
        return -1;
    }
    $result = $checkSQL->get_result();
    $row = $result->fetch_assoc();
    $name = $row['category'];
    $name = str_replace("_", " ", $name);
    if ($result->num_rows != 1) {
        return -2;
    }
    $updateSQL = $con->prepare("UPDATE settings SET value = ? WHERE id = ?");
    $updateSQL->bind_param("ii", $status, $id);
    if (!$updateSQL->execute()) {
        return -1;
    }

    logDown(0, "Settings changed for $name with $status ", 2, 3);
    return 1;
}

function delSupervisors($id)
{
    $N = count($id);
    if ($N < 1) {
        return 0;
    }

    $con = connect();
    $assigned = 0;
    for ($i = 0; $i < $N; $i++) {
        //We check to see if this staff is already assigned to field not empty
        $checkSQL = $con->prepare("SELECT * FROM cpu WHERE supervisor_id =  ? AND full > 0");
        $checkSQL->bind_param("i", $id[$i]);
        if (!$checkSQL->execute()) {
            continue;
        }

        $row = $checkSQL->get_result();
        $existSQL = $con->prepare("SELECT * FROM supervisors WHERE id = ?");
        $existSQL->bind_param("i", $id[$i]);
        if (!$existSQL->execute()) {
            return script("Denied", 1);
        }

        $res = $existSQL->get_result();
        if ($res->num_rows != 1) {
            return script("Supervisor Not Found", 1);
        }

        $supervisorRow = $res->fetch_assoc();
        $passportLocation = $supervisorRow['location'];
        if (strlen($passportLocation) < 4) {
            //Try to delete
            @unlink("../uploads/" . $passportLocation); //Possible notices, warnings surpressed with @
        }
        if ($row->num_rows > 0) {
            $assigned++;
            continue;
        }
        //Let us delete all messages sent from th(ese)is supervisor(s)
        $query1 = $con->prepare("DELETE FROM supervisors where id = ?");
        $query1->bind_param("i", $id[$i]);
        if (!$query1->execute()) {
            return script("Denied", 1);
        }

        $query = $con->prepare("DELETE FROM supervisors where id = ?");
        $query->bind_param("i", $id[$i]);
        if (!$query->execute()) {
            return script("Denied", 1);
        }
    }
    if ($assigned) {
        logDown(0, "Tried deleting staff who is currently supervising a student", 2, 3);
        return script("You tried to delete $N supervisor(s), but $assigned could not be deleted because they are already assigned to students", 1);
    }
    logDown(0, "$N staff deleted", 3, 3);
    return script("Action Completed", 1);
}

function delStudents($id)
{
    $N = count($id);
    if ($N < 1) {
        return 0;
    }

    $con = connect();
    for ($i = 0; $i < $N; $i++) {
        $checkSQL = $con->prepare("SELECT id,CONCAT(lastname, ' ', firstname, ' - ',regno) as details FROM students WHERE cpu_id != 0 AND id = ?");
        $checkSQL->bind_param("i", $id[$i]);
        if (!$checkSQL->execute()) {
            return 0;
        }

        $res = $checkSQL->get_result();
        if ($res->num_rows) {
            return -1;
        }

        $row = $res->fetch_assoc();
        $name = $row['details'];
        $query = $con->prepare("DELETE FROM students where id = ? ");
        $query->bind_param("i", $id[$i]);
        if (!$query->execute()) {
            return 0;
        }
    }
    logDown(0, "Student ($name) was deleted", 3, 3);
    return 1;
}

function listTitles($id = -1)
{
    $con = connect();
    $query = $con->query("SELECT id,name FROM titles");
    $formElement = '';
    while ($row = $query->fetch_assoc()) {
        $formElement .= "<option " . (($row['id'] == $id) ? 'selected="selected"' : '') . " value='" . $row['id'] . "'> " . $row['name'] . "</option>";
    }
    return $formElement;
}

function titleIdExists($id)
{
    $con = connect();
    $checkTitle = $con->prepare("SELECT id FROM titles WHERE id = ?");
    $checkTitle->bind_param("i", $id);
    if (!$checkTitle->execute()) {
        return 0;
    }

    $checkTitleResult = $checkTitle->get_result();
    if ($checkTitleResult->num_rows != 1) {
        return 0;
    } else {
        return 1;
    }
}

function signup($firstname, $lastname, $fileno, $password, $cpassword, $answer, $title, $email, $phone)
{

    $noExistsMsg = array("File Number Invalid", "alert-danger");
    $mailPhoneExists = array("Phone Number / Email Invalid", "alert-danger");
    $errorMsg = array("Fill Form Properly", "alert-danger");
    $captchaMsg = array("Captcha Invalid", "alert-danger");
    $sucMsg = array("Registration Completed", "alert-success");
    $disMsg = array("Registration is disabled", "alert-danger");
    $isRegOpen = getSettings('supervisor_reg');
    if ($isRegOpen != 1) {
        return $disMsg;
    }
    if ($answer != $_SESSION['real_captcha']) {
        return $captchaMsg;
    } elseif (strlen($fileno) != 5 || strlen($firstname) < 3 || strlen($lastname) < 3 || strlen($password) < 3 || ($password != $cpassword) || strlen($phone) != 11 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $errorMsg;
    } else {
        $fileno = strtoupper($fileno);
        $firstname = strtoupper($firstname);
        $lastname = strtoupper($lastname);
        $password = salt($password);
        $con = connect();
        if (!titleIdExists($title)) {
            return $errorMsg;
        }

        $checkPhoneAndEmail = $con->prepare("SELECT * FROM supervisors WHERE email = ? or phone = ?");
        $checkPhoneAndEmail->bind_param("ss", $email, $phone);
        if (!$checkPhoneAndEmail->execute()) {
            return $mailPhoneExists;
        }
        $resCheck = $checkPhoneAndEmail->get_result();
        if ($resCheck->num_rows > 0) {
            return $mailPhoneExists;
        }

        mysqli_close($con); //Prepared Statements > 1
        $con = connect();
        $existSQL = $con->prepare("SELECT * FROM supervisors WHERE fileno = ? AND title_id = 0");
        $existSQL->bind_param("s", $fileno);
        if (!$existSQL->execute()) {
            return $noExistsMsg;
        } else {
            $result = $existSQL->get_result();
            if ($result->num_rows != 1) {
                return $noExistsMsg;
            }

            $row = $result->fetch_assoc();
            $ID = $row['id'];
            $updateSQL = $con->prepare("UPDATE supervisors SET firstname = ? , lastname = ?, title_id = ?, password = ?,  status = 1, email = ?, phone = ? WHERE fileno = ?");
            $updateSQL->bind_param("ssissss", $firstname, $lastname, $title, $password, $email, $phone, $fileno);
            if (!$updateSQL->execute()) {
                return $errorMsg;
            }
            $msg = "Dear $lastname, $firstname,
            Your account has now been created and auto-validated.
            We are glad to have you here with us.";
            sendMail($email, "Welcome to UNILORIN Project Supervision System", $msg);
            sms($msg, $phone);
            logDown($ID, "Account creation with $fileno", 1, 2);
            return $sucMsg;
        }
        return $errorMsg;
    }
}

function canThisIdRequestForChange($id)
{
    $con = connect();
    $query = $con->query("SELECT students.id FROM `students` INNER JOIN cpu ON students.cpu_id = cpu.id WHERE students.id = '$id' AND students.id NOT IN (SELECT change_request.student_id FROM change_request) LIMIT 1");
    return $query->num_rows;
}

function sendChangeOfField($reason, $id)
{
    if (strlen($reason) < 10 || !canThisIdRequestForChange($id)) {
        return "<script>alert('Kindly Fill This Form Carefully');</script>";
    }
    $con = connect();
    $supervisor = $con->query("SELECT cpu.supervisor_id as id FROM students INNER JOIN cpu on cpu.id = students.cpu_id WHERE students.id = '$id'");
    if (!$supervisor->num_rows) {
        return "<script>alert('You do not seem to have a supervisor yet');</script>";
    }

    $result = $supervisor->fetch_assoc();
    $supervisor_id = $result['id'];
    $checkSQL = $con->query("SELECT * FROM change_request WHERE student_id = '$id'");
    //Let us check if the student already sent progress/project upload
    $progCheck = $con->query("SELECT * FROM progress WHERE student_id = '$id' ");
    if ($progCheck->num_rows) {
        return script("You are not allowed to do this. You have already sent a report on your project progress", 1);
    }

    if ($checkSQL->num_rows > 0) {
        return "<script>alert('You have already used this once. You Will Not Be Allowed To Do This Again');</script>";
    }

    $query = $con->prepare("INSERT INTO `change_request`( `student_id`, `supervisor_id`, `reason`) VALUES (?,?,?)");
    $query->bind_param("iis", $id, $supervisor_id, $reason);
    if (!$query->execute()) {

        return "<script>alert('Kindly Fill This Form Carefully');</script>";
    } else {
        logDown($id, "Request to change field sent", 2, 1);
        $adminMail = getAdminDetails('email');
        $adminPhone = getAdminDetails('phone');
        $msg = "Dear System Administrator,
        one of the students just sent a request for change of field.
        Kindly attend to this request.
        Thank you!";
        sms($msg, $adminPhone);
        sendMail($adminMail, 'Change of Field Request', $msg);
        return "<script>alert('Form successfully submitted');</script>";
    }
}

function getAdminDetails($type)
{
    $con = connect();
    $query = ($con->query("SELECT * FROM users"))->fetch_assoc();
    return $query[$type];
}

function signupstudent($firstname, $lastname, $regno, $password, $cpassword, $answer, $email, $phone)
{

    $errorMsg = array("Fill Form Properly", "alert-danger");
    $noExistsMsg = array("Matric Number Invalid", "alert-danger");
    $mailPhoneExists = array("Email/Phone Already Exist", "alert-danger");
    $captchaMsg = array("Captcha Invalid", "alert-danger");
    $sucMsg = array("Registration Completed", "alert-success");
    $disMsg = array("Registration is disabled", "alert-danger");
    $isRegOpen = getSettings('student_reg');
    if ($isRegOpen != 1) {
        return $disMsg;
    }
    if ($answer != $_SESSION['real_captcha']) {
        return $captchaMsg;
    } elseif (strlen($regno) != 10 || strlen($phone) != 11 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($firstname) < 3 || strlen($lastname) < 3 || strlen($password) < 3 || strlen($email) < 5 || ($password != $cpassword)) {
        return $errorMsg;
    } else {
        $regno = strtoupper($regno);
        $email = strtolower($email);
        $firstname = strtoupper($firstname);
        $lastname = strtoupper($lastname);
        $password = salt($password);
        $con = connect();
        $existSQL = $con->prepare("SELECT * FROM students WHERE regno = ? AND phone = 0");
        $existSQL->bind_param("s", $regno);
        if (!$existSQL->execute()) {
            return $noExistsMsg;
        } else {
            $result = $existSQL->get_result();
            if ($result->num_rows != 1) {
                return $noExistsMsg;
            }

            $row = $result->fetch_assoc();
            $ID = $row['id'];
            // echo getThisFromStudent($regno,"email");exit;
            $checkPhoneAndEmail = $con->prepare("SELECT * FROM students WHERE email = ? or phone = ?");
            $checkPhoneAndEmail->bind_param("ss", $email, $phone);
            if (!$checkPhoneAndEmail->execute()) {
                return $mailPhoneExists;
            }
            $checkPhoneAndEmailResult = $checkPhoneAndEmail->get_result();
            if ($checkPhoneAndEmailResult->num_rows) {
                return $mailPhoneExists;
            }

            $updateSQL = $con->prepare("UPDATE students SET firstname = ? , lastname = ?, email = ?, phone = ?, password = ?,  status = 1 WHERE regno = ?");
            $updateSQL->bind_param("ssssss", $firstname, $lastname, $email, $phone, $password, $regno);
            if (!$updateSQL->execute()) {
                return $errorMsg;
            }
            $msg = "Dear $lastname, $firstname,
            Your account has now been created and auto-validated.
            We are glad to have you here with us.";
            sendMail($email, "Welcome to UNILORIN Project Supervision System", $msg);
            sms($msg, $phone);
            logDown($ID, "Account creation for student $regno", 1, 1);

            return $sucMsg;
        }
        return $errorMsg;
    }
}

function getDetailsById($id, $user = "students")
{
    $con = connect();
    $table = "supervisors";
    if ($user == "students") {
        $table = "students";
    }

    $query = $con->prepare("SELECT * FROM $table WHERE id = ?");
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        echo "Access Denied";
        exit;
    } else {

        $result = $query->get_result();
        if ($result->num_rows != 1) {
            //Log down
            session_destroy();
            echo "<script>alert('Stop Modifying URls');window.location='logout.php';</script>";
            exit;
        }
        return $result->fetch_assoc();
    }
}

function salt($string)
{
    return md5(md5($string));
}


function login($username, $password, $answer, $cat)
{
    $con = connect();
    if ($answer != @$_SESSION['real_captcha']) {
        echo script('Captcha Verification Failed');
    } elseif (strlen($username) < 3) {
        echo script('Username is required');
    } elseif (strlen($password) < 3) {
        echo script('Password is required');
    } elseif ($cat != 0 && $cat != 1) {
        echo "<script>alert('Fill form properly'); </script>";
    } else {
        $username = strtoupper($username);
        $table = "supervisors";
        $user = 2;
        $where = "fileno";
        $category = "supervisor";
        $location = "supervisor.php";
        if ($cat == 0) {
            $table = "students";
            $where = "regno";
            $user = 1;
            $category = "student";
            $location = "dashboard_student.php";
        }
        $login_var = $category . "_login";

        $login_allowed = getSettings($login_var);
        if ($login_allowed == 0) {
            session_destroy();
            echo script(ucwords($table) . " access to the portal has been denied by the admin", 'index.php');
            exit;
        }
        $password = salt($password);
        // die($password);
        $con = connect();
        $query = $con->prepare("SELECT status FROM $table WHERE $where = ? AND password = ? ");
        $query->bind_param("ss", $username, $password);
        if (!$query->execute()) {
            echo "<script>alert('Fill form properly.'); </script>";
        } else {
            $result = $query->get_result();
            $row = $result->fetch_assoc();
            if ($result->num_rows == 1) {
                //Record found
                if ($row['status'] != 1) {
                    //Inactive user
                    echo script('You are not an active user.\nReason: Account Deactivated.', 1);
                    exit;
                    die();
                } else {
                    //Record found and valid
                    session_regenerate_id(true);
                    $_SESSION['id'] = $username;
                    $id = getIdFromSession($username, $category);
                    $_SESSION['category'] = $category;
                    $_SESSION['LAST_ACTIVITY_' . strtoupper($table)] = time();
                    logDown($id, "Login validated", 3, $user);
                    echo script('Access Granted', $location);
                }
            } else {
                echo "<script>alert('Authentication Failed'); </script>";
            }
        }
    }
}

function getStamp()
{
    date_default_timezone_set("Africa/Lagos");
    $stamp = date('D, d-M-Y h:i A');
    return $stamp;
}

function logDown($id, $msg, $level, $user = 1)
{
    /*
     * user 1 : Student
     * user 2 : Supervisor
     * user 3 : Admin
     */
    if ($user == 1) {
        $table = 'student_logs';
    } elseif ($user == 2) {
        $table = 'supervisor_logs';
    } else {
        $table = 'admin_logs';
    }
    $con = connect();
    $stamp = getStamp();
    $con->query("INSERT INTO $table VALUES (NULL,'$id','$stamp','$msg', '$level')");
}

function getUserFullName($id, $cat = "student")
{
    $con = connect();
    $output = "";

    if ($cat == "student") {
        $query = $con->query("SELECT firstname,lastname FROM students WHERE regno = '$id' LIMIT 1");
        $result = $query->fetch_assoc();
        $output = strtoupper($result['lastname']) . ", " . ucwords(strtolower($result['firstname']));
    } else {

        $query = $con->query("SELECT supervisors.firstname AS fn,supervisors.lastname AS ln, titles.name FROM supervisors INNER JOIN titles ON titles.id = supervisors.title_id WHERE fileno = '$id' LIMIT 1");
        $result = $query->fetch_assoc();

        $output = ucwords(strtolower($result['name'])) . " " . ucwords(strtolower($result['ln'])) . " " . ucwords(strtolower($result['fn']));
    }
    return $output;
}

function canThisIdAccess($id)
{
    $con = connect();
    $staffid = getIdFromSession($_SESSION['id'], "supervisor");
    $query = $con->prepare("SELECT * FROM cpu WHERE id = ? AND supervisor_id = ? ");
    $query->bind_param("is", $id, $staffid);
    if (!$query->execute()) {
        return -1;
    }

    $result = $query->get_result();
    if ($result->num_rows < 1) {
        return -1;
    }

    return 1;
}

function canThisStudentIdBeAccess($id)
{
    $con = connect();
    $staffid = getIdFromSession(@$_SESSION['id'], "supervisor");
    $query = $con->prepare("SELECT students.id FROM students INNER JOIN cpu ON cpu.id = students.cpu_id WHERE cpu.supervisor_id = ? AND students.id = ?");
    $query->bind_param("is", $staffid, $id);
    if (!$query->execute()) {
        return -1;
    }

    $result = $query->get_result();
    if ($result->num_rows < 1) {
        return -1;
    }

    return 1;
}

function countById($id)
{
    if (canThisIdAccess($id) == -1) {
        return -1;
    }
    $con = connect();
    $query = $con->prepare("SELECT * FROM students WHERE cpu_id = ?");
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        return -1;
    }

    $result = $query->get_result();
    return $result->num_rows;
}

function uploadPassport($file, $id, $user = "student")
{
    if (!$_FILES[$file]['size'] || ($_FILES[$file]['size'] / 1024) > IMAGE_SIZE) {
        return "<script>alert('Kindly Select Valid Image With Size Not More Than " . IMAGE_SIZE . ". KB');</script>";
    }

    $valid_extension = array("jpg", "png", "gif", "jpeg");
    if ($_FILES[$file]['size'] && !in_array(strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION)), $valid_extension)) {
        return "<script>alert('Invalid Image Selected');</script>";
    }
    $con = connect();
    if ($_FILES[$file]['size']) {
        $table_name = "supervisors";
        $where = "fileno";
        if ($user == 'student') {
            $table_name = 'students';
            $where = "regno";
        }
        $query = $con->query("SELECT `location` FROM $table_name WHERE $where = '$id'");
        if ($query->num_rows != 1) {
            return "<script>alert('Record Does Not Exist');</script>";
        }
        $row = $query->fetch_assoc();
        $loc = $row['location'];
        if (strlen($loc) > 7) {
            //try to delete the file if it exists
            @unlink("uploads/" . $loc); //Error surpressed with the @ symbol because the file might not exist or the file might be a readonly file
        }
        //Let us now move the uploaded file to the server
        $loc = genRand() . "." . strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION));
        $upload = move_uploaded_file(@$_FILES[$file]['tmp_name'], "uploads/" . $loc);
        if (!$upload) {
            return "<script>alert('Image not uploaded\nConsider allowing read/write permissions');</script>";
        }
        chmod("uploads/" . $loc, 0777);
        $update = $con->query("UPDATE $table_name SET location = '$loc' WHERE $where = '$id'");
        if ($update) {
            return "<script>alert('Upload Successful');</script>";
        } else {
            return "<script>alert('Upload Not Successful');</script>";
        }
    }
}

function getPassport($user, $cat = "student")
{
    $table_name = "supervisors";
    $where = "fileno";
    if ($cat == 'student') {
        $table_name = 'students';
        $where = "regno";
    }
    $output = "no.jpg";
    $con = connect();
    $query = $con->query("SELECT location FROM $table_name WHERE $where = '$user'");
    $row = $query->fetch_assoc();
    if (strlen($row['location']) > 7) {
        $output = $row['location'];
    }

    return $output;
}

function getIdFromSession($session, $user = 'student')
{
    $table_name = "supervisors";
    $where = "fileno";
    if ($user == 'student') {
        $table_name = 'students';
        $where = "regno";
    }
    $con = connect();
    $query = $con->prepare("SELECT id FROM $table_name WHERE $where = ?");
    $query->bind_param("s", $session);
    if (!$query->execute()) {
        return -1;
    }

    $result = $query->get_result();
    $row = $result->fetch_assoc();
    $id = $row['id'];
    return $id;
}

function validateIfFieldHasBeenChosen()
{
    $con = connect();
    $id = getIdFromSession($_SESSION['id'], "staff"); //! here
    // // $check = $con->query("SELECT * FROM cpu WHERE supervisor_id = '$id'")
    // //First we check if students has already been assigned before this supervisor choose fields
    // //Also, ensure that the total assigned is not upto the students assigned
    // $init = $con->query("SELECT students.id FROM cpu INNER JOIN students ON students.cpu_id = cpu.id INNER JOIN supervisors ON supervisors.id = cpu.supervisor_id  WHERE cpu.supervisor_id = '$id' ");
    // $count = $init->num_rows;
    $check = $con->query("SELECT max FROM supervisors where id = '$id' AND field = 1");
    // $row = $check->fetch_assoc()['max'];
    // if ($count == $row) {
    //     return 0;
    // }
    if ($check->num_rows > 0) {
        return 1;
    }
    return 0;
}

function listAllGroups()
{
    $con = connect();
    $query = $con->query("SELECT cpu.id as id, cpu.full as full, cpu.no as no, field_of_interests.name as name FROM cpu INNER JOIN field_of_interests ON field_of_interests.id = cpu.field_id WHERE cpu.supervisor_id = '" . getIdFromSession($_SESSION['id'], 'staff') . "' ");
    return $query;
}

function listAllStudents()
{
    $con = connect();
    $id = getIdFromSession($_SESSION['id'], 'supervisor');
    $query = $con->query("SELECT students.location as location, students.email as email, students.phone as phone, CONCAT(students.regno, ' - ',students.lastname, ' ', students.firstname) AS std, students.id as id FROM students INNER JOIN cpu ON students.cpu_id = cpu.id WHERE cpu.supervisor_id = '$id' ");
    return $query;
}

function saveFields($fields, $no)
{
    $countFields = @count($fields);
    $countNumber = @count($no);
    if ($countNumber < 1 || $countFields < 1 || ($countNumber != $countFields)) {
        return "<script>alert('Fill form properly');</script>";
    } else {
        $con = connect();
        $id = getIdFromSession($_SESSION['id'], "staff");
        //Check if the supervisor has already done this phase
        $check = $con->query("SELECT * FROM supervisors WHERE id = '$id' AND field = 1");
        if ($check->num_rows > 0) {
            return '<script>alert("System detects that you have already done this phase\nYou will not be allowed to re-choose fields");</script>';
        } else {

            for ($i = 0; $i < $countFields; $i++) {
                if ($no[$i] == 0) { //Ensuring that checked checkboxes has a value in front
                    return "<script>alert('Kindly Fill In Number Of Students You\'d Like For Each Selected Fields');</script>";
                }
            }
            $total = array_sum($no);

            $myMax = getMyMax($id);
            $cpuMax = getCPUMaxById($id);
            if ($total > $myMax) {
                logDown($id, "Tried to assign $myMax instead of maximum of $total", 3, 2);
                return script('Attempt Blocked.\nThe maximum number assigned to you ' . $myMax . ' but you just tried to assign ' . $total . ' for yourself.');
                exit;
            }
            if (($cpuMax + $total) > $myMax) {
                logDown($id, "Tried assigning more than max", 3, 2);
                return script('Attempt Blocked.\nThe maximum number assigned to you ' . $myMax . ' but you just tried to assign ' . $total . ' for yourself (You have ' . $cpuMax . ' assigned already).');
                exit;
            }
            //Supervisor has not chosen field(s) of interests
            for ($i = 0; $i < $countFields; $i++) {
                $checkUpdate = $con->prepare("SELECT * FROM cpu WHERE supervisor_id = ? AND field_id = ?");
                $checkUpdate->bind_param("ii", $id, $fields[$i]);
                if (!$checkUpdate->execute()) {
                    return "<script>alert('Form Not Properly Filled. Only Integers Are Required');</script>";
                }
                $res = $checkUpdate->get_result();
                if ($res->num_rows == 1) {
                    //Update the field
                    $up = $con->query("UPDATE cpu SET no = no + $no[$i] WHERE supervisor_id = '$id' AND field_id = '$fields[$i]' ");
                    if (!$up) {
                        return "<script>alert('Form Not Properly Filled. Only Integers Are Required');</script>";
                    }
                } else {
                    $query = $con->prepare("INSERT INTO cpu (field_id,supervisor_id,no) VALUES (?,?,?) ");
                    $query->bind_param("iii", $fields[$i], $id, $no[$i]);
                    if (!$query->execute()) {
                        return "<script>alert('Form Not Properly Filled. Only Integers Are Required');</script>";
                    }
                }
            }
            $con->query("UPDATE supervisors SET field = 1 WHERE id = '$id'");
            $total = $cpuMax + $total;
            return "<script>alert('Record Saved. Total Number Of Students You\'d Be Supervising = $total');window.location='" . $_SERVER['PHP_SELF'] . "'</script>";
        }
    }
}

function genImageFromText($txt)
{
    if (strlen($txt) > 1) {
        // $img = imagecreate(124, 140);
        $font = 300;
        $w = 300;
        $space = 4;
        $txt = str_replace(" ", "\n", $txt);
        $h = imagefontheight($font);
        $fw = imagefontwidth($font);
        $color = "3B5998";
        $txt = explode("\n", wordwrap($txt, ($w / $fw), "\n"));
        $int = hexdec($color);
        $lines = count($txt);
        $im = imagecreate(124, 140);
        // $im = imagecreate($w, (($h * $lines) + ($lines * $space)));
        // $bg = imagecolorallocate($im, 255, 255, 255);
        $bg = imagecolorallocate($im, 173, 230, 181);

        $color = imagecolorallocate(
            $im,
            0xFF & ($int >> 0x10),
            0xFF & ($int >> 0x8),
            0xFF & $int
        );
        $y = 20;
        foreach ($txt as $text) {
            ob_start();

            $x = (($w - ($fw * strlen($text))) / 2);
            imagestring($im, $font, 20, $y, ucwords(strtolower($text)), $color);
            $y += ($h + $space);
        }
        echo imagepng($im);
        printf('<img src="data:image/png;base64,%s"/ width="124" height="140">', base64_encode(ob_get_clean()));
    }
}

function updateRecordById($id, $firstname, $lastname, $password, $title = -1, $max = 0, $user = "students", $email = "", $phone = "")
{
    if (strlen($id) < 1 || strlen($firstname) < 3 || strlen($lastname) < 3 || (strlen($password) > 0 && strlen($password) < 3) || strlen($max) > 2) {
        return script('Fill Form Properly.');
    } else {
        $con = connect();
        if ($title != -1) {
            if (!titleIdExists($title) && $user != 'students') {
                return script('Title Mismatch');
            }
        }

        $table = "students";
        if ($user != 'students') {
            $table = 'supervisors';
        }

        if (strlen($password) > 0) {
            $password = salt($password);
            if ($table != 'students') {
                $max = intval($max);
                //Get Current Allocated Number Of Students
                $getCount = countTotalBySupId($id);
                $full = $getCount['full'];
                if ($full > $max) {
                    return script('Oh Snap\nWe could not get that saved!\nNumber of Students Already Assigned To This Supervisor Is ' . $full . ', and you are trying to reduce this to ' . $max);
                }

                if ($max < 1) {
                    return script("Maximum Value Error");
                }

                $sql = "UPDATE supervisors SET firstname = ?, lastname = ? , title_id = ?,  password = ?, max = ? WHERE id = ?";
                $updateSQL = $con->prepare($sql);
                $updateSQL->bind_param("ssisii", $firstname, $lastname, $title, $password, $max, $id);
            } else {
                $sql = "UPDATE students SET firstname = ?, lastname = ? , password = ? WHERE id = ?";
                $updateSQL = $con->prepare($sql);
                $updateSQL->bind_param("sssi", $firstname, $lastname, $password, $id);
            }
        } else {
            if ($table != 'students') {
                $max = intval($max);
                //Get Current Allocated Number Of Students
                $getCount = countTotalBySupId($id);
                $full = $getCount['full'];
                if ($full > $max) {
                    return script('Oh Snap\nWe could not get that saved!\nNumber of Students Already Assigned To This Supervisor Is ' . $full . ', and you are trying to reduce this to ' . $max);
                }

                if ($max < 1) {
                    return script("Maximum Value Error");
                }

                $sql = "UPDATE supervisors SET firstname = ?, lastname = ? , title_id = ?, max = ? WHERE id = ?";
                $updateSQL = $con->prepare($sql);
                $updateSQL->bind_param("ssiii", $firstname, $lastname, $title, $max, $id);
            } else {
                if (strlen($phone) != 11 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) < 7) {
                    return script("Fill form properly");
                }

                $checkPhoneAndEmail = $con->prepare("SELECT * FROM students WHERE (email = ? or phone = ?) AND id != ?");
                $checkPhoneAndEmail->bind_param("ssi", $email, $phone, $id);
                if (!$checkPhoneAndEmail->execute()) {
                    return script('Mail/Phone Already Exists');
                }
                $checkPhoneAndEmailResult = $checkPhoneAndEmail->get_result();
                if ($checkPhoneAndEmailResult->num_rows) {
                    return script('Mail/Phone Exists');
                }

                $sql = "UPDATE students SET email = ?, phone = ?,  firstname = ?, lastname = ? WHERE id = ?";
                $updateSQL = $con->prepare($sql);
                $updateSQL->bind_param("ssssi", $email, $phone, $firstname, $lastname, $id);
            }
        }
        if (!$updateSQL->execute()) {
            return "<script>alert('Something about you is not right!'); </script>";
        }

        return "<script>alert('Record Updated Successfully'); </script>";
    }
}

function isStudentAssigned($id, $type = 0)
{
    $con = connect();
    if ($type == 0) {
        $arr = array(0, -1, 0);
        $query = $con->query("SELECT name FROM field_of_interests f JOIN cpu c ON f.id = c.field_id JOIN students s ON c.id =s.cpu_id  WHERE s.regno = '$id'  LIMIT 1");
        if ($query->num_rows != 1) {
            return $arr;
        }

        $row = $query->fetch_assoc();
        $arr = array(1, $row['name']);
        return $arr;
    } else {
        $query = $con->query("SELECT id FROM students WHERE cpu_id != 0 AND id = '$id' ");
        return $query->num_rows;
    }
}

function isIdAlreadyAllocated($id)
{
    $con = connect();
    $query = $con->query("SELECT cpu_id FROM students WHERE regno = '$id'");
    $row = $query->fetch_assoc();
    return ($row['cpu_id']);
}

function checkForFreeFields($against)
{
    $con = connect();
    //First, we check to be sure students are not selecting same fields
    //$check = $con->query("SELECT * FROM allocation WHERE field_id = '$against'");
    //$total_students = countStudents();
    // $this_field = $con->query("SELECT allocation.id FROM allocation INNER JOIN students ON students.id = allocation.student_id INNER JOIN cpu ON cpu.id = students.cpu_id WHERE allocation.field_id = '$against'")->num_rows;
    // $total_fields = countFields();
    // $percentage = ($this_field / $total_students) * 100;
    // die("Total students = $total_students and Fields = $total_fields AND $this_field while perc == $percentage");
    //* Let's check if this field is selected
    $exist = $con->query("SELECT * FROM cpu WHERE field_id = '$against'")->num_rows;
    if ($exist) {
        $sql = "SELECT field_of_interests.name as name FROM cpu INNER JOIN field_of_interests ON cpu.field_id = field_of_interests.id WHERE cpu.no != cpu.full ";
        $query = $con->query($sql);
        $output = "";
        if ($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                $output .= $row['name'] . ", ";
            }
        } else {
            $output = "";
        }
    } else {
        $output = "";
    }

    return $output;
}

function forceAllocate($student_id, $sup_id, $field_id)
{

    $con = connect();

    $insert_status = false;

    $checkIfItExists = $con->query("SELECT * FROM cpu WHERE field_id = '$field_id' AND supervisor_id = '$sup_id' ");
    if ($checkIfItExists->num_rows < 1) {
        $insert_status = true;
        $insert = $con->query("INSERT INTO cpu (field_id, supervisor_id, no, full) VALUES ('$field_id','$sup_id','1','1')");
        $thisNewId = $con->insert_id;
    } else {
        //? Two things involved
        //! 1. Has the supervisor used up his/her no in CPU?
        $fetch = $con->query("SELECT * FROM cpu WHERE supervisor_id = '$sup_id' AND field_id = '$field_id'")->fetch_assoc();
        $db_no = $fetch['no'];
        $db_full = $fetch['full'];
        //! Case 1 : Is the quota filled up ?
        if ($db_no == $db_full) {
            //Kindly update both
            $insert = $con->query("UPDATE cpu SET no = no + 1, full = full + 1 WHERE  supervisor_id = '$sup_id' AND field_id = '$field_id'");
        } else {
            //full equals no
            $insert = $con->query("UPDATE cpu SET full = full + 1 WHERE  supervisor_id = '$sup_id' AND field_id = '$field_id'");
        }
        $this_id = $con->query("SELECT id FROM cpu WHERE  supervisor_id = '$sup_id' AND field_id = '$field_id'")->fetch_assoc();
        $thisNewId = $this_id['id'];
    }


    if ($insert) {
        //First we check if students has already been assigned before this supervisor choose fields

        $update = $con->query("UPDATE students SET cpu_id = '$thisNewId' WHERE id = '$student_id'; ");
        if (!$update) {
            //Reverse action if the update failed
            $insert_status ? $con->query("DELETE FROM cpu WHERE id = '$thisNewId'") : $con->query("UPDATE cpu SET no = no -1, full = full -1 WHERE  supervisor_id = '$sup_id' AND field_id = '$field_id'");
            return 0;
        } else {
            $init = $con->query("SELECT students.id, supervisors.max FROM cpu INNER JOIN students ON students.cpu_id = cpu.id INNER JOIN supervisors ON supervisors.id = cpu.supervisor_id  WHERE cpu.supervisor_id = '$sup_id' ");
            $row = $init->fetch_assoc();
            $num = $init->num_rows;
            if ($num) {
                if ($num == $row['max']) {

                    //? Check to see if the total assigned is the max of supervisor
                    $con->query("UPDATE supervisors SET field = 1 WHERE id = '$sup_id'");
                }
            }

            return 1;
        }
    }

    return 0;
}
function lastResort($student_id, $field_id)
{
    //First, we check to see lazySupervisors (lecturers who are yet to be seen in table 'cpu')
    $con = connect();
    $regno = getRegnoFromId($student_id);
    if ($field_id == -1) {
        //Student chose none
        $check_free = $con->query("SELECT id FROM `field_of_interests` ORDER BY RAND() LIMIT 1 ");
        $row = $check_free->fetch_assoc();
        $field_id = $row['id'];
    }
    $query_check_cpu = $con->query("SELECT id FROM supervisors WHERE id NOT IN (SELECT supervisor_id FROM cpu) ORDER BY max ASC;");

    //We check to see if there are no lazy supervisors
    if ($query_check_cpu->num_rows) {
        //There are lazy, force feed them into cpu table based on the first interest of student
        //Pick a supervisor by random
        $row = $query_check_cpu->fetch_assoc();
        $sup_id = $row['id'];
        $response = forceAllocate($student_id, $sup_id, $field_id);
        if ($response) {
            logDown($student_id, "Student allocation was done by checking for supervisors who are lazy. The staff (" . getSupervisorNameById($sup_id) . ") was seen for student " . getStudentDetailsById($student_id), 1, 1);
        } else {
            logDown($student_id, "Student allocation failed while trying to assign supervisors who are lazy. The staff (" . getSupervisorNameById($sup_id) . ") was seen for student " . getStudentDetailsById($student_id), 3, 1);
        }
    } else {
        //There are no lazy supervisors
        //So, we move on to check for supervisors who are yet to use up their quota
        $sql = $con->query("SELECT supervisors.max AS supMax, cpu.no as cpuNo, supervisors.id as sup_id FROM supervisors INNER JOIN cpu ON cpu.supervisor_id = supervisors.id WHERE supervisors.id IN (SELECT cpu.supervisor_id FROM cpu)
        GROUP BY cpu.supervisor_id
        HAVING SUM(cpu.no) != ABS(supervisors.max)
        ORDER BY RAND()");
        if ($sql->num_rows) {
            $row = $sql->fetch_assoc();
            $sup_id = $row['sup_id'];
            //There are supervisors who are yet to use up their quota
            //Force feed students best preference
            $response = forceAllocate($student_id, $sup_id, $field_id);
            if ($response) {
                logDown($student_id, "Student allocation was done by checking for supervisors who are yet to use up their quota. The staff (" . getSupervisorNameById($sup_id) . ") was seen for student " . getStudentDetailsById($student_id), 1, 1);
            } else {
                logDown($student_id, "Student allocation failed while trying to assign supervisors who are yet to use up their quota. The staff (" . getSupervisorNameById($sup_id) . ") was seen for student " . getStudentDetailsById($student_id), 3, 1);
            }
        } else {
            //All supervisors used up their quota already (ADMIN is at fault here for not putting into consideration the number of students)
            //Redirect into contacting the admin of a failed allocation
            notifyAdminOnAllocate($regno, "Chose none and system could not allocate because there are no available fields");
            $response = 0;
        }
    }

    return $response; // 1 for success, 0 for failed
}

function isAssignRequestById($id)
{
    $query = connect()->query("SELECT  id FROM assign_request WHERE status = 0 AND student_id = '$id';");
    return ($query->num_rows);
}

function saveToHistory($studentId, $field, $pref)
{
    $con = connect();
    //Check if it exists - Students are give the flexibility to change course
    $check = ($con->query("SELECT * FROM allocation WHERE student_id = '$studentId'"))->num_rows;
    if ($check) {
        $query = $con->query("UPDATE allocation SET field_id = '$field' , preference = '1' WHERE student_id = '$studentId'");
    } else {
        $query = $con->query("INSERT INTO allocation (student_id, field_id, preference) VALUES ('$studentId','$field','$pref')");
    }
}
function allocator($fields, $id)
{
    $checkID = getIdFromSession($id);
    if (isAssignRequestById($checkID)) {
        logDown($id, "Has a pending request but tried to allocate", 2, 1);
        return "<script>alert('System Detects That You Still Have A Pending Request From A Supervisor.');window.location='" . $_SERVER['PHP_SELF'] . "';</script>";
    }
    if (isIdAlreadyAllocated($id)) {
        logDown($id, "Tried to allocate again after already being assigned", 3, 1);
        return "<script>alert('System Detects That You Have Already Completed Your Allocation.');window.location='" . $_SERVER['PHP_SELF'] . "';</script>";
        exit;
    }
    //fields is an array which contain all what the student selected (in preference)
    //id is the ID of the student who called this function
    $max = 3; //Maximum fields allowed
    $count = sizeof($fields);

    //This tells us how many elements are there in the fields
    foreach ($fields as $key => $value) {
        if (empty($value) && $value != 0) {
            logDown($id, "Didn't select any field and submitted the form", 1, 1);
            return "<script>alert('You are to select just 3 fields of interest');</script>";
            break;
        }
    }

    if ($max != $count) {
        return "<script>alert('You are to select just 3 fields of interest!');</script>";
    }
    $same = 0;
    if (count(array_unique($fields)) == 1) {
        $same = 1;
    }
    $available = checkForFreeFields($fields[0]); //Generate available fields of interest
    if ($fields[0] == 0) {
        echo automateAllocation($id);
        logDown($id, "Allocated automatically", 1, 2);
        return;
    }
    //CPU has been checked but no free supervisors, so we move on to check supervisors with available space in their quota
    if (strlen($available) > 4) {
        $suggestion = "Available Fields = $available";
    }
    $suggestion = str_replace("'", "", $suggestion);
    $suggestion = str_replace('"', "", $suggestion);
    if ($same) {
        //Student Chose All Three Fields Of Interest As Same
        //Loop just once
        $idExists = isThisIdInDatabase($fields[0]);
        $getField = getFieldById($fields[0]);
        $field_name = $getField['name'];
        if (!$idExists && $fields[0] != 0) {
            //Id does not exists
            logDown($id, "Somehow managed to select an invalid field", 3, 1);
            return "<script>alert('Invalid Field Selected');</script>";
        } else {

            //Id exists
            $tryToAssign = canThisIdBeAssigned($fields[0]);
            if ($tryToAssign) {
                finalizeAllocation($tryToAssign, $id);
                saveToHistory($checkID, $fields[0], 1);
                logDown($id, "Project allocation success ($field_name)", 1, 1);
                return "<script>alert('Congratulations. $field_name Allocation was successful');window.location='" . $_SERVER['PHP_SELF'] . "'</script>";
            } else {
                if (strlen($available) > 4) {
                    return "<script>alert('Allocation Failed. System will generate available fields');document.write('$suggestion');</script>";
                } else {
                    $lr = lastResort($checkID, $fields[0]);
                    if ($lr == 1) {
                        finalizeAllocation($tryToAssign, $id, 1);
                        saveToHistory($checkID, $fields[0], 1);
                        logDown($id, "Project allocation success ($field_name)", 1, 1);

                        return "<script>alert('Congratulations. $field_name Allocation was successful');window.location='" . $_SERVER['PHP_SELF'] . "'</script>";
                    } else {
                        return "<script>alert('Allocation Failed. ');document.write('Try again later.');</script>";
                    }
                }
            }
        }
    } else {
        //Start of Student Interest Differs
        for ($i = 0; $i < $max; $i++) {
            $idExists = isThisIdInDatabase($fields[$i]);
            $getField = getFieldById($fields[$i]);
            $field_name = $getField['name'];
            if ($fields[$i] == 0) {
                //Perform random allocation (Student chose none)
                echo automateAllocation($id);
                logDown($id, "Allocated automatically", 1, 2);
                return;
                break;
            } elseif (!$idExists) {
                //No supervisors chose this field
                //Check for lazy supervisors
                $lr = lastResort($checkID, $fields[$i]);
                $tryToAssign = canThisIdBeAssigned($fields[$i]);
                if ($lr == 1) {
                    finalizeAllocation($tryToAssign, $id, 1);
                    //$field_name must have changed because of the loop, so we reset it back
                    $getField = getFieldById($fields[0]);
                    $field_name = $getField['name'];
                    saveToHistory($checkID, $fields[$i], ($i + 1));
                    logDown($id, "Project allocation success ($field_name)", 1, 1);
                    return "<script>alert('Congratulations. $field_name Allocation was successful');window.location='" . $_SERVER['PHP_SELF'] . "'</script>";
                } else {
                    return "<script>alert('Allocation Failed. ');document.write('Try again later.');</script>";
                }
            } else {

                //Id exists
                $tryToAssign = canThisIdBeAssigned($fields[$i]);
                if ($tryToAssign) {
                    finalizeAllocation($tryToAssign, $id);
                    saveToHistory($checkID, $fields[$i], ($i + 1));
                    return "<script>alert('Congratulations. $field_name Allocation was successful');window.location='" . $_SERVER['PHP_SELF'] . "'</script>";
                    break;
                }
            }
        } //End of for loop
        //Let us call the last resort to the first choice of interest
        if (strlen($available) > 4) {
            return "<script>alert('Allocation Failed. System will generate available fields');document.write('$suggestion');</script>";
        } else {
            $lr = lastResort($checkID, $fields[0]);
            $tryToAssign = canThisIdBeAssigned($fields[0]);
            if ($lr == 1) {
                finalizeAllocation($tryToAssign, $id, 1);
                saveToHistory($checkID, $fields[0], 1);
                //$field_name must have changed because of the loop, so we reset it back
                $getField = getFieldById($fields[0]);
                $field_name = $getField['name'];
                logDown($id, "Project allocation success ($field_name)", 1, 1);
                return "<script>alert('Congratulations. $field_name Allocation was successful');window.location='" . $_SERVER['PHP_SELF'] . "'</script>";
            } else {
                return "<script>alert('Allocation Failed. ');document.write('Try again later.');</script>";
            }
        }
        //  return "<script>alert('Selected Fields Already Full. Try Again With Another Fields');document.write('$suggestion');</script>";
    } // End of Student Interest differs
}

function notifyAdminOnAllocate($id, $fields)
{
    $date = getStamp();
    $id = getIdFromSession($id, 'student');
    $phone = getAdminDetails('phone');
    $email = getAdminDetails('email');
    $msg = "Dear System Administrator,
    One of the students could not be allocated because the system detects that there are no available field of interests.";
    sendMail($email, "Failed Allocation", $msg);
    sms($msg, $phone);
    connect()->query("INSERT IGNORE INTO failed_allocate (student_id, entry_date, fields) VALUES ('$id','$date','$fields')");
}

function finalizeAllocation($cpu, $matric, $type = 0)
{
    $con = connect();
    $matric = strtoupper($matric);
    $getPhoneSQL = $con->query("SELECT phone, concat(lastname, ' ', firstname) as fullname, email FROM `students` WHERE regno = '$matric'");
    $result = $getPhoneSQL->fetch_assoc();
    $phone = $result['phone'];
    $name = $result['fullname'];
    $email = $result['email'];
    if ($cpu == 0) {
        //* This means force allocate has been used and thus, we need to select cpu from student
        $dbCpu = $con->query("SELECT cpu_id FROM students WHERE regno = '$matric'")->fetch_assoc()['cpu_id'];
        if ($dbCpu == 0) {
            return "<script>alert('Allocation Error. Try Again');</script>";
        }
        $cpu = $dbCpu;
    }
    if ($type == 0) {
        $query = $con->query("UPDATE students SET cpu_id = '$cpu' WHERE regno = '$matric' LIMIT 1 ");
        $query2 = $con->query("UPDATE cpu SET full = full+1 WHERE id = '$cpu' LIMIT 1");
        if (!$query || !$query2) {
            return "<script>alert('Allocation Error. Try Again');</script>";
        }
    }
    $name = substr($name, 0, 20);
    $field_name = getFieldNameFromCpuID($cpu);
    $field_name = ucwords(strtolower($field_name));
    $name = ucwords(strtolower($name));
    $msg =
        "Dear $name,
  Your project allocation ($field_name) was successful.
  Cheers. :-)";
    sms($msg, $phone);
    $msg = "Dear $name,
Your field of interest is $field_name.
Kindly login to your portal to upload project proposal.
We want to wish you all the best.
Build on the soldier of giants by exploring.";
    sendMail($email, "Project Allocation", $msg);
}

function getStudentCpuIdById($id)
{
    $con = connect();
    $sql = ($con->query("SELECT cpu_id FROM students WHERE id = '$id'"));
    $row = $sql->fetch_assoc();
    $student_cpu = $row['cpu_id'];
    return $student_cpu;
}
function getFieldIdFromCpuId($cpu)
{
    $con = connect();
    $sql = "SELECT field_of_interests.id as id FROM field_of_interests INNER JOIN cpu ON cpu.field_id = field_of_interests.id WHERE cpu.id = '$cpu'";
    $row = ($con->query($sql))->fetch_assoc();
    return $row['id'];
}
function automateAllocation($mat)
{
    $sql = "SELECT cpu.id as id, field_of_interests.id as fid FROM cpu INNER JOIN field_of_interests ON cpu.field_id = field_of_interests.id WHERE cpu.no != cpu.full ORDER BY RAND()";
    $con = connect();
    $query = $con->query($sql);
    $student_id = getIdFromSession($mat);
    if ($query->num_rows == 0) {
        // $id = getIdFromSession($mat, "student");
        $lr = lastResort($student_id, -1);
        if ($lr == 1) {
            //Resort succeed
            $stud_cpu = getStudentCpuIdById($student_id);
            $fieldId = getFieldIdFromCpuId($stud_cpu);
            finalizeAllocation($stud_cpu, $mat, 1);
            saveToHistory($student_id, $fieldId, 1);
            return "<script>alert('Congratulations. Allocation was successful');window.location='" . $_SERVER['PHP_SELF'] . "'</script>";
        } else {
            //Result failed
            return "<script>alert('Oops.  Allocation failed (Possible Reasons: No available fields). Try again later');window.location='" . $_SERVER['PHP_SELF'] . "'</script>";
        }
    } else {
        //Not equal to 0
        $row = $query->fetch_assoc();
        $fieldId = $row['fid'];
        $cpu = $row['id'];
        $fieldId = getFieldIdFromCpuId($cpu);
        finalizeAllocation($cpu, $mat);
        saveToHistory($student_id, $fieldId, 1);
        return "<script>alert('Congratulations. Random allocation was successful');window.location='" . $_SERVER['PHP_SELF'] . "'</script>";
    }
}

function canThisIdBeAssigned($id)
{
    $con = connect();
    $query = $con->query("SELECT full,id,no FROM cpu WHERE field_id = '$id' AND full != no ORDER BY RAND()");
    if ($query->num_rows == 0) {
        return 0;
    }
    //Meaning The ID Can Not Be Assigned
    else {
        //What if the ID could be assigned  ?
        $row = $query->fetch_assoc();
        $idFull = $row['full'];
        $idId = $row['id'];
        $idNo = $row['no'];
        if ($idFull != $idNo) {
            //We can assign this
            return $idId;
        } else {
            //This is filled up
            return 0;
        }
    }
}

function isThisIdInDatabase($id)
{
    $con = connect();
    $query = $con->prepare("SELECT id FROM `field_of_interests` WHERE id IN (SELECT field_id FROM cpu) AND id = ?");
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        return 0;
    }

    $result = $query->get_result();
    if ($result->num_rows > 0) {
        return 1;
    }

    return 0;
}

function getMatesById($id)
{
    $stud = getIdFromSession($_SESSION['id'], 'student');
    $con = connect();
    $query = $con->query("SELECT * FROM students WHERE cpu_id = '$id' AND id != '$stud' "); //to restrict displaying me  AND regno != '$user'
    if ($query->num_rows < 1) {
        $output = '<div class="alert alert-info"><i class="icon-info-sign"></i> You are yet to have  mates with same field as yours
    </div>';
    } else {
        $output = '';
        while ($row = $query->fetch_assoc()) {
            $output .= "<li>
  <a href='#'>
      <img style='min-height:124px;max-height:124px;min-width:140px;min-width:140px;' src ='uploads/" . (($row['location'] == null) ? 'no.jpg' : $row['location']) . "' width='124' height='140' class='img-polaroid img-circle '>
    <div>
    <span>
    <p class='subject'>" . $row['firstname'] . "  " . $row['lastname'] . "</p>

    </span>
    </div>
  </a>
</li>";
        }
    }
    return $output;
}

function recordFromChange($id)
{
    $con = connect();
    $checkSQL = $con->query("SELECT * FROM change_request WHERE student_id = '$id' AND response != 1");
    if ($checkSQL->num_rows < 1) {
        return "";
    }

    $result = $checkSQL->fetch_assoc();
    $reason = htmlspecialchars($result['reason']);
    $response = $result['admin'];
    $response2 = $result['response'];

    if ($response2 == 0) {
        $response = 'Not Answered Yet';
    } elseif ($response2 == -1) {
        $response = 'Rejected.';
    } else {
        $response = 'Approved';
    }

    $output = "<table class='table table-hover'>
                <tr><th>Your Reason</th><th>Response</th></tr>
                <tr><td>$reason</td><td>$response</td></tr>
            </table>";
    return $output;
}

function updateMax($id)
{
    //Cases where supervisor approves request from admin and the max has been breached, this function updates the maximum value of the supervisor to the count of all number in the cpu (no)
    $con = connect();
    $query = $con->query("SELECT SUM(no) AS no FROM cpu WHERE supervisor_id = '$id'");
    $row = $query->fetch_assoc();
    $sum = $row['no'];
    $myMax = $con->query("SELECT max FROM supervisors WHERE id = '$id'");
    $row2 = $myMax->fetch_assoc();
    $max = $row2['max'];
    if ($sum > $max) {
        $con->query("UPDATE supervisors SET max = '$sum' WHERE id = '$id' LIMIT 1");
    }
}

function countChangeFieldRequest($id = 0)
{
    $con = connect();
    if ($id == 0) {
        $query = $con->query("SELECT * FROM change_request WHERE admin = 0 OR (admin != 0 and response = 0)");
        if ($query->num_rows > 0) {
            return '<span class="badge badge-important">' . $query->num_rows . "</span>";
        }
    } else {
        $query = $con->query("SELECT change_request.reason as reason, change_request.id as id, students.regno as regno, students.lastname as ln, students.firstname as fn FROM `change_request` INNER JOIN students ON change_request.student_id = students.id WHERE change_request.supervisor_id = '$id' AND response = 0 AND change_request.admin = 1");
        if ($query->num_rows > 0) {
            return '<span class="badge badge-important">' . $query->num_rows . "</span>";
        }
    }
}

function getRequestForChangeOfFieldById($id)
{
    $con = connect();
    $query = $con->query("SELECT change_request.reason as reason, change_request.id as id, students.regno as regno, students.lastname as ln, students.firstname as fn FROM `change_request` INNER JOIN students ON change_request.student_id = students.id WHERE change_request.supervisor_id = '$id' AND response = 0 AND change_request.admin = 1");
    if (!$query->num_rows) {
        return "You do not have any pending request";
    }
    $sn = 0;
    $output = "<h4 class='alert alert-info'>The listed students requested for change of interest. The admin approved this request. However, we'd like to have your approval.</h4><table class='table table-hover'>
  <tr><th>SN</th><th>Student Detail</th><th>Reason</th><th>Action</th></tr>";
    while ($row = $query->fetch_assoc()) {
        ++$sn;
        $id = $row['id'];
        $fullname = substr(($row['ln'] . " " . $row['fn']), 0, 20);
        $fullname = $row['regno'] . " - " . $fullname;
        $reason = $row['reason'];
        $action = "<a href='" . $_SERVER['PHP_SELF'] . "?id=$id&status=-2'><button onClick='return confirm(\"Are you sure you wish to DECLINE the request  ?\")' class='btn btn-danger'> Decline</button></a> || <a href='" . $_SERVER['PHP_SELF'] . "?id=$id&status=2'><button onClick='return confirm(\"Are you sure you wish to ACCEPT the request ?\")' class='btn btn-success'> Accept</button></a>";
        $output .= "
  <tr><td>$sn</td><td>$fullname</td><td>$reason</td><td>$action</td></tr>
";
    }
    return $output . "</table>";
}

function updateChangeOfField($supervisor_id, $status, $change_id)
{
    $con = connect();
    $checkSQL = $con->query("SELECT change_request.student_id as studId, cpu.id as cpuId  FROM change_request JOIN students ON change_request.student_id = students.id JOIN cpu ON cpu.id = students.cpu_id  WHERE change_request.supervisor_id = '$supervisor_id' AND change_request.response = 0  AND change_request.id = '$change_id'");
    if ($checkSQL->num_rows != 1 || ($status != 1 && $status != -1)) {
        logDown($supervisor_id, "Tried to update change of field request not sent to the logged in supervisor", 3, 2);
        return '<script>alert("Access Denied");</script>';
    } else {
        if ($status == -1) {
            //Rejected

            $updateSQL = $con->query("UPDATE change_request SET response = '-1' WHERE id = '$change_id'");
            if ($updateSQL) {
                logDown($supervisor_id, "Change of field request was rejected for student", 1, 2);
                return '<script>alert("Record Updated");</script>';
            } else {
                return '<script>alert("Record Not Updated");</script>';
            }
            //Quickly  Reject This And Return
            exit; //Enforcing that the control does not go down, although this won't execute
        }
        $row = $checkSQL->fetch_assoc();
        $cpuId = $row['cpuId'];
        $studId = $row['studId'];

        /*
        We need to do the following
        1. Decrease the number of allocation in the particular staff
        2. We need to reset cpu_id of the student to 0
        3. We need to update change_request to be -1 or 1

         */
        $sql = "
UPDATE `students` SET cpu_id = 0 WHERE id = '$studId';
UPDATE cpu SET full = full-1 WHERE id = '$cpuId' AND full != 0;
UPDATE change_request SET response = '$status' WHERE id = '$change_id';
DELETE FROM `progress` WHERE `student_id` = '$studId';
";

        $fullQuery = $con->multi_query($sql);
        if ($fullQuery) {
            $studPhone = getStudentPhoneById($studId);
            $studEmail = getStudentEmailById($studId);
            $msg = "Dear student,
            Your request to change field of interest has now been approved.
            You can now choose a new field of interest.";
            sendMail($studEmail, "Request Approved TO Change Field For Project", $msg);
            sms($msg, $studPhone);
            logDown($supervisor_id, "Change of field request was approved for student", 1, 2);
            return '<script>alert("Record Updated");</script>';
        } else {
            return '<script>alert("Record Not Updated");</script>';
        }
    }
}

function getCpuId($id)
{
    $con = connect();
    $query = $con->query("SELECT cpu_id FROM students WHERE id = '$id'");
    if ($query->num_rows < 1) {
        return -1;
    }

    $result = $query->fetch_assoc();
    return $result['cpu_id'];
}

function getInboxMessages($id, $group, $type)
{
    if ($type == 'students') {
        if ($group == 'yes') {
            $table = 'stud_to_group';
            $group = 'Group';
            $where = "cpu_id";
            $id = getCpuId($id);
        } else {
            $table = 'stud_to_sup';
            $group = 'Student';
            $where = 'student_id';
        }
    } else {
        if ($group == 'yes') {
            $table = 'sup_to_group';
            $group = 'Group';
            $where = "cpu_id";
            $id = getCpuId($id);
        } else {
            $table = 'sup_to_stud';
            $group = 'Supervisor';
            $where = 'student_id';
        }
    }
    $con = connect();
    $query = $con->query("SELECT * FROM $table  WHERE $where = '$id'");
    return array($query, $group);
}

function sendMessageFromStudent($id, $to, $msg, $file)
{
    $arrayTo = array(0, 1, 2);
    if (strlen($msg) < 3 || !in_array($to, $arrayTo)) {
        logDown($id, "Tried to send message but message was not approved (Too Scanty or Filled the form in an unacceptable way", 2, 1);
        return '<script>alert("Fill Form Properly");</script>';
    }
    $valid_extension = array("csv", "xls", "xlsx", "doc", "docx", "ppt", "pptx", "pdf", "jpg", "png", "gif", "jpeg");
    //Check for valid file size
    if (($_FILES[$file]['size'] && !in_array(strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION)), $valid_extension)) || ($_FILES[$file]['size'] && $_FILES[$file]['error']) > 0) {
        logDown($id, "Added an invalid extension of " . (strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION))) . " and was denied", 3, 1);
        return '<script>alert("Invalid File Extension");</script>';
    }

    $cpu_id = getCpuId($id);
    if ($cpu_id < 1) {
        logDown($id, "No field of interest and tried to send message", 2, 1);
        return '<script>alert("You do not have a field yet. \n You are not allowed to do this.");</script>';
        exit;
    } else {
        $supervisor_id = getSupervisorByCpuId($cpu_id);
        if ($supervisor_id < 1) {
            return '<script>alert("You do not have a field yet. \n You are not allowed to do this.");</script>';
        }
    }
    $loc = 0; //Just in case the message do not have an attachment
    if ($_FILES[$file]['size']) {
        //The message has an attachment
        $loc = genRand() . "." . strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION));
        $upload = move_uploaded_file(@$_FILES[$file]['tmp_name'], "uploads/" . $loc);
        if (!$upload) {
            logDown($id, "File upload error, possibly: Read/Write Permission", 3, 1);
            return "<script>alert('Attachment not uploaded\nConsider allowing read/write permissions');</script>";
        }
        chmod("uploads/" . $loc, 0777);
    }
    /*
    To Detail
    0 -> Supervisor
    1 -> Group
    2 -> Both
     */
    $date = getStamp();
    $con = connect();
    if ($to == 0) {
        $sql = "INSERT INTO stud_to_sup (student_id,supervisor_id,attachment,msg,entry_date) VALUES (?,?,?,?,?)";
        $query = $con->prepare($sql);
        $query->bind_param("iisss", $id, $supervisor_id, $loc, $msg, $date);
        if (!$query->execute()) {
            logDown($id, "Message to supervisor failed", 3, 1);
            return "<script>alert('Message not sent to supervisor. Fill form properly');</script>";
        } else {
            logDown($id, "Message sent to supervisor: " . (substr($msg, 0, 60)) . " ..", 3, 1);
            return "<script>alert('Message Sent To Your Supervisor.');</script>";
        }
    } elseif ($to == 1) {
        $sql = "INSERT INTO stud_to_group (student_id,cpu_id,attachment,msg,entry_date) VALUES (?,?,?,?,?)";
        logDown($id, "Messaged the group", 3, 1);
        $query = $con->prepare($sql);
        $query->bind_param("iisss", $id, $cpu_id, $loc, $msg, $date);
        if (!$query->execute()) {
            return "<script>alert('Message not sent. Fill form properly');</script>";
        } else {
            return "<script>alert('Message Sent To Group.');</script>";
        }
    } else {
        $sql = "INSERT INTO stud_to_sup (student_id,supervisor_id,attachment,msg,entry_date) VALUES (?,?,?,?,?)";
        $query = $con->prepare($sql);
        $query->bind_param("iisss", $id, $supervisor_id, $loc, $msg, $date);
        //Insert to group
        $sql2 = "INSERT INTO stud_to_group (student_id,cpu_id,attachment,msg,entry_date) VALUES (?,?,?,?,?)";
        $query2 = $con->prepare($sql2);
        $query2->bind_param("iisss", $id, $cpu_id, $loc, $msg, $date);
        if (!$query->execute()) {
            return "<script>alert('Message not sent to supervisor. Fill form properly');</script>";
        }

        if (!$query2->execute()) {
            return "<script>alert('Message not sent to group. Fill form properly');</script>";
        }

        return "<script>alert('Message Sent To Both Supervisor And Group.');</script>";
    }
    return "<script>alert('Something about you is not right.');</script>";
}

function genRand()
{
    return md5(mt_rand(1, 3456789) . date('dmyhmis'));
}

function getSupervisorByCpuId($id)
{
    $con = connect();
    $query = $con->query("SELECT supervisor_id as id FROM cpu WHERE  id = '$id'");
    if ($query->num_rows != 1) {
        return -1;
    }

    $result = $query->fetch_assoc();
    return $result['id'];
}

function getMySentMessages($id)
{
    $con = connect();
    $output = "";
    $queryToGetGroupMessage = $con->query("SELECT * FROM stud_to_group WHERE student_id = '$id' ORDER BY id DESC;");
    $queryToGetSupMessage = $con->query("SELECT * FROM stud_to_sup WHERE student_id = '$id' ORDER BY id DESC;");
    if ($queryToGetGroupMessage->num_rows > 0) {

        while ($row1 = $queryToGetGroupMessage->fetch_assoc()) {
            $output .= '<div class="post">
      ' . htmlspecialchars($row1['msg']) . '
          <hr>
      Sent to: <strong>Group</strong>
      <i class="icon-calendar"></i> ' . $row1['entry_date'] . '
          <div class="pull-right">' . ((strlen($row1['attachment']) < 10) ? "" : '<a class="btn btn-link"  href="uploads/' . $row1['attachment'] . '"  ><i class="icon-download"></i> Download Attachment </a>') . '

          </div>
      </div>';
        }
    }

    if ($queryToGetSupMessage->num_rows > 0) {
        $output .= '<hr/><strong>Start of Private Messages Directly Sent To Your Supervisor</strong>';

        while ($row2 = $queryToGetSupMessage->fetch_assoc()) {
            $output .= '<div class="post">
      ' . htmlspecialchars($row2['msg']) . '
          <hr>
      Sent to: <strong>Supervisor</strong>
      <i class="icon-calendar"></i> ' . $row2['entry_date'] . '
          <div class="pull-right">' . ((strlen($row2['attachment']) < 10) ? "" : '<a class="btn btn-link"  href="uploads/' . $row2['attachment'] . '"  ><i class="icon-download"></i> Download Attachment </a>') . '

          </div>
      </div>';
        }
    }
    if ($queryToGetGroupMessage->num_rows < 1) {
        $output .= '<div class="alert alert-info"><i class="icon-info-sign"></i> You are yet to send any message to the group! </div>';
    }
    if ($queryToGetSupMessage->num_rows < 1) {
        $output .= '<div class="alert alert-info"><i class="icon-info-sign"></i> You are yet to send any message to your supervisor! </div>';
    }

    if ($queryToGetGroupMessage->num_rows < 1 && $queryToGetSupMessage->num_rows < 1) {
        $output = '<div class="alert alert-danger"><i class="icon-info-sign"></i> You are yet to send any message from your portal! </div>';
    }

    return $output;
}

function getSupervisorInboxMessage($id)
{
    $con = connect()->query("SELECT stud_to_sup.attachment as attachment, stud_to_sup.msg as msg, stud_to_sup.entry_date as date, students.lastname as ln, students.firstname as fn, students.regno as regno FROM stud_to_sup INNER JOIN students ON stud_to_sup.student_id = students.id WHERE supervisor_id = '$id'");
    return $con;
}

function sendMessageFromSupervisor($id, $to, $msg, $file, $type = 'group')
{
    if (strlen($msg) < 3) {
        logDown($id, "Message sending failed because message was scanty", 2, 2);
        return '<script>alert("Fill Form Properly");</script>';
    }
    $valid_extension = array("csv", "xls", "xlsx", "doc", "docx", "ppt", "pptx", "pdf", "jpg", "png", "gif", "jpeg");
    //Check for valid file size
    if (($_FILES[$file]['size'] && !in_array(strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION)), $valid_extension)) || ($_FILES[$file]['size'] && $_FILES[$file]['error']) > 0) {
        logDown($id, "Message sending failed because of invalid extension" . (strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION))) . " uploaded", 2, 2);
        return '<script>alert("Invalid File Extension");</script>';
    }
    if ($to != 0 && (canThisIdAccess($to) < 1) && $type == 'group') {
        logDown($id, "Message sending failed because supervisor tried to send to group not defined for the supervisor", 3, 2);
        return '<script>alert("You are not authorized to send this message");</script>';
    }
    if ($to != 0 && (canThisStudentIdBeAccess($to) < 1) && $type != 'group') {
        logDown($id, "Message sending failed because student not in the supervisor's care", 3, 2);
        return '<script>alert("You are not authorized to send this message");</script>';
    }
    $loc = 0; //Just in case the message do not have an attachment
    if ($_FILES[$file]['size']) {
        //The message has an attachment
        $loc = genRand() . "." . strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION));
        $upload = move_uploaded_file(@$_FILES[$file]['tmp_name'], "uploads/" . $loc);
        if (!$upload) {
            logDown($id, "Message sending failed because of read/write permission for uploads", 3, 2);
            return "<script>alert('Attachment not uploaded. Consider allowing read/write permissions');</script>";
        }
        chmod("uploads/" . $loc, 0777);
    }
        /*
    To Detail
    0 -> All Groups
    >1 -> Group
     */;
    $date = getStamp();
    $con = connect();
    if ($type == 'group') {
        if ($to == 0) {
            $getAllFields = $con->query("SELECT id FROM cpu WHERE supervisor_id = '$id'");
            while ($row = $getAllFields->fetch_assoc()) {
                $cpu_id = $row['id'];
                $sql = "INSERT INTO sup_to_group (cpu_id,supervisor_id,attachment,msg,entry_date) VALUES (?,?,?,?,?)";

                $query = $con->prepare($sql);
                $query->bind_param("iisss", $cpu_id, $id, $loc, $msg, $date);
                if (!$query->execute()) {
                    logDown($id, "Message Sending Failed", 3, 2);
                    return "<script>alert('Message not sent to groups. Fill form properly');</script>";
                }
            }
            logDown($id, "Message sent to all groups", 1, 2);
            return "<script>alert('Message Sent To All Your Group(s).');</script>";
        } else {
            if (canThisIdAccess($to) > 0) {
                $sql = "INSERT INTO sup_to_group (supervisor_id,cpu_id,attachment,msg,entry_date) VALUES (?,?,?,?,?)";
                $query = $con->prepare($sql);
                $query->bind_param("iisss", $id, $to, $loc, $msg, $date);
                if (!$query->execute()) {
                    logDown($id, "Message sending failed because of a threat", 3, 2);
                    return "<script>alert('Message not sent. Fill form properly');</script>";
                } else {
                    logDown($id, "Message to sent to only one group", 1, 2);
                    return "<script>alert('Message Sent To Group.');</script>";
                }
            }
        }
        logDown($id, "Something about sending message from supervisor is not right", 3, 2);
        return "<script>alert('Something about you is not right.');</script>";
    } else {
        //Sent to students start
        if ($to == 0) {

            $getAllStudents = listAllStudents();
            while ($row = $getAllStudents->fetch_assoc()) {
                $student_id = $row['id'];
                $sql = "INSERT INTO sup_to_stud (student_id,supervisor_id,attachment,msg,entry_date) VALUES (?,?,?,?,?)";
                $query = $con->prepare($sql);
                $query->bind_param("iisss", $student_id, $id, $loc, $msg, $date);
                if (!$query->execute()) {
                    logDown($id, "Message sending to students failed because message was invalid", 3, 2);
                    return "<script>alert('Message not sent to students. Fill form properly');</script>";
                }
            }
            logDown($id, "Message sent to all students", 1, 2);
            return "<script>alert('Message Sent To All Your Students.');</script>";
        } else {
            if (canThisStudentIdBeAccess($to) > 0) {
                $sql = "INSERT INTO sup_to_stud (student_id,supervisor_id,attachment,msg,entry_date) VALUES (?,?,?,?,?)";
                $query = $con->prepare($sql);
                $query->bind_param("iisss", $to, $id, $loc, $msg, $date);
                if (!$query->execute()) {
                    logDown($id, "Message sending failed because message was invalid", 3, 2);
                    return "<script>alert('Message not sent. Fill form properly');</script>";
                } else {
                    logDown($id, "Message Sent to Student " . (getStudentDetailsById($to)), 1, 2);
                }

                return "<script>alert('Message Sent To Student.');</script>";
            }
        }
        logDown($id, "Message sending failed because illegal usage of this function was foreseen", 3, 2);
        return "<script>alert('Something about you is not right.');</script>";
        //end of send to student
    }
}

function actionOnProgress($id, $chapter, $action)
{
    $con = connect();
    $query = $con->prepare("SELECT * FROM progress WHERE student_id = ? AND chapter = ?");
    $query->bind_param("ii", $id, $chapter);
    if (!$query->execute()) {
        logDown($id, "Student could not view progress possibly : Invalid Chapter", 3, 1);
        return script("You are not authorized", 1);
    }
    $result = $query->get_result();
    if ($result->num_rows != 1) {
        logDown($id, "Student could not view progress because student is yet to upload", 2, 1);
        return script("You are yet to upload, you can not $action yet!", 1);
    }
    $row = $result->fetch_assoc();
    $file = $row['link'];
    $chap_title = (($chapter == 0) ? 'Proposal' : (($chapter == 6) ? "Full Project" : "Chapter $chapter"));
    if ($action == 'download') {
        logDown($id, "Student Downloaded $chap_title", 1, 1);
        return script("Your file will be downloaded now", "uploads/$file");
    } else {
        if ($row['status'] == 1) {
            logDown($id, "Student tried to delete $chap_title but disallowed. Why? Approved Already", 3, 1);
            return script("File already approved, you can not delete", 1);
        }
        $delSQL = $con->prepare("DELETE FROM progress WHERE chapter = ? AND student_id = ?");
        $delSQL->bind_param("ii", $chapter, $id);
        if (!$delSQL->execute()) {
            logDown($id, "Student tried to delete $chap_title but was not allowed", 3, 1);
            return script("Could not delete", 1);
        }
        //Try to delete previous file
        @unlink("uploads/$file");
        logDown($id, "Student Deleted $chap_title", 1, 1);
        return script("Upload deleted", 1);
    }
    return script("You should not be seeing this", 1);
}

function checkProgressByStudentId($id, $chapter)
{
    $query = connect()->query("SELECT * FROM progress WHERE student_id = '$id' AND chapter = '$chapter' ");
    if ($query->num_rows != 1) {
        return array(0, "No upload", 0);
    }

    $row = $query->fetch_assoc();
    if ($row['status'] == 1) {
        return array(1, $row['date_accepted'], $row['link']);
    } elseif ($row['status'] == 0) {
        return array(-1, "No response from your supervisor yet", $row['link']);
    } elseif ($row['status'] == -1) {
        return array(-1, "Not approved: Message From Your Supervisor: <br/><i>\"" . $row['response'] . "\"</i>", $row['link']);
    }
}

function uploadProgress($chapter, $file)
{
    $id = getIdFromSession($_SESSION['id'], 'student');
    $chap_title = (($chapter == 0) ? 'Proposal' : (($chapter == 6) ? "Full Project" : "Chapter $chapter"));
    if ($chapter < 0 || $chapter > 6 || strlen($chapter) > 1 || !($_FILES[$file]['size'])) {
        logDown($id, "Student Used Progress Upload In Way That Is Considered Bad For $chap_title", 3, 1);
        return script("Kindly Re-upload", 1);
        exit; //An insult to return :-D
    }
    $valid_extension = array("jpg", "png", "gif", "jpeg", "pdf", "xls", "xlsx", "txt", "csv", "doc", "docx", "ppt", "pptx");
    if (!in_array(strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION)), $valid_extension)) {
        logDown($id, "Student Upload Project Progress For $chap_title But Was Disallowed Because Of Invalid File Upload With Format " . (strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION))), 3, 1);
        return script("Invalid File Uploaded", 1);
    }
    $con = connect();
    $id = getIdFromSession($_SESSION['id'], 'student');
    $isAssign = isStudentAssigned($_SESSION['id']); //Check if student is assigned or not
    if ($isAssign[0] == 0) {
        logDown($id, "Tried to upload project progress but yet to have a supervisor", 2, 1);
        return script("You are yet to have a supervisor", 1);
    }

    //Let us check if this chapter has been approved
    $checkSQL = $con->query("SELECT link, status, chapter as chap FROM progress WHERE student_id = '$id' AND chapter = (SELECT max(chapter) FROM progress WHERE student_id = '$id') ");
    $fetch = $checkSQL->fetch_assoc();
    $currentChapter = $fetch['chap'];
    if (($currentChapter == null || $currentChapter == "" || $currentChapter < 0 || $checkSQL->num_rows < 1) && $chapter != 0) {
        logDown($id, "Yet to upload project proposal but tried to upload for $chap_title", 2, 1);
        return script("You are yet to upload your project proposal", 1);
    }
    if ($checkSQL->num_rows/* The chapter is already in the database */ && $currentChapter == $chapter) {
        $maxChapter = $fetch['chap'];
        $status = $fetch['status'];

        if (($maxChapter == $chapter) && $status == 1) {
            logDown($id, "Tried to upload project progress for $chap_title but status : approved already", 1, 1);
            return script("Already Approved", 1);
        }
        if ($chapter > ($maxChapter + 1) || ($status != 1 && $chapter != $maxChapter)) {
            logDown($id, "Tried to upload project progress but skipped to a phase not pending approval from previous progress ($chap_title)", 2, 1);
            return script("Take one step at a time. You are not allowed to skip a chapter", 1);
        }
        $link = "uploads/" . $fetch['link'];
        //We move the incoming file
        $loc = genRand() . "." . strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION));
        $upload = @move_uploaded_file(@$_FILES[$file]['tmp_name'], "uploads/" . $loc);
        if (!$upload) {
            logDown($id, "Project Progress Upload For Student Disabled (Read/Write Permission)", 3, 1);
            return script('Attachment not uploaded. Permission denied.');
        }
        chmod("uploads/" . $loc, 0777); //Own this file
        //Now, check if this chapter exists, if it does, delete this file, update else, insert fresh
        //We'd try to delete the previous file because it exists
        @unlink($link);
        $actionSQL = "UPDATE progress SET link = '$loc', status = 0, response = 0 WHERE student_id = '$id' AND chapter = '$chapter' LIMIT 1 ";
    } elseif ($checkSQL->num_rows && $chapter == ($currentChapter + 1)) {
        //Next chapter
        //We move the incoming file
        $loc = genRand() . "." . strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION));
        $upload = @move_uploaded_file(@$_FILES[$file]['tmp_name'], "uploads/" . $loc);
        if (!$upload) {
            logDown($id, "Project Progress Upload For Student Disabled (Read/Write Permission)", 3, 1);
            return script('Attachment not uploaded. Permission denied.');
        }
        chmod("uploads/" . $loc, 0777); //Own this file
        $actionSQL = "INSERT INTO progress (student_id, chapter, link, response, date_accepted, status) VALUES ('$id','$chapter','$loc','0','00-00-0000','0')";
    } elseif ($checkSQL->num_rows == 0) {
        //This must be the project proposal
        if ($chapter != 0) {
            return script("Kindly upload your project proposal", 1);
        }
        //We move the incoming file
        $loc = genRand() . "." . strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION));
        $upload = @move_uploaded_file(@$_FILES[$file]['tmp_name'], "uploads/" . $loc);
        if (!$upload) {
            logDown($id, "Project Proposal Progress Upload For Student Disabled (Read/Write Permission)", 3, 1);
            return script('Attachment not uploaded. Permission denied.');
        }
        chmod("uploads/" . $loc, 0777); //Own this file
        $actionSQL = "INSERT INTO progress (student_id, chapter, link, response, date_accepted, status) VALUES ('$id','0','$loc','0','00-00-0000','0')";
    } else {
        logDown($id, "Student used project upload in a bad way", 3, 1);
        return script("You are not authorized", 1);
    }
    //Query the SQL
    if ($con->query($actionSQL)) {
        $cpu = getCpuId($id);
        $sup_id = getSupervisorByCpuId($cpu);
        $phone = getSupervisorPhoneById($sup_id);
        $email = getSupervisorEmailById($sup_id);
        $std_name = getStudentDetailsById($id);
        $stamp = getStamp();
        $std_name = ucwords(strtolower(substr($std_name, 0, 30)));

        $msg =
            "Dear Supervisor,
  One of your supervisees ($std_name) just uploaded progress for $chap_title on $stamp.
  Kindly login your portal to give this supervisee a feedback.
  Cheers. :-)";
        sms($msg, $phone);
        sendMail($email, "Student Uploaded $chap_title", $msg);
        logDown($id, "Student project upload success $chap_title", 1, 1);
        return script('Your upload was successful.\nCheck back later to see the status of your project\nThank you', 1);
    } else {
        logDown($id, "Student used project upload in a bad way", 3, 1);
    }

    return script('Server was unable to process your request\nTry again', 1);
}

function canThisProgressIdBeAccessed($progressId)
{
    $id = getIdFromSession($_SESSION['id'], 'supervisor');
    $con = connect();
    $query = $con->prepare("SELECT supervisors.id FROM supervisors INNER JOIN cpu ON cpu.supervisor_id = supervisors.id INNER JOIN students ON students.cpu_id = cpu.id INNER JOIN progress ON progress.student_id = students.id WHERE progress.id = ? LIMIT 1");
    $query->bind_param("i", $progressId);
    if (!$query->execute()) {
        return 0;
    }

    $result = $query->get_result();
    $row = $result->num_rows;
    return $row;
}

function getStudentPhoneById($id)
{
    $con = connect();
    $query = $con->query("SELECT phone FROM students WHERE id = '$id'");
    $row = $query->fetch_assoc();
    return $row['phone'];
}

function getSupervisorPhoneById($id)
{
    $con = connect();
    $query = $con->query("SELECT phone FROM supervisors WHERE id = '$id'");
    $row = $query->fetch_assoc();
    return $row['phone'];
}

function getSupervisorEmailById($id)
{
    $con = connect();
    $query = $con->query("SELECT email FROM supervisors WHERE id = '$id'");
    $row = $query->fetch_assoc();
    return $row['email'];
}

function getStudentEmailById($id)
{
    $con = connect();
    $query = $con->query("SELECT email FROM students WHERE id = '$id'");
    $row = $query->fetch_assoc();
    return $row['email'];
}

function updateStudentProgress($id, $action, $msg = "")
{
    if (!canThisProgressIdBeAccessed($id)) {
        return script("You do not have access to do this.", 1);
    }

    if ($msg != "" && strlen($msg) < 3) {
        return script("Fill Response Field....", 1);
    }

    $con = connect();
    $sup_id = getIdFromSession($_SESSION['id'], 'supervisor');
    $checkSQL = $con->query("SELECT chapter FROM progress WHERE id = '$id'");
    $row = $checkSQL->fetch_assoc();
    $chap = $row['chapter'];
    $chap_title = (($chap == 0) ? 'Proposal' : (($chap == 6) ? "Full Project" : "Chapter $chap"));
    if ($action == 'yes') {
        //Accept
        $stamp = getStamp();
        $sms =
            "Hello dear student,
Your last upload has been reviewed and approved by your supervisor.
You should login your portal, upload the next chapter (if any).";
        logDown($sup_id, "Approved student project progress for  $chap_title", 1, 2);
        $query = $con->query("UPDATE progress SET status = 1, date_accepted = '$stamp'  WHERE id = '$id'");
    } else {
        //Reject
        $sms =
            "Hello,
Your last upload was rejected because your supervisor made some remarks.
Login your portal to view the remarks made and make amendments.
Thank you.";
        logDown($sup_id, "Rejected student project progress for  $chap_title", 1, 2);
        $prep = $con->prepare("UPDATE progress SET status = -1, response = ? WHERE id = ?");
        $prep->bind_param("si", $msg, $id);
        $query = $prep->execute();
    }
    if (!$query) {
        return script("Please try again, we could not save your reply", 1);
    }

    $get = $con->query("SELECT student_id FROM progress WHERE id = '$id'");
    $row = $get->fetch_assoc();
    $student_id = $row['student_id'];
    $phone = getStudentPhoneById($student_id);
    $email = getStudentEmailById($student_id);
    @sms($sms, $phone);
    if ($action != 'yes') {
        $sms =
            "Hello,
Your last upload was rejected because your supervisor made some remarks:
<br/><strong>$msg</strong><br/>
Login your portal to view the remarks made and make amendments.
Thank you.";
    }

    @sendMail($email, "Update on your project $chap_title", $sms);
    return script("Thank You. Feedback sent");
}

function getProgress($onlyApproved = 0)
{
    $id = getIdFromSession($_SESSION['id'], 'supervisor');
    $con = connect();
    if ($onlyApproved == 0) {
        $sql = "SELECT progress.status, progress.link, progress.chapter FROM `students`  INNER JOIN progress ON progress.student_id = students.id INNER JOIN cpu ON cpu.id = students.cpu_id INNER JOIN  supervisors ON supervisors.id = cpu.supervisor_id WHERE supervisors.id = '$id'";
    } else {
        $sql = "SELECT progress.student_id as std_id, progress.id as id, progress.status, progress.link, progress.chapter FROM `students`  INNER JOIN progress ON progress.student_id = students.id INNER JOIN cpu ON cpu.id = students.cpu_id INNER JOIN  supervisors ON supervisors.id = cpu.supervisor_id WHERE supervisors.id = '$id' AND progress.status = 0";
    }

    $query = $con->query($sql);
    return $query;
}

function getFailed()
{

    $con = connect();
    $sql = "SELECT failed_allocate.student_id as id, CONCAT(students.lastname, ', ', students.firstname, ' (',students.regno,')') AS std , failed_allocate.entry_date as date, failed_allocate.fields as fields FROM failed_allocate INNER JOIN students ON failed_allocate.student_id = students.id WHERE students.cpu_id < 1 ";
    $query = $con->query($sql);
    return $query;
}

function fetchStudentByMat($matric)
{
    $id = getIdFromSession($matric, 'student');
    if ($id < 1) {
        return 0;
    }
    $con = connect();
    $query = $con->query("SELECT students.lastname as ln, students.firstname as fn, students.cpu_id as cpu_id, students.id as id, students.regno as regno FROM students WHERE id='$id'");
    if ($query->num_rows != 1) {
        return 0;
    }

    return $query->fetch_assoc();
}

function resetStudentData($id)
{

    $con = connect();
    $checkSQL = $con->query("SELECT cpu_id FROM students WHERE id = '$id' AND cpu_id > 0");
    if ($checkSQL->num_rows != 1) {
        logDown(0, "Admin tried to reset student data for student who do not exist", 3, 3);
        return -1;
    }
    /* We need to do the following
    1: Decrement the counter for the supervisor in CPU table
    2: Update Students table, set cpu_id = 0 where the students id argument
    3: Delete all messages sent to/fro the student
     */
    logDown(0, "Admin reset student data for student " . (getStudentDetailsById($id)), 3, 3);
    $cpu_id = getCpuId($id);
    $sqlToDecrement = "UPDATE cpu SET full = full -1 WHERE id = '$cpu_id' LIMIT 1; ";
    $sqlToUpdateStudent = "UPDATE students SET cpu_id = 0 WHERE id = '$id' LIMIT 1; ";
    $sqlToDeleteMessages = "DELETE FROM stud_to_group WHERE student_id = '$id';
                          DELETE FROM stud_to_sup WHERE student_id = '$id';
                          DELETE FROM sup_to_stud WHERE student_id = '$id';
                          DELETE FROM progress WHERE student_id = '$id';
                          DELETE FROM  special_request WHERE student_id = '$id';
                          DELETE FROM assign_request WHERE student_id = '$id';";
    $fullQuery = $sqlToDeleteMessages . $sqlToDecrement . $sqlToUpdateStudent;
    if (!$con->multi_query($fullQuery)) {
        return 0;
    } else {
        $reg = getStudentDetailsById($id);
    }
    $email = getStudentEmailById($id);
    $phone = getStudentPhoneById($id);
    $msg = "Dear $reg,
    Your data on the portal has been refreshed.
    Kindly visit the portal to be allocated to a supervisor.";
    sendMail($email, "Data Reset", $msg);
    sms($msg, $phone);
    logDown(0, "Student data was reset: $reg ", 3, 3);
    return 1;
}

function getFieldsToAdmin()
{
    $con = connect();
    $query = $con->query("SELECT supervisors.lastname as ln, supervisors.firstname as fn, titles.name as title_name, field_of_interests.name as  name, cpu.id as cpu_id, supervisors.fileno as fileno FROM cpu INNER JOIN supervisors ON supervisors.id = cpu.supervisor_id INNER JOIN field_of_interests ON field_of_interests.id = cpu.field_id INNER JOIN titles ON titles.id = supervisors.title_id");
    if ($query->num_rows < 1) {
        return 0;
    }

    return $query;
}

function isSupervisorLazy($id)
{
    $con = connect();
    $query = $con->prepare("SELECT id FROM  supervisors WHERE id NOT IN (SELECT cpu.supervisor_id FROM cpu) AND  id = ?");
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        return 1;
    }

    $result = $query->get_result();
    if ($result->num_rows == 0) {
        return 0;
    }

    return 1;
}

function assigner($matric, $field, $supervisor = 'direct')
{
    $con = connect();
    $sql = ($supervisor == 'direct') ? "SELECT id FROM cpu WHERE id = ?" : "SELECT id FROM field_of_interests WHERE id = ?";
    $query = $con->prepare($sql);
    $query->bind_param("i", $field);
    if (!$query->execute()) {
        return -1;
    }

    $result = $query->get_result();
    if ($result->num_rows != 1) {
        return -1;
    }

    $id = getIdFromSession($matric);
    if (!$id) {
        return -1;
    }

    if ($supervisor == 'direct') {
        $sql = "INSERT IGNORE INTO assign_request (student_id, cpu_id,date_entry) VALUES (?,?,?)";
        $insertSQL = $con->prepare($sql);
        $date_entry = getStamp();
        $insertSQL->bind_param("iis", $id, $field, $date_entry);
    } else {
        if (!isThisSupervisorFree($supervisor)) {
            return script('Sorry, this supervisor is not free\nMaximum Number Reached!!!');
        }

        $sql = "INSERT IGNORE INTO special_request (student_id, supervisor_id, field_id, date_entry) VALUES (?,?,?,?)";
        $insertSQL = $con->prepare($sql);
        $date_entry = getStamp();
        $insertSQL->bind_param("iiis", $id, $supervisor, $field, $date_entry);
    }
    if (!$insertSQL->execute()) {
        logDown(0, "Admin tried to assign student to supervisor but system denied it. Why? Form invalid", 3, 3);
        return script("Request Not Sent.", 1);
    }
    logDown(0, "Admin sent request for assign on " . ($std_name = getStudentDetailsById($id)) . " to supervisor " . ($sup_name = getSupervisorNameById($supervisor)), 1, 1);
    $phone = getSupervisorPhoneById($supervisor);
    $email = getSupervisorEmailById($supervisor);
    $sup_name = ucwords(strtolower($sup_name));
    $std_name = ucwords(strtolower($std_name));
    $field_name = ucwords(strtolower(getFieldNameFromCpuID($field)));
    $msg = "
  Hello Dear $sup_name,
  System Administrator sent a request for $std_name for field of interest with the name '$field_name'.
  Kindly login your portal to reply this request.
  Thank You!
  ";
    sms($msg, $phone);
    sendMail($email, "Request to supervise a student", $msg);
    return script('Request Sent. \nBe informed that if this student request has already being sent to the selected supervisor, the record will not be duplicated.\nWhich means you\'d have to wait for a response from the supervisor before sending another request', 1);
}

function ChangeRequest($id, $response, $type = 'admin')
{
    $con = connect();
    //updateChangeOfField($supervisor_id, $status, $change_id);
    if ($type == 'admin') {
        $sql = "UPDATE change_request SET admin = '$response' WHERE id = '$id' AND response = -1 ";
        if ($response == 'yes') {
            $response = 1;
            $sql = "UPDATE change_request SET admin = '$response' WHERE id = '$id' ";
        } else {
            $response = -1;
        }

        $check = $con->prepare("SELECT * FROM change_request WHERE admin = 0 AND id = ?");
        $check->bind_param("i", intval($id));
        if (!$check->execute()) {
            return script("Form rejected", 1);
        }

        $result = $check->get_result();
        $row = $result->fetch_assoc();
        $sup_id = $row['supervisor_id'];
        $sup_phone = getSupervisorPhoneById($sup_id);
        $sup_email = getSupervisorEmailById($sup_id);
        $det = getSupervisorNameById($sup_id);
        if ($result->num_rows != 1) {
            return script("Please fill form properly", 1);
        }

        $con->query($sql);
        if ($response == 'yes') {
            $msg = "Dear $det, the admin just approved a student's request to change field of interest. The admin awaits your approval.";
            sendMail($sup_email, "Change of Request From Admin", $msg);
            sms($msg, $sup_phone);
        }
        return script("Request Response Saved");
    } elseif ($type == 'admin2') {
        $check = $con->prepare("SELECT * FROM change_request WHERE admin != 0 AND id = ?");
        $check->bind_param("i", intval($id));
        if (!$check->execute()) {
            return script("Form rejected", 1);
        }

        $result = $check->get_result();
        if ($result->num_rows != 1) {
            return script("Please fill form properly", 1);
        }
        $query = $con->query("SELECT supervisor_id as sup, student_id as stud FROM change_request WHERE id = '$id' ");
        $row = $query->fetch_assoc();
        $sup = $row['sup'];
        $stud = $row['stud'];
        if ($response == 'yes') {
            $response = 1;


            return updateChangeOfField($sup, 1, $id);
        } else {
            updateChangeOfField($sup, -1, $id);
            $con->query("UPDATE change_request SET response = -1 WHERE id = '$id'; ");
            return ("Student request to change field has now been rejected finally!");
        }
    } else {
        //Called by supervisor
        $check = $con->prepare("SELECT * FROM change_request WHERE admin = 1 AND id = ?");
        $check->bind_param("i", intval($id));
        if (!$check->execute()) {
            return script("Form rejected", 1);
        }

        $result = $check->get_result();
        if ($result->num_rows != 1) {
            return script($result->num_rows . "Please fill form properly", 1);
        }

        $con->query("UPDATE change_request SET admin = '$response' WHERE id = '$id' ");
        return script("Request Response Saved");
    }
}

function isThisSupervisorFree($id)
{
    $con = connect();
    $max = getMyMax($id);
    $getCount = countTotalBySupId($id);
    $full = $getCount['full'];
    if ($full < $max) {
        return 1;
    } else {
        return 0;
    }
}

function getCountById($id, $type = 'not all')
{
    $con = connect();
    if ($type == 'not all') {
        $sql = "SELECT assign_request.student_id as stdid, assign_request.id as id, assign_request.cpu_id as cpu_id  FROM `assign_request` INNER JOIN cpu ON cpu.id = assign_request.cpu_id WHERE cpu.supervisor_id = '$id' AND status = 0";
    } else {
        $sql = "SELECT assign_request.student_id as stdid, assign_request.id as id, assign_request.cpu_id as cpu_id  FROM `assign_request` INNER JOIN cpu ON cpu.id = assign_request.cpu_id WHERE cpu.supervisor_id = '$id'";
    }
    $query = $con->query($sql);
    return $query;
}

function getSpecialCountById($id, $type = 'not all')
{
    $con = connect();
    if ($type == 'not all') {
        $sql = "SELECT special_request.student_id as stdid, special_request.id as id, special_request.field_id as field_id  FROM `special_request` WHERE special_request.supervisor_id = '$id' AND status = 0";
    } else {
        $sql = "SELECT special_request.student_id as stdid, special_request.id as id, special_request.field_id as field_id  FROM `special_request` WHERE special_request.supervisor_id = '$id'";
    }
    $query = $con->query($sql);
    return $query;
}

function getAllStudentInAssign($type = 0)
{
    $con = connect();
    if ($type == 0) {
        return $con->query("SELECT * FROM assign_request");
    } else {
        return $con->query("SELECT * FROM special_request");
    }
}

function getSupervisorNameByCpuId($cpuid)
{
    $con = connect();
    $query = $con->prepare("SELECT CONCAT(titles.name, ' ', supervisors.lastname, ' ', supervisors.firstname, ' (', supervisors.fileno, ')' ) AS fullDetails FROM supervisors INNER JOIN titles ON titles.id = supervisors.title_id INNER JOIN cpu ON cpu.supervisor_id = supervisors.id  WHERE cpu.id = ?");
    $query->bind_param("i", $cpuid);
    if (!$query->execute()) {
        return "";
    }

    $result = $query->get_result();
    $row = $result->fetch_assoc();
    return $row['fullDetails'];
}

function getSupervisorNameById($id, $all = 0)
{
    $con = connect();
    if ($all == 0) {
        $query = $con->prepare("SELECT CONCAT(titles.name, ' ', supervisors.lastname, ' ', supervisors.firstname, ' (', supervisors.fileno, ')' ) AS fullDetails FROM supervisors INNER JOIN titles ON titles.id = supervisors.title_id INNER JOIN cpu ON cpu.supervisor_id = supervisors.id  WHERE supervisors.id = ?");
    } else {
        $query = $con->prepare("SELECT CONCAT(titles.name, ' ', supervisors.lastname, ' ', supervisors.firstname, ' (', supervisors.fileno, ')' ) AS fullDetails FROM supervisors INNER JOIN titles ON titles.id = supervisors.title_id  WHERE supervisors.id = ?");
    }
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        return "";
    }

    $result = $query->get_result();
    if ($result->num_rows < 1) {
        return "Undefined Name";
    }
    $row = $result->fetch_assoc();
    return @$row['fullDetails'];
}

function delAssign($id, $type = 0)
{
    $con = connect();
    if ($type == 0) {
        $query = $con->prepare("DELETE FROM assign_request WHERE student_id = ? AND status = 0 LIMIT 1");
    } else {
        $query = $con->prepare("DELETE FROM  special_request WHERE student_id = ? AND status = 0 LIMIT 1");
    }

    $query->bind_param("i", $id);
    $query->execute();
    logDown(0, "Admin deleted request for student " . getStudentDetailsById($id), 3, 3);
    return script('Deleted!', $_SERVER['PHP_SELF']);
}

function script($msg, $loc = 0)
{
    $location = '';
    if ($loc == 1) {
        $location = "window.location = '" . $_SERVER['PHP_SELF'] . "'";
    } elseif (strlen($loc) > 1) {
        $location = "window.location = '$loc'";
    } else {
        $location = '';
    }
    $script = '<script>alert("' . $msg . '");' . $location . ';</script>';
    return $script;
}

function countTotalBySupId($id)
{
    $con = connect();
    $query = $con->prepare("SELECT SUM(no) as no, SUM(full) as full FROM `cpu` WHERE supervisor_id = ?");
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        return -1;
    }

    $result = $query->get_result();
    return $result->fetch_assoc();
}

function assignReply($id, $status, $type)
{
    $con = connect();
    $myId = getIdFromSession($_SESSION['id'], 'supervisor');
    //Before anything, let us check if this student is not assigned
    if ($type == 'normal') {
        $check = $con->prepare("SELECT assign_request.id, assign_request.student_id as std_id, supervisors.max as max, supervisors.id as sup_id FROM assign_request  INNER JOIN cpu ON cpu.id = assign_request.cpu_id INNER JOIN supervisors on cpu.supervisor_id = supervisors.id WHERE supervisors.id = '$myId' AND assign_request.id = ? AND assign_request.status = 0");
        $check->bind_param("i", $id);
        if (!$check->execute()) {
            return (script("You do not have access to do this", 1));
        }

        $result = $check->get_result();
        if (!$result->num_rows) {
            logDown($myId, "Tried to access request not sent to him/her", 2, 2);
            return (script("You do not have access to do this", 1));
        }
        $approve = 0;
        if ($status == 1) {
            $row = $result->fetch_assoc();
            $myMax = $row['max'];
            $supId = $row['sup_id'];
            $std_id = $row['std_id'];
            $isStudFree = isStudentAssigned($std_id, 1);
            if ($isStudFree != 0) {
                logDown($myId, "Tried to access request for student already allocated", 3, 2);
                return script("This student has already being allocated", 'notification_supervisor.php');
            }
            $getCount = countTotalBySupId($supId);
            // $no = $getCount['no'];
            $full = $getCount['full'];
            if ($full >= $myMax) {
                logDown($myId, "Tried to access request meanwhile maximum number of student for supervisor has been reached", 3, 2);
                return script('Kindly Delete This Request\nYou have already reached your maximum', 1);
            }
            $approve = 1;
            $phone = getStudentPhoneById($std_id);

            $fullSQL = " UPDATE students SET students.cpu_id = (SELECT assign_request.cpu_id FROM 	assign_request WHERE assign_request.id = '$id') WHERE students.id = (SELECT assign_request.student_id FROM assign_request WHERE assign_request.id = '$id');

  UPDATE cpu  SET full = full + 1, no = IF((full-1) >= no, no + 1, no)  WHERE cpu.id = (SELECT assign_request.cpu_id FROM 	assign_request WHERE assign_request.id = '$id');

  UPDATE assign_request SET status = 1 WHERE id = '$id'

  ";
        } else {
            $fullSQL = "UPDATE assign_request SET status = -1 WHERE id = '$id'
  ";
        }
    } else {
        //Special Requests Which Include Inserting If Max Is Still Free
        $check = $con->prepare("SELECT special_request.student_id,
    special_request.field_id as field_id,
    special_request.student_id as std_id,
    supervisors.max as max
    FROM special_request INNER JOIN supervisors on special_request.supervisor_id = supervisors.id WHERE supervisors.id = '$myId' AND special_request.id = ?");
        $check->bind_param("i", $id);
        if (!$check->execute()) {
            return (script("You do not have access to do this", 1));
        }

        $result = $check->get_result();
        if (!$result->num_rows) {
            logDown($myId, "Tried to access request not sent to him/her", 3, 2);
            return (script("You do not have access to do this", 1));
        }
        $row = $result->fetch_assoc();
        $field_id = $row['field_id'];
        $student_id = $row['student_id'];
        if ($status == 1) {
            $approve = 1;
            $myMax = $row['max'];
            $std_id = $row['std_id'];
            $isStudFree = isStudentAssigned($std_id, 1);
            if ($isStudFree != 0) {
                logDown($myId, "Tried to access request for student already approved : " . getStudentDetailsById($std_id), 2, 2);
                return script("This student has already being allocated", 'notification_supervisor.php');
            }
            $getCount = countTotalBySupId($myId);
            $full = $getCount['full'];
            if ($full >= $myMax) {
                logDown($myId, "Tried to access request but maximum already reached. Student : " . getStudentDetailsById($student_id), 3, 2);
                return script('Kindly Delete This Request.\nYou have already reached your maximum', 1);
            }
            //Let us check if the stuff exists in CPU
            $checkExist = $con->query("SELECT id FROM cpu WHERE field_id = '$field_id' AND supervisor_id = '$myId'");
            $phone = getStudentPhoneById($std_id);
            if ($checkExist->num_rows == 1) {
                //Increment
                // echo "Increment"; exit;
                $row = $checkExist->fetch_assoc();
                $cpu_id = $row['id'];
                $fullSQL = "
      UPDATE cpu SET full = full + 1, no = IF((full-1) >= no, no + 1, no)  WHERE cpu.id = '$cpu_id';
      UPDATE students SET students.cpu_id = '$cpu_id' WHERE students.id = '$student_id';
      UPDATE special_request SET status = 1 WHERE id = '$id';

      ";
                // die($fullSQL);
            } else {
                //Insert
                // echo "Insert"; exit;
                $insert = $con->query("INSERT INTO cpu (field_id, supervisor_id, no, full) VALUES ('$field_id','$myId','1','1')");
                $thisNewId = $con->insert_id;
                //Let us now update student
                $fullSQL = "
      UPDATE students SET students.cpu_id = '$thisNewId' WHERE students.id = '$student_id';
      UPDATE special_request SET status = 1 WHERE id = '$id';
      ";
                logDown($myId, "Approved request to supervise student : " . getStudentDetailsById($student_id), 2, 2);
            }
        } else {
            //If rejected
            logDown($myId, "Rejected request to supervise student : " . getStudentDetailsById($student_id), 2, 2);
            $fullSQL = "UPDATE special_request SET status = -1 WHERE id = '$id';
  ";
        }
        //End of Special
    }
    // die($fullSQL);
    $updateSQL = $con->multi_query($fullSQL);
    if (!$updateSQL) {
        return (script("You do not have access to do this", 1));
    } else
    if ($approve) {
        saveToHistory($student_id, $field_id, 1);
        $send = sms("Dear student, you now have a supervisor to supervise you. Kindly login your portal to check this ASAP.", $phone);
    }

    return (script("Response Saved", 1));
}

function getMyFiles($id)
{
    $con = connect();
    $cpu = getCpuId($id);
    $query1 = "SELECT msg, attachment, entry_date FROM stud_to_group WHERE cpu_id  = '$cpu' AND attachment != 0
  UNION
  SELECT msg, attachment, entry_date FROM sup_to_group WHERE cpu_id  = '$cpu' AND attachment != 0
  UNION
  SELECT msg, attachment, entry_date FROM sup_to_stud WHERE student_id  = '$id'  AND attachment != 0
  UNION
  SELECT msg, attachment, entry_date FROM stud_to_sup WHERE student_id  = '$id'  AND attachment != 0";

    $query = $con->query($query1);
    return $query;
}

function editCpuCount($cpu_id, $no)
{
    $con = connect();
    $id = getIdFromSession($_SESSION['id'], 'supervisor');
    if (canThisIdAccess($cpu_id) < 1) {
        return script("You do not have access to this", 1);
    }
    if ($no == 0) {
        return script("Why not delete this instead?", 1);
    }

    $countSQL = $con->query("SELECT * FROM cpu WHERE id = '$cpu_id'");
    $res = $countSQL->fetch_assoc();
    $currentCount = $res['full'];
    //Let's check to see if the number coming is below the current number of students already enrolled
    if ($no < $currentCount) {
        return script("Sorry, the number you entered is below the number of students already enrolled. ", 1);
    }

    if (strlen($no) < 1 || strlen($no) > 3) {
        logDown($id, "Edit Number Of Students For " . getFieldNameFromCpuID($cpu_id) . " invalid", 2, 2);
        return script("Fill form properly", 1);
    }

    $getCurrentCount = $con->query("SELECT SUM(no) as total FROM `cpu` WHERE supervisor_id = '$id' AND id != '$cpu_id'");
    $result = $getCurrentCount->fetch_assoc();
    $currentCount = $result['total'];
    $max = getMyMax($id);
    if (($currentCount + $no) > $max) {
        logDown($id, "Tried to edit a group and was blocked when trying to assign more than max", 3, 2);
        return script('Attempt Blocked.\nYou can\'t allocate more than your maximum');
    }
    $con->query("UPDATE cpu SET no = '$no' WHERE id = '$cpu_id' AND supervisor_id = '$id'");
    logDown($id, "Edit Number Of Students For " . getFieldNameFromCpuID($cpu_id) . " approved", 2, 2);
    return script("Action Completed", 1);
}

function delCpuOnStaff($cpu_id)
{
    $con = connect();
    $id = getIdFromSession($_SESSION['id'], 'supervisor');
    if (canThisIdAccess($cpu_id) < 1) {
        logDown($id, "Tried to access group not defined for supervisor. Group name: " . getFieldNameFromCpuID($cpu_id), 3, 2);
        return script("You do not have access to this", 1);
    }
    $checkSQL = $con->prepare("SELECT * FROM cpu WHERE id = ?");
    $checkSQL->bind_param("i", $cpu_id);
    if (!$checkSQL->execute()) {
        return script("You are not allowed to delete this.", 1);
    }

    $res = $checkSQL->get_result();
    if ($res->num_rows == 0) {
        logDown($id, "Tried to delete a group not within his/her reach", 3, 2);
        return script("Access Denied");
    }
    $row = $res->fetch_assoc();
    if ($row['full'] == 0) {
        $checkAgain = $con->prepare("SELECT id FROM students WHERE cpu_id = ?");
        $checkAgain->bind_param("i", $cpu_id);
        if (!$checkAgain->execute()) {
            return script("You are not allowed to do this", 1);
        }

        $res2 = $checkAgain->get_result();
        if ($res2->num_rows == 0) {
            //Now, you can delete
            //We need to keep it cool and clean, so we delete all requests on this ID and group messages sent (if any)

            $delSQL = $con->multi_query("
      DELETE FROM cpu WHERE id = '$cpu_id' AND supervisor_id = '$id' AND full = 0;
      DELETE FROM assign_request WHERE cpu_id = '$cpu_id';
      DELETE FROM stud_to_group WHERE  cpu_id = '$cpu_id';
            ");
            if ($delSQL) {
                logDown($id, "Group deleted for supervisor = " . getFieldNameFromCpuID($cpu_id) . " but tried to delete the group", 3, 2);
                return script("Action Carried Out", 1);
            }
            return script("Unauthorized Access", 1);
        } else {
            logDown($id, "There exists " . $res2->num_rows . " Student(s) who are currently enrolled to " . getFieldNameFromCpuID($cpu_id) . " but tried to delete the group", 3, 2);
            return script("There exists " . $res2->num_rows . " Student(s) who are currently enrolled to this field of yours. Kindly request the admin to clear them before you proceed with this.", 1);
        }
    } else {
        logDown($id, "There exists " . $row['full'] . " Student(s) who are currently enrolled to " . getFieldNameFromCpuID($cpu_id) . " but tried to delete the group", 3, 2);
        return script("There exists " . $row['full'] . " Student(s) who are currently enrolled to this field of yours. Kindly request the admin to clear them before you proceed with this.", 1);
    }
    return script("You are not allowed to do this", 1);
}

function getMessageForGroupByID($cpu, $id, $type = "student")
{
    if ($type != 'student') {
    }
    $con = connect();
    $get = $con->query("SELECT * FROM sup_to_group WHERE sup_to_group.cpu_id = '$cpu'");
    $get2 = $con->query("SELECT * FROM stud_to_group WHERE stud_to_group.cpu_id = '$cpu'");
    return (array($get2, $get));
}

function changePassword($oldPass, $nPass, $cPass, $type = "student")
{
    if (strlen($nPass) < 4) {
        return script("Please enter a stronger password");
    }
    if ($nPass != $cPass) {
        return script("New password and confirm password mismatch");
    }

    $con = connect();
    $table = 'students';
    $id = getIdFromSession($_SESSION['id'], 'student');
    if ($type != 'student') {
        $table = 'supervisors';
        $id = getIdFromSession($_SESSION['id'], 'supervisor');
    }
    $checkSQL = $con->prepare("SELECT password FROM $table WHERE id = ?");
    $checkSQL->bind_param("i", $id);
    $checkSQL->execute();
    $res = $checkSQL->get_result();
    if ($res->num_rows != 1) {
        return script("You are not an active user", 1);
    }

    $row = $res->fetch_assoc();
    if ($row['password'] != salt($oldPass)) {
        return script("Old password invalid", 1);
    }

    $nPass = salt($nPass);
    if ($row['password'] == $nPass) {
        return script("System Detects You Are Trying To Change To Your Current Password, You'll Not Be Allowed To Proceed With This", 1);
    }

    $updateSQL = $con->query("UPDATE $table SET password = '$nPass' WHERE id = '$id' LIMIT 1");
    if ($updateSQL) {
        logDown($id, "Password changed", 3, (($type == 'student') ? 1 : 2));
        return script("Password updated");
    }
    return script("Unknown Error Occured, Kindly Retry Again");
}

function addAnnouncement($id, $cpu, $content, $file)
{
    if (strlen($content) < 10) {
        return script("Kindly enter more characters", 1);
    }
    $date = getStamp();
    $N = count($cpu);
    $con = connect();
    $valid_extension = array("jpg", "png", "gif", "jpeg", "pdf", "xls", "xlsx", "txt", "csv", "doc", "docx", "ppt", "pptx");
    if ($_FILES[$file]['size'] && !in_array(strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION)), $valid_extension)) {
        logDown($id, "Invalid file upload for announcement. Extension Bad: " . (strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION))), 3, 2);
        return script("Invalid File Upload", 1);
    }
    $loc = 0; //Just in case the message do not have an attachment
    if ($_FILES[$file]['size']) {
        //The message has an attachment
        $loc = genRand() . "." . strtolower(pathinfo(@$_FILES[$file]['name'], PATHINFO_EXTENSION));
        $upload = move_uploaded_file(@$_FILES[$file]['tmp_name'], "uploads/" . $loc);
        if (!$upload) {
            logDown($id, "Attachment for announcement not uploaded (Read/Write Permission) ", 3, 2);
            return script('Attachment not uploaded\nConsider allowing read/write permissions');
        }
        chmod("uploads/" . $loc, 0777);
    }
    for ($i = 0; $i < $N; $i++) {
        if (canThisIdAccess($cpu[$i]) < 1) {
            return script("You are not allowed", 1);
        }

        $sql = "INSERT INTO announcements (cpu_id,supervisor_id,attachment,msg,entry_date) VALUES (?,?,?,?,?)";
        $query = $con->prepare($sql);
        $query->bind_param("iisss", $cpu[$i], $id, $loc, $content, $date);
        if (!$query->execute()) {
            return script("Could not update this, kindly retry by typing less characters", 1);
        }
    }
    logDown($id, "Announcement Sent To $N Groups. Message Preview: " . substr($content, 0, 200), 1, 2);
    return script("Announcement Sent To $N Group(s)", 1);
}

function getAnnouncements()
{
    $con = connect();
    $id = getIdFromSession($_SESSION['id']);
    $cpu = getCpuId($id);
    if ($cpu < 1) {
        return script("You are yet to have a field of interest");
    }

    $query = $con->query("SELECT announcements.attachment as loc, announcements.cpu_id as cpu, announcements.msg as msg, announcements.entry_date as date FROM `announcements` WHERE announcements.cpu_id = '$cpu' ;");
    return $query;
}



function sms($msg, $phoneno)
{
    if (strlen($phoneno) != 11) {
        return "";
    }
    $phoneno = "234" . @substr($phoneno, -10);
    $sender = ("UNILORIN");

    // Start
    $email = "job@students.unilorin.edu.ng";
    $email = base64_decode("am9iQHN0dWRlbnRzLnVuaWxvcmluLmVkdS5uZw==");
    $password = $email;
    $message = $msg;
    $sender_name = $sender;
    $recipients = $phoneno;
    $forcednd =  1;
    $data = array("email" => $email, "password" => $password, "message" => $message, "sender_name" => $sender_name, "recipients" => $recipients, "forcednd" => $forcednd);
    $data_string = json_encode($data);
    $ch = curl_init('https://app.multitexter.com/v2/app/sms');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string)));
    $result = curl_exec($ch);
    // $res_array = json_decode($result);
    // print_r($res_array);
    // End
    // $send = @file_get_contents("http://api.smartsmssolutions.com/smsapi.php?username=" . SMS_USER . "&password=" . SMS_PASS . "&sender=$sender&recipient=$phoneno&message=$msg");
}

// sms("Hello World", "08100134741");

function getAllProgressByKey($key)
{
    $con = connect();
    $db_val = switchKey($key);
    $result = "";
    $start_table = '		<table cellpadding="0" cellspacing="0" border="0" class="table tabled" id="">

  <thead>
    <tr>
      <th>SN</th>
      <th>Student Name</th>
      <th>Student Matric</th>
    </tr>

  </thead>

  <tbody>
';

    $end_table = '
  </tbody>

  <tfoot>
    <tr>
      <th>SN</th>
      <th>Student Name</th>
      <th>Student Matric</th>
    </tr>

  </tfoot>


</table>';
    $myId = getIdFromSession($_SESSION['id'], 'supervisor');

    //-1 => Doesn't exist
    //if not 0-6, get status
    if ($db_val == -1) {
        //Get all those students who do not exist in database with status = 1
        $query = $con->query("SELECT CONCAT(students.lastname, ', ', students.firstname) as name, students.regno as matric, students.id FROM students INNER JOIN cpu ON students.cpu_id = cpu.id INNER JOIN supervisors ON cpu.supervisor_id = supervisors.id WHERE cpu.supervisor_id = '$myId' AND students.id NOT IN (SELECT progress.student_id FROM progress )");
        $result = $query->num_rows;
        if ($result == 0) {
            if (validateIfFieldHasBeenChosen()) {
                $result = "<h3 class='alert alert-info'>System detects that all your students already moved to a phase greater than or equal to proposal.</h3>";
            } else {
                $result = "<h3 class='alert alert-danger'>Oh, snap you are yet to have a field of interest</h3>";
            }
        } else {
            $output = "";
            $sn = 0;
            while ($row = $query->fetch_assoc()) {
                $output .= '
        <td>' . ++$sn . '</td>
        <td>' . $row['name'] . '</td>
        <td>' . $row['matric'] . '</td>
        </tr>
        ';
            }
            $result = $start_table . $output . $end_table;
        }
    } elseif (in_array($db_val, range(0, 6))) {

        //That means that $db_val is 0-6
        $query = $con->query("SELECT CONCAT(students.lastname, ' ', students.firstname) as name, students.regno as matric FROM students INNER JOIN cpu ON cpu.id = students.cpu_id INNER JOIN progress ON progress.student_id = students.id INNER JOIN supervisors ON cpu.supervisor_id = cpu.supervisor_id WHERE progress.status = 1 AND progress.chapter = '$db_val' AND cpu.supervisor_id = '$myId' GROUP BY matric");
        $result = $query->num_rows;
        if ($result == 0) {
            $result = "<h3 class='alert alert-info'>No Student In This Current Phase.</h3>";
        } else {
            $output = "";
            $sn = 0;
            while ($row = $query->fetch_assoc()) {
                $output .= '
        <td>' . ++$sn . '</td>
        <td>' . $row['name'] . '</td>
        <td>' . $row['matric'] . '</td>
        </tr>
        ';
            }
            $result = $start_table . $output . $end_table;
        }
    } else {
        $myMax = getMyMax($myId);
        //First, we get the total number of students for this staff
        $query1 = $con->query("SELECT id as number FROM students WHERE cpu_id IN (SELECT cpu.id FROM cpu WHERE cpu.supervisor_id = '$myId')");
        $no1 = $query1->num_rows;
        $allKeys = range(0, 6);
        $allValues = [];
        $i = 0;
        //Secondly, we get the number of those in proposal, chapter 1 -5 to clearance
        for ($i; $i < 7; $i++) {
            $query = $con->query("SELECT progress.id FROM progress INNER JOIN students ON students.id = progress.student_id INNER JOIN cpu ON cpu.id = students.cpu_id WHERE cpu.supervisor_id = '$myId' AND progress.status = '1' AND progress.chapter = '$i'; ");
            $no = $query->num_rows;
            array_push($allValues, $no);
        }
        $finalReport = array_combine($allKeys, $allValues);
        $sum = array_sum($finalReport);
        $result = "<h4>There are <span class='text-info'>$no1</span> number of students who are currently assigned to you. <br/>Maximum Number of Students Assigned To You By The System Administrator = <span class='text-info'>$myMax</span> <hr/>Out of these <span class='text-info'>$no1 students</span>, <span class='text-info'>$sum</span> students were seen in the progress list (Only those who you approved). Breakdown below:<br/></h4>
    <table class='table' cellpadding='3' cellspacing='2' border='2'>
    <tr><th>SN</th><th>Chapter</th><th>Total Number of Students</th></tr>";
        $i = 0;
        for ($i = 0; $i < 7; $i++) {
            $result .= "<tr><td>" . ($i + 1) . "</td><td>";
            $result .= (($i == 0) ? "Proposal" : (($i == 6) ? "Clearance" : "Chapter $i"));
            $result .= "</td><td>" . ($finalReport[$i]);

            $result .= "</td></tr>";
        }
        $result .= "
    </table>
    ";
    }
    return $result;
}

function getDetailedReportOf($type)
{
    $input = array('fields_supervisors', 'fields_students', 'supervisors_students', 'allocation');
    if (!in_array($type, $input)) {
        return "<h1>Access Denied</h1>";
    } else {
        echo '<a href="report.php"><button class="btn btn-success">Go Back</button></a>';
        echo '<table cellpadding="0" cellspacing="0" border="0" class="table" id="example">';

        $con = connect();
        if ($type == 'fields_students') {
            echo "<caption><h3>Field of Interests Selected By Students</h3></caption>";
            // $query1 = $con->query("SELECT field_of_interests.name, CONCAT(students.lastname, ' ', students.firstname, ' (', students.regno,') ') as stud FROM field_of_interests INNER JOIN cpu ON cpu.field_id = field_of_interests.id INNER JOIN students ON students.cpu_id = cpu.id ORDER BY field_of_interests.name");
            $query1 = $con->query("SELECT field_of_interests.id, field_of_interests.name FROM field_of_interests WHERE field_of_interests.id IN (SELECT cpu.field_id FROM cpu) ");
            while ($row1 = $query1->fetch_assoc()) {
                $sn = 1;
                $field_name = $row1['name'];
                $field_id = $row1['id'];
                $query2 = $con->query("SELECT field_of_interests.name as field, CONCAT(students.lastname, ' ', students.firstname) as name, students.regno as reg FROM students INNER JOIN cpu ON students.cpu_id = cpu.id  INNER JOIN field_of_interests ON cpu.field_id = field_of_interests.id WHERE field_of_interests.id = '$field_id'");
                $count = $query2->num_rows;
                $count = (($count > 1) ? "$count students" : "$count student");
                echo "<tr><td colspan='3' style='text-align:center' class='alert alert-info'>------- <b>$field_name</b> is been used by  <i>$count</i> ------------------------</td></tr>";
                echo '<tr><th>SN</th><th>Matric</th><th>Student Name</th></tr>';
                while ($row2 = $query2->fetch_assoc()) {
?>
<tr>
    <td><?php echo $sn++; ?></td>
    <td><?php echo $row2['reg'] ?></td>
    <td><?php echo $row2['name'] ?></td>
</tr>
<?php
                }
            }
            $query3 = $con->query("SELECT field_of_interests.name as name, COUNT(students.id) as no FROM field_of_interests INNER JOIN cpu ON cpu.field_id = field_of_interests.id INNER JOIN students ON students.cpu_id = cpu.id  GROUP BY field_of_interests.id ORDER BY COUNT(students.id) DESC");
            $sn = 1;
            echo "<tr><th colspan='3' style='text-align:center' class='alert alert-success'>Statistics</th></tr>";
            while ($row3 = $query3->fetch_assoc()) {
                ?>

<tr>
    <td><?php echo $sn++; ?></td>
    <td><?php echo $row3['name'] ?></td>
    <td><?php echo $row3['no'] ?></td>
</tr>
<?php
            }
        } elseif ($type == 'fields_supervisors') {
            echo "<caption><h3>Field of Interests Selected By Supervisors</h3></caption>";

            // $query1 = $con->query("SELECT field_of_interests.name, CONCAT(titles.name,' ', supervisors.lastname, ' ', supervisors.firstname, ' (', supervisors.fileno,') ') as sup FROM field_of_interests INNER JOIN cpu ON cpu.field_id = field_of_interests.id INNER JOIN supervisors ON supervisors.id = cpu.supervisor_id INNER JOIN titles ON titles.id = supervisors.title_id ORDER BY field_of_interests.name");
            // $query2 = $con->query("SELECT field_of_interests.name, COUNT(supervisors.id) as no FROM field_of_interests INNER JOIN cpu ON cpu.field_id = field_of_interests.id INNER JOIN supervisors ON supervisors.id = cpu.supervisor_id  GROUP BY field_of_interests.id ORDER BY COUNT(supervisors.id) DESC");

            $query1 = $con->query("SELECT field_of_interests.id, field_of_interests.name FROM field_of_interests WHERE field_of_interests.id IN (SELECT cpu.field_id FROM cpu) ");
            while ($row1 = $query1->fetch_assoc()) {
                $sn = 1;
                $field_name = $row1['name'];
                $field_id = $row1['id'];
                $query2 = $con->query("SELECT field_of_interests.name as field, CONCAT(titles.name,' ', supervisors.lastname, ' ', supervisors.firstname, ' (', supervisors.fileno,') ') as sup FROM supervisors INNER JOIN titles ON titles.id = supervisors.title_id  INNER JOIN cpu ON supervisors.id = cpu.supervisor_id  INNER JOIN field_of_interests ON cpu.field_id = field_of_interests.id WHERE field_of_interests.id = '$field_id'");
                $count = $query2->num_rows;
                $count = (($count > 1) ? "$count supervisors" : "$count supervisor");
                echo "<tr><td colspan='3' style='text-align:center' class='alert alert-info'>------- <b>$field_name</b> is been used by  <i>$count</i> ------------------------</td></tr>";
                echo '<tr><th>SN</th><th colspan="2">Student Name</th></tr>';
                while ($row2 = $query2->fetch_assoc()) {
                ?>
<tr>
    <td><?php echo $sn++; ?></td>
    <td colspan='2'><?php echo $row2['sup'] ?></td>
</tr>
<?php
                }
            }
            $query3 = $con->query("SELECT field_of_interests.name, COUNT(supervisors.id) as no FROM field_of_interests INNER JOIN cpu ON cpu.field_id = field_of_interests.id INNER JOIN supervisors ON supervisors.id = cpu.supervisor_id  GROUP BY field_of_interests.id ORDER BY COUNT(supervisors.id) DESC");
            $sn = 1;
            echo "<tr><th colspan='3' style='text-align:center' class='alert alert-success'>Statistics</th></tr>";
            while ($row3 = $query3->fetch_assoc()) {
                ?>

<tr>
    <td><?php echo $sn++; ?></td>
    <td><?php echo $row3['name'] ?></td>
    <td><?php echo $row3['no'] ?></td>
</tr>
<?php
            }
        } elseif ($type == 'supervisors_students') {
            echo "<caption><h3>Matchings Between Supervisors and Students</h3></caption>";

            //SELECT supervisors.id, CONCAT(students.lastname, ' ', students.firstname, ' (', students.regno,') ') as stud FROM supervisors INNER JOIN cpu ON cpu.supervisor_id = supervisors.id INNER JOIN students ON cpu.id = students.cpu_id
            $query1 = $con->query("SELECT supervisors.id, CONCAT(titles.name,' ', supervisors.lastname, ' ', supervisors.firstname, ' (', supervisors.fileno,') ') as sup FROM supervisors INNER JOIN titles ON titles.id = supervisors.title_id");
            while ($row1 = $query1->fetch_assoc()) {
                $sup_id = $row1['id'];
                $query2 = $con->query("SELECT field_of_interests.name as field, CONCAT(students.lastname, ' ', students.firstname) as name, students.regno as reg FROM students INNER JOIN cpu ON students.cpu_id = cpu.id  INNER JOIN field_of_interests ON cpu.field_id = field_of_interests.id WHERE cpu.supervisor_id = '$sup_id'");
                $count = $query2->num_rows;
                $sup_name = $row1['sup'];
                $count = (($count > 1) ? "$count students" : "$count student");
                echo "<tr><td colspan='3' style='text-align:center' class='alert alert-info'>------- <b>$sup_name</b> is supervising  <i>$count</i> ------------------------</td></tr>";
            ?>

<tr>
    <th>Matric Number</th>
    <th>Name</th>
    <th>Field of Interest</th>
</tr>
<?php
                //Get students from supervisors

                while ($row2 = $query2->fetch_assoc()) {
                ?>
<tr>
    <td><?php echo $row2['reg'] ?></td>
    <td><?php echo $row2['name'] ?></td>
    <td><?php echo $row2['field'] ?>
    </td>
</tr>
<?php
                }
                echo "<tr><td colspan='3'></td></tr>";
            }
        } else {
            //Allocation History

            $query = $con->query("SELECT CONCAT(students.lastname, ' ', students.firstname, ' (', students.regno,') ') as stud, field_of_interests.name as field_name, allocation.preference as prefer FROM allocation INNER JOIN students ON students.id = allocation.student_id INNER JOIN field_of_interests ON field_of_interests.id = allocation.field_id ORDER BY allocation.preference ASC");
            $countAll  = $query->num_rows;

            echo "<tr><th colspan='4' style='text-align:center' class='alert alert-info'>Statistical Analysis For $countAll Students</th></tr>";
            $firstChoice = $con->query("SELECT id FROM allocation WHERE preference = 1")->num_rows;
            $secondChoice = $con->query("SELECT id FROM allocation WHERE preference = 2")->num_rows;
            $thirdChoice = $con->query("SELECT id FROM allocation WHERE preference = 3")->num_rows;
            echo "<tr><th colspan='2'>First Choice </th><td colspan='2'>$firstChoice</td></tr>";
            echo "<tr><th colspan='2'>Second Choice </th><td colspan='2'>$secondChoice</td></tr>";
            echo "<tr><th colspan='2'>Third Choice </th><td colspan='2'>$thirdChoice</td></tr>";
            echo "<tr><th colspan='4'> <table><tr><td>$countAll</td><td>$firstChoice</td><td>$secondChoice</td><td>$thirdChoice</td></tr></table> </th></tr>";
            $analysis = "The system was able to assign <span class='alert alert-info'>" . substr((($firstChoice / $countAll) * 100), 0, 4) . "%</span> of first choice, <span class='alert alert-info'>" . substr((($secondChoice / $countAll) * 100), 0, 4) . "%</span> of second choice and <span class='alert alert-info'>" . substr((($thirdChoice / $countAll) * 100), 0, 4) . "%</span> of third choice";
            echo "<tr><th colspan='4' style='text-align:center' class='alert alert-danger'>$analysis</th></tr>";
            echo "<tr><td colspan='4'>
<br/>
<br/>
<br/>
<br/>
</td</tr>";

            $sn = 1;
            echo "<tr><th>SN</th><th>Student</th><th>Field</th><th>Preference</th></tr>";
            while ($row = $query->fetch_assoc()) {
                $name = $row['stud'];
                $field = $row['field_name'];
                $pref = intval($row['prefer']);
                if ($pref == 1) {
                    $pref = "First Choice";
                } elseif ($pref == 2) {
                    $pref = "Second Choice";
                } else {
                    $pref = "Third Choice";
                }
                echo "<tr><td>" . ($sn++) . "</td><td>$name</td><td>$field</td><td>$pref</td></tr>";
            }
        }
    }
    echo '</table><a href="report.php"><button class="btn btn-success">Go Back</button></a>';
}
function getAllReportByKey($key)
{
    $con = connect();
    $db_val = switchKey($key);
    $result = "";
    $start_table = '		<table cellpadding="0" cellspacing="0" border="0" class="table tabled" id="">

  <thead>
    <tr>
      <th>SN</th>
      <th>Student Name</th>
      <th>Student Matric</th>
    </tr>

  </thead>

  <tbody>
';

    $end_table = '
  </tbody>

  <tfoot>
    <tr>
      <th>SN</th>
      <th>Student Name</th>
      <th>Student Matric</th>
    </tr>

  </tfoot>


</table>';
    //$myId = getIdFromSession($_SESSION['id'], 'supervisor');

    //-1 => Doesn't exist
    //if not 0-6, get status
    if ($db_val == -1) {
        //Get all those students who do not exist in database with status = 1
        $query = $con->query("SELECT CONCAT(students.lastname, ', ', students.firstname) as name, students.regno as matric, students.id FROM students INNER JOIN cpu ON students.cpu_id = cpu.id INNER JOIN supervisors ON cpu.supervisor_id = supervisors.id WHERE  students.id NOT IN (SELECT progress.student_id FROM progress )");
        $result = $query->num_rows;
        if ($result == 0) {
            $result = "<h3 class='alert alert-info'>System detects that all students already moved to a phase greater than or equal to proposal.</h3>";
        } else {
            $output = "";
            $sn = 0;
            while ($row = $query->fetch_assoc()) {
                $output .= '
        <td>' . ++$sn . '</td>
        <td>' . $row['name'] . '</td>
        <td>' . $row['matric'] . '</td>
        </tr>
        ';
            }
            $result = $start_table . $output . $end_table;
        }
    } elseif (in_array($db_val, range(0, 6))) {
        $db_val2 = $db_val + 1;
        //That means that $db_val is 0-6
        if ($db_val != 6)
            $query = $con->query("SELECT CONCAT(students.lastname, ' ', students.firstname) as name, students.regno as matric FROM students INNER JOIN cpu ON cpu.id = students.cpu_id INNER JOIN progress ON progress.student_id = students.id INNER JOIN supervisors ON cpu.supervisor_id = cpu.supervisor_id WHERE progress.status = 1 AND progress.chapter = '$db_val' AND progress.student_id NOT IN (SELECT progress.student_id FROM progress WHERE progress.chapter = '$db_val2' AND progress.status = 1 )  GROUP BY matric");
        else
            $query = $con->query("SELECT CONCAT(students.lastname, ' ', students.firstname) as name, students.regno as matric FROM students INNER JOIN cpu ON cpu.id = students.cpu_id INNER JOIN progress ON progress.student_id = students.id INNER JOIN supervisors ON cpu.supervisor_id = cpu.supervisor_id WHERE progress.status = 1 AND progress.chapter = '$db_val' AND progress.student_id   GROUP BY matric"); //Project Clearance

        $result = $query->num_rows;
        if ($result == 0) {
            $result = "<h3 class='alert alert-info'>No Student In This Current Phase. Check next/previous phase</h3>";
        } else {
            $output = "";
            $sn = 0;
            while ($row = $query->fetch_assoc()) {
                $output .= '
        <td>' . ++$sn . '</td>
        <td>' . $row['name'] . '</td>
        <td>' . $row['matric'] . '</td>
        </tr>
        ';
            }
            $result = $start_table . $output . $end_table;
        }
    } elseif ($db_val == 100) {
        //System Allocation History
        $result =  '<br/><form action="" method="post">
        <p><select name="type" id="" required class="">
            <option value="">Select one</option>
            <option value="fields_supervisors">Fields Selected By Supervisors</option>
            <option value="fields_students">Fields Selected By Students</option>
            <option value="supervisors_students">Supervisors/Students</option>
            <option value="allocation">Allocation on preference evaluation</option>
        </select></p><p>
        <input type="submit" class="btn btn-info" value="Query"/></p>
    </form>';
        /*$allKeys = range(0, 6);
        $allValues = [];
        $result1 = "<table><tr><th>SN</th><th>Supervisor's Name</th><th>Number of students allocated</th></tr>";
        $result2 = "<table><tr><th>SN</th><th>Matric</th><th>Name</th><th>Supervisor</th><th>Field</th></tr>";
        $i = $counter = 0;
        $getAllSup = connect()->query("SELECT cpu.id AS i, supervisors.id as sup FROM supervisors INNER JOIN cpu ON cpu.supervisor_id = supervisors.id WHERE supervisors.id IN (SELECT cpu.supervisor_id FROM cpu) GROUP BY cpu.supervisor_id");
        while ($row = $getAllSup->fetch_assoc()) {
            $cpu = $row['i'];
            $supId = $row['sup'];
            $howMany = connect()->query("SELECT id FROM students WHERE cpu_id = '$cpu' ");
            $result1 .= "<tr><td>" . ++$counter . "</td><td>" . getSupervisorNameById($supId) . "</td><td>" . $howMany->num_rows . "</td></tr>";
        }
        $result1 .= "</table>";
        $getSupForStudents = connect()->query("SELECT students.regno as mat, CONCAT(students.lastname, ', ', students.firstname, ' ') AS stud, cpu.supervisor_id as sup, cpu.id as id FROM students INNER JOIN cpu ON students.cpu_id = cpu.id");
        $resCounter = 0;
        while ($value = $getSupForStudents->fetch_assoc()) {
            $sup = $value['sup'];
            $stud = $value['stud'];
            $cpu = $value['id'];
            $mat = $value['mat'];
            $result2 .= "<tr><td>" . ++$resCounter . "</td><td>$mat</td><td>$stud</td><td>" . getSupervisorNameById($sup) . "</td><td>" . getFieldNameFromCpuID($cpu) . "</td></tr>";
        }
        $result2 .= "</table>";
        for ($i; $i < 7; $i++) {
            $query = $con->query("SELECT progress.id FROM progress INNER JOIN students ON students.id = progress.student_id INNER JOIN cpu ON cpu.id = students.cpu_id WHERE  progress.status = '1' AND progress.chapter = '$i'; ");
            $no = $query->num_rows;
            array_push($allValues, $no);
        }
        $finalReport = array_combine($allKeys, $allValues);
        $result = " <table class='table' cellpadding='3' cellspacing='2' border='2'>
<tr><th>SN</th><th>Supervisor</th><th>Total Number of Students</th></tr>";
        $i = 0;
        // for ($i = 0; $i < 7; $i++) {
        //     $result .= "<tr><td>" . ($i + 1) . "</td><td>";
        //     $result .= (($i == 0) ? "Proposal" : (($i == 6) ? "Clearance" : "Chapter $i"));
        //     $result .= "</td><td>" . ($finalReport[$i]);

        //     $result .= "</td></tr>";
        // }
        $result .= "</table>" . $result1 . $result2;
        */
    } else { //General Report
        $myMax = countStudents();
        //First, we get the total number of students for this staff
        $query1 = $con->query("SELECT id FROM students ");
        $no1 = $query1->num_rows;
        $query2 = $con->query("SELECT id FROM students WHERE cpu_id != 0");
        $no2 = $query2->num_rows;
        $allKeys = range(0, 6);
        $allValues = [];
        $i = 0;
        $j = 0;
        //Secondly, we get the number of those in proposal, chapter 1 -5 to clearance
        for ($i; $i < 7; $i++) {
            $j = $i + 1;
            $query = $con->query("SELECT CONCAT(students.lastname, ' ', students.firstname) as name, students.regno as matric FROM students INNER JOIN cpu ON cpu.id = students.cpu_id INNER JOIN progress ON progress.student_id = students.id INNER JOIN supervisors ON cpu.supervisor_id = cpu.supervisor_id WHERE progress.status = 1 AND progress.chapter = '$i' AND progress.student_id NOT IN (SELECT progress.student_id FROM progress WHERE progress.chapter = '$j' AND progress.status = 1 )  GROUP BY matric");
            $no = $query->num_rows;
            array_push($allValues, $no);
        }
        $finalReport = array_combine($allKeys, $allValues);
        $sum = array_sum($finalReport);
        $result = "<h4>There are <span class='text-info'>$no1</span> number of students, allocated students = <span class='text-info'>$no2</span>. <br/><!--Total Number of Students = <span class='text-info'>$myMax</span> <hr/>Out of these, $no2 have been allocated. <span class='text-info'>$no1 students</span>, <span class='text-info'>$sum</span> students were seen in the progress list (Only those who you approved). -->Breakdown below:<br/></h4>
    <table class='table' cellpadding='3' cellspacing='2' border='2'>
    <tr><th>SN</th><th>Chapter</th><th>Total Number of Students</th></tr>";
        $i = 0;
        for ($i = 0; $i < 7; $i++) {
            $result .= "<tr><td>" . ($i + 1) . "</td><td>";
            $result .= (($i == 0) ? "Proposal" : (($i == 6) ? "Clearance" : "Chapter $i"));
            $result .= "</td><td>" . ($finalReport[$i]);

            $result .= "</td></tr>";
        }
        $result .= "
    </table>
    ";
    }
    return $result;
}

function getLogsByType($type)
{
    if ($type == 'admin') {
        $table_name = "admin_logs";
    } elseif ($type == 'supervisor') {
        $table_name = 'supervisor_logs';
    } else {
        $table_name = 'student_logs';
    }
    return (connect()->query("SELECT * FROM $table_name"));
}

function clearLogsByType($type)
{
    if ($type == 'admin') {
        $table_name = "admin_logs";
    } elseif ($type == 'supervisor') {
        $table_name = 'supervisor_logs';
    } else {
        $table_name = 'student_logs';
    }
    $query = (connect()->query("TRUNCATE $table_name"));
    if ($query) {
        logDown(0, "Truncated $type logs", 3, 3);
        return script(ucwords($type) . " logs deleted");
    } else {
        logDown(0, "Could not truncate $type logs", 3, 3);
        return script(ucwords($type) . " logs could not be truncated");
    }
}

function delProgress($id)
{
    $con = connect();
    $query = $con->prepare("SELECT * FROM progress WHERE id = ?");
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        logDown(0, "Tried to delete a progress but failed while querying", 3, 3);
        return script("Attempt Blocked!", 1);
    }
    $result = $query->get_result();
    if ($result->num_rows != 1) {
        logDown(0, "Admin modified delete progress to access student who doesn't exist", 3, 3);
        return script("Dear admin, you are toying with URLs. Stop!", 3, 3);
    }
    $row = $result->fetch_assoc();
    $student = getStudentDetailsById($row['student_id']);
    $stud_id = $row['student_id'];
    $chapter = $row['chapter'];
    $chap_title = (($chapter == 0) ? 'Proposal' : (($chapter == 6) ? "Full Project" : "Chapter $chapter"));
    $phone = getStudentPhoneById($stud_id);
    $status = $row['status'];
    if ($status != 1) {
        logDown(0, "Admin tried to delete progress for a project who is yet to be approved, let the student do this instead", 3, 3);
        return script("Kindly tell the student to delete this", 1);
    }
    //if status == 1, what happens? First, we check to see if there is any progress chapter > than the current yet-to-be-deleted chapter

    $check = (connect())->query("SELECT * FROM progress WHERE student_id = '$stud_id' AND chapter > '$chapter' ");
    if ($check->num_rows > 0) {
        logDown(0, "Tried to delete a progress for student ($student) - $chap_title without deleting prior chapters", 3, 3);
        return script("You need to delete previous chapter before this", 1);
    }
    $stamp = getStamp();
    //if everything goes as planned
    $msg = "Dear $student, your $chap_title has been deleted by the system administrator on $stamp. Kindly re-upload!";
    $delSQL = (connect())->query("DELETE FROM progress WHERE id = '$id' LIMIT 1");
    if ($delSQL) {
        logDown(0, "Admin deleted $student progress for $chap_title", 3, 3);
        sms($msg, $phone);
        return script("Action carried out successfully", 1);
    } else {
        logDown(0, "Admin deleted $student progress for $chap_title  but failed", 3, 3);
        return script("Error occured while trying to delete", 1);
    }
}

function getStudentAvatarById($id)
{
    $con = connect();
    $query = $con->prepare("SELECT location FROM students WHERE id = ? LIMIT 1");
    $query->bind_param("i", $id);
    if (!$query->execute()) {
        return "";
    }
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    $loc = $row['location'];
    if (strlen($loc) < 5) {
        return "";
    }

    return $loc;
}
function zipFilesByStudentId($id, $type, $user = 'student')
{
    $student_name = substr($fullname = getStudentDetailsById($id), 0, 15);
    if ($user != 'student') {
        if (canThisStudentIdBeAccess($id) < 1 || !in_array($type, array(1, -1, 0, 2))) {
            return script("Access to these files is denied", 1);
        }
    }
    $file_name = preg_replace('/[^a-z0-9]+/', '-', strtolower($student_name)) . ".zip";
    $con = connect();
    if ($type == 2) {
        $sql = "SELECT * FROM progress WHERE student_id = '$id'";
    } else {
        $sql = "SELECT * FROM progress WHERE student_id = '$id' AND status = '$type'";
    }
    $query = $con->query($sql);
    if ($query->num_rows < 1) {
        return script("No uploaded files by this student", 1);
    }
    $zip = new ZipArchive();
    $zip->open($file_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $path = 'uploads/';
    $sn = 0;
    $stamp = getStamp();
    $info = "Hi, howdy! " . PHP_EOL . "$fullname summary of project as at $stamp" . PHP_EOL;
    $help = $path . "Read me for " . preg_replace('/[^a-z0-9]+/', '-', strtolower($fullname)) . ".txt";
    $readme = fopen($help, 'x+'); // Adding a readme
    chmod($help, 0777);
    while ($row = $query->fetch_assoc()) {
        $file = $row['link'];
        $sn++;
        $chapter = $row['chapter'];
        if ($chapter == 0) {
            $chapter = "Proposal";
        } elseif ($chapter == 6) {
            $chapter = "Project Clearance";
        } else {
            $chapter = "Chapter $chapter";
        }
        $status = $row['status'];
        $date = $row['date_accepted'];
        if ($status == 0) {
            $status = 'not attended to by supervisor';
            $date = '';
        } elseif ($status == 1) {
            $status = 'approved by supervisor ';
            $date = " on $date";
        } else {
            $status = 'not approved by supervisor on $date';
        }
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (is_file($path . $file)) {
            $old_name = $path . $file;
            $new_name = $chapter . "." . $ext;
            $zip->addFile($old_name, $new_name);
            $info .= "$chapter ($new_name) status was $status $date " . PHP_EOL;
        }
    }
    // ob_start();
    fwrite($readme, $info);
    fclose($readme);
    $zip->addFile($help, "Readme.txt");
    $zip->close();
    //Let us use ob_start();
    // ob_start();
    if (!headers_sent()) {
        foreach (headers_list() as $header) {
            // header_remove($header);
        }
    }
    @unlink($help);

    //Send zip folder
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Content-Type: application/zip");
    header('Content-Disposition: attachment; filename=' . basename($file_name));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_name));
    // ob_end_flush();
    // flush();
    readfile($file_name);
    // header('Content-Type: application/zip');
    // header('Content-disposition: attachment; filename=' . $zipname . '.zip');
    // readfile($zip);
    @unlink($file_name);

    // exit;
}

function editAdmin($username, $password, $first_name, $last_name, $email, $phone, $old_password)
{
    $con = connect();

    if (strlen($username) < 5 || strlen($first_name) < 5 || strlen($last_name) < 5 || strlen($email) < 7 || strlen($phone) != 11 || strlen($old_password) < 5 || (strlen($password) > 1 && strlen($password) < 5)) {
        return -1;
    }
    $row = ($con->query("SELECT * FROM users"))->fetch_assoc();
    $db_password = $row['password'];
    if ($db_password != md5($old_password)) {
        return -2;
    }
    if (strlen($password) > 4) {
        $password = md5($password);
    } else {
        $password = $db_password;
    }

    $update = "UPDATE users SET username = ?, password = ?, firstname = ?, lastname = ?, email = ?, phone = ?";
    $query = $con->prepare($update);
    $query->bind_param("ssssss", $username, $password, $first_name, $last_name, $email, $phone);
    if ($query->execute()) {
        return 1;
    }

    return -5;
}

function checkMaintenance()
{
    $maintenance = getSettings("maintenance");
    if ($maintenance == 1) {

        session_destroy();
        echo
        '<!DOCTYPE html>
<html lang="en">
<head>
<title>Maintenance Mode Activated</title>
<style>
	body{
	background: rgb(30,30,30);
	color:gold;
	}
</style>
</head>
<body>
<h1 align="center">We are currently undergoing maintenance</h1>
<h1 align="center"><font size="+5"> &#128679; </font></h1>
<p align="center">We will be back shortly</p>
<h1 align="center">&#128281; &#128284;</h1>
</body>
</html>
';
        exit;
    }
}

function getPasswordById($id)
{
    $row = (connect()->query("SELECT password FROM students WHERE id = '$id'"))->fetch_assoc();
    $password = $row['password'];
    $password = substr($password, 0, 5);
    return $password;
}
function printClearance($reg)
{
    if (!headers_sent()) {
        foreach (headers_list() as $header) {
            // header_remove($header);
        }
    }
    $con = connect();
    $id = getIdFromSession($reg);
    $getCount = (connect()->query("SELECT id FROM progress WHERE student_id = '$id' AND status = 1 "))->num_rows;
    $student_name = substr($fullname = getStudentDetailsById($id), 0, 15);
    $row = (connect()->query("SELECT location, password, lastname, firstname, cpu_id FROM students WHERE id = '$id'"))->fetch_assoc();
    $name = strtoupper($row['lastname']) . ", " . ucwords(strtolower($row['firstname']));
    $cpu = $row['cpu_id'];
    $password = getPasswordById($id);
    $passport = "uploads/" . $row['location'];
    if (!file_exists($passport)) {
        $passport = "images/group.jpg";
    }
    $email = getStudentEmailById($id);
    $timeframe = '<tr><th style="text-align:center"><b>Project Phase</b></th><th style="text-align:center"><b>Date Accepted</b></th></tr>';
    for ($i = 0; $i < 7; $i++) {
        $timeframe .= "<tr><td>";
        if ($i == 0) {
            $timeframe .= "Project Proposal";
        } elseif ($i < 6) {
            $timeframe .= "Chapter " . ($i);
        } else {
            $timeframe .= "Project Clearance";
        }
        $timeframe .= "</td><td>";

        $row = checkProgressByStudentId($id, $i);

        if ($row[0] == 1) {
            $timeframe .= $row['1'];
        } else {
            $timeframe .= "Something about this student is not right";
        }
        $timeframe .= "</td></tr>";
    }

    $phone = getStudentPhoneById($id);
    $supervisor = getSupervisorNameByCpuId($cpu);
    $supervisor = substr($supervisor, 0, -8);
    $barcode = "$fullname has completed project under the supervision of $supervisor.";
    $barcodeOutput = generateQR($id, $barcode);

    $field = getFieldNameFromCpuID($cpu);
    if ($getCount != 7) {
        logDown($id, "Student $fullname tried to print clearance when he/she is yet to complete project", 3, 1);
        return script("You are yet to complete your project");
        exit; //Being careful

    }

    $file_name = preg_replace('/[^a-z0-9]+/', '-', strtolower($student_name)) . ".pdf";
    require_once 'PDF/tcpdf_config_alt.php';

    // Include the main TCPDF library (search the library on the following directories).
    $tcpdf_include_dirs = array(
        realpath('PDF/tcpdf.php'),
        '/usr/share/php/tcpdf/tcpdf.php',
        '/usr/share/tcpdf/tcpdf.php',
        '/usr/share/php-tcpdf/tcpdf.php',
        '/var/www/tcpdf/tcpdf.php',
        '/var/www/html/tcpdf/tcpdf.php',
        '/usr/local/apache2/htdocs/tcpdf/tcpdf.php',
    );
    foreach ($tcpdf_include_dirs as $tcpdf_include_path) {
        if (@file_exists($tcpdf_include_path)) {
            require_once $tcpdf_include_path;
            break;
        }
    }

    class MYPDF extends TCPDF
    {
        //Page header
        public function Header()
        {
            // get the current page break margin
            $bMargin = $this->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $this->AutoPageBreak;
            // disable auto-page-break
            $this->SetAutoPageBreak(false, 0);
            // set bacground image
            $img_file = K_PATH_IMAGES . "watermark.jpg";
            // die($img_file);
            $this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
            $this->SetAlpha(0.5);

            // restore auto-page-break status
            $this->SetAutoPageBreak($auto_page_break, $bMargin);
            // set the starting point for the page content
            $this->setPageMark();
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($fullname);
    $pdf->SetProtection(array('print', 'modify', 'copy', 'extract', 'assemble'), $password, null, 0, null);
    $pdf->SetTitle($fullname . " Project Clearance");
    $pdf->SetSubject('Student Project Clearance');
    $pdf->SetKeywords("UNILORIN, University, University of Ilorin, Ilorin, Project, Clearance, System, Clearance System, SSIS, Supervision, Supervision System, Interaction System, Interaction, Computer Science, Computer, Software,  $fullname");

    // set default header data
    // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
    // $pdf->setFooterData(array(0,64,0), array(0,64,128));

    // set header and footer fonts
    // $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    // $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, 7, PDF_MARGIN_RIGHT);
    // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    // $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->SetAutoPageBreak(true, 5);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once dirname(__FILE__) . '/lang/eng.php';
        $pdf->setLanguageArray($l);
    }

    // ---------------------------------------------------------

    // set default font subsetting mode
    $pdf->setFontSubsetting(true);

    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('dejavusans', '', 14, '', true);

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();
    $src = $barcodeOutput;
    // set text shadow effect
    $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));
    // Set some content to print
    $html = <<<EOD
<style>
table th{font-weight:bold}
</style>
<h5 style="text-align:center"><img src="images/logo.png" width="50" height="50"/><br/>UNIVERSITY OF ILORIN, ILORIN<br/>FACULTY OF COMMUNICATION &amp; INFORMATION SCIENCES <br/>DEPARTMENT OF COMPUTER SCIENCE </h5><p align="center">STUDENT PROJECT CLEARANCE : &nbsp;ACADEMIC SESSION: 20____/20____</p>
<table width="100%" border="1">
<tr><th colspan="2" style="text-align:center"><b>Personal Data</b></th></tr>
<tr><th>Name</th><td>$name</td></tr>
<tr><th>Matric</th><td>$reg</td></tr>
<tr><th>Email</th><td>$email</td></tr>
<tr><th>Tel</th><td>$phone</td></tr>
<tr><th colspan="2"  style="text-align:center"><b>Project Data</b></th></tr>
<tr><th>Supervisor</th><td>$supervisor</td></tr>
<tr><th>Field of interest</th><td>$field</td></tr>
<tr><th colspan="2"  style="text-align:center"><b>Timeframe</b></th></tr>
$timeframe
</table>
<table>
<tr><td colspan="2" style="text-align:center"><br/><br/>I, <b>$fullname</b>, hereby declare that the work I submitted for assessment contained no section copied in whole or in part from any other source unless explicitly identified in quotation marks and with detailed, complete and accurate referencing. <br/><br/>____________________________________________<br/>Student's Signature</td></tr>
</table>
EOD;

    // @unlink($barcodeOutput);
    $html .= <<<EOD
<br/>
<table width="100%" style="text-align:center;">
<tr><td></td><td></td></tr>
<tr><td>________________________________</td><td>________________________________</td></tr>
<tr><td>Supervisor's Signature</td><td>Project Coordinator</td></tr>
<tr><td></td><td></td></tr>

<tr><th style="text-align:center">
<img weight="150" height="150" src="$src">
</th><th style="text-align:center">
<img weight="150" height="150" src="$passport">
</th></tr>
</table>
<font size="-3"><i>Not valid unless signed.</i></font>
EOD;
    // die($html);
    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    // ---------------------------------------------------------

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
    $pdf->Output($file_name, 'D');
}

function generateQR($id, $data)
{
    $imgname = intval($id) . ".png";
    // === Create qrcode image
    include 'phpqrcode/qrlib.php';
    QRcode::png($data, $imgname, QR_ECLEVEL_L, 11.45, false);

    // === Adding image to qrcode
    $QR = imagecreatefrompng($imgname);

    imagefilter($QR, IMG_FILTER_COLORIZE, 41, 255, 111); //     rgb(197, 167, 95) || rgb(27, 78, 25) || rgb(41, 22, 111) || rgb(15, 81, 22)
    imagealphablending($QR, false);

    // === Change image color
    $im = imagecreatefrompng($imgname);
    //This changes the color
    $r = 41;
    $g = 22;
    $b = 111;
    for ($x = 0; $x < imagesx($im); ++$x) {
        for ($y = 0; $y < imagesy($im); ++$y) {
            //imagefilter($im, IMG_FILTER_COLORIZE, 0, 255, 0); //This changes the color
            $index = imagecolorat($im, $x, $y);
            $c = imagecolorsforindex($im, $index);
            if (($c['red'] < 100) && ($c['green'] < 100) && ($c['blue'] < 100)) { // dark colors
                // here we use the new color, but the original alpha channel
                $colorB = imagecolorallocatealpha($im, 0x12, 0x2E, 0x31, $c['alpha']);
                imagesetpixel($im, $x, $y, $colorB);
            }
        }
    }

    imagepng($im, $imgname);
    imagedestroy($im);

    // === Convert Image to base64
    $type = pathinfo($imgname, PATHINFO_EXTENSION);
    $data = file_get_contents($imgname);
    $imgbase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    chmod($imgname, 0777);

    // === Show image
    // $html = <<<EOD
    // <img src="$imgbase64" style="position:relative;display:block;width:200px;height:200px;margin:auto;">
    // EOD;

    // return array($imgname,$imgbase64);
    return $imgname;
}

// $con = connect();
// $query = $con->query("SELECT * FROM students WHERE id != 107");
// while ($row = $query->fetch_assoc()) {
//     $pass = salt($row['regno']);
//     $up = $con->query("UPDATE students SET password = '$pass' WHERE id = $row[id]");
// }