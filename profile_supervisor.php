<?php include('header_dashboard.php'); ?>
<?php include('supervisor_session.php'); ?>
    <body>
		<?php include('navbar_supervisor.php'); ?>
        <div class="container-fluid">
            <div class="row-fluid">
				<?php include('supervisor_sidebar.php'); ?>
                <div class="span6" id="content">
                     <div class="row-fluid">
					    <!-- breadcrumb -->
				
									
					     <ul class="breadcrumb">
						
							<li><a href="#">Supervisor</a><span class="divider">/</span></li>
							<li><a href="#"><b>Profile</b></a></li>
						</ul>
						 <!-- end breadcrumb -->
					 
                        <!-- block -->
                        <div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
                                <div id="" class="muted pull-left"></div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
										<div class="alert alert-info"><i class="icon-info-sign"></i> About Me</div>
							
                                        <h3>  <?php 
                                      $id = getIdFromSession($_SESSION['id'],'supervisor');
                                    echo getSupervisorNameById($id); ?>
                                        </h3>
                                    <p>

                                 
                                    </p>
						
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