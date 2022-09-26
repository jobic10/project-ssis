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
								<li><a href="#">Message</a><span class="divider">/</span></li>
								<li><a href="#"><b>Inbox</b></a><span class="divider">/</span></li>
								<li><a href="#">School Year: <?php echo date('Y'); ?></a></li>
						</ul>
						 <!-- end breadcrumb -->
					 
                        <!-- block -->
                        <div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
                                <div id="" class="muted pull-left"></div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
  								
										<ul class="nav nav-pills">
										<li class="active">
										<a href="supervisor_message.php"><i class="icon-envelope-alt"></i>Inbox</a>
										</li>
										<?php /*li class="">
										<a href="sent_message.php"><i class="icon-envelope-alt"></i>Group messages</a>*/?>
										</li>
										</ul>
										
									<?php
								 $id = getIdFromSession($_SESSION['id'],"supervisor");
								 $query= getSupervisorInboxMessage($id);
								$count_my_message = $query->num_rows;	
								if ($count_my_message != '0'){
								 while($row = ($query->fetch_assoc())){
								 $sender_name = $row['ln'].", ".$row['fn'];
								 ?>
											<div class="post"  id="del<?php echo $id; ?>">
										
											<div class="message_content">
											<?php echo $row['msg']; ?>
											</div>
											
													<hr>
											Sent by: <strong><?php echo $sender_name ?></strong>
											<i class="icon-calendar"></i> <?php echo $row['date']; ?>
												<?php if (strlen($row['attachment']) > 5){?>	<div class="pull-right">
														<a class="btn btn-link"  href="uploads/<?php echo $row['attachment']; ?>" ><i class="icon-download"></i> Download </a>
													</div>
												<?php } ?>
											</div>
											
								<?php }}else{ ?>
								<div class="alert alert-info"><i class="icon-info-sign"></i> No Inbox  Messages</div>
								<?php } ?>	
                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
<!-- 					
<script type="text/javascript">
	$(document).ready( function() {
		$('.remove').click( function() {
		var id = $(this).attr("id");
			$.ajax({
			type: "POST",
			url: "remove_inbox_message.php",
			data: ({id: id}),
			cache: false,
			success: function(html){
			$("#del"+id).fadeOut('slow', function(){ $(this).remove();}); 
			$('#'+id).modal('hide');
			$.jGrowl("Your Sent message is Successfully Deleted", { header: 'Data Delete' });	
			}
			}); 		
			return false;
		});				
	});
</script>
			<script>
			jQuery(document).ready(function(){
			jQuery("#reply").submit(function(e){
					e.preventDefault();
					var id = $('.reply').attr("id");
					var _this = $(e.target);
					var formData = jQuery(this).serialize();
					$.ajax({
						type: "POST",
						url: "reply.php",
						data: formData,
						success: function(html){
						$.jGrowl("Message Successfully Sent", { header: 'Message Sent' });
						$('#reply'+id).modal('hide');
						}
						
					});
					return false;
				});
			});
			</script> -->

                </div>
				<?php include('create_message.php') ?>
            </div>
		<?php include('footer.php'); ?>
        </div>
		<?php include('script.php'); ?>
    </body>
</html>