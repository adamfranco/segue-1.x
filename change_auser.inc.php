<? /* $Id$ */
// changes the active user
//include("dbwrapper.inc.php");
$changeauser = strtolower($_REQUEST[changeauser]);
print "change_auser started with $changeauser...<BR>";
$debug = 1;
if ($ltype == 'admin') {	// must be admin to do this:
	print "we are admin.";
	//printpre($_auth_mods);

// we'll check first the db, to see if they're there, then ldap if they're not there.
/* 	if ($r=db_valid($changeauser,"",1)) { */
/* 		$a = db_fetch_assoc($r); */
/* 		$auser = $changeauser; */
/* 		$aemail = $a['email']; */
/* 		$afname = $a['fname']; */
/* 		$atype = $a['type']; */
/* 		$aid = $a['id']; */
/* 		$amethod = 'db'; */
/* 		 */
/* 		log_entry("change_auser","","","","$luser as $auser"); */
/* 	} else if ($res = ldap_valid($changeauser,"",1)) { */
/* 		$aid = db_get_value("users","id","uname='$changeauser'"); */
/* 		$afname = db_get_value("users","fname","uname='$changeauser'"); */
/* 		$aemail = db_get_value("users","email","uname='$changeauser'"); */
/* 		$atype = db_get_value("users","type","uname='$changeauser'"); */
/* 		$auser = $changeauser; */
/* 		$amethod = 'ldap'; */
/* 		 */
/* 		log_entry("change_auser","","","","$luser as $auser"); */
/* 	} */

	$valid = 0;
	foreach ($_auth_mods as $_auth) {
		$func = "_valid_".$_auth;
			print "<BR>AUTH: trying ".$_auth ."..."; //debug
		if ($x = $func($changeauser,"",1)) {
			$valid = 1;
			break;
		}
	}

	if ($valid) {

		$_SESSION[auser] = $changeauser;
		$_SESSION[aemail] = $x[email];
		$_SESSION[afname] = $x[fullname];
		$_SESSION[atype] = $x[type];
		$_SESSION[aid] = $x[id];
		$_SESSION[amethod] = $x[method];
		log_entry("change_auser","$luser as $auser");
	}
}

printpre("<p>$aid, $afname, $auser, $aemail, $atype<BR>");
//exit();
header("Location: index.php?$sid");

?>
	