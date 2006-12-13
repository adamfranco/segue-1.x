<?php // $Id$
session_start();// start the session manager :) -- important, as we just learned

require_once("../../functions.inc.php");
require_once("../../config.inc.php");
require_once("../../dbwrapper.inc.php");

//$dbdb_link = "achapin_segue-moodle";
//$moodle_url = "http://slug.middlebury.edu/~achapin/moodle163";


$cid = db_connect ($dbhost, $dbuser, $dbpass, $dbdb);

/******************************************************************************
 * Make sure there is an authenticated Segue user
 * go back to previous page http referrer
 ******************************************************************************/
if (!isset($_SESSION[aid])) {
	print "You must be logged into Segue to use this link";
	exit;
}

/******************************************************************************
 * Make sure a slot name is passed
 ******************************************************************************/
if (!isset($_REQUEST[site]) || !$_REQUEST[site]) {
	print "Missing Segue slot name";
	exit;
	
} else {
	$site_slot = $_REQUEST[site];
	$segue_site_id =  db_get_value("slot", "FK_site", "slot_name='".addslashes($site_slot)."'");
	$node_id = $_REQUEST[node];		
}


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

print $firstname."<br>";;
print $lastname."<br>";
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
	
print $query."<br>";

$r = db_query($query);
$a = db_fetch_assoc($r);
$site_title = $a['site_title'];
$site_theme = $a['site_theme'];
$segue_site_owner = $a['FK_createdby'];

//print "site_title: ".$site_title."<br \>"; 
//print "site_theme: ".$site_theme."<br \>"; 
//print "segue_site_owner: ".$segue_site_owner."<br \>"; 
//exit;

print "Moodle-Segue API<hr>";

$cid2 = db_connect ($dbhost_link, $dbuser_link, $dbpass_link, $dbdb_link);

/******************************************************************************
 * Check for corresponding Moodle site
 ******************************************************************************/
if (!isset($segue_site_id) || !$segue_site_id) {
	print "no Segue site name or id passed<br>";
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

print $query."<br>";
$r = db_query($query);

if (db_num_rows($r) > 0) {
	print "linked moodle site found<br>";
	
} else if (!isset($segue_site_owner) || !$segue_site_owner ||  !isset($site_title) || !$site_title || !isset($site_theme) || !site_theme) {
	print "missing data<br>";
	exit;
	
} else {
	print "no linked moodle site found<br>";
			
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
	print $query."<br>";
	//exit;
	$r = db_query($query);	

}


/******************************************************************************
 * Check for corresponding Moodle user
 ******************************************************************************/
if (!isset($segue_user_id) || !$segue_user_id) {
	print "no Segue user id passed<br>";
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

print $query."<br>";
//exit;

$r = db_query($query);


// create an auth token for validation
$auth_token = md5(time().rand(1, 1000));
print "auth_token: ".$auth_token."<hr \>";


// linked user found
if (db_num_rows($r) > 0) {
	print "linked moodle user found<br>";	
	
	// update authentication table with new auth_token
	$query = "
		Update
			authentication
		SET
			auth_token = '".addslashes($auth_token)."',
			auth_time = NOW()
		WHERE
			user_id = '".addslashes($segue_user_id)."'		
	";
	
	print $query."<br>";
	$r = db_query($query);	

//no linked user found	
} else {
	print "no linked moodle user found<br>";

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
			auth_time = NOW()
		";
	print $query."<br>";
//	exit;
	$r = db_query($query);
	
	$auth_id = lastid($r);		
	$query = "
		INSERT INTO
			user_link
		SET
			FK_auth_id = '".addslashes($auth_id)."'
		";
	print $query."<br>";
	$r = db_query($query);		
		
}


//exit;
header("Location: ".$moodle_url."/segue/segue_link.php?userid=".addslashes($segue_user_id)."&siteid=".addslashes($segue_site_id)."&auth_token=".addslashes($auth_token));

?>
