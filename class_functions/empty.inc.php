<? /* $Id$ */


function isclass ($class) {
	global $auser,$_isclass_cache;
	if (isset($_isclass_cache[$class])) return $_isclass_cache[$class];
	$auser = strtolower($auser);
	$v = ereg("^(([a-zA-Z]{1,})([0-9]{1,})([a-zA-Z]{0,})-([lsfw]{1})([0-9]{2}))$",$class);
	if (!$v && isgroup($class)) $v = 1;
	$_isclass_cache[$class] = $v;
	return $v;
}

function getuserclasses($user,$time="now") {
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
			if ($time == "now" && ($a[class_year] == date('Y') && $a[class_semester] == $semester)) {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);
			} else if ($time == "past" && (($a[class_year] < date('Y') || semorder($a[class_semester]) < semorder($semester)))) {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);
			} else if ($time == "future" && (($a[class_year] == date('Y') && semorder($a[class_semester]) > semorder($semester)) || ($a[class_year] > date('Y')))) {
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
	ereg("([a-zA-Z]{1,})([0-9]{1,})([a-zA-Z]{0,})-([lsfw]{1})([0-9]{2})",$code,$r);
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

function coursefoldersite($cl) {
	return 0;
}

function ldapfname($uname) {
	return "n/a";
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
	$usernames = array_merge($db_users,$usernames);	

	if ($lc && $usernames) {
		foreach ($usernames as $u=>$f)
			$usernames[strtoupper($u)] = $usernames[strtolower($u)] = $f;
	}
	
	return $usernames;
}

?>
