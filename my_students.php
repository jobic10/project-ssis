<?php
if (isset($_GET['zip'], $_GET['std']) && in_array($_GET['zip'], range(-1, 1))) {
    $file_access = 1;
    require_once 'functions.php';
    require_once 'supervisor_session.php';
    $mat = $_GET['std'];
    $type = $_GET['zip'];
    echo zipFilesByStudentId($mat, $type, 'supervisor');
}
if (!isset($_GET['id'])) {
    ?>
<script>window.location='view_by_field.php';</script>
    <?php
exit;
}
include 'header_dashboard.php';?>
<?php include 'supervisor_session.php';?>
<?php
if (!isset($_GET['id'])) {
    header("location: logout.php");
}
$get_id = $_GET['id'];

?>
    <body>
		<?php include 'navbar_supervisor.php';?>
        <div class="container-fluid">
            <div class="row-fluid">
				<?php include 'supervisor_sidebar.php';?>
                <div class="span9" id="content">
                     <div class="row-fluid">


                        <!-- block -->
                        <div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
                                <div id="" class="muted pull-right">
								<?php
$count_my_student = countById($get_id);
if ($count_my_student == -1) {
    echo "<h1 class='alert alert-danger'>ACCESS TO PROJECTS NOT ALLOCATED TO YOU DENIED</h1>";
    exit;
}
?>
								Number of Students: <span class="badge badge-info"><?php echo $count_my_student; ?></span> <a onclick="window.open('print_student.php<?php echo '?id=' . $get_id; ?>')"  class="btn btn-success"><i class="icon-list"></i> Student List</a>
								</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">

<?php
$my_student = getMyStudents($get_id);
echo $my_student;
?>

                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
                </div>
            </div>
		<?php include 'footer.php';?>
        </div>
		<?php include 'script.php';?>
    </body>
</html>