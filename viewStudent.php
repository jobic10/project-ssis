<?php
if (!isset($file_access))
die("Direct Access To File Not Allowed");
?>
		<!-- user delete modal -->
					<div id="view<?php echo $id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
					<h3 id="myModalLabel">

					<?php 
				echo getStudentDetailsById($id);?>
					</h3>
					</div>
					<div class="modal-body">
					<div class="">
					<table class="table table-hover">
						<tr>
							<th>SN</th>
							<th>Project</th>
							<th>Approved</th>
						</tr>
						<?php  
											  $sn = 1;
											  for ($i = 0; $i < 6; $i++){
									echo "<tr><td>".($sn++)."</td><td>";
									if ($i == 0) echo "Project Proposal";else echo "Chapter ".($i);
												echo "</td><td>";
												  $row = checkProgressByStudentId($id,$i);
												  if ($row[0] == 0){
										 
										 echo '<span class="badge badge-danger">Not cleared yet</span>';
												  }else{
													echo '<span class="badge badge-success">Cleared On '.$row[1]['date_accepted'].'</span>';
												  }
												  echo '</td></tr>';
												  ?>
										
											<?php
											  }?>
										
					</table>
					<p>
						ss
					</p>
					</div>
					</div>
					<div class="modal-footer">
					<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-large"></i> Close</button>
					<button name="backup_delete" class="btn btn-danger"><i class="icon-check icon-large"></i> Yes</button>
					</div>
					</div>