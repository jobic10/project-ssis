<?php include('header_dashboard.php'); ?>
<?php include('session.php'); ?>
    <body>
		<?php include('navbar_student.php'); ?>
        <div class="container-fluid">
            <div class="row-fluid">
				<?php include('student_sidebar.php'); ?>
                <div class="span9" id="content">
                     <div class="row-fluid">
					    <!-- breadcrumb -->
				
									
					     <ul class="breadcrumb">
						
							<li><a href="#"><b>My Class</b></a><span class="divider">/</span></li>
							<li><a href="#">School Year: <?php echo date('Y'); ?></a></li>
						</ul>
						 <!-- end breadcrumb -->
					 
				
					 
                        <!-- block -->
                        <div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
                                <div id="" class="muted pull-left">Student Notification</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
							
						
							</div>
	
				
					<?php $query = getAnnouncements(); 
					$count = ($query->num_rows);
					if ($count  > 0){
					while($row = ($query->fetch_assoc())){
						$msg = $row['msg'];
						$loc = $row['loc'];
						$date = $row['date'];
						$cpu = $row['cpu'];
						if ($loc == 0){
							$link = "";
						}else{
							$link = "<a href='uploads/$loc'><button class='btn btn-info'>Download Attachment</button></a>";
							
						}
					?>	<div class="post" >
					<div class="message_content">
					<?php echo $row['msg']."<hr/>".$link; ?>
					</div>
					
							<hr>
					Sent by: <strong><?php echo getSupervisorNameByCpuId($cpu); ?></strong>
					<i class="icon-calendar"></i> <?php echo $date; ?>
						
					</div>
					<?php
					} }else{
					?>
					<div class="alert alert-info"><strong><i class="icon-info-sign"></i> No Announcements Found</strong></div>
					<?php
					}
					?>
					
						
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