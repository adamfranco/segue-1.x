<? /* $Id$ */

// include the common class functions
require_once(dirname(__FILE__)."/common.inc.php");

function isclass ($class) {
	global $auser,$_isclass_cache;
	
	if (isset($_isclass_cache[$class])) 
		return $_isclass_cache[$class];
		
	$auser = strtolower($auser);
	
	// Check the name against the form of our class codes
	$isClass = ereg("^(([a-zA-Z]{1,})([0-9]{1,})([a-zA-Z]{0,})-([a-zA-Z]{1,})([0-9]{2}))$",$class);
	
	// If this isn't a class, but itself, but is a class group, then it is a class.
	if (!$isClass && isgroup($class)) 
		$isClass = TRUE;
	
	$_isclass_cache[$class] = $isClass;
	return $isClass;
}

/******************************************************************************
 * getclassstudents queries LDAP for all the students in a class
 ******************************************************************************/

function getclassstudents($class_id) {
	global $cfg;
	
	$classes = array();
	$query = "
	SELECT
		class_external_id,
		class.FK_owner AS class_owner_id,
		classgroup.FK_owner AS classgroup_owner_id
	FROM 
		class
			LEFT JOIN 
		classgroup ON FK_classgroup = classgroup_id
	WHERE 
		classgroup_name = '$class_id'
		OR class_external_id = '$class_id'
	";

	$r = db_query($query);
	while ($resultArray = db_fetch_assoc($r)) {
		$classes[] = array (
			'class_owner_id' => $resultArray['class_owner_id'],
			'classgroup_owner_id' => $resultArray['classgroup_owner_id'],
			'class_id' => $resultArray['class_external_id']
		);
	}
	
	/******************************************************************************
	 * DB Class info: queries ugroup_user table for all users who are part
	 * of the $class_id group
 	******************************************************************************/

	$allparticipants = array();
	foreach ($classes as $class_array) {
		$class_id = $class_array['class_id'];
 		
 		if ($class_array['classgroup_owner_id'])
 			$owner_id = $class_array['classgroup_owner_id'];
 		else
 			$owner_id = $class_array['class_owner_id'];

		$ugroup_id = getClassUGroupId($class_id);
			
		$db_participants = array();
		$external_memberlist_participants = array();
		$participant = array();
		$participants = array();
		
		$query = "
		SELECT
			user_id,
			user_fname,
			user_uname,
			user_email,
			user_type
		FROM
			ugroup_user
				INNER JOIN
			user
				ON
			FK_user = user_id
		WHERE
			FK_ugroup = $ugroup_id
		ORDER BY
			user_type DESC, user_uname
		";
		$r = db_query($query);
			
		while ($a = db_fetch_assoc($r)) {
			$participant[id] = $a[user_id];
			$participant[fname] = $a[user_fname];
			$participant[uname] = $a[user_uname];
			$participant[email] = $a[user_email];
			$participant[type] = $a[user_type];
			$participant[memberlist] = "db";
			$db_participants[$a[user_uname]]= $participant;
		}
		
		
		/******************************************************************************
		 * External member list source (e.g. LDAP group member
		 ******************************************************************************/
		$external_memberlist_participants = array();
		$external_memberlist_participant_unames = array();
		
		/******************************************************************************
		* Compile definitive participant list from:
		* $db_participants = all group members whose membership is defined in ugroup_user
		* $external_memberlist_participants = all group members whose membership is
		* determined by an external membership list (e.g. ldap group)
		* if participant is in ugroup_user only then memberlist is db
		* if participant is in external member list only then memberlist is external
		* if participant is in both ugroup_user and external member list then
		* member list is external
		 ******************************************************************************/
		 
		$participants = $external_memberlist_participants;
		$participants_unames = $external_memberlist_participant_unames;
		 
		foreach (array_keys($db_participants) as $key) {
			if (!in_array($db_participants[$key][uname], $external_memberlist_participant_unames)) {
				$participants[$db_participants[$key][uname]] = $db_participants[$key];
				$participants_unames = $db_participants[$key][uname];
			}			
		}	
		
		/******************************************************************************
		 * add participants of current class to array of participants from all classes
		 * (relevant when a site is a group of classes...)
		 ******************************************************************************/
		$allparticipants = array_merge($allparticipants,$participants);

	}
	//return $participants;
	return $allparticipants;

}


