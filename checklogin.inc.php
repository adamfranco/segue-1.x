<? /* $Id$ */

// the atype, aemail, a**** variables are the "acting user" variables... they define
// who is acting. for stud and prof, these will match l**** but admins can set them themselves

$error = 0;


// if they have already logged in, or there is a session in their name, check if it's still valid
if (session_is_registered("luser")) {
	if (($lmethod != 'db' && ldap_valid($luser,$lpass)) || db_valid($luser,$lpass)) {
		if (!$error) return;		// ok, they passed the test
	} else {
	   error("login"); $error=1;
	}
}

//
// Let's check if there's an existing session - if so, destroy it.
if (session_is_registered("coursesdb")) {
  session_unset();
  session_destroy();
}
session_start();
$coursesdb="Loaded";
session_register("coursesdb");
$sid = SID;

if (!isset($name)) {  // they have not yet entered any login info
	//include("login.inc.php");
	printc("You must be authenticated to view this page. Please log in above.");
} else {
	$valid=0;
	// first off, if the ldap name and password works, use that
	if ($results = ldap_valid($name,$password)) {
		$valid=1;
		$lid = db_get_value("user","user_id","user_uname='$name'");
		$luser = $name;
		$lpass = $password;
		$fname = $results[0]["cn"][0];
		if (ereg(",",$fname)) {			// if there's a comma, change name from "Schine, Gabriel B" to "Gabriel B Schine"
			$vars = split(",",$fname);
			$fname = $vars[1] . " " . $vars[0];
		}
		$lfname = $fname;
		$lemail = $results[0]["mail"][0];
		$ltype = db_get_value("users","type","uname='$name'");
//		$placement = $a[placement];
		$lmethod = 'ldap';
	// otherwise, use the database
	} else if ($r = db_valid($name,$password)) {
		$valid=1;
		$a = db_fetch_assoc($r);
		$lid = $a[user_id];
		$luser = $name;
		$lpass = $password;
		$lfname = $a[user_fname];
		$lemail = $a[euser_mail];
		$ltype = $a[user_type];
		$lmethod = 'db';
	// otherwise, they just entered an incorrect password
	} else {
		error("login");
		printc("The username and password you entered are invalid.");
//		include("login.inc.php");
	}
	if ($valid) {	// register all of the needed variables
					// and send them to the correct page
		
		// set the acting user variables.. default to same as login -- may change later
		$auser = $luser;
		$aemail = $lemail;
		$afname = $lfname;
		$atype = $ltype;
		$aid = $lid;
		log_entry("login","$luser logged in");
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

//		header("Location: index.php?$sid");
			
	}
}

function db_valid($name,$pass,$admin_auser=0) {
	global $dbhost, $dbuser,$dbpass, $dbdb;
	db_connect($dbhost,$dbuser,$dbpass,$dbdb);
	$query = "SELECT * FROM user WHERE user_uname='$name'".(($admin_auser)?"":" AND user_pass='$pass' and user_authtype='db'");
//	print $query; // debug
	$r = db_query($query);
	//$a = db_fetch_assoc($r);
	
	//if (db_num_rows($r)  && $a['pass'] == $pass) {
	if (db_num_rows($r)) {
		return $r;
	}
	return 0;
}

function ldap_valid($name,$pass,$admin_auser=0) {
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
		ldap_close($c);
		
		// check if they're in the database yet
		db_connect($dbhost, $dbuser, $dbpass, $dbdb);
		$query = "SELECT * FROM user WHERE user_uname='$name'";
		$res = db_query($query);
		$num = db_num_rows($res);
//		print "res=$res num=$num<BR>";//debug
		if ($num==0) {		// no entries w/ that name
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
			$query = "INSERT INTO user SET user_uname='$uname', user_email='$email', user_fname='$fname',user_type='$usertype',user_pass='LDAP PASS',user_authtype='ldap'";
			db_query($query);
		} else {
			$a = db_fetch_assoc($res); // get their info
//			print "They were already in the users db.<BR>";//debug
			if ($a[user_authtype] != 'ldap') { // looks like they are valid w/ ldap, but don't have the ldap status set
				// let's repair this
//				print "for some reason, ldap status was not set for them... updating...<BR>";//debug
				$query = "UPDATE user SET user_authtype='ldap' WHERE user_id=$a[id]";
				db_query($query);
			}
			// Otherwise everything is dandy!
		}
	//	print "LDAP_valid: done.<BR>";//debug
		return $results;
	}
	return 0;
}

// this exit line is very important!!! do no remove it!
exit();

?>
