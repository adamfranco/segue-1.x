<?php // $Id$
session_start();// start the session manager :) -- important, as we just learned

require_once("../../functions.inc.php");
require_once("../../config.inc.php");
require_once("../../dbwrapper.inc.php");
//error_reporting(0);

ob_start();

$cid = db_connect ($dbhost, $dbuser, $dbpass, $dbdb);

//printpre ($_SESSION);
//printpre ($_SERVER);
//printpre ($_REQUEST);

/******************************************************************************
 * start HTML output
 ******************************************************************************/
?>
<html>
<head>
<title>Segue - Measure Connector</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
a {
	color: #003366;
	text-decoration: none;
}
a:hover {
	text-decoration: underline;
}
.error {
	margin-top: 10px;
	color: #990000;
	font-size: 14px;

}
.connection {	
	font-size: 16px;
	text-align: center;
	padding: 10px;
	margin: 50px;
	border: 1px dotted #666666;

}

</style>
</head>
<body>
<?
print "<div class='connection'>";

print "Connnecting to Moodle...<br/><br/>";

if ($_SERVER['HTTP_REFERER']) print "<a href='".$_SERVER['HTTP_REFERER']."'>&lt;&lt; back</a> | ";	
print "<a href='".$cfg['full_uri']."'>Segue Home</a>";


/******************************************************************************
 * Make sure there is an authenticated Segue user
 * go back to previous page http referrer
 ******************************************************************************/
if (!isset($_SESSION[aid])) {
	print "<div class='error'>You must be logged into Segue to use this link...</div>";
	print "</body</html>";
	link_log($auth_id, 0, $category="errors",$description="No Segue user authenticated");
	exit;
}

/******************************************************************************
 * Make sure a slot name is passed
 ******************************************************************************/
if (!isset($_REQUEST[site]) || !$_REQUEST[site]) {
	print "<div class='error'>No Segue site name or id passed...</div>";
	print "</body</html>";
	link_log($_SESSION[aid], 0, $category="errors",$description="No Segue site id passed");
	exit;
	
} else {
	$site_slot = $_REQUEST[site];
	$segue_site_id =  db_get_value("slot", "FK_site", "slot_name='".addslashes($site_slot)."'");
	$node_id = $_REQUEST[node];		
}

print "</div>";
//exit;

/******************************************************************************
 * Get Segue user information from Segue session array
 ******************************************************************************/

$segue_user_id = $_SESSION[aid];
$username = $_SESSION[auser];
$useremail = $_SESSION[aemail];
$userfname = $_SESSION[afname];
$names = split(" ",$userfname);

preg_match('/^(.*)\s([^\s]+)$/', $userfname, $matches);
$firstname = trim($matches[1]);
$lastname = trim($matches[2]);

//print $firstname."<br>";;
//print $lastname."<br>";
//exit;

/******************************************************************************
 * Get the Segue site title, theme and owner
 ******************************************************************************/

$query = "
		SELECT
			site_title, site_theme, FK_createdby
		FROM
			site
		WHERE
			site_id = '".addslashes($segue_site_id)."'				
	";
	
//print $query."<br>";

$r = db_query($query);
$a = db_fetch_assoc($r);
$site_id = $segue_site_id;
$site_title = $a['site_title'];
$site_theme = $a['site_theme'];
$segue_site_owner = $a['FK_createdby'];

//print "site_title: ".$site_title."<br \>"; 
//print "site_theme: ".$site_theme."<br \>"; 
//print "segue_site_owner: ".$segue_site_owner."<br \>"; 
//exit;

//print "Moodle-Segue API<hr>";

$cid2 = db_connect ($dbhost_link, $dbuser_link, $dbpass_link, $dbdb_link);

/******************************************************************************
 * Check for corresponding Moodle site
 ******************************************************************************/
if (!isset($segue_site_id) || !$segue_site_id) {
	print "<div class='error'>No Segue site name or id passed</div>";
	print "</body</html>";
	link_log($_SESSION[aid], 0, $category="errors",$description="No Segue site id passed");
	exit;
	
} else {
	$query = "
		SELECT
			FK_moodle_site_id
		FROM
			segue_moodle
		WHERE
			FK_segue_site_id = '".addslashes($segue_site_id)."'				
	";	
}

//print $query."<br>";
$r = db_query($query);

if (db_num_rows($r) > 0) {
	$admin_report = "linked moodle site found<br>"; 
	
} else if (!isset($segue_site_owner) || !$segue_site_owner ||  !isset($site_title) || !$site_title || !isset($site_theme) || !site_theme) {
	print "<div class='error'>Data needed for this link is missing...</div>";
	print "</body</html>";
	exit;
	
} else {
	$admin_report = "no linked moodle site found<br>";
			
	$query = "
		INSERT INTO
			segue_moodle
		SET
			FK_segue_site_id = '".addslashes($segue_site_id)."',
			site_title = '".addslashes($site_title)."',
			site_slot = '".addslashes($site_slot)."',
			site_owner_id = '".addslashes($segue_site_owner)."',
			site_theme = '".addslashes($site_theme)."'
		";
//	print $query."<br>";
	//exit;
	$r = db_query($query);	

}


/******************************************************************************
 * Check for corresponding Moodle user
 ******************************************************************************/
