<? // authentication.inc.php
// handles the authentication of scripts executed and decides if the user needs to be
// authenticated in the first place.
// - this script essentially has the same structure as checklogin.inc.php
// - but adds functionality specific to coursesdb. i may eventually combine these two into one


// include the authentication modules
foreach ($_auth_mods as $i) include("auth_mods/$i.inc.php");

// this array contains a list of actions that don't *require* the user to be authenticated
$actions_noauth = array("site","login","default","previewtheme","fullstory","list","username_lookup");


$loginerror=0;
$_loggedin=0;

$name = strtolower($name);

// first off, if el user is already logged in, lets make sure their info is good
if (session_is_registered("luser")) {
	if (!loginvalid($luser,$lpass,1)) $loginerror=1;
	else $_loggedin=1;
//	include("checklogin.inc.php");
//	return;
} 

// if we're not yet logged in
if (!$_loggedin) {
	if ($loginform) {	// they just entered their name & pass
		// now, assuming they were successful
		if (loginvalid($name,$password)) {
			$newquerystring = ereg_replace("PHPSESSID","OLDID",urldecode($getquery));
			$newurl = ereg_replace("PHPSESSID","OLDID",urldecode($gotourl));
			$_loggedin=1;
//			header("Location: index.php?$sid&$newquerystring");
			if (!ereg('\?',$newurl)) $g = '?';
			//print "$newurl$g&$sid";
			header("Location: $newurl$g&$sid");
		} else {
		// username or passwd incorrect
			$loginerror=1;
		}
	}
	if (!$_loggedin) { // if we still have no login
		
		if ($loginerror) error("The username and password pair you entered is not valid. Please try again.<BR>");
		if ($action) $try = $action;
		else $try = trim($SCRIPT_NAME,"/");
		if (!in_array($try,$actions_noauth)) {
			$loginerror=1;
			error("You must be authenticated to view this page. Please log in above.");
		}
	}
}


function loginvalid($user,$pass,$alreadyloggedin=0) {
	global $lmethod,$lid,$luser,$lpass,$lfname,$lemail,$ltype;
	global $auser,$aemail,$afname,$atype,$aid;
	global $_auth_mods;
		
	// we have two choices in this function. either the user has already logged in
	// or we have to check for them
	if ($alreadyloggedin) {	
//		print "lmethod: $lmethod - $luser<BR>";
		$func = "_valid_".$lmethod;		
		if ($func($user,$pass))		
			return 1;		// ok, they passed the test
		else
			return 0;			
	} else {
		$valid=0;
		
//		$valid = $x = _valid_pam($user,$pass);
//		print_r($_auth_mods);
		
		foreach ($_auth_mods as $_auth) {
			$func = "_valid_".$_auth;
//			print "<BR>AUTH: trying ".$_auth ."..."; //debug
			if ($x = $func($user,$pass)) {
				$valid = 1;
				break;
			}
		}
		$lid = $x[id];
		$luser = $user;
		$lpass = $pass;
		$lfname = $x[fullname];
		$lemail = $x[email];
		$ltype = $x[type];
		$lmethod = $x[method];
		
		if ($valid) {	// register all of the needed variables
						// and send them to the correct page
			
			// set the acting user variables.. default to same as login -- may change later
			$auser = $luser;
			$aemail = $lemail;
			$afname = $lfname;
			$atype = $ltype;
			$aid = $lid;
			$amethod = $lmethod;
			log_entry("login","","","",$luser);
			session_register("luser");
			session_register("lpass");
			session_register("lemail");
			session_register("lfname");
			session_register("ltype");
			session_register("lid");
			session_register("lmethod");
			session_register("auser");
			session_register("aemail");
			session_register("afname");
			session_register("atype");
			session_register("aid");
			session_register("amethod");
			return 1;
				
		} else return 0;
	}
	return 0;
}


function _auth_check_db($x,$add_to_db=0) {
	// check to see if the user is already in the db... if not, add their info (if add_to_db is set)
	// $x is an array that contains user info
	// $x[user] and $x[method] must be set
	global $dbuser,$dbhost,$dbpass,$dbdb;
	db_connect($dbhost, $dbuser, $dbpass, $dbdb);
	$query = "select * from users where uname='".$x[user]."' and status='".$x[method]."'";
	$r = db_query($query);	
	if (db_num_rows($r)) {		// they have an entry already -- pull down their info
		$a = db_fetch_assoc($r);
		$x[fullname] = $a[fname];
		$x[email] = $a[email];
		$x[type] = $a[type];
		$x[id] = $a[id];
		
		// return the new array with info
		return $x;
	} else {					// they have no database entry
		if ($add_to_db) {		// add them to the database and return new id
			$query = "insert into users set uname='$x[user]', email='$x[email]', fname='$x[fullname]', type='$x[type]', pass='".strtoupper($x[method])." PASS', status='$x[method]'";
			$r = db_query($query);
			// if (!$r) error occured;
			$x[id] = lastid();
			return $x;
		} else { return 0; } // no database entry, don't add to db, so return 0
	}
}
	
/* 	if ($num==0) {		// no entries w/ that name */
/* 		// add them to the db */
/* 		$fname = $results[0]["cn"][0]; */
/* 		if (ereg(",",$fname)) {			// if there's a comma, change name from "Schine, Gabriel B" to "Gabriel B Schine" */
/* 			$vars = split(",",$fname); */
/* 			$fname = $vars[1] . " " . $vars[0]; */
/* 		} */
/* 		$uname=$name; */
/* 		$email = $results[0]["mail"][0]; */
/* 		// let's find out what kind of user they are */
/* 		$areprof=0; */
/* 		foreach ($results[0]["memberof"] as $item) { */
/* 			if (eregi("all_faculty",$item)) { */
/* 				$areprof=1; */
/* 			} */
/* 		} */
/* 		$usertype = ($areprof)?"prof":"stud"; */
/*  */
/* 		$status = 'ldap'; */
/* 		$query = "insert into users set uname='$uname', email='$email', fname='$fname',type='$usertype',pass='LDAP PASS',status='ldap'"; */
/* 		db_query($query); */
/* 	} */
/* } */