function getuserclasses($user,$time="all") {
	$user = strtolower($user);
	$classes = array();
	$semester = currentsemester ();

	// add in the DB classes
	$query = "
		SELECT
			class_department,
			class_number,
			class_section,
			class_semester,
			class_year
		FROM
			user
				INNER JOIN
			ugroup_user
				ON
			user_id = FK_user
				INNER JOIN
			class
				ON
			class.FK_ugroup = ugroup_user.FK_ugroup
		WHERE
			user_uname = '$user'
	";
	$semester = currentsemester ();
	$r = db_query($query);
	while ($a = db_fetch_assoc($r)) {
		$class_code = generateCodeFromData($a[class_department],$a[class_number],$a[class_section],$a[class_semester],$a[class_year]);
		if (!$classes[$class_code]) {
			if ($time == "now" && isSemesterNow($a[class_semester], $a[class_year])) {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);

			} else if ($time == "past" && isSemesterPast($a[class_semester], $a[class_year])) {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);

			} else if ($time == "future" && isSemesterFuture($a[class_semester], $a[class_year])) {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);

			} else if ($time == "all") {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);
			}
		}
	}
	return $classes;
}

function generateCourseCode($id) {
	$query = "
		SELECT
			class_department,
			class_number,
			class_section,
			class_semester,
			class_year
		FROM
			class
		WHERE
			class_id = $id
	";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$code = $a[class_department].$a[class_number].$a[class_section]."-".$a[class_semester].substr($a[class_year],2);
	return $code;
}

function generateCodeFromData($dept,$number,$section,$semester,$year,$ext_id="",$owner="") {
	$code = $dept.$number.$section."-".$semester.substr($year,2);
	return $code;
}

function generateTermsFromCode($code) {
	ereg("([a-zA-Z]{1,})([0-9]{1,})([a-zA-Z]{0,})-([a-zA-Z]{1,})([0-9]{2})",$code,$r);
	$department = $r[1];
	$number = $r[2];
	$section = $r[3];
	$semester = $r[4];
	$year = "20".$r[5];
	
	$terms = "
		class_department='$department' AND
		class_number='$number' AND
		class_section='$section' AND
		class_semester='$semester' AND
		class_year='$year'
	";
	return $terms;
}

//This function checks for non-Segue sites (those in web courses database created in course folders)
function coursefoldersite($cl) {
	return 0;
}

function ldapfname($uname) {
	$uname = strtolower($uname);
	if (isgroup($uname)) return "Students in group";
	if (isclass($uname)) return "Students in class";
	if ($fname = db_get_value("user","user_fname","user_uname='$uname'")) return $fname;
	else return "n/a";
}

function userlookup($name,$type=LDAP_BOTH,$wild=LDAP_WILD,$n=LDAP_LASTNAME,$lc=0) {
	$usernames = array();
	$query = "
		SELECT
			user_uname,
			user_fname
		FROM
			user
		WHERE
			user_uname LIKE '%$name%'
				OR
			user_fname LIKE '%$name%'
	";
	global $dbhost, $dbuser,$dbpass, $dbdb;
	db_connect($dbhost, $dbuser, $dbpass, $dbdb);
	$r = db_query($query);
	$db_users = array();
	while ($a = db_fetch_assoc($r)) {
		$db_users[$a[user_uname]] = $a[user_fname];
	}

/******************************************************************************
 * 	add in the ugroups
 ******************************************************************************/
	$query = "
		SELECT
			ugroup_name
		FROM
			ugroup
		WHERE
			ugroup_name LIKE '%$name%'
	";
		$r = db_query($query);
	$ugroups = array();
	while ($a = db_fetch_assoc($r)) {
		$ugroups[$a[ugroup_name]] = $a[ugroup_name]." (Group)";
	}
	
	$usernames = array_merge($db_users,$usernames,$ugroups);	

	if ($lc && $usernames) {
		foreach ($usernames as $u=>$f)
			$usernames[strtoupper($u)] = $usernames[strtolower($u)] = $f;
	}
	
	return $usernames;
}

?>
