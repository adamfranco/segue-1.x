<? /* $Id$ */

// handles the authentication of scripts executed and decides if the user needs to be
// authenticated in the first place.
// - this script essentially has the same structure as checklogin.inc.php
// - but adds functionality specific to segue. i may eventually combine these two into one

// include the authentication modules
foreach ($_auth_mods as $i) include("auth_mods/$i.inc.php");

$loginerror=0;
$_loggedin=0;

//$name = strtolower($name);

require_once("auth_not_req_actions.inc.php");

// first off, if el user is already logged in, lets make sure their info is good
if ($_SESSION[luser]) {
	if (!loginvalid($luser,$lpass,1)) $loginerror=1;
	else $_loggedin=1;
} 

// if we're not yet logged in
if (!$_loggedin) {
	if ($_REQUEST[loginform]) {	// they just entered their name & pass
	
		// now, assuming they were successful
		if (loginvalid($_REQUEST[name],$_REQUEST[password])) {
			$newquerystring = ereg_replace("PHPSESSID","OLDID",urldecode($_REQUEST[getquery]));
			$newurl = ereg_replace("PHPSESSID","OLDID",urldecode($_REQUEST[gotourl]));
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
		if ($_REQUEST[action]) $try = $_REQUEST[action];
		if ($action) $try = $action;
		else $try = trim($_SERVER['SCRIPT_NAME'],"/");
		// :: hack for fullstory w/out auth
		if (trim($_SERVER['SCRIPT_NAME'],"/") == "fullstory.php") $try = "fullstory.php";

		if (!in_array($try,$actions_noauth)) {
			$loginerror=1;
			error("You must be authenticated to view this page. Please log in above.");
		}
	}
}


function loginvalid($user,$pass,$alreadyloggedin=0) {
//	global $lmethod,$lid,$luser,$lpass,$lfname,$lemail,$ltype;
//	global $auser,$aemail,$afname,$atype,$aid;
	global $_auth_mods;
		
	// we have two choices in this function. either the user has already logged in
	// or we have to check for them
	if ($alreadyloggedin) {	
//		print "lmethod: $lmethod - $luser<BR>";
		if (!$_SESSION[lmethod]) {
			error("An unknown error happened during authentication. Please <a href='index.php?login'>logout</a> and try again. Ignore the error(s) below.");
			return 0;
		}
		$func = "_valid_".$_SESSION[lmethod];		
/* 		if ($func($user,$pass)) */
/* 			return 1;		// ok, they passed the test */
/* 		else */
/* 			return 0; */
		return 1;
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
//		print "<BR>$valid<BR>";
//		print_r($x);
		
		if ($valid) {	// register all of the needed variables
						// and send them to the correct page
			
			// set the acting user variables.. default to same as login -- may change later
			$_SESSION[aid] = $_SESSION[lid] = $x[id];
			$_SESSION[auser] = $_SESSION[luser] = $user;
//			$_SESSION[lpass] = $pass;
			$_SESSION[afname] = $_SESSION[lfname] = $x[fullname];
			$_SESSION[aemail] = $_SESSION[lemail] = $x[email];
			$_SESSION[atype] = $_SESSION[ltype] = $x[type];
			$_SESSION[amethod] = $_SESSION[lmethod] = $x[method];
			log_entry("login","$_SESSION[luser] authenticated");
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
//	$query = "SELECT * FROM user WHERE user_uname='".$x[user]."' AND user_authtype='".$x[method]."'";
	$query = "SELECT * FROM user WHERE user_uname='".$x[user]."'";
	$r = db_query($query);	
	if (db_num_rows($r)) {		// they have an entry already -- pull down their info
		$a = db_fetch_assoc($r);
		
		// if their authentication method is not db, then sync the db to the other method
		if (strtolower($a[user_authtype]) != "db" 
			&& (
				$x[fullname] != $a[user_fname]
				|| $x[email] != $a[user_email] 
			 	|| 	($x[type] != $a[user_type] 
					&& $a[user_type] != "admin")
			)
		) {
			$x[fullname] = addslashes($x[fullname]);
			$query = "
				UPDATE
					user 
				SET  
					user_email='$x[email]', 
					user_fname='$x[fullname]'
			";
			if ($a[user_type] != "admin") {
				$query .= ", user_type='$x[type]'";
			}
			
			$query .="
				WHERE
					user_uname='$x[user]'
			";
			$r = db_query($query);
		}
		
		if ($a[user_type] == 'admin') {
			$x[type] = $a[user_type];
		}
				
		$x[id] = $a[user_id];
		// return the new array with info
		return $x;
	} else {					// they have no database entry
		if ($add_to_db) {		// add them to the database and return new id
			$x[fullname] = addslashes($x[fullname]);
			$query = "INSERT INTO user SET user_uname='$x[user]', user_email='$x[email]', user_fname='$x[fullname]', user_type='$x[type]', user_pass='".strtoupper($x[method])." PASS', user_authtype='$x[method]'";
			$r = db_query($query);
			
			// the query could fail if a user with that username is already in the database, but:
			if (!$r) return 0;
			//echo $query."<br>";
			// if (!$r) error occured;
			$x[id] = lastid();
			return $x;
		} else { return 0; } // no database entry, don't add to db, so return 0
	}
}
