<?php
    require "../assets/includes/connect.php";  //Connect - includes session_start();
    //$file = json_decode(file_get_contents('../site-api/asset.php?userid=' . $_GET['id'] . '&hash=' . $_GET['file']));
	connectDatabase();
	$id = -1;
	$file = "";
	if(isset($_GET['id'])) {
		if(is_numeric($_GET['id'])) {
	    	$id = intval($_GET['id']);
	    } else {
	    	connectForumDatabase();
	    	try {
	    		$id = intval(forumQuery("SELECT * FROM `$forum_member_table` WHERE `username`=?", array($_GET['id']))[0]['memberId']);
	    	} catch(Exception $e) {
	    		include '../404.php';
	        	die();
	    	}
	    }
	}
	if(isset($_GET['file'])) $file = $_GET['file'];
	
	$asset = imageExists($id, $file);
	$filename = NULL;
	if(sizeof($asset) > 0){
		$filename = $asset[0]['name'];
	}
	if($filename == NULL){
		include "../404.php";
		die;
	}
	
	$raw = $asset[0];
	$obj = array(
		"name" => $raw['customName'],
		"type" => $raw['assetType'],
		"url" => "/uploads/uploaded/" . $raw['name'],
		"filename" =>  $raw['name'],
		"md5" => $raw['hash'],
	  	"upload_time" => $raw['date'],
	  	"uploaded_by" => array(
	  		"name" => $raw["user"],
	  		"id" => $raw["userid"]
	  	),
		"downloads" => array(
			"this_week" => intval($raw['downloadsThisWeek']),
			"total" => intval($raw['downloadCount'])
		),
		"description" => $raw['description']
	);
	
	$userData = getUserInfo($raw["userid"]);
	if($userData['usertype'] == "suspended"){
		include "../404.php";
		die();
	}
?>
<!DOCTYPE html>
<html>
<head>
    <?php
        echo file_get_contents('../Header.html'); //Imports the metadata and information that will go in the <head> of every page
    ?>

    <link href='/uploads/style.css' rel='stylesheet' type='text/css'>
