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
	$ugroup_id = db_get_value("ugroup","ugroup_id","ugroup_name='".addslashes($class)."'");
	$classinfo = db_get_line("class","
				class_department='".addslashes($department)."' AND
				class_number='".addslashes($number)."' AND
				class_section='".addslashes($section)."' AND
				class_semester='".addslashes($semester)."' AND
				class_year='20".addslashes($year)."'");
	
	if (!$ugroup_id) {
									
		$query = "
			INSERT INTO
				ugroup
			SET
				ugroup_name = '".addslashes($class)."',
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
				class_external_id='".addslashes($class)."',
				class_department='".addslashes($department)."',
				class_number='".addslashes($number)."',
				class_section='".addslashes($section)."',
				class_semester='".addslashes($semester)."',
				class_year='20".addslashes($year)."'',
				class_name='',
				FK_owner=NULL,
				FK_ugroup='".addslashes($ugroup_id)."'
		";
		db_query($query);
	}
	return $ugroup_id;
}

/**
 * Takes a class code and calls above function after parsing the code.
 */
function synchronizeClassDBFromCode($code) {
	ereg("([a-zA-Z]{2})([0-9]{3})([a-zA-Z]{0,1})-([a-zA-Z]{1,})([0-9]{2})",$code,$r);
	synchronizeClassDB($r[1],$r[2],$r[3],$r[4],$r[5]);
}

/**
 * Takes a system name and checks if it's a class or user. If it's a class,
 * makes sure that the local DB is synchronized with the class data,
 * otherwise, checks the user db
 */
function synchronizeLocalUserAndClassDB($systemName) {
	global $cfg;
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
		synchronizeUserDB($systemName,$email,$fullname,$type,$cfg["network"]=="kenyon"?"pam":"ldap");
		// done and done.
	}
	// that should do it...
}

/**
 * takes user info and returns a user_id that refers to that user data. will
 * add user to the DB if necessary
 */
function synchronizeUserDB($user, $email, $fullname, $type, $loginMethod) {
	
	$query = "
		SELECT 
			* 
		FROM 
			user 
		WHERE 
			user_uname='".addslashes($user)."'
	";
	
	$r = db_query($query);
	
	if (!db_num_rows($r)) {		// add the user to the DB with $loginMethod
		
		//$fullname = addslashes($fullname);
		
		$query = "
			INSERT INTO 
				user 
			SET 
				user_uname='".addslashes($user)."', 
				user_email='".addslashes($email)."', 
				user_fname='".addslashes($fullname)."',
				user_type='".addslashes($type)."', 
				user_pass='".addslashes(strtoupper($loginMethod))." PASS', 
				user_authtype='".addslashes($loginMethod)."'
		";
		
		$r = db_query($query);
		
		// the query could fail if a user with that username is already in the database, but: (?)
		if (!$r) return 0;
		$id = lastid();
		return $id;
	}
	$r = db_fetch_assoc($r);
	return $r['user_id'];
}

/**
 * Break the sitename into parts for searching the classes table
 * and return the where string.
 *
 * @param string sitename
 * @return string
 */
 function getClassWhereClauseForSitename($sitename) {
 	// Break appart sitename into class parts
	ereg("^([a-zA-Z]+)([0-9]+)([a-zA-Z]*)-([a-zA-Z]+)([0-9]{2,4})$", $sitename, $matches);
	$class_department = $matches[1];
	$class_number = $matches[2];
	$class_section = $matches[3];
	$class_semester = $matches[4];
	$class_year = $matches[5];
	
 	return "(class_department = '$class_department'
				AND class_number = '$class_number'
				AND class_section = '$class_section'
				AND class_semester = '$class_semester'
				AND class_year = '$class_year')";
 }

/**
 * Return the id of the ugroup for the specified class
 * 
 * @param classId
 * @return integer
 * @access public
 * @date 10/7/04
 */
function getClassUGroupId ($class_id) {
	global $debug;
	
	$ugroup_id = db_get_value("ugroup","ugroup_id","ugroup_name = '".addslashes($class_id)."'");
	//$ugroup_id = db_get_value("class","FK_ugroup","class_external_id = '$class_id'");
	
	// If we don't have a ugroup id, then maybe we were passed the segue version of
	// the class Id instead of the external Id.
	if (!$ugroup_id) {
		$ugroup_id = db_get_value("class","FK_ugroup", generateTermsFromCode($class_id));
	}
	
	if ($debug && !$ugroup_id)
		printError("Could not find a ugroup id for class_id, '$class_id'"); 
	
	return $ugroup_id;
}

function getClassgroupListsForGroupsContainingClasses($classesArray) {
		global $dbhost, $dbuser, $dbpass, $dbdb;
		
		$classgroupLists = array();
		
		if (!count($classesArray))
			return $classgroupLists;

		$query = "
SELECT
	classgroup_name,
	class_department,
	class_number,
	class_section,
	class_semester,
	class_year
FROM
	class
		INNER JOIN
			classgroup ON FK_classgroup = classgroup_id
WHERE
";
			$i = 0;
			foreach ($classesArray as $className) {
				$query .= "\n\t";
				if ($i > 0)
					$query .= "OR ";
				$query .= "(";
				$query .= generateTermsFromCode($className);
				$query .= ")";
				$i++;
			}
			$query .="
ORDER BY
	classgroup_name
";
		$r = db_query($query);
		if (db_num_rows($r)) {
			while ($a = db_fetch_assoc($r)) {
				if (!isset($classgroupLists[$a['classgroup_name']])) {
					$classgroupLists[$a['classgroup_name']] = array();
				}
				$code = generateCodeFromData(
							$a['class_department'],
							$a['class_number'],
							$a['class_section'],
							$a['class_semester'],
							$a['class_year']);
				$classgroupLists[$a['classgroup_name']][$code] = array(
						'code' => $a['class_department'].$a['class_number'],
						'sect' => $a['class_section'],
						'sem' => $a['class_semester'],
						'year' => $a['class_year']
					);
			}
		}
		
		return $classgroupLists;
	}


