<?php
if (!isset($file_access))
die("Direct Access To File Not Allowed");
?>
		<!-- Edit Group Modal -->
<div id="edit<?php echo $id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h3 id="myModalLabel">Editing <?php echo $row['name']; ?></h3>
	</div>
		<div class="modal-body">
	<form method='POST'>

		<div>
		<p>Enter Number of Students You'd Like To Supervise</p>
	<input type="number" min="0" max="100" minlength="0" maxlength="3" required value="<?php echo $row['no'] ?>" name="no" class="input-block-level">
	<input type="hidden"  name="id" value ="<?php echo $id; ?>">
	<button  class="btn btn-success" type="submit" name="editNo"><i class="icon-check icon-large"></i> Save Changes</button>
		</div>
	</form>
		</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-large"></i> Close</button>
		
	</div>
</div>
				