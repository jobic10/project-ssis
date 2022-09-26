<?php include 'header_dashboard.php';?>
<style>
	/* Style the tab */
	.tab {
		overflow: hidden;
		border: 1px solid #ccc;
		background-color: #f1f1f1;
	}

	/* Style the buttons that are used to open the tab content */
	.tab button {
		background-color: inherit;
		float: left;
		border: none;
		outline: none;
		cursor: pointer;
		padding: 14px 16px;
		transition: 0.3s;
	}

	/* Change background color of buttons on hover */
	.tab button:hover {
		background-color: #ddd;
	}

	/* Create an active/current tablink class */
	.tab button.active {
		background-color: #ccc;
	}

	/* Style the tab content */
	.tabcontent {
		display: none;
		padding: 6px 12px;
		border: 1px solid #ccc;
		border-top: none;
	}

	/* Fade */

	.tabcontent {
		animation: fadeEffect 1s;
		/* Fading effect takes 1 second */
	}

	/* Go from zero to full opacity */
	@keyframes fadeEffect {
		from {
			opacity: 0;
		}

		to {
			opacity: 1;
		}
	}
</style>

<?php include 'supervisor_session.php';?>

<body>
	<?php include 'navbar_supervisor.php';
if (isset($_GET['action'], $_GET['uploadId']) && ($_GET['action'] == 'download' || $_GET['action'] == 'delete') && (in_array($_GET['uploadId'], (range(0, 10))))) {
    $id = getIdFromSession($_SESSION['id'], 'student');
    echo actionOnProgress($id, $_GET['uploadId'], $_GET['action']);
}

if (isset($_POST['action'], $_POST['id'])) {
    $id = $_POST['id'];
    $action = $_POST['action'];
    if ($action == 'yes') {
        //No need for MSG
        echo updateStudentProgress($id, $action);
    } else {
        //
        if (!isset($_POST['msg'])) {
            echo script("Fill the response field");
        }

        echo updateStudentProgress($id, $action, $_POST['msg']);
    }
}
?>
	<div class="container-fluid">
		<div class="row-fluid">
			<?php include 'supervisor_sidebar.php';?>
			<div class="span9" id="content">
				<div class="row-fluid">
					<!-- breadcrumb -->



					<ul class="breadcrumb">
						<?php /*<li><a href="#"><?php echo $class_row['class_name']; ?></a> <span class="divider">/</span></li>*/?>
						<?php /*<li><a href="#"><?php echo $class_row['subject_code']; ?></a> <span class="divider">/</span></li>*/?>
						<li><a href="#">School Year: <?php echo date('Y'); ?></a> <span class="divider">/</span></li>
						<li><a href="#"><b>Progress</b></a></li>
					</ul>
					<!-- end breadcrumb -->

					<!-- block -->
					<div id="block_bg" class="block">
						<div class="navbar navbar-inner block-header">
							<div id="" class="muted pull-left">
								<h4> Student Project Progress</h4>
							</div>
						</div>
						<div class="block-content collapse in">
							<div class="span12">
								<!-- Tab links -->

								<div class="tab">
									<?php
//$store is in special_db.php
$i = 0;
foreach (array_keys($store) as $key) {?>
										<button id = "tabno<?php echo $i++; ?>" class="tablinks" onclick="loadThis(event, '<?php echo $key; ?>')"><?php echo $key; ?></button>
									<?php }?>


								</div>

								<!-- Tab content -->
								<?php
foreach (array_keys($store) as $key) {
    echo '<div id="' . $key . '" class="tabcontent">
										<h3>' . $store[$key] . '</h3>
										' . getAllProgressByKey($key) . '
									</div>';
}?>


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
	<script>
		/*Credits:
	 https://www.w3schools.com/bootstrap/bootstrap_tabs_pills.asp
	 https://www.w3schools.com/howto/howto_js_tabs.asp
	 https://www.elated.com/javascript-tabs/
	 https://codepen.io/wizly/pen/BlKxo/
	 The above links really helped me under more concepts on Tabbed Panels
	 Gotten on the 16-Oct-2019 at 16:00:00
	 */
		function loadThis(evt, cityName) {
			// Declare all variables
			var i, tabcontent, tablinks;

			// Get all elements with class="tabcontent" and hide them
			tabcontent = document.getElementsByClassName("tabcontent");
			for (i = 0; i < tabcontent.length; i++) {
				tabcontent[i].style.display = "none";
			}

			// Get all elements with class="tablinks" and remove the class "active"
			tablinks = document.getElementsByClassName("tablinks");
			for (i = 0; i < tablinks.length; i++) {
				tablinks[i].className = tablinks[i].className.replace(" active", "");
			}

			// Show the current tab, and add an "active" class to the button that opened the tab
			document.getElementById(cityName).style.display = "block";
			evt.currentTarget.className += " active";
		}
		// Get the element with id="tabno8" and click on it
document.getElementById('tabno8').click();
// alert(default);
	</script>
</body>

</html>