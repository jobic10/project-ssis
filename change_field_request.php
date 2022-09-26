<?php include('header_dashboard.php'); ?>
<?php include('supervisor_session.php'); ?>
<body>
		<?php include('navbar_supervisor.php'); ?>
        <div class="container-fluid">
            <div class="row-fluid">
			<?php include('supervisor_sidebar.php'); ?>
			               <div class="span9" id="content">
                     <div class="row-fluid">
					    <!-- breadcrumb -->	
									<ul class="breadcrumb">
										
											<li><a href="#"><b>My Class</b></a><span class="divider">/</span></li>
										<li><a href="#">School Year: <?php echo date('Y'); ?></a><span class="divider">/</span></li>
										<li><a href="#"><b>Change Request</b></a></li>
									</ul>
						 <!-- end breadcrumb -->
                        <!-- block -->
                        <div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
                                <div id="" class="muted pull-right"></div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
								<?php
								$id = getIdFromSession($_SESSION['id'],'supervisor');

								if (isset($_GET['id'],$_GET['status']) && ($_GET['status'] == 2 || $_GET['status'] == -2)){
									$status = $_GET['status'];
                                    echo ChangeRequest($_GET['id'],$_GET['status'],'supervisor');
                                    // updateChangeOfField($id,$status,$_GET['id']);
								}
								echo getRequestForChangeOfFieldById($id); ?>
								
                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
                </div>
            </div>
		<?php include('footer.php'); ?>
        </div>
		<?php include('script.php'); ?>
    </body>
</html>
