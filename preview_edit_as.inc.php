<?php

/**
 * This action will preview the current site as a particular user.
 * 
 * @package segue
 * @version $Id$
 * @date $Date$
 * @copyright 2004 Middlebury College
 */

// Make sure that the realUser is the owner of the site, to prevent anyone from
// seeing content that they shouldn't.
if ($_SESSION['auser'] == $thisSite->owner) {
	
	// Store the real user
	$realUser = $_SESSION['auser'];
	$previewUser = $_REQUEST['previewuser'];
	
	// Change the auser temporarily
	print "<div style='border: 2px solid red; text-align: center; font-size: large;'>Previewing Edit Mode as '$previewUser'.</div>";

	$_SESSION['auser'] = $previewUser;
	$_REQUEST['action'] = $_REQUEST['action']."&previewuser=".$previewUser;
	$action = $_REQUEST['action'];
	$_SESSION['__no_inst_ips'] = TRUE;
	$_REQUEST['nostatus'] = TRUE;
	$previewTitle = "Previewing as '".$previewUser."': ";
	
	// Run the site.inc.php action now as the preview user
	include("viewsite.inc.php");
	
	// Change the acting user back to its original one.
	$_SESSION['auser'] = $realUser;
	unset($_SESSION['__no_inst_ips']);
	
} else {
	$_REQUEST['action'] = 'viewsite';
	include("viewsite.inc.php");
}
?>