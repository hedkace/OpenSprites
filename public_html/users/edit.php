<?php
include '../assets/includes/connect.php';
header("Content-Type: application/json");

if(!isset($_GET['userid'])) {
    $id = $logged_in_userid;
} else {
    $id = $_GET['userid'];
}

// only allow numbers
$chars = "0123456789";
$pattern = "/[^".preg_quote($chars, "/")."]/";
$id = preg_replace($pattern, "", $id);

if($id == 0 || $id == '') {
	die('false');
}

if(isset($_GET['bgcolor'])) {
    // only allow these chars
    $chars = "0123456789, ()abcdefghijklmnopqrstuvwxyz";
    $pattern = "/[^".preg_quote($chars, "/")."]/";
    $s = preg_replace($pattern, "", $_GET['bgcolor']);
    
    setProfileSettings($logged_in_userid, array("bgcolor" => $s));
}

echo json_encode(getProfileSettings($id), JSON_PRETTY_PRINT);
?>