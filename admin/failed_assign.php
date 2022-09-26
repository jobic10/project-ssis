<?php include('header.php'); ?>
<?php include('session.php'); ?>

<body>
	<?php include('navbar.php'); ?>
	<div class="container-fluid">
		<div class="row-fluid">
			<?php include('sidebar_dashboard.php'); ?>
			<div class="span3" id="adduser">

			</div>
			<div class="span9" id="">
				<div class="row-fluid">
					<!-- block -->
					<?php
					if (isset($_GET['del'], $_GET['id'])) {
						$id  = base64_decode($_GET['id']);
						echo delAssign($id, 1);
					}

					if (isset($_POST['fetch'])) {
						$matric = $_POST['matric'];
						$row = fetchStudentByMat($matric);
						if ($row != 0) {
							?>
							<div id="block_bg" class="block">
								<div class="navbar navbar-inner block-header">
									<div class="muted pull-left"><?php echo $name = $row['ln'] . ", " . $row['fn']; ?></div>
								</div>
								<div class="block-content collapse in">
									<div class="span12">


										<table cellpadding="0" cellspacing="0" border="0" class="table" id="example">
											<thead>
												<tr>
													<td colspan="2">
														<?php if ($row['cpu_id'] > 0) echo '<a  href="' . $_SERVER['PHP_SELF'] . '?action=clear&regno=' . base64_encode($row['regno']) . '" id="clear" onClick="return confirm(\'Are you sure about this\')"  class="btn btn-danger" name=""><i class="icon-trash icon-large"></i> Clear Student Field Of Study</a>';
																else echo '<a   href="' . $_SERVER['PHP_SELF'] . '?action=assign&regno=' . base64_encode($row['regno']) . '" id="allocate" onClick="return confirm(\'Are you sure about this\')"  class="btn btn-success" name=""><i class="icon-signin icon-large"></i> Assign Student To Field</a>'; ?>
													</td>
												</tr>

												<tr>
													<th>Student name</th>
													<th>Field Of Interest</th>
												</tr>
											</thead>
											<tbody>


												<tr>
													<td>
														<?php echo $name;  ?>
													</td>
													<td><?php echo (getFieldNameFromCpuID($row['cpu_id'])); ?></td>



												</tr>


											</tbody>
										</table>
									</div>
								</div>
							</div>
							<?php } else {
									echo "<h1 class='text-error'>Invalid Matriculation Number</h1>";
								}
							} else
