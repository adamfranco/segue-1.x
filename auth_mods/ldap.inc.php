<? /* $Id$ */

function _valid_ldap($name,$pass,$admin_auser=0) {
//	print "hallooo!";
	$name = strtolower($name);
	global $cfg;
	$ldap_user = $cfg[ldap_user_bind_dn]."=".(($admin_auser)?$cfg[ldap_voadmin_user]:$name);
	$ldap_pass = ($admin_auser)?$cfg[ldap_voadmin_pass]:$pass;

	
	// check if we already have an ldap connection... otherwise, open a new one
	if (!($c = ldap_connect($cfg[ldap_server]))) $c = ldap_connect($cfg[ldap_server]);
	$r = @ldap_bind($c,$ldap_user,$ldap_pass);
	
//	print "@ldap_bind($c,$ldap_user,$ldap_pass);";
		
	if ($r) { // they're good!
	
		// pull down their info
		$return = array (
			$cfg[ldap_username_attribute], 
			$cfg[ldap_fullname_attribute],
			$cfg[ldap_email_attribute], 
			$cfg[ldap_group_attribute]
		);
		
		$dn = $cfg[ldap_base_dn].",".$cfg[ldap_user_dn];
		$filter = $cfg[ldap_username_attribute]."=".$name;		
		
//		print "$name with $pass was in the LDAP database!<BR>";//debug
		
		$sr = ldap_search($c,$dn,$filter,$return);
		$results = ldap_get_entries($c,$sr);
//		print "Found $name's entries: ".ldap_count_entries($c,$sr)."<BR>";//debug
		$numldap = ldap_count_entries($c,$sr);
		if (!$numldap) return 0; // if we don't have any entries, return false
		//ldap_close($c);
		
		$x = array();
		$x[user] = $name;
		$x[pass] = $pass;
		$x[method] = 'ldap';
		$x[fullname] = $results[0][$cfg[ldap_fullname_attribute]][0];
		$x[email] = $results[0][$cfg[ldap_email_attribute]][0];
		// are they prof?
		
		if (is_array($results[0][$cfg[ldap_group_attribute]])) {
			$isProfSearchString = implode("|", $cfg[ldap_prof_groups]);
			foreach ($results[0][$cfg[ldap_group_attribute]] as $item) {
				if (eregi($isProfSearchString,$item)) {
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
