<? // authentication.inc.php
// handles the authentication of scripts executed and decides if the user needs to be
// authenticated in the first place.
// - this script essentially has the same structure as checklogin.inc.php
// - but adds functionality specific to coursesdb. i may eventually combine these two into one

// this array contains a list of actions that don't *require* the user to be authenticated
$actions_noauth = array("site","login","default","previewtheme","fullstory");


$loginerror=0;
$_loggedin=0;

$name = strtolower($name);

// first off, if el user is already logged in, lets make sure their info is good
if (session_is_registered("luser")) {
	if (!loginvalid($luser,$lpass,$useldap,1)) $loginerror=1;
	else $_loggedin=1;
//	include("checklogin.inc.php");
//	return;
} 

// if we're not yet logged in
if (!$_loggedin) {
	if ($loginform) {	// they just entered their name & pass
		// now, assuming they were successful
		if (loginvalid($name,$password,$useldap)) {
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


function loginvalid($user,$pass,$useldap,$alreadyloggedin=0) {
	global $lmethod,$lid,$luser,$lpass,$lfname,$lemail,$ltype;
	global $auser,$aemail,$afname,$atype,$aid;
	
	// we have two choices in this function. either the user has already logged in
	// or we have to check for them
	if ($alreadyloggedin) {
		if (($lmethod != 'db' && ldap_valid($user,$pass)) || db_valid($user,$pass))
			return 1;		// ok, they passed the test
		else
			return 0;
	} else {
		$valid=0;
		if ($useldap) {
			// first off, if the ldap name and password works, use that
			if ($results = ldap_valid($user,$pass)) {
				$valid=1;
				$lid = db_get_value("users","id","uname='$user'");
				$luser = $user;
				$lpass = $pass;
				$fname = $results[0]["cn"][0];
				if (ereg(",",$fname)) {			// if there's a comma, change name from "Schine, Gabriel B" to "Gabriel B Schine"
					$vars = split(",",$fname);
					$fname = $vars[1] . " " . $vars[0];
				}
				$lfname = $fname;
				$lemail = $results[0]["mail"][0];
				$ltype = db_get_value("users","type","uname='$user'");
				$lmethod = 'ldap';
			// otherwise, use the database
			} else if ($r = db_valid($user,$pass)) {
				$valid=1;
				$a = db_fetch_assoc($r);
				$lid = $a[id];
				$luser = $user;
				$lpass = $pass;
				$lfname = $a[fname];
				$lemail = $a[email];
				$ltype = $a[type];
				$lmethod = 'db';
			// otherwise, they just entered an incorrect password
			} else {
				return 0;
		//		include("login.inc.php");
			}
		} else {
			if ($r = db_valid($user,$pass)) {
				$valid=1;
				$a = db_fetch_assoc($r);
				$lid = $a[id];
				$luser = $user;
				$lpass = $pass;
				$lfname = $a[fname];
				$lemail = $a[email];
				$ltype = $a[type];
				$lmethod = 'db';
			// otherwise, they just entered an incorrect password
			} else {
				return 0;
		//		include("login.inc.php");
			}
		}
		if ($valid) {	// register all of the needed variables
						// and send them to the correct page
			
			// set the acting user variables.. default to same as login -- may change later
			$auser = $luser;
			$aemail = $lemail;
			$afname = $lfname;
			$atype = $ltype;
			$aid = $lid;
			$amethod = $lmethod;
			log_entry("login","$luser");
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
				
		}
	}
	return 0;
}

function db_valid($name,$pass,$admin_auser=0) {
	$name = strtolower($name);
	global $dbhost, $dbuser,$dbpass, $dbdb;
	db_connect($dbhost,$dbuser,$dbpass,$dbdb);
	$query = "select * from users where uname='$name'".(($admin_auser)?"":" and pass='$pass' and status='db'");
//	print $query; // debug
//	$query = "select * from users where uname='$name'and pass='$pass' and status!='ldap'";
//	$query = "select * from users where uname='$name'";
	$r = db_query($query);
//	$a = db_fetch_assoc($r);
	
//	if (db_num_rows($r)  && $a['pass'] == $pass) {
	if (db_num_rows($r)) {
		return $r;
	} /*else {
	    $query = "select * from users where email='$name' and pass='$pass' and status='open'";
	    $r = db_query($query);
	    if (db_num_rows($r)) {
	        $logmethod = "open";
	        return $r;
	    }
	}*/
	return 0;
}

function ldap_valid($name,$pass,$admin_auser=0) {
	$name = strtolower($name);
	global $dbhost, $dbuser, $dbpass, $dbdb, $ldapserver, $ldap_voadmin_user, $ldap_voadmin_pass;
	
	$ldap_user = "cn=".(($admin_auser)?$ldap_voadmin_user:$name).",cn=Recipients,ou=MIDD,o=MC";
	$ldap_pass = ($admin_auser)?$ldap_voadmin_pass:$pass;
//	print "$ldap_user, $ldap_pass<BR>";//debug
	$c = ldap_connect($ldapserver);
	$r = @ldap_bind($c,$ldap_user,$ldap_pass);
	//$r=ldap_bind($c,"cn=gschine,cn=Recipients,ou=MIDD,o=MC","");  //debug
	
	if ($r) { // they're good!
		// pull down their info
		$return = array("uid","cn","mail","extension-attribute-1","memberOf");
		$base_dn = "ou=Midd,o=MC";
		$filter = "uid=$name";
		
//		print "$name with $pass was in the LDAP database!<BR>";//debug
		
		$sr = ldap_search($c,$base_dn,$filter,$return);
		$results = ldap_get_entries($c,$sr);
//		print "Found $name's entries: ".ldap_count_entries($c,$sr)."<BR>";//debug
		$numldap = ldap_count_entries($c,$sr);
		if (!$numldap) return 0; // if we don't have any entries, return false
		ldap_close($c);
		
		// check if they're in the database yet
		db_connect($dbhost, $dbuser, $dbpass, $dbdb);
		$query = "select * from users where uname='$name'";
		$res = db_query($query);
		$num = db_num_rows($res);
//		print "res=$res num=$num<BR>";//debug
		if ($numldap && $num==0) {		// no entries w/ that name
//			print "They were not yet in the users database. Adding...<BR>";//debug
			// add them to the db
			$fname = $results[0]["cn"][0];
			if (ereg(",",$fname)) {			// if there's a comma, change name from "Schine, Gabriel B" to "Gabriel B Schine"
				$vars = split(",",$fname);
				$fname = $vars[1] . " " . $vars[0];
			}
//			print "fname=$fname<BR>";//debug
			$uname=$name;
			$email = $results[0]["mail"][0];
			// let's find out what kind of user they are
//			print "email=$email<BR>";//debug
			$areprof=0;
			foreach ($results[0]["memberof"] as $item) {
				if (eregi("all_faculty",$item)) {
					$areprof=1;
				}
			}
			$usertype = ($areprof)?"prof":"stud";
//			print "type=$usertype<BR>";//debug
			$status = 'ldap';
			$query = "insert into users set uname='$uname', email='$email', fname='$fname',type='$usertype',pass='LDAP PASS',status='ldap'";
			db_query($query);
		} else {
			$a = db_fetch_assoc($res); // get their info
//			print "They were already in the users db.<BR>";//debug
			if ($a[status] != 'ldap') { // looks like they are valid w/ ldap, but don't have the ldap status set
				// let's repair this
//				print "for some reason, ldap status was not set for them... updating...<BR>";//debug
				$query = "update users set status='ldap' where id=$a[id]";
				db_query($query);
			}
			// Otherwise everything is dandy!
		}
	//	print "LDAP_valid: done.<BR>";//debug
		return $results;
	}
	return 0;
}