<? /* $Id$ */
// changes the active user
//include("dbwrapper.inc.php");
$changeauser = strtolower($_REQUEST[changeauser]);
//print "change_auser started with $changeauser...<br />";
$debug = 0;
if ($ltype == 'admin') {	// must be admin to do this:
//	print "we are admin.";
	//printpre($_auth_mods);

	$valid = 0;
	foreach ($_auth_mods as $_auth) {
		$func = "_valid_".$_auth;
//			print "<br />AUTH: trying ".$_auth ."..."; //debug
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
		
		
		unset($_SESSION["discussion_set"]);
		unset($_SESSION["oldversion"]);
		unset($_SESSION["newversion"]);
		unset($_SESSION["expand_personalsites"]);
		unset($_SESSION["expand_recentactivity"]);
		unset($_SESSION["expand_othersites"]);
		unset($_SESSION["expand_editorsites"]);
		unset($_SESSION["expand_pastclasses"]);
		unset($_SESSION["expand_upcomingclasses"]);
	}
}

//printpre("<p>$aid, $afname, $auser, $aemail, $atype<br />");
//exit();
$getVars = "";
foreach ($_GET as $key => $val) {
		$getVars .= "&".$key."=".$val;
	}
header("Location: index.php?$sid".$getVars);
exit;

?>
	