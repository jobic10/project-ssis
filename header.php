<?php require 'functions.php';
$file_access = 1;
checkMaintenance();
?>
<!DOCTYPE html>
<html class="no-js">

<head>
    <title><?php echo PROJECT_TITLE; ?></title>
    <meta name="description" content="Student Project Allocation ">
    <meta name="keywords"
        content="UNILORIN, CIS, UNIVERSITY, ILORIN, PROJECT, STUDEMT, ALLOCATION, SUPERVISOR, SUPERISION, INTERACTION, MESSAGE, CHAT">
    <meta name="author" content="OWONUBI JOB SUNDAY">
    <meta charset="UTF-8">

    <!-- Bootstrap -->
    <link href="admin/images/favicon.ico" rel="icon" type="image">
    <link href="admin/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
    <link href="admin/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen" />
    <link href="admin/bootstrap/css/font-awesome.css" rel="stylesheet" media="screen" />
    <link href="admin/bootstrap/css/my_style.css" rel="stylesheet" media="screen" />
    <link href="admin/vendors/easypiechart/jquery.easy-pie-chart.css" rel="stylesheet" media="screen" />
    <link href="admin/assets/styles.css" rel="stylesheet" media="screen" />
    <!-- calendar css -->
    <link href="admin/vendors/fullcalendar/fullcalendar.css" rel="stylesheet" media="screen">
    <!-- index css -->
    <link href="admin/bootstrap/css/index.css" rel="stylesheet" media="screen" />
    <!-- data table css -->
    <link href="admin/assets/DT_bootstrap.css" rel="stylesheet" media="screen" />
    <!-- notification  -->
    <link href="admin/vendors/jGrowl/jquery.jgrowl.css" rel="stylesheet" media="screen" />
    <link rel="shortcut icon" href="images/logo.png">
    <style>
    body {
        background: url('admin/images/<?php echo mt_rand(1, 16); ?>.jpg') repeat center center fixed;
        -webkit-background-size: cover !important;
        -moz-background-size: cover !important;
        -o-background-size: cover !important;
        background-size: cover !important;
    }
    </style>
    <!-- wysiwug  -->
    <link rel="stylesheet" type="text/css" href="admin/vendors/bootstrap-wysihtml5/src/bootstrap-wysihtml5.css" />
    <script src="admin/vendors/jquery-1.9.1.min.js"></script>
    <script src="admin/vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>