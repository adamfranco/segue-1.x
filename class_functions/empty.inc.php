<? /* $Id$ */


function isclass ($class) {
	return 0;
}

function getuserclasses($user,$time="now") {
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
	$r = db_query($query);
	while ($a = db_fetch_assoc($r)) {
		$class_code = generateCodeFromData($a[class_department],$a[class_number],$a[class_section],$a[class_semester],$a[class_year]);
/* 		print "<pre>"; print_r($a); print "</pre>"; */
/* 		print $class_code."<br>"; */
		if (!$classes[$class_code]) {
			if ($time == "now" && $a[class_year] == date('Y') && $a[class_semester] == $semester) {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);
			} else if ($time == "past" && ($a[class_year] < date('Y') || semorder($a[class_semester]) < semorder($semester))) {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);
			} else if ($time == "future" && ($a[class_year] == date('Y') && semorder($a[class_semester]) > semorder($semester)) || ($a[class_year] > date('Y'))) {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);
			} else if ($time == "all") {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);
			}
		}
	}
	
	return $classes;
}

function coursefoldersite($cl) {
	return 0;
}

function ldapfname($uname) {
	return "n/a";
}

function userlookup($name,$type=LDAP_BOTH,$wild=LDAP_WILD,$n=LDAP_LASTNAME,$lc=0) {
	
	$usernames = array();

/******************************************************************************
 * add in the db users
 ******************************************************************************/
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
	$usernames = array_merge($db_users,$usernames);	

	if ($lc && $usernames) {
		foreach ($usernames as $u=>$f)
			$usernames[strtoupper($u)] = $usernames[strtolower($u)] = $f;
	}
	
	return $usernames;
}

?>
