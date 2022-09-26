<?php
date_default_timezone_set("Africa/Lagos");
$file_size = 300;
$img_size = 500;
// error_reporting(0);
if (!isset($_SESSION)) session_start(); //If session isn't set, start it

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // if (!function_exists('salt')) require '';
    sleep(mt_rand(0, 2)); //! Remove this in production.
    //? I have a variable that stores the format I will be using to display error and success message
    $error = "<div class='alert alert-danger' align='center'><h4>";
    $success = "<div class='alert alert-success' align='center'><h4>";
    $end = "</h4></div>";

    //special_db.php is a file that gets created automatically while installing
    //If the file exists, that means installation is completed already
    if (file_exists("../includes/special_db.php")) {
        echo "$success Installation already completed. $end";
        exit;
    }
    //First, let's accept form data
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $db_name = strtolower($_POST['db_name']);
    $number = $_POST['number'];
    $mail = strtolower($_POST['mail']);
    $captcha = $_POST['captcha'];
    $host = $_POST['host'];
    $captcha_answer =  $_SESSION['real_captcha'];
    //End of accepting data

    /*Start of checking for empty values
    * Let us use PHP function strlen and isset function*/
    $values = array_values($_POST);
    foreach ($values as $val) {
        if (strlen($val) < 1) {
            echo "$error All fields must be properly entered. &#10060; $end";
            exit;
            break;
        }
    }
    //    echo "<pre>";
    //     print_r($values);
    //     echo "</pre>";

    //Checking for captcha valid answer
    if ($captcha_answer != $captcha) {
        echo "$error &#128483; Invalid Answer Provided $end";
        exit;
    }

    //Validating Email
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        echo "$error &#128231; Invalid Email Provided $end";
        exit;
    }

    //Validating phone number
    $first_val = substr($number, 0, 1);
    if ($first_val != 0) {
        echo "$error &#128222; Phone number should start with 0 $end";
        exit;
    } elseif (strlen($number) != 11) {
        echo "$error &#128222; Phone number should be of length 11 $end";
        exit;
    }

    //Using the database parameters provided by the user to make a connection
    try {
        error_reporting(0);
        $connect = new mysqli($host, $db_user, $db_pass);
        if (mysqli_connect_errno()) throw new Exception("$error Invalid Database Configurations &#128581; $end");
        //If connection is not successful, throw an error
        if (!$connect) {
            error_reporting(0);
            die("$error Invalid Database Configuration $end");
        }

        //Automatically generate database and its corresponding tables
        $sql_drop = "DROP DATABASE IF EXISTS `$db_name`";
        $execute_drop_db = $connect->query($sql_drop);

        //Now, lets see if the database name the user entered doesn't exist
        $query_db = $connect->query("SHOW DATABASES LIKE '$db_name' ");
        $check_db = $query_db->num_rows; //Number of rows returned from the checking of database

        if ($check_db > 0) {
            die("Kindly use another database name");
        }

        //Generating random username and password
        $username_choice = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
        $username = $username_choice[mt_rand(0, 6)];
        $password = $username_choice[mt_rand(0, 6)];
        $password_hash = md5($password); //Password Salted (With sub-domain) and Hashed
        $admin = "INSERT INTO `users` (`user_id`, `username`, `password`, `firstname`, `lastname`, `email`, `phone`)
    VALUES    ('1','$username','$password_hash','Administrator','System','$mail','$number'); INSERT INTO `settings` (`id`, `category`, `value`) VALUES (1, 'student_login', 0),(2, 'supervisor_login', 0),(3, 'supervisor_reg', 0),(4, 'student_reg', 0),(5, 'maintenance', 1);";

        $sql_create = "CREATE DATABASE IF NOT EXISTS `$db_name`; ";
        $execute_create_db = $connect->query($sql_create);

        $connect->select_db($db_name);
        $sql = file_get_contents('db/ssis_db_f1l3.sql');
        $value = $connect->multi_query($sql . $admin) or die("Time out. Kindly restart installation");
        // $connect->query($admin) or die($admin);

        if ($value) {
            $file_to_be_created = "../includes/special_db.php";
            $file = fopen($file_to_be_created, "w+"); //Create special_db.php
            chmod($file_to_be_created, 0777);  //Permission setting
            $stamp = date('l, d-F-Y h:i A'); //The date and time
            $codes_to_write =
                "<?php
    error_reporting(0);
    //File created automatically on $stamp;
    date_default_timezone_set('Africa/Lagos');
    function connect(){
    @session_start();
    //\$con = mysqli_connect('$host','$db_user','$db_pass','$db_name');
    \$con =new mysqli('$host','$db_user','$db_pass','$db_name');
    if (\$con->connect_errno || mysqli_connect_errno())die('Unknown Error Occured...');
    return \$con;
    }
    ?>";

fwrite($file, $codes_to_write); // Write the codes to special_db.php file
fclose($file); //Close the file
if (!$file) { //If the file is not created: Something is wrong
throw new Exception("$error File not created..
Consider giving file permission access to write/read files on your server. $$file_to_be_created
<br /> <a href='#'
    onclick='alert(\"We trust that you will need to upload files (Most especially images), relay this to the developer or your host so as not to cause problem after installation.\")'><button
        class='btn btn-info'>Why do I need to do this ?</button></a> $end");
}

//Installation Completed
$suffix = "admin/index.php?admin=ssis";
$adminUrl = $_SERVER['HTTP_HOST'] . "/" . $suffix;
echo "
$success Installation was successful. &#9786;<br />
You'd need to take note of your username and password down as the system will prompt you to enter the login parameters.
<hr />Please Note Down The Following:
<table class='table table-responsive table-bordered'>
    <tr>
        <th>Username</th>
        <td>$username</td>
    </tr>
    <tr>
        <th>Password</th>
        <td>$password</td>
    </tr>
    <tr>
        <th>Admin url</th>
        <td>$adminUrl</td>
    </tr>
    <tr>
        <td colspan='2'>The system is under maintenance (no one is able to perform operations except you)</td>
    </tr>
    <tr>
        <td colspan='2'>Relay any question to the developer @ 08100134741 or a designated staff.</td>
    </tr>
</table>
<hr />
<a href='$suffix'><button class='btn btn-primary'>Proceed to login </button></a>
$end ";;
}
} catch (Exception $e) {
exit($e->getMessage());
}
}