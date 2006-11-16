<? /* $Id$ */

function _valid_ldap($name,$pass,$admin_auser=0) {
	// Check that a password is given to prevent anonymous binds
	if (!strlen($pass))
		return false;
	
//	print "hallooo!";
	$name = strtolower($name);
	global $cfg;
	
	// check if we already have an ldap connection... otherwise, open a new one
	if (!($c = ldap_connect($cfg[ldap_server]))) $c = ldap_connect($cfg[ldap_server]);
	
	// bind as the admin and search for the proper name to bind as
	$admin_ldap_user = $cfg[ldap_voadmin_user_dn];
	$admin_ldap_pass = $cfg[ldap_voadmin_pass];

	$r = @ldap_bind($c,$admin_ldap_user,$admin_ldap_pass);
//	print "<br />@ldap_bind($c,$admin_ldap_user,$admin_ldap_pass); <br /> \"$r\"";
//	if (!$r) print "<br />Could not bind as admin.";
	
	$userSearchDN = (($cfg[ldap_user_dn])?$cfg[ldap_user_dn].",":"").$cfg[ldap_base_dn];
	$searchFilter = "(".$cfg[ldap_username_attribute]."=".$name.")";
	
//	print "<br />$userSearchDN <br />$searchFilter <br />";
	
	$searchResource = ldap_search($c, $userSearchDN, $searchFilter);
	$searchResult = ldap_first_entry($c, $searchResource);
	
//	print "<br />ldap_search($c, $userSearchDN, $searchFilter);";
//	print "<br />ldap_first_entry($c, $searchResource);";
//	print "<br />$searchResult";
	
	if ($searchResult) {
		$userFullBindDN = ldap_get_dn($c, $searchResult);
//		print $userFullBindDN;
	} else {
//		print "<br />no search result";
		return 0;
	}
	
	// bind as the proper user
	$ldap_user = (($admin_auser)?$admin_ldap_user:$userFullBindDN);
	$ldap_pass = ($admin_auser)?$cfg[ldap_voadmin_pass]:$pass;
	
	// No need to unbind, as unbind kills the link, just bind again.
	$r = @ldap_bind($c,$ldap_user,$ldap_pass);
	
//	print "<br />@ldap_bind($c,$ldap_user,$ldap_pass);";
		
	if ($r) { // they're good!
	
		// pull down their info
		$return = array (
			$cfg[ldap_username_attribute], 
			$cfg[ldap_fullname_attribute],
			$cfg[ldap_email_attribute], 
			$cfg[ldap_group_attribute]
		);
		
		$userSearchDN = (($cfg[ldap_user_dn])?$cfg[ldap_user_dn].",":"").$cfg[ldap_base_dn];
		$searchFilter = "(".$cfg[ldap_username_attribute]."=".$name.")";
		
//		print "$name with $pass was in the LDAP database!<br />";//debug
		
		$sr = ldap_search($c,$userSearchDN,$searchFilter,$return);
		$results = ldap_get_entries($c,$sr);
		$results[0] = array_change_key_case($results[0], CASE_LOWER);
		$numldap = ldap_count_entries($c,$sr);
		if (!$numldap) return 0; // if we don't have any entries, return false
		ldap_unbind($c);
		
		$x = array();
		$x[user] = $name;
		$x[pass] = $pass;
		$x[method] = 'ldap';
		$x[fullname] = $results[0][strtolower($cfg[ldap_fullname_attribute])][0];
		$x[email] = $results[0][strtolower($cfg[ldap_email_attribute])][0];

		// are they prof?
		//printpre($results[0]);
		
		if (is_array($results[0][strtolower($cfg[ldap_group_attribute])])) {
			$isProfSearchString = implode("|", $cfg[ldap_prof_groups]);
			foreach ($results[0][strtolower($cfg[ldap_group_attribute])] as $item) {
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
		//printpre($x);
	// 	exit;
		// now check if they're in the database, add if necessary, and get id
		$x = _auth_check_db($x,1);	
		return $x;
				
	}
	return 0;
}

?>
