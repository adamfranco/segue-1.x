<? // change_auser.inc.php
// changes the active user
print "ok";
/*
ob_start();
session_start();
include("config.php");
include("dbwrapper.php");
include("error.php");
//include("checklogin.php");
include("validate.php");

//print "change_auser started with $name...<BR>";

if ($ltype == 'admin') {	// must be admin to do this:
	print "we are admin.";
	
// we'll check first the db, to see if they're there, then ldap if they're not there.
	if ($r=db_valid($changeauser,"")) {
	
	$query = "select * from users where uname='$name'";	
	$r = db_query($query);
		$a = db_fetch_assoc($r);
		$luser = $changeauser;
		$lemail = $a['email'];
		$lfname = $a['fname'];
		$ltype = $a['type'];
		$aid = $a['id'];
		$lmethod = 'db';
		
		//log_entry("change_auser","$luser as $auser");
	} else if ($res = ldap_valid($changeauser,"")) {
		$lid = db_get_value("users","id","uname='$changeauser'");
		$lfname = db_get_value("users","fname","uname='$changeauser'");
		$lemail = db_get_value("users","email","uname='$changeauser'");
		$ltype = db_get_value("users","type","uname='$changeauser'");
		$luser = $changeauser;
		$lmethod = 'ldap';
		
		//log_entry("change_auser","$luser as $auser");
	}
	if ($ltype == 'admin') {header("Location: admin.php?$sid");}
	if ($ltype == 'prof') {header("Location: prof.php?$sid");}
	if ($ltype == 'stud') {header("Location: stud_classes.php?$sid");}

}

//print "$aid, $afname, $auser, $aemail, $atype<BR>";

//header("Location: index.php?$sid");
*/
?>
	