<? /* $Id$ */


function isclass ($class) {
	return 0;
}

function getuserclasses($user,$time="now") {
	$query = "
		SELECT
			class_code,
			class_name,
			class_semester,
			class_year,
			owner.user_uname AS owner_uname,
			owner.user_fname AS owner_fname
		FROM
			user
				INNER JOIN
			ugroup_user
				ON
			user.user_id = ugroup_user.FK_user
				INNER JOIN
			class
				ON
			class.FK_ugroup = ugroup_user.FK_ugroup
				LEFT JOIN
			user AS owner
				ON
			class.FK_owner = owner.user_id
		WHERE
			user.user_uname = '$user'
	";
	$r = db_query($query);
	
	$classes = array();
	$semester = currentsemester();
	
	while ($a = db_fetch_assoc($r)) {
		if ($time == "now" && $a[class_year] == date('Y') && $a[class_semester] == $semester) {
		
		} else if ($time == "past" && ($a[class_year] < date('Y') || semorder($a[class_semester]) < semorder($semester))) {
			$classes[$class_code] = array("code"=>"$a[class_code]","sect"=>"","sem"=>$a[class_semester],"year"=>$a[class_year]);
		} else if ($time == "future" && ($a[class_year] == date('Y') && semorder($a[class_semester]) > semorder($semester)) || ($a[class_year] > date('Y'))) {
			$classes[$a[class_code]] = array("code"=>"$a[class_code]","sect"=>"","sem"=>$a[class_semester],"year"=>$a[class_year]);
		} else if ($time == "all") {
			$classes[$a[class_code]] = array("code"=>"$a[class_code]","sect"=>"","sem"=>$a[class_semester],"year"=>$a[class_year]);
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
