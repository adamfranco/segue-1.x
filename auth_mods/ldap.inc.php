<? /* $Id$ */

function _valid_ldap($name,$pass,$admin_auser=0) {
	$name = strtolower($name);
	global $dbhost, $dbuser, $dbpass, $dbdb, $ldapserver, $ldap_voadmin_user, $ldap_voadmin_pass;
	
	$ldap_user = "cn=".(($admin_auser)?$ldap_voadmin_user:$name).",cn=Recipients,ou=MIDD,o=MC";
	$ldap_pass = ($admin_auser)?$ldap_voadmin_pass:$pass;
	// check if we already have an ldap connection... otherwise, open a new one
	if (!($c = ldap_connect())) $c = ldap_connect($ldapserver);
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
		//ldap_close($c);
		
/* 		// check if they're in the database yet */
/* 		db_connect($dbhost, $dbuser, $dbpass, $dbdb); */
/* 		$query = "select * from users where uname='$name'"; */
/* 		$res = db_query($query); */
/* 		$num = db_num_rows($res); */
/* //		print "res=$res num=$num<BR>";//debug */
/* 		 else { */
/* 			$a = db_fetch_assoc($res); // get their info */
/* //			print "They were already in the users db.<BR>";//debug */
/* 			if ($a[status] != 'ldap') { // looks like they are valid w/ ldap, but don't have the ldap status set */
/* 				// let's repair this */
/* //				print "for some reason, ldap status was not set for them... updating...<BR>";//debug */
/* 				$query = "update users set status='ldap' where id=$a[id]"; */
/* 				db_query($query); */
/* 			} */
/* 			// Otherwise everything is dandy! */
/* 		} */
/* 	//	print "LDAP_valid: done.<BR>";//debug */
/* 		return $results; */

		$x = array();
		$x[user] = $name;
		$x[pass] = $pass;
		$x[method] = 'ldap';
		$x[fullname] = $results[0]["cn"][0];
		$x[email] = $results[0]["mail"][0];
		// are they prof?
		if (is_array($results[0]["memberof"])) {
			foreach ($results[0]["memberof"] as $item) {
				if (eregi("All_Staff",$item) || eregi("All_Faculty",$item)) {
					$areprof=1;
				}
			}
		}
		$x[type] = ($areprof)?"prof":"stud";
		if (ereg(",",$x[fullname])) {			// if there's a comma, change name from "Schine, Gabriel B" to "Gabriel B Schine"
			$vars = split(",",$x[fullname]);
			$fname = $vars[1] . " " . $vars[0];
			$x[fullname] = $fname;
		}
								
		// now check if they're in the database, add if necessary, and get id
		$x = _auth_check_db($x,1);		
		return $x;
				
	}
	return 0;
}

/*

	if ($num==0) {		// no entries w/ that name
		// add them to the db
		$fname = $results[0]["cn"][0];
		if (ereg(",",$fname)) {			// if there's a comma, change name from "Schine, Gabriel B" to "Gabriel B Schine"
			$vars = split(",",$fname);
			$fname = $vars[1] . " " . $vars[0];
		}
		$uname=$name;
		$email = $results[0]["mail"][0];
		// let's find out what kind of user they are
		$areprof=0;
		foreach ($results[0]["memberof"] as $item) {
			if (eregi("all_faculty",$item)) {
				$areprof=1;
			}
		}
		$usertype = ($areprof)?"prof":"stud";

		$status = 'ldap';
		$query = "insert into users set uname='$uname', email='$email', fname='$fname',type='$usertype',pass='LDAP PASS',status='ldap'";
		db_query($query);
	}
	
*/

?>
