<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * Displays the 'sign up' sheet.
 *
 * @package esoTalk
 */

$form = $data["form"];
?>
<a name="iframe-begin"></a>
<div id='joinSheet' class='sheet'>
<div class='sheetContent'>

<h3><?php echo T("Sign Up"); ?></h3>

<?php echo $form->open(); ?>

<div class='sheetBody'>

<div class='section'>
<ul class='form'>

<?php
// Loop through the form sections (eg. "avatar", "notifications").
foreach ($form->getSections() as $k => $v): ?>

<li><label><?php echo $v; ?></label> <div class='fieldGroup'>
<?php
// Loop through each of the fields in this section and output it.
foreach ($form->getFieldsInSection($k) as $field): ?>

<?php echo $field; ?>
<?php endforeach; ?>
</div></li>

<?php endforeach; ?>

</ul>
Please comment that you have requested an account <a href="http://scratch.mit.edu/projects/47606468/" style="color: rgb(0, 178, 255) !important; font-weight: 600;" target="_blank">
into this project</a>. An admin will check it is there before you can login.</div>

</div>

<div class='buttons'>
<small><?php printf(T("Already have an account? <a href='%s' class='link-login'>Log in!</a>"), URL("user/login")); ?></small>
<?php
echo $form->button("submit", T("Sign Up"), array("class" => "big submit"));
echo $form->cancelButton();
?>
</div>

<?php echo $form->close(); ?>

</div>
</div>