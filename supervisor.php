<?php include('header_dashboard.php'); ?>
<?php include('supervisor_session.php'); ?>

    <body id="class_div">
		<?php include('navbar_supervisor.php'); ?>
        <div class="container-fluid">
            <div class="row-fluid">
				<?php include('supervisor_sidebar.php'); 
				
				if (isset($_POST['editNo'], $_POST['id'], $_POST['no'])){
					$no = @$_POST['no'];
					$cpu_id = @$_POST['id'];
					$i = getIdFromSession($_SESSION['id'],'supervisor');
					echo editCpuCount($cpu_id,$no);
					}

					if (isset($_GET['delID'])){
						echo delCpuOnStaff($_GET['delID']);
					}
				?>
                <div class="span9" id="content">
                     <div class="row-fluid">
					    <!-- breadcrumb -->	
					     <ul class="breadcrumb">
								
								<li><a href="#"><b>My Class</b></a><span class="divider">/</span></li>
								<li><a href="#">School Year: <?php echo date('Y'); ?></a></li>
						</ul>
						 <!-- end breadcrumb -->
                        <!-- block -->
                        <div class="block">
								<div class="navbar navbar-inner block-header">
									<div id="count_class" class="muted pull-right"></div>
								</div>
                            <div class="block-content collapse in">
                                <div class="span12">
										<?php include('supervisor_class.php'); ?>
                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
							
                </div>
				<?php include('supervisor_right_sidebar.php') ?>
            </div>
		<?php include('footer.php'); ?>
        </div>
		<?php include('script.php'); ?>
    </body>
</html>