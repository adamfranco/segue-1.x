<? // script to check login information

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
		$lid = db_get_value("users","id","uname='$name'");
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
		$lid = $a[id];
		$luser = $name;
		$lpass = $password;
		$lfname = $a[fname];
		$lemail = $a[email];
		$ltype = $a[type];
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

//		header("Location: index.php?$sid");
			
	}
}

function db_valid($name,$pass,$admin_auser=0) {
	global $dbhost, $dbuser,$dbpass, $dbdb;
	db_connect($dbhost,$dbuser,$dbpass,$dbdb);
	$query = "select * from users where uname='$name'".(($admin_auser)?"":" and pass='$pass' and status='db'");
//	print $query; // debug
//    $query = "select * from users where uname='$name'and pass='$pass' and status!='ldap'";
	//$query = "select * from users where uname='$name'";
	$r = db_query($query);
	//$a = db_fetch_assoc($r);
	
	//if (db_num_rows($r)  && $a['pass'] == $pass) {
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
		$query = "select * from users where uname='$name'";
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

// this entire function should not be needed for coursesdb
/*
// in this function we will make sure that the student is registered for mots
// classes only and faculty will see all their classes listed in mots
function update_user_classes($ldap_results, $id, $type) {
	global $dbhost,$dbuser,$dbpass, $dbdb;
	db_connect($dbhost, $dbuser, $dbpass, $dbdb);
	$classes = db_get_value("users","classes","id=$id"); // pull down their classes list
	$classes = unserialize($classes);
	$blacklist = unserialize(db_get_value("users","blacklist","id=$id")); // pull down their blacklist
	$status = db_get_value("users","status","id=$id");
	if ($status != 'ldap') {	// they're not an ldap user -- exit
		return;
	}
	
	$memberlist = $ldap_results[0][memberof];
	
	$madeachange = 0;
	
	//may not need this foreach anymore since classes is now an array of course codes
	$classlist = array();
	if ($classes) {
		foreach ($classes as $c) {
			//$code = db_get_value("classes","code","id=$c");
			array_push($classlist,$c);
		}
	}
	
	if (!$classes) $classes = array();
	$blist = array();
	if ($blacklist) {
		foreach ($blacklist as $c) {
			$code = db_get_value("classes","code","id=$c");
			array_push($blist,$code);
		}
	}
	
	foreach ($memberlist as $group) {	// go through each memberof entry and check for classes
		$parts = explode(',',$group);
		$iscourse = $courseinfo = 0;
		foreach ($parts as $p) {
			if ($p == 'cn=Courses')
				$iscourse = 1;
			if (ereg("([a-z]{2})([0-9]{3})([a-z]{1})-([a-z]{1,2})([0-9]{2})",$p,$regs)) { // a class name
				$courseinfo =1;
            }
		}		
			    	
		if ($iscourse && $courseinfo) {		// ok, let's check if they're in the class
			$coursecode = $regs[1] . $regs[2] . $regs[3] ."-". $regs[4] . $regs[5];
			$semester = $regs[4];
			//year info is 2 digits
			$year = $regs[5];
			
		if ($type == 'stud') {
			if (db_num_rows($r=db_query("select code from classes where code='$coursecode'"))) {
			//include("test_functions.php");
				// the course exists in MOTS	
				// let's add the course to student classlist
				$query = "select * from classes where code='$coursecode'";
				$r = db_query($query);
				$a=db_fetch_assoc($r);
				$studlist=unserialize($a[users]);
				
				 if (!$studlist) $studlist = array();
				       
				 if (!in_array($id,$studlist)) {
					  array_push($studlist,$id);
				      // update user array for class in classes table
				      db_query("update classes set users='".serialize($studlist)."' where code='$coursecode'");
			      }		        
	        }
	     }
	     		        
        	
			print "Check if student is in class $coursecode (found in LDAP).<BR>";//debug
			
			if (!in_array($coursecode,$classlist) && !in_array($coursecode,$blist)) {
				// ok, so they are not added or blacklisted to this course
				print "    --> they are not in the class, nor blacklisted.<BR>";//debug
				
				//
				if ($type == 'stud') {
					if (db_num_rows($r=db_query("select code from classes where code='$coursecode'"))) {
						// the course exists in MOTS
						
						// let's add the course to their classlist
						$query = "select * from classes where code='$coursecode'";
						$a=db_fetch_assoc($r);
						array_push($classes,$coursecode);
						print "--> 1 - exists! adding... id=$classid carray=".serialize($classes)."<BR>";//debug
						$madeachange=1; // we made a change to the classes list
						
						//now lets add them to the course user list
						$query = "select * from classes where code='$coursecode'";
						$r = db_query($query);
						$a=db_fetch_assoc($r);
						$studlist=unserialize($a[users]);

						 if (!$studlist) $studlist = array();
						       
						 if (!in_array($id,$studlist)) {
							  array_push($studlist,$id);
						      // update user array for class in classes table
						      db_query("update classes set users='".serialize($studlist)."' where code='$coursecode'");
					      }						
						
					} else { 
					    print "--> 0 - the class doesnt exist in mots<BR>"; 
					} //debug
				}
				
				//add to prof classes array all the classes taught by prof based on LDAP info
				if ($type == 'prof') {		
				   if (!in_array($coursecode,$classlist)) {
						array_push($classes,$coursecode);
						$madeachange=1; // we made a change to the classes list	
					}
			    }
				
			}
			
		}
	}
	if ($madeachange) {	// the classes list was updated
		// only now do we update the db
		$query = "update users set classes='".serialize($classes)."' where id=$id";		
		db_query($query);
		
	}
}
*/

// this exit line is very important!!! do no remove it!
exit();

?>
