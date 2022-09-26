<?php include 'header_dashboard.php';?>
<?php include 'session.php';?>
<body>
		<?php include 'navbar_student.php';?>
        <div class="container-fluid">
            <div class="row-fluid">
				<?php include 'student_sidebar.php';?>
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
                                <div id="" class="muted pull-left">Student Message</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">

										<ul class="nav nav-pills">
										<li class="<?php if (!isset($_GET['group'])) {
    echo 'active';
}
?>"><a href="student_message.php"><i class="icon-envelope-alt"></i>Private Messages</a></li>	<li class="<?php if (isset($_GET['group'])) {
    echo 'active';
}
?>"><a href="student_message.php?group"><i class="icon-envelope-alt"></i>Group Messages</a></li>
										<li class=""><a href="sent_message_student.php"><i class="icon-envelope-alt"></i>Sent messages</a></li>
										</ul>

									<?php
$id = getIdFromSession($_SESSION['id'], 'student');
if (isset($_GET['group'])) {
    $group = 'yes';
} else {
    $group = 'no';
}
$queryInbox = getInboxMessages($id, $group, 'students');
$count_my_message = ($queryInbox[0]->num_rows);
if ($count_my_message != '0') {
    while ($row = ($queryInbox[0])->fetch_assoc()) {
        // $sender_name = getStudentDetailsById($row['student_id']);
        $sender_name = "Supervisor";
        ?>
											<div class="post"  id="del<?php echo $id; ?>">
											<div class="message_content">
											<?php echo $row['msg']; ?>
											</div>

													<hr>
											Sent by: <strong><span class="text-info"><?php echo $sender_name ?></span></strong>
											<i class="icon-calendar"></i> <?php echo $row['entry_date']; ?>
													<?php /*    <div class="pull-right">
        <a class="btn btn-link"  href="#reply<?php echo $id; ?>" data-toggle="modal" ><i class="icon-reply"></i> Reply </a>
        </div>
        <div class="pull-right">
        <a class="btn btn-link"  href="#<?php echo $id; ?>" data-toggle="modal" ><i class="icon-remove"></i> Remove </a>
        </div>*/?>
											</div>

								<?php }} else {?>
								<div class="alert alert-info"><i class="icon-info-sign"></i> No <?php echo $queryInbox[1]; ?> Message</div>
								<?php }?>
                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>

<!-- <script type="text/javascript">
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
</script> -->


                </div>
				<?php include 'create_message_student.php'?>
            </div>
		<?php include 'footer.php';?>
        </div>
		<?php include 'script.php';?>
    </body>
</html>