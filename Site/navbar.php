<script>
	// @deprecated
	var loggedInUser = "Use OpenSprites.user.name";
	var loggedInUserId = "Use OpenSprites.user.id";
	
	// let's try to merge to using proper JS technique. Remember, we want this code to be maintainable.
	var OpenSprites = OpenSprites || {};
	OpenSprites.user = {};
	OpenSprites.user.name = <?php echo json_encode($logged_in_user); ?>;
	OpenSprites.user.id = <?php echo json_encode($logged_in_userid); ?>;
</script>
<div class="header">
    <div class="container">	
		<a class="scratch" href="/"></a>

        <ul class="left">
            <li>
				<a href="/sprites">Sprites</a>
            </li>
            <li>
				<a href="/scripts">Scripts</a>
            </li>
            <li>
				<a href="/media">Media</a>
            </li>
			<li class="last">
				<a href="http://opensprites.gwiddle.co.uk/forums/">Forums</a>
            </li>
        </ul>
		
	<ul class="right">
		<li class='navbar-upload'>
			<a href="/upload/"><img class='upload-icon' src='/assets/images/upload.png' /> Upload</a>
        </li>
        <?php if($logged_in_user == 'not logged in') { ?>
            <li><a href="/register/">Sign Up</a>
            </li>
            <li class="last" id='login' onclick="window.location = '/login/';"><span>Log In</span></li>
        <?php } else  { ?>  <!-- display login info/username/etc -->
		<li><a style="padding: 0; padding-left: 10px; padding-right: 10px;" href="/users/<?php echo $logged_in_userid . '/'; ?>"><?php echo $logged_in_user; ?></a></li>
		<li class="last" onclick="window.location = 'http://dev.opensprites.gwiddle.co.uk/logout.php?return=/';"><span>Log Out</span></li>
        <?php } ?>
        </ul>
    </div>
</div>