if (isset($_GET['action'], $_GET['regno']) && ($_GET['action'] == 'clear' || $_GET['action'] == 'assign')) {
								$matric = base64_decode($_GET['regno']);
								$row = fetchStudentByMat($matric);
								if ($row < 1) echo "<h1 class='text-error'>Denied</h1>";
								else {
									if ($_GET['action'] == 'clear') {
										$id = getIdFromSession($matric, "student");
										// echo $id;
										$result = resetStudentData($id);
										if ($result < 1) {
											echo '<script>alert("This student do not have any allocation \n Kindly assign field of interest.");window.location="' . $_SERVER['PHP_SELF'] . '";</script>';
										} else {
											echo '<script>alert("Done!");window.location="' . $_SERVER['PHP_SELF'] . '";</script>';
										}
									} else {
										//If Assign
										if (isset($_POST['field'], $_POST['supervisor'])) {
											$field = $_POST['field'];
											$supervisor = $_POST['supervisor'];
											$assign = assigner($matric, $field, $supervisor);
											if ($assign == -1) {
												echo "<h1 class='text-error'>Form Denied</h1>";
											} else {
												echo $assign;
											}
										}
										?>
								<div id="block_bg" class="block">
									<div class="navbar navbar-inner block-header">
										<div class="muted pull-left"><?php echo $name = $row['ln'] . ", " . $row['fn']; ?></div>
									</div>
									<div class="block-content collapse in">
										<div class="span12">
											<form action="<?php echo $_SERVER['PHP_SELF']; ?>?action=assign&regno=<?php echo base64_encode($matric) ?>" method="post">
												<table cellpadding="0" cellspacing="0" style="width:100%" border="0" class="table" id="example">
													<tr>
														<th style="width:30%">Student Matric</th>
														<td style="width:70%"><?php echo $matric ?></td>
													</tr>
													<tr>
														<td colspan="2"><?php $row =  getAllFields();
																					if ($row) {
																						?>
																<select required name="field" id="" style="width:100%">
																	<option value="">Choose Field</option>
																	<?php while ($result = $row->fetch_assoc()) {
																						$id = $result['id'];
																						$display = ucwords(strtolower($result['name']));
																						echo "<option value='" . $id . "'>" . $display . "</option>";
																					}
																					?>
																</select>

															<?php
																		} else {
																			echo "<span class='text-error'>No Supervisors Yet.. Try again later</span>";
																			exit;
																		}
																		?></td>
													</tr>
													<tr>
														<td colspan="2"><?php $row =  getAllSupervisor();
																					if ($row) {
																						?>
																<select required name="supervisor" id="" style="width:100%">
																	<option value="">Choose Supervisor</option>
																	<?php while ($result = $row->fetch_assoc()) {
																						$display = $result['bio'] . " Chose " . $result['no'] . " field(s) Has a max. of " . $result['myMax'] . ", " . $result['current'] . " out of " . $result['expected'] . " assigned";
																						$id = $result['id'];
																						$display = ucwords(strtolower($display));
																						echo "<option value='" . $id . "'>" . $display . "</option>";
																					}
																					?>
																</select>

															<?php
																		} else {
																			echo "<span class='text-error'>No Supervisors Yet.. Try again later</span>";
																			exit;
																		}
																		?></td>
													</tr>

													<tr>
														<td colspan="2" class='alert alert-info'>Request will not be sent to any supervisor who already used up his/her maximum.
															<hr />Kindly Note That Assigning Field Of Interest From Admin Does Not Guarantee That Allocation Was Successful. A Request Will Be Sent To Selected Supervisor And On Approval, Allocation Will Be Made!</td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																		if ($row) { ?>
																<input type="submit" class="btn btn-success" onClick='return validate("Do you really want to do this?")' value="Send Request">
															<?php
																		} else {
																			echo "<span class='text-error'>No Supervisors Yet.. Try again later</span>";
																		}
																		?>

														</td>
													</tr>
												</table>
											</form>
										</div>
									</div>
								</div>
						<?php

								} //End of assign

							} //Student must be valid
						}  //Two actions only (assign or clear)
						else {
							$getAssign = getFailed();
							?>
						<div id="block_bg" class="block">
							<div class="navbar navbar-inner block-header">
								<div class="muted pull-left">Students with failed allocations (No available fields).</div>
							</div>
							<div class="block-content collapse in">
								<div class="span12">


									<table cellpadding="0" cellspacing="0" border="0" class="table" id="example">
										<thead>

											<tr>
												<th>Student name</th>
												<th>Field Of Interest</th>
												<th>Entry Date</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>

											<?php while ($row = $getAssign->fetch_assoc()) { ?>
												<tr>
													<td>
														<?php
																$regno = getRegnoFromId($row['id']);
																$regno = base64_encode($regno);
																echo getStudentDetailsById($row['id']);  ?>
													</td>
													<td><?php echo $get = (($row['fields']));
																?></td>
													<td><?php echo (($row['date'])); ?></td>
													<td><?php

																echo "<a href='special_assign.php?action=assign&regno=$regno'><button class='btn btn-primary'>Assign </button></a>";


																?></td>


												</tr>
											<?php } ?>

										</tbody>
									</table>
								</div>
							</div>
						</div>
					<?php
					}
					?>
					<!-- /block -->
				</div>


			</div>
		</div>
		<?php include('footer.php'); ?>
	</div>
	<?php include('script.php'); ?>
</body>

</html>