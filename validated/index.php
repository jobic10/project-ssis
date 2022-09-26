<?php
//We check to see if the database connection file is present, if it is go back to root
if (file_exists("special_db.php")) {
    header("location: ../");
    exit;
}

//Check if we can access this 
if (!isset($access)) die("Sorry, you are not allowed to view this!");

$title = "Home";
// error_reporting(0);
if (!isset($_SESSION)) session_start(); //If session isn't started, start it
//We need to find a way to fend off automated requests, so we use captcha
$var1 = rand(1, 5);
$var2 = rand(1, 5);

$_SESSION['real_captcha'] = $var1 + $var2; //This session stores the answer of the captcha
$_SESSION['captcha'] = "$var1 + $var2"; //This session displayes the question on the form

$sys = "Project Coordinator "; //Wasn't sure the arrangement, so I used a variable to store it. It makes it easier
$d_name = $d_name_abb = $d_pass = $d_number = $d_email = ""; //Quick tips for non-expert installer

if (isset($_GET['help'])) {
    $d_name = "onClick='alert(\"You are expected to enter the full name of your $sys (Do not abbreviate or use Acronyms)\")'";
    $d_email = "onClick='alert(\"Enter the email address associated with your $sys\")'";
    $d_number = "onClick='alert(\"Enter the mobile phone number associated with your $sys\")'";
    $d_pass = "onClick='alert(\"Just want to be sure that you have access to this\")'";
    $d_name_abb = "onClick='alert(\"Enter your $sys acronym/abbreviation e.g. \")'";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Installation In Progress</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="validated/css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="validated/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="validated/css/style.css">
</head>

<body>
    <noscript>
        <h1>We need you to allow JavaScript in your web browser for full functionality</h1>
        <style>
        .container {
            display: none;
        }

        h1 {
            text-align: center;
        }
        </style>

    </noscript>
    <div class="bg-top navbar-light text-center font-weight-bolder">

        University of Ilorin<br />
        <img class="img-fluid" width="50" height="50" src="images/logo.png" alt="">
    </div>


    <section class="ftco-section ftco-no-pt ftc-no-pb">
        <div class="container">
            <h4 class="text-info" align="center">Student-Supervisor Interaction System (SSIS) Installation <a
                    href="javascript:void(0);">&#128421;</a>
                <?php if (!isset($_GET['help'])) echo '<span class="float-right"> <a title="Installation with help" onClick="return confirm(\'You will be redirected to the installation system with help tips\')" href="' . $_SERVER["PHP_SELF"] . '?help" >&#10067;</a>  </span>';
                else  echo '<span class="float-right"> <a <a onClick="return confirm(\'You will be redirected to the installation system as an expert\')" href="' . $_SERVER["PHP_SELF"] . '" >&#127776;</a>  </span>'; ?>
            </h4>
            <p id='response'></p>
            <table class="table table-bordered" id='installer'>
                <tr>
                    <td><label for="mail"><?php echo $sys; ?> Mail:</label></td>
                    <td><input class="w-100" type="email" <?php echo $d_email ?> required name="mail" id="mail"></td>
                </tr>
                <tr>
                    <td><label for="number"><?php echo $sys ?> Phone Number:</label></td>
                    <td><input class="w-100" required type="number" <?php echo $d_number; ?> name="number" id="number">
                    </td>
                </tr>
                <tr>
                    <td><label for="captcha">What will be the outcome of <span
                                class="text-info font-weight-bolder alert-info"><?php echo $_SESSION['captcha']; ?></span>
                            ?</label></td>
                    <td><input class="w-100" required type="number" min="2" max="10"
                            placeholder="<?php echo $_SESSION['captcha'];; ?>" name="captcha" id="captcha"></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center">
                        <hr />
                        <?php if (isset($_GET['help'])) echo "<div class='alert alert-info'>Database Credentials. <hr/>These credentials are given to you by the developer. If you are not given, you will not be able to proceed with this installation. </div>";
                        else echo "Database Credentials " ?>
                    </td>

                </tr>
                <tr>
                    <td><label for="host">Server Host:</label></td>
                    <td><input class="w-100" required type="text" placeholder="e.g. localhost" name="host" id="host">
                    </td>
                </tr>
                <tr>
                    <td><label for="db_name">Database Name:</label></td>
                    <td><input class="w-100" required type="text" placeholder="Database name" name="db_name"
                            id="db_name"></td>
                </tr>
                <tr>
                    <td><label for="username">Database Username:</label></td>
                    <td><input class="w-100" required type="text" placeholder="Database username" name="username"
                            id="username"></td>
                </tr>
                <tr>
                    <td><label for="passcode">Database Password:</label></td>
                    <td><input class="w-100" required type="password" placeholder="Database password" name="passcode"
                            id="passcode"></td>
                </tr>
                <tr>
                    <td colspan="2"><a href="#"><button class="w-100 btn btn-info font-weight-bolder"
                                value="Begin System Installation" type="submit" onclick="install()" name="name"
                                id="submit"> Begin System Installation</button></a></td>
                </tr>

            </table>

        </div>
    </section>




    <footer class="footer">
        <div class="col-md-12 text-center">

            <hr />
            <p class="text-info">
                Copyright &copy; <?php echo date('Y'); ?> All rights reserved | University of Ilorin. Developed by
                Owonubi Job Sunday

            </p>

        </div>
    </footer>

    <script type="text/javascript">
    function install() {
        let table = document.getElementById('installer').style;

        var xml = new XMLHttpRequest();
        let mail = document.getElementById('mail').value;
        let number = document.getElementById('number').value;
        let passcode = document.getElementById('passcode').value;
        let username = document.getElementById('username').value;

        let captcha = document.getElementById('captcha').value;
        let db_name = document.getElementById('db_name').value;
        let host = document.getElementById('host').value;

        let response = document.getElementById('response');
        response.innerHTML = (
            "<div class='alert alert-info' align='center'><h4><img src='validated/ajax-loader.gif'><hr/>Please wait...</h4></div>"
        );
        table.display = "none";
        xml.onreadystatechange = function() {
            if (xml.readyState == 4 && xml.status == 200) {
                if (xml.responseText.length < 700) {
                    table.display = "";
                    response.innerHTML = xml.responseText;

                } else {
                    table.display = "none";
                    response.innerHTML = xml.responseText;
                }


            } else {
                // table.visibility = "";
                // response.innerHTML = "<h4>Processing...</h4>";

            }

        }
        xml.open("POST", "validated/install.php", true);
        xml.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xml.send("mail=" + mail + "&host=" + host + "&number=" + number + "&db_user=" + username + "&db_pass=" +
            passcode + "&db_name=" + db_name + "&captcha=" + captcha);
        //  alert("mail="+mail+"&host="+host+"&name="+name+"&number="+number+"&db_user="+username+"&db_pass="+passcode+"&db_name="+db_name+"&acc_name="+acc_name+"&captcha="+captcha);
    }
    </script>
</body>

</html>