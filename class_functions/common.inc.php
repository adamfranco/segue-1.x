<? /* $Id$ */

/**
 * This is a file containing common functions require for syncrhonizing
 * class and user data with our local database
 *
 * It's really just an ugly hack to fix a bug.
 * @copyright 2003 Middlebury College
 */

/**
 * takes a bunch of class info and makes sure that it's in the classes & ugroups
 * tables in the database.
 * @return int The user group ID, either added or fetched from existing row.
 */
function synchronizeClassDB($department, $number, $section, $semester, $year) {
	$class = $department.$number.$section."-".$semester.$year;				
	$ugroup_id = db_get_value("ugroup","ugroup_id","ugroup_name='$class'");
	$classinfo = db_get_line("class","
				class_department='$department' AND
				class_number='$number' AND
				class_section='$section' AND
				class_semester='$semester' AND
				class_year='20$year'");
	
	if (!$ugroup_id) {
									
		$query = "
			INSERT INTO
				ugroup
			SET
				ugroup_name = '$class',
				ugroup_type = 'class'
		";
		db_query($query);
		$ugroup_id = lastid();
	}
	
	if (!$classinfo) {		
		$query = "
			INSERT INTO
				class
			SET
				class_external_id='$class',
				class_department='$department',
				class_number='$number',
				class_section='$section',
				class_semester='$semester',
				class_year='20$year',
				class_name='',
				FK_owner=NULL,
				FK_ugroup=$ugroup_id
		";
		db_query($query);
	}
	return $ugroup_id;
}

/**
 * Takes a class code and calls above function after parsing the code.
 */
function synchronizeClassDBFromCode($code) {
	ereg("([a-zA-Z]{2})([0-9]{3})([a-zA-Z]{0,1})-([lsfw]{1})([0-9]{2})",$code,$r);
	synchronizeClassDB($r[1],$r[2],$r[3],$r[4],$r[5]);
}

/**
 * Takes a system name and checks if it's a class or user. If it's a class,
 * makes sure that the local DB is synchronized with the class data,
 * otherwise, checks the user db
 */
function synchronizeLocalUserAndClassDB($systemName) {
	if ($systemName == 'everyone' || $systemName == 'institute') return;
	if (isclass($systemName)) { // it's a class code
		synchronizeClassDBFromCode($systemName);
	} else { // we're going to assume this is a user
		// look up their email address & full name
		$unames = userlookup($systemName,LDAP_USER,LDAP_EXACT,LDAP_LASTNAME,0,true);
		$info = $unames[$systemName];
		$fullname = $info[0];
		$email = $info[1];
		$type = $info[2];
		// we're going to assume the loginMethod is LDAP -- THIS COULD BE A STUPID MOVE! (remember, this is a hack)
		synchronizeUserDB($systemName,$email,$fullname,$type,"ldap");
		// done and done.
	}
	// that should do it...
}

/**
 * takes user info and returns a user_id that refers to that user data. will
 * add user to the DB if necessary
 */
function synchronizeUserDB($user, $email, $fullname, $type, $loginMethod) {
	$query = "SELECT * FROM user WHERE user_uname='".$user."'";
	$r = db_query($query);	
	if (!db_num_rows($r)) {		// add the user to the DB with $loginMethod
		$query = "INSERT INTO user SET user_uname='$user', user_email='$email', user_fname='$fullname',
				 user_type='$type', user_pass='".strtoupper($loginMethod)." PASS', user_authtype='$loginMethod'";
		$r = db_query($query);
		
		// the query could fail if a user with that username is already in the database, but: (?)
		if (!$r) return 0;
		$id = lastid();
		return $id;
	}
	$r = db_fetch_assoc($r);
	return $r['user_id'];
}