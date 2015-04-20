<?php
    require "../assets/includes/connect.php"; //Connect - includes session_start(); require "../assets/includes/avatar.php";
    
    /*if($logged_in_user == 'not logged in' or $user_banned) {
        header('Location: /');
        die;
    }*/
	
	function unique_id($l = 8) {
		return substr(md5(uniqid(mt_rand(), true)), 0, $l);
	}
	
	$sessionId = unique_id(8);
	setCookie("upload_session_id", $sessionId);
?>
<!DOCTYPE html>
<html>

<head>
    <!--Imports the metadata and information that will go in the <head> of every page-->
    <?php echo file_get_contents( '../Header.html'); ?>

    <!--Imports styling-->
    <link href='upload.css' rel='stylesheet' type='text/css'>
	
	<script type="text/javascript">
		window.uploadCsrfToken = "<?php echo $sessionId; ?>";
	</script>
</head>

<body>
    <!--Imports navigation bar-->
    <?php include "../navbar.php"; ?>

    <div class="container main" style="text-align:center;">
        <div class="main-inner">
			
			<div id="upload-area">
				<div id="upload-status">
					<p>Select some files to start</p>
					<div class='progress'><div class='bar'></div></div>
				</div>
				<div id="upload-button-container">
					<div id="select-button">
						<input type='file' name='uploadfiles[]' id='uploadbtn' multiple title="Select files to upload" />
						Select files
					</div>
					<div id="upload-button">
						Start upload
					</div>
				</div>
				<p class='upload-message'>Drop files here</p>
			</div>

        </div>
    </div>

    <!-- footer -->
    <?php echo file_get_contents( '../footer.html'); ?>
	<script type="text/javascript" src="/assets/js/md5.js"></script>
	<script type="text/javascript" src="/assets/js/jszip.min.js"></script>
	<script type="text/javascript" src="upload.js"></script>
</body>

</html>