</head>
<body>
	<?php if($obj['type'] == "image"){ ?>
	<a href="#_" class="lightbox" id="img1">
		<img src="<?php echo $obj['url']; ?>">
	</a>
    <?php
		}
        include "../navbar.php"; // Imports navigation bar
    ?>
    
    <script>
        OpenSprites.view = {type: "file"};
        OpenSprites.view.file = <?php echo json_encode($obj); ?>;
    </script>
    
    <!-- Main wrapper -->
    <?php if($obj['type'] == 'sound') { ?>
        <div id='overlay-img'></div>
    	<canvas id='background-img'></canvas>
		<canvas id="vis-canvas"></canvas>
    <?php } else { ?>
        <div id='overlay-img'></div>
    	<canvas id='background-img'></canvas>
    <?php } ?>
    <div id='dark-overlay'><div id='overlay-inner'>
		    <div id='username' class='asset-name'>
                <?php
                    echo htmlspecialchars($obj['name']);
                ?>
            </div>
        <div id="user-pane-right">
            <div id='description'>
                <strong>By:</strong> <a href='/users/<?php echo $obj['uploaded_by']['id']; ?>/'><?php echo htmlspecialchars($obj['uploaded_by']['name']); ?></a><br/>
				<strong>Uploaded on:</strong> <?php
				sscanf($obj['upload_time'], "%u-%u-%u %u:%u:%u", $year, $month, $day, $hour, $min, $sec);
				$month = date('F', mktime(0, 0, 0, $month, 10));
				echo $day.' '.$month.' '.$year;
				?><br/>
				<strong>Downloads:</strong> <?php echo $obj['downloads']['total']; ?><hr/>
            </div>
            <div id='follow'>
                <a <?php if($obj['type'] != 'script') { ?>href="/uploads/download.php?id=<?php echo $obj['uploaded_by']['id']; ?>&file=<?php echo $obj['md5']; ?>"<?php } ?> target="_blank"><?php echo 'Download this ' . $obj['type']; ?></a>
            </div>
            <?php if($logged_in_userid == $obj['uploaded_by']['id']) { ?>
            <div id='delete'>
                <a class="file_delete" href="/uploads/delete.php?file=<?php echo $obj['md5']; ?>">Delete</a>
            </div>
            <?php } else {
                if ($is_admin == true) { ?>
                    <div id='delete'>
                        <a class="file_delete" href="/uploads/admindelete.php?id=<?php echo $obj['uploaded_by']['id']; ?>&file=<?php echo $obj['md5']; ?>">Delete (Admin)</a>
                    </div>
                <?php }
            }?>
			
			<?php if($logged_in_userid == $obj['uploaded_by']['id'] || $is_admin) { ?>
				<div id="rename"><a class="file_rename" href="javascript:void(0)">Edit title or description<?php if($is_admin && $logged_in_userid !== $obj['uploaded_by']['id']){ echo " (Admin)"; } ?></a></div>
			<?php } ?>
        </div>
        <div id="user-pane-left">
			<?php if($obj['type'] != "script"){ ?>
				<a href="#img1">
					<img class="img-preview" src="/uploads/thumbnail.php?file=<?php echo $obj['filename']; ?>">
					<script>
						OpenSprites.etc = OpenSprites.etc || {};
						OpenSprites.etc.bgSrc = "/uploads/thumbnail.php?file=" + <?php echo json_encode($obj['filename']); ?>;
					</script>
				</a>
			<?php } else { ?>
				<div class="img-preview"></div>
				<script>
					OpenSprites.etc = {};
					var model = OpenSprites.models.ScriptPreview($(".img-preview"));
					$.get(OpenSprites.view.file.url, function(data){
						model.loadJson(data);
					});
				</script>
			<?php } ?>
        </div>
    </div></div>

    <div class="container main" id="collections">
        <div class="main-inner">
            <?php if($obj['type'] == 'sound') { ?>
				<audio style="width: 100%;" controls preload='metadata' src='<?php echo $obj['url'] ?>';></audio><br/><br/>
            <?php } ?>
			<h1>Description</h1>
			<p class='desc'></p>
			<?php if($obj['type'] == 'image'){ ?>
				<h2>Direct links</h2>
				<p>Use this link to embed this image on websites.</p>
				<input type="text" value="http://opensprites.gwiddle.co.uk/uploads/uploaded/<?php echo urlencode($obj['filename']); ?>" class="image-url" onfocus="$(this).select();" />
				<p>Copy and paste this BBCode to embed the image on forums such as the Scratch forums.</p>
				<input type="text" value="[img]http://opensprites.gwiddle.co.uk/uploads/uploaded/<?php echo urlencode($obj['filename']); ?>[/img]" class="image-url" onfocus="$(this).select();" />
			<?php } ?>
			<?php if($obj['type'] == 'script') { ?>
        	    <h2>Script</h2>
        	    <div id='script_preview'></div>
        	    <script>
        	        var scriptPrv = OpenSprites.models.ScriptPreview($("#script_preview"));
					$.get(OpenSprites.view.file.url, function(data) {
						scriptPrv.loadJson(data);
						$("#script_preview").attr('style', 'background: white; padding: 10px; border-radius: 20px;');
						$("#script_preview pre:first-child").css('display', 'block');
					});
        	    </script>
        	<?php } ?>
        </div>
    </div>
	
	<script src='/assets/lib/stackblur/stackblur.js'></script>
	<script>
		$(".file_delete").click(function(e){
			e.preventDefault();
			if(confirm("Are you sure you want to delete this file?")) location.href = $(this).attr("href");
		});
		
		// blurred background

		/**
		* By Ken Fyrstenberg
		*
		* drawImageProp(context, image [, x, y, width, height [,offsetX, offsetY]])
		*
		* If image and context are only arguments rectangle will equal canvas
		*/
		function drawImageProp(ctx, img, x, y, w, h, offsetX, offsetY) {
			if (arguments.length === 2) {
			x = y = 0;
				w = ctx.canvas.width;
				h = ctx.canvas.height;
			}
		
			// default offset is center
			offsetX = typeof offsetX === "number" ? offsetX : 0.5;
			offsetY = typeof offsetY === "number" ? offsetY : 0.5;
			
			// keep bounds [0.0, 1.0]
			if (offsetX < 0) offsetX = 0;
			if (offsetY < 0) offsetY = 0;
			if (offsetX > 1) offsetX = 1;
			if (offsetY > 1) offsetY = 1;
		
			var iw = img.width,
				ih = img.height,
				r = Math.min(w / iw, h / ih),
				nw = iw * r,   // new prop. width
				nh = ih * r,   // new prop. height
				cx, cy, cw, ch, ar = 1;
		
			// decide which gap to fill    
			if (nw < w) ar = w / nw;
			if (nh < h) ar = h / nh;
			nw *= ar;
			nh *= ar;
		
			// calc source rectangle
			cw = iw / (nw / w);
			ch = ih / (nh / h);
		
			cx = (iw - cw) * offsetX;
			cy = (ih - ch) * offsetY;
		
			// make sure source rectangle is valid
			if (cx < 0) cx = 0;
			if (cy < 0) cy = 0;
			if (cw > iw) cw = iw;
			if (ch > ih) ch = ih;

			// fill image in dest. rectangle
			ctx.drawImage(img, cx, cy, cw, ch,  x, y, w, h);
		}
		function drawBg(){
			var canvasId = "background-img";
			var canvas = document.getElementById(canvasId);
            try {
                var context = canvas.getContext("2d");
                var img = new Image();
                img.onload = function() {
                    drawImageProp(context, img);
                    stackBlurCanvasRGB(canvas, 0, 0, canvas.width, canvas.height, 10);
                }
                if(typeof OpenSprites.etc.bgSrc !== 'undefined'){
                    img.src = OpenSprites.etc.bgSrc;
                }
            } catch(e) {}
		}
		
		drawBg();
		$(window).resize(drawBg);
	</script>
    
	<?php if($logged_in_userid == $obj['uploaded_by']['id'] || $is_admin) { ?>
	
	<!-- modal -->
    <div class="modal-overlay"></div>
    <div class="modal edit-asset">
		<div class="modal-content">
			<h1>Edit title or description</h1>
			<p class='input-error' style='display:none;'>Sample Text</p>
			<input type="text" id="file-name" maxlength='32' value="<?php echo htmlspecialchars($obj['name']); ?>"/><br/><br/>
			<textarea id="file-desc" maxlength='500' value="<?php echo htmlspecialchars($obj['description']); ?>"></textarea><br/>
			<p>Descriptions support <acronym title="A simple text-formatting system">Markdown</acronym>. Click <a href="http://markdowntutorial.com/" target="_blank">here</a> to learn more about Markdown.</p>
			<div class="buttons-container">
				<button class='btn red'>Cancel</button>
				<button class='btn blue'>OK</button>
			</div>
		</div>
	</div>
	
	<?php } ?>
    
    <script src='/assets/lib/marked/marked.js'></script>
    <script>
        var desc = <?php echo json_encode(htmlspecialchars($obj['description'])); ?>;
		
		var descModel = OpenSprites.models.MdSection($(".about-section.desc"));
		descModel.updateMarkdown(desc);
    </script>
    
    <?php if($obj['type'] == "sound"){ ?>
		<!-- background colors! -->
		<script src="/assets/lib/please/please.js"></script>
		<?php if(!isset($_GET['vis']) || $_GET['vis'] === "default"){ //default ?>
		<!-- Circle visualizer -->
		<script>$('#overlay-img').css('transition', 'none');</script>
		<script src="/assets/js/dankswag/bass_vis.js"></script>
		<?php } else if ($_GET['vis'] === "bars") { ?>
		<!-- Bars -->
		<script src='/assets/js/dankswag/bars.js'></script>
		<?php } else { // none ?>
		<!-- Y U no want visualizer??? -->
		<?php } ?>
	<?php } ?>
	
    <script>
        var j = Please.make_color({format: 'hsv'});
        var c = Please.make_scheme({
            h: j.h,
            s: j.s,
            v: j.v
        },
        {
            scheme_type: 'complement'
        });
        $('#overlay-img').css('background', c[0]);
        setInterval(function() {
            j = Please.make_color({format: 'hsv'});
            c = Please.make_scheme({
                h: j.h,
                s: j.s,
                v: j.v
            },
            {
                scheme_type: 'complement'
            });
            if(!document.getElementsByTagName('audio')[0].paused)
                $('#overlay-img').css('background', c[0]);
        }, 2000);
		
		var audioPlayer = $("audio");
		if(audioPlayer.length > 0){
			$(audioPlayer).on("play", function(){
				$("#overlay-img").fadeIn();
				$("#background-img").fadeOut();
			});

			$(audioPlayer).on("pause", function(){
				$("#overlay-img").fadeOut();
				$("#background-img").fadeIn();
			});
		}
    </script>
	
	<script src="/uploads/edit.js"></script>
	
	<?php if($obj['type'] == 'script') { ?>
	<script src='/assets/js/jszip.min.js'></script>
	<script>
		var input = <?php echo json_encode(file_get_contents($obj['url'])); ?>;
		var name = <?php echo json_encode($obj['name']); ?>
	
		$('#follow a').click(function() {
			var sprite = {
				"objName": name,
				"scripts": [[10, 10, JSON.parse(input)]],
				"sounds": [],
				"costumes": [{
				"costumeName": "costume1",
				"baseLayerID": 0,
				"baseLayerMD5": "f9a1c175dbe2e5dee472858dd30d16bb.svg",
				"bitmapResolution": 1,
				"rotationCenterX": 47,
				"rotationCenterY": 55
			}],
				"currentCostumeIndex": 0,
				"scratchX": 0,
				"scratchY": 0,
				"scale": 1,
				"direction": 90,
				"rotationStyle": "normal",
				"isDraggable": false,
				"indexInLibrary": 100000,
				"visible": true,
				"spriteInfo": {}
			};
			
			var zip = new JSZip();
			zip.file("sprite.json", JSON.stringify(sprite));
			zip.file("0.svg", '<svg version="1.1" id="cat" x="0px" y="0px" width="95px" height="111px" viewBox="0 0 95 111" enable-background="new 0 0 95 111" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"> <g> <g id="Layer_3"> <path fill="#FAA51D" stroke="#000000" d="M22.462,79.039c-2.415-0.451-5.304-1.309-7.742-3.503&#xD;&#xA;&#x9;&#x9;C9.268,70.629,7.526,62.535,3.672,64.622c-3.856,2.088-3.782,15.165,8.353,19.194c4.182,1.391,7.998,1.396,11.091,1.312&#xD;&#xA;&#x9;&#x9;c0.811-0.025,7.717-0.654,10.079-4.074c2.361-3.42,0.719-4.272-0.09-4.744C32.295,75.838,25.878,79.677,22.462,79.039z"/> <path fill="#FFFFFF" d="M4.236,64.877c-1.989,0.613-3.075,4.998-2.076,8.484c0.998,3.49,2.634,5.022,3.863,6.398&#xD;&#xA;&#x9;&#x9;c1.528,1.038-0.72-2.402,1.361-4.15c2.075-1.744,5.733-0.914,5.733-0.914s-2.909-3.987-4.57-6.396&#xD;&#xA;&#x9;&#x9;C6.975,65.988,6.359,64.375,4.236,64.877z"/> </g> <g> <path fill="#FAA51D" d="M38.217,86.756c0,0-8.832,6.2-17.071,8.412l0.086,0.215c1.247,1.824,5.87,7.497-0.334,9.496&#xD;&#xA;&#x9;&#x9;c-5.333,1.717-15.12-13.104-10.821-15.902c2.626-1.713,4.892-0.252,4.892-0.252s3.474-1.07,6.001-2.345&#xD;&#xA;&#x9;&#x9;c4.303-2.161,5.784-3.453,5.784-3.453s4.184-4.306,6.856-4.137C36.281,78.96,41.669,83.504,38.217,86.756z"/> <path fill="none" stroke="#231F20" stroke-width="1.2" d="M21.232,95.383c1.247,1.824,5.87,7.497-0.334,9.496&#xD;&#xA;&#x9;&#x9;c-5.333,1.717-15.329-13.344-11.03-16.145c2.626-1.713,5.101-0.01,5.101-0.01s3.474-1.072,6.001-2.348&#xD;&#xA;&#x9;&#x9;c4.303-2.161,5.784-3.453,5.784-3.453"/> <path fill="none" stroke="#231F20" stroke-width="1.2" d="M38.217,86.756c0,0-10.123,7.107-18.804,8.819"/> </g> <path fill="#FAA51D" stroke="#231F20" stroke-width="1.2" d="M52.169,74.885c0,0,1.235,0.165,4.744,3.676&#xD;&#xA;&#x9;c3.509,3.508,6.026,2.16,8.911,0.724c2.877-1.443,10.537-6.126,6.49-9.817c-4.049-3.688-6.207,1.146-9.715,2.405&#xD;&#xA;&#x9;c-3.512,1.26-5.061-2.487-6.858-4.287c-0.589-0.593-1.188-1.099-1.729-1.505c0,0-0.971-0.76-1.906,2.79&#xD;&#xA;&#x9;C51.172,72.412,50.162,73.415,52.169,74.885z"/> <g id="Layer_2_1_"> <path fill="#FAA51D" stroke="#231F20" stroke-width="1.2" d="M46.753,82.012c1.188-0.912,2.397-2.402,3.951-4.713&#xD;&#xA;&#x9;&#x9;c1.296-1.927,2.7-5.578,2.7-5.578c0.875-2.521,1.934-6.576-1.902-7.296c-1.553-0.291-4.079-0.098-7.67-0.776&#xD;&#xA;&#x9;&#x9;c-3.593-0.681-6.798-2.522-9.517,2.233c-2.718,4.757-9.59,8.271-1.056,16.563c0,0,4.901,3.842,10.764,9.639&#xD;&#xA;&#x9;&#x9;c4.831,4.775,12.045,10.602,12.045,10.602s18.972,2.188,19.535-0.693c1.922-9.79-14.777-6.911-14.777-6.911&#xD;&#xA;&#x9;&#x9;s-4.605-3.933-6.725-5.794c-3.478-3.059-11.125-10.771-11.125-10.771"/> <path fill="#FFFFFF" d="M51.253,75.434c0,0,2.47-2.66-2.469-5.317c-4.939-2.657-7.213-0.017-8.739,1.521&#xD;&#xA;&#x9;&#x9;c-2.644,2.655,3.443,6.611,3.443,6.611l3.176,3.204c0,0,1.738-1.647,2.499-2.979C50.036,77.26,51.253,75.434,51.253,75.434"/> </g> <g id="Layer_8"/> <path fill="#FAA51D" stroke="#231F20" stroke-width="1.2" d="M29.926,73.218c0.749-0.571,2.889-2.202,4.854-3.657&#xD;&#xA;&#x9;c2.428-1.799,6.117-5.849,1.077-7.646c-5.04-1.801-7.507,1.604-11.519,4.946c-2.159,1.801-5.308,2.699-4.319,6.209&#xD;&#xA;&#x9;c0.993,3.511,4.862,13.408,11.789,10.17c6.929-3.239-1.799-9.18-3.06-11.157"/> <g id="Layer_2"> <path fill="#FAA51D" stroke="#231F20" stroke-width="1.2" d="M52.709,14.156c-1.54-0.143-4.75-0.316-6.518-0.231&#xD;&#xA;&#x9;&#x9;c-4.728,0.225-9.224,1.928-9.224,1.928L23.949,7.357l2.235,18.906c0.646-0.782-10.555,12.804-3.479,24.224&#xD;&#xA;&#x9;&#x9;c7.08,11.426,22.233,16.518,40.988,12.792c18.755-3.729,23.229-14.531,21.986-20.246c-1.242-5.714-8.322-7.823-8.322-7.823&#xD;&#xA;&#x9;&#x9;s-0.09-4.48-3.328-9.97c-1.926-3.268-8.348-8.041-8.348-8.041L62.822,5.647l-7.452,7.204L52.709,14.156z"/> <path fill="#FFFFFF" d="M76.42,35.066l-2.482-2.064l-9.115,2.661c0,0,0,3.419-4.367,4.367c-4.37,0.951-11.211-2.277-11.211-2.277&#xD;&#xA;&#x9;&#x9;L41.46,41.17c0,0-8.437,0.928-8.739,6.081C32.048,58.704,46.1,63.479,51.425,63.783c2.905,0.167,8.235-0.338,12.277-1.141&#xD;&#xA;&#x9;&#x9;c17.752-3.234,22.551-13.919,21.31-19.635c-1.242-5.714-7.978-7.196-7.978-7.196L76.42,35.066z"/> <path fill="none" stroke="#231F20" stroke-width="1.2" d="M10.673,46.155c0,0,4.107,0.374,5.974,0.268&#xD;&#xA;&#x9;&#x9;c1.865-0.107,5.492-0.587,5.492-0.587"/> <path fill="none" stroke="#231F20" stroke-width="1.2" d="M81.656,40.671c0,0,4.549-0.743,6.859-1.549&#xD;&#xA;&#x9;&#x9;c2.715-0.942,4.543-2.545,4.543-2.545"/> <path fill="none" stroke="#231F20" stroke-width="1.2" d="M22.337,41.909c0,0-2.384-1.777-6.117-3.43&#xD;&#xA;&#x9;&#x9;c-4.134-1.831-6.405-2.303-6.405-2.303"/> <path fill="none" stroke="#231F20" stroke-width="1.2" d="M82.117,46.622c0,0,2.726,1.104,5.533,1.385&#xD;&#xA;&#x9;&#x9;c2.77,0.276,4.646,0.11,4.646,0.11"/> <path fill="none" stroke="#000000" stroke-linecap="round" stroke-miterlimit="10" d="M52.35,14.212&#xD;&#xA;&#x9;&#x9;c2.84,0.7,3.887,1.469,3.887,1.469"/> <line fill="none" stroke="#000000" x1="33.898" y1="13.684" x2="39.956" y2="18.042"/> </g> <g id="Layer_5"> <path fill="#FFFFFF" stroke="#231F20" d="M71.84,25.366c2.924,4.479,3.033,9.591,0.242,11.415&#xD;&#xA;&#x9;&#x9;c-2.793,1.825-7.426-0.332-10.354-4.813c-2.933-4.48-3.037-9.589-0.244-11.415C64.275,18.73,68.913,20.884,71.84,25.366z"/> <path fill="#231F20" d="M71.089,32.522c0,1.08-0.802,1.956-1.8,1.956c-0.993,0-1.803-0.877-1.803-1.956&#xD;&#xA;&#x9;&#x9;c0-1.08,0.81-1.958,1.803-1.958C70.287,30.564,71.089,31.442,71.089,32.522"/> </g> <g id="Layer_7"> <path fill="#FFFFFF" stroke="#231F20" d="M47.867,28.619c2.926,4.48,2.619,9.862-0.681,12.015&#xD;&#xA;&#x9;&#x9;c-3.302,2.159-8.351,0.272-11.276-4.208c-2.928-4.48-2.624-9.86,0.678-12.017C39.891,22.253,44.938,24.137,47.867,28.619z"/> <path fill="#231F20" d="M46.079,34.507c0,1.081-0.803,1.957-1.801,1.957c-0.992,0-1.803-0.878-1.803-1.957&#xD;&#xA;&#x9;&#x9;c0-1.08,0.811-1.957,1.803-1.957C45.274,32.55,46.079,33.427,46.079,34.507"/> </g> <path fill="#5E4A42" stroke="#000000" d="M59.766,37.926c1.854,0,4.555-0.284,4.697,0.569c0.143,0.855-1.709,4.203-2.988,4.345&#xD;&#xA;&#x9;c-1.283,0.142-6.125-2.353-6.195-3.919C55.206,37.355,58.055,37.926,59.766,37.926z"/> <g id="Layer_4"> <path fill="none" stroke="#231F20" stroke-width="1.2" d="M46.774,45.235c0,0,10.347,3.054,14.217,3.897&#xD;&#xA;&#x9;&#x9;c3.868,0.842,10.851,1.684,10.851,1.684s-7.99,10.245-17.328,7.644C45.176,55.863,45.345,49.975,46.774,45.235z"/> </g> </g> </svg>');
			var content = zip.generate({type:"blob"});
			saveAs(content, name+".sprite2");
		});
	</script>
	<?php } ?>
    
    <!-- footer -->
    <?php echo file_get_contents('../footer.html'); ?>
</body>
</html>
