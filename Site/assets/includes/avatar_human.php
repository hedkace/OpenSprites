<?php
  // gets the user's image src
  $raw_json = file_get_contents("http://scratch.mit.edu/site-api/users/all/" . $_GET['username'] . "/");
  $user_arr = json_decode($raw_json);
  $user_avatar = $user_arr["thumbnail_url"];
  echo '<img src="http:' . $user_avatar . '">';
?>