if (!isset($segue_user_id) || !$segue_user_id) {
	print "<div class='error'>No Segue user id passed...</div><br>";
	print "</body</html>";
	link_log(0, $segue_site_id, $category="errors",$description="No Segue user id passed");
	exit;
	
} else {

	$query = "
			SELECT
				user_link.system, user_link.user_id, auth_id
			FROM
				user_link
			INNER JOIN
				authentication
			ON
				FK_auth_id = auth_id
			WHERE
				authentication.system = 'segue'
				AND
				user_link.system = 'moodle'
				AND
				authentication.user_id = '".addslashes($segue_user_id)."'				
	";

}

//print $query."<br>";
//exit;

$r = db_query($query);


/******************************************************************************
 * Create an auth token for validation
 ******************************************************************************/
$auth_token = md5(time().rand(1, 1000));
//print "auth_token: ".$auth_token."<hr \>";


/******************************************************************************
 * Store the referrer so link to Segue in Moodle is more accurate
 * (i.e. to specific page in linked Segue where Moodle link is...)
 ******************************************************************************/
	if ($_REQUEST['referer']) {
		$referer = "'".addslashes($_REQUEST['referer'])."'";
	
	} else if ($_SERVER['HTTP_REFERER'] 
		&& (strpos($_SERVER['HTTP_REFERER'], $cfg['full_uri']) === 0
			|| strpos($_SERVER['HTTP_REFERER'], $cfg['classsitesurl']) === 0
			|| strpos($_SERVER['HTTP_REFERER'], $cfg['personalsitesurl']) === 0
		))
	{
		$referer = "'".addslashes($_SERVER['HTTP_REFERER'])."'";
	} else {
		$referer = "NULL";
	}
	
	
	// linked user found
	if (db_num_rows($r) > 0) {
	//	print "linked moodle user found<br>";	
		
		// update authentication table with new auth_token
		$query = "
			Update
				authentication
			SET
				auth_token = '".addslashes($auth_token)."',
				auth_time = NOW(),
				referer = ".$referer."
			WHERE
				user_id = '".addslashes($segue_user_id)."'		
		";
		
	//	print $query."<br>";
		$r = db_query($query);	
	
	//no linked user found	
	} else {
	//	print "no linked moodle user found<br>";
	
		$query = "
			INSERT INTO
				authentication
			SET
				system = 'segue',
				username = '".addslashes($_SESSION[auser])."',
				firstname = '".addslashes($firstname)."',
				lastname = '".addslashes($lastname)."',
				email = '".addslashes($_SESSION[aemail])."',
				user_id = '".addslashes($segue_user_id)."',
				auth_token = '".addslashes($auth_token)."',
				auth_time = NOW(),
				referer = ".$referer."
			";
	//	print $query."<br>";
	//	exit;
		$r = db_query($query);
		
		$auth_id = lastid($r);		
		$query = "
			INSERT INTO
				user_link
			SET
				FK_auth_id = '".addslashes($auth_id)."'
			";
	//	print $query."<br>";
		$r = db_query($query);		
			
	}



//exit;
if ($_REQUEST['continue'] == "1" || $_SESSION[ltype] != "admin") {
	header("Location: ".$moodle_url."/segue/segue_link.php?userid=".addslashes($segue_user_id)."&siteid=".addslashes($segue_site_id)."&auth_token=".addslashes($auth_token));
}

/******************************************************************************
 * Segue Admin Report
 ******************************************************************************/
if ($_SESSION[ltype] == "admin") {
	print "<div class='connection'>";
	print "Segue Administrator Reference<br /><br />";
	print "<form action='$PHP_SELF' method='post'>";
	print "<table align='center' cellpadding='5'>";
	print "";
	print "<tr><td>Segue Site Title:</td><td>".$site_title." (id:".$segue_site_id.")</td></tr>"; 
	print "<tr><td>Segue Site Owner:</td><td>".$_SESSION[afname]." (id:".$segue_site_owner.")</td></tr>"; 
	print "<tr><td></td></tr>"; 
	print "<tr><td>Moodle Link Status:</td><td>".$admin_report."</td></tr>"; 
	print "</table>";
	print "<input type='hidden' name='continue' value='1'";
	print "<input type='hidden' name='referer' value='".$_SERVER['HTTP_REFERER']."'>";
	print "<div style='text-align: right;'><input type='submit' value='Continue'></div>";
	print "</form>";
	print "</div>";
	exit;
}


function link_log($auth_id="",$site_link_id="",$category="event",$description="") {
	global $dbhost_link, $dbuser_link, $dbpass_link, $dbdb_link;
	$cid2 = db_connect ($dbhost_link, $dbuser_link, $dbpass_link, $dbdb_link);
	
	$query = " 
		INSERT INTO 
			logs
		SET
			FK_auth_id = '".addslashes($auth_id)."',
			FK_site_link = '".addslashes($site_link_id)."',
			category = '".addslashes($category)."',
			description = '".addslashes($description)."'
	";
	//print $query;
	//exit;
	$r = db_query($query);
}

// function printpre($array, $return=FALSE) {
// 	ob_start();
// 	print "\n<pre>";
// 	print_r($array);
// 	print "\n</pre>";
// 	
// 	if ($return)
// 		return ob_get_clean();
// 	else
// 		ob_end_flush();
// }


?>
