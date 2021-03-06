<?php
include '../assets/includes/connect.php';
include '../assets/includes/validate.php';

header("Content-Type: application/json");

if(!isset($_POST['userid'])) {
    $id = $logged_in_userid;
} else {
    $id = $_POST['userid'];
}

$id = intval($id);

if($id == 0 || $id == '') {
	die(json_encode(array("status"=>"error", "message"=>"Whoops, it seems your browser sent an incomplete request", "problem"=>"")));
}

if($id != $logged_in_userid && !$is_admin){
	die(json_encode(array("status"=>"error", "message"=>"403 please go away", "problem"=>""))); // sassy :P
}

if(!isset($_POST['bgcolor']) || !isset($_POST['about']) || !isset($_POST['location'])){
	die(json_encode(array("status"=>"error", "message"=>"Whoops, it seems your browser sent an incomplete request", "problem"=>"")));
}

$bgcolor = $_POST['bgcolor'];
$bgcolor = preg_replace("/\\s*/", "", $bgcolor); // strip whitespace

$bgreg1 = "/rgb\([0-9]+,[0-9]+,[0-9]+\)/i";   // format: rgb(n, n, n)
$bgreg2 = "/(#[0-9a-f]{6})|(#[0-9a-f]{3})/i"; // format: #hhh or #hhhhhh
if(preg_match($bgreg1, $bgcolor) !== 1 && preg_match($bgreg2, $bgcolor) !== 1){
	$bgcolor = "avatar"; // don't bother with an error message, we don't tolerate haxx :P
}

setProfileSettings($logged_in_userid, array("bgcolor" => $bgcolor));

$location = $_POST['location'];
if(hasBadWords($location)) die(json_encode(array("status"=>"error", "message"=>"Whoops, our bad word detector found a problem with your location.", "problem"=>"#text-location")));
if(strlen($location) > 30) $location = substr($location, 0, 30); // don't bother with an error message
$lRes = forumQuery("SELECT * FROM `et_profile_data` WHERE `memberId`=? AND `fieldId`=?", array($id, 2));
if(sizeof($lRes) == 0){
	forumQuery0("INSERT INTO `et_profile_data` (`data`, `memberId`, `fieldId`) VALUES (?, ?, ?)", array($location, $id, 2));
} else {
	forumQuery0("UPDATE `et_profile_data` SET `data`=? WHERE `memberId`=? AND `fieldId`=?", array($location, $id, 2));
}

$about = $_POST['about'];
if(hasBadWords($about)) die(json_encode(array("status"=>"error", "message"=>"Whoops, our bad word detector found a problem with your about section.", "problem"=>"#aboutme")));
if(strlen($about) > 500) $about = substr($about, 0, 500); // don't bother with an error message
$aRes = forumQuery("SELECT * FROM `et_profile_data` WHERE `memberId`=? AND `fieldId`=?", array($id, 1));
if(sizeof($aRes) == 0){
	forumQuery0("INSERT INTO `et_profile_data` (`data`, `memberId`, `fieldId`) VALUES (?, ?, ?)", array($about, $id, 1));
} else {
	forumQuery0("UPDATE `et_profile_data` SET `data`=? WHERE `memberId`=? AND `fieldId`=?", array($about, $id, 1));
}

echo json_encode(array("status"=>"success","settings"=>array("about" => $about, "location" => $location, "bgcolor" => $bgcolor)));
?>
