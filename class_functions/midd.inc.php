<? /* $Id$ */

// include the common class functions
require_once("class_functions/common.inc.php");

$_isclass_cache = array();
function isclass ($class) {
	global $auser,$_isclass_cache;
	if (isset($_isclass_cache[$class])) return $_isclass_cache[$class];
	$auser = strtolower($auser);
	$v = ereg("^(([a-zA-Z]{0,})([0-9]{1,})([a-zA-Z]{0,})-([lsfw]|bl{1})([0-9]{2}))$",$class);
	if (!$v && isgroup($class)) $v = 1;
	$_isclass_cache[$class] = $v;
	return $v;
}

function getuserclasses($user,$time="all") {
	$user = strtolower($user);
	global $cfg;
	
	$ldap_user = $cfg[ldap_voadmin_user_dn];
	$ldap_pass = $cfg[ldap_voadmin_pass];

	$classes = array();

	if (!$user)
		return $classes;
	
	$c = ldap_connect($cfg[ldap_server]);
	$r = @ldap_bind($c,$ldap_user,$ldap_pass);
	if ($r && true) {		// connected & logged in
	
		$return = array(
			$cfg[ldap_username_attribute],
			$cfg[ldap_fullname_attribute],
			$cfg[ldap_email_attribute],
			$cfg[ldap_group_attribute]
		);
		$userSearchDN = (($cfg[ldap_user_dn])?$cfg[ldap_user_dn].",":"").$cfg[ldap_base_dn];
		$searchFilter = "(".$cfg[ldap_username_attribute]."=".$user.")";
		
		$sr = ldap_search($c,$userSearchDN,$searchFilter,$return);
		$res = ldap_get_entries($c,$sr);
		if ($res['count']) {
			$res[0] = array_change_key_case($res[0], CASE_LOWER);
	//		print "<pre>";print_r($res);print"</pre>";
			$num = ldap_count_entries($c,$sr);
	//		print "num: $num<br>";
			ldap_close($c);
			if ($num) {
				for ($i = 0; $i<$res[0][strtolower($cfg[ldap_group_attribute])]['count']; $i++) {
					$f = $res[0][strtolower($cfg[ldap_group_attribute])][$i];
	//				print "$f<br>";
					$parts = explode(",",$f);
					foreach ($parts as $p) {
	//					print "$p<br>";
	//					if (ereg("cn=([a-zA-Z]{0,3})([0-9]{1,3})([a-zA-Z]{0,1})-([lsfw]|bl{1})([0-9]{2})",$p,$r)) {
						if (eregi($cfg[ldap_groupname_attribute]."=([a-zA-Z]{0,4})([0-9]{1,4})([a-zA-Z]{0,1})-([lsfw]|bl{1})([0-9]{2})",$p,$r)) {
	//						print "goood!";
							$semester = currentsemester ();
	/* 						print "<pre>"; */
	/* 						print_r($r); */
	/* 						print "</pre>"; */
							$class = $r[1].$r[2].$r[3]."-".$r[4].$r[5];
	/******************************************************************************
	 * update the classes table with the ldap information
	 ******************************************************************************/
							$sem = $r[4];
							$year = $r[5];					
							$user_id = db_get_value("user","user_id","user_uname = '$user'");
							$ugroup_id = db_get_value("ugroup","ugroup_id","ugroup_name='$class'");
							$classinfo = db_get_line("class","
										class_department='$r[1]' AND
										class_number='$r[2]' AND
										class_section='$r[3]' AND
										class_semester='$sem' AND
										class_year='20$r[5]'");
							
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
										class_department='$r[1]',
										class_number='$r[2]',
										class_section='$r[3]',
										class_semester='$sem',
										class_year='20$r[5]',
										class_name='',
										FK_owner=NULL,
										FK_ugroup=$ugroup_id
								";
								db_query($query);
							}
							
							$ugroup_userinfo = db_get_line("ugroup_user","FK_ugroup=$ugroup_id AND FK_user=$user_id");
	
							if (!$ugroup_userinfo) {
								$query = "
									INSERT INTO
										ugroup_user
									SET
										FK_ugroup = $ugroup_id,
										FK_user = $user_id
								";
								db_query($query);
							}
							
	/******************************************************************************
	 * end update
	 ******************************************************************************/
	
							if ($time == "now" && $r[5] == date('y') && semorder($r[4]) == semorder($semester)) {
	//							print "<br>------------>now<br>";
								$classes[$class] = array("code"=>"$r[1]$r[2]","sect"=>$r[3],"sem"=>$r[4],"year"=>$r[5]);
							} else if ($time == "past" && ($r[5] < date('y') || semorder($r[4]) < semorder($semester))) {
	//							print "<br>------------>past<br>";
								$classes[$r[1].$r[2].$r[3]."-".$r[4].$r[5]] = array("code"=>"$r[1]$r[2]","sect"=>$r[3],"sem"=>$r[4],"year"=>$r[5]);
							} else if ($time == "future" && (($r[5] == date('y') && semorder($r[4]) > semorder($semester)) || ($r[5] > date('y')))) {
	//							print "<br>------------>future<br>";
								$classes[$r[1].$r[2].$r[3]."-".$r[4].$r[5]] = array("code"=>"$r[1]$r[2]","sect"=>$r[3],"sem"=>$r[4],"year"=>$r[5]);
							} else if ($time == "all") {
	//							print "<br>------------>all<br>";
								$classes[$r[1].$r[2].$r[3]."-".$r[4].$r[5]] = array("code"=>"$r[1]$r[2]","sect"=>$r[3],"sem"=>$r[4],"year"=>$r[5]);
							}
						}
					}
				}
			}
		}
	}
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
			if ($time == "now" && ($a[class_year] == date('Y') && semorder($a[class_semester]) == semorder($semester))) {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);
			} else if ($time == "past" && ($a[class_year] < date('Y') || ($a[class_year] == date('Y') && semorder($a[class_semester]) < semorder($semester)))) {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);
			} else if ($time == "future" && (($a[class_year] == date('Y') && semorder($a[class_semester]) > semorder($semester)) || ($a[class_year] > date('Y')))) {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);
			} else if ($time == "all") {
				$classes[$class_code] = array("code"=>"$class_code","sect"=>$a[class_section],"sem"=>$a[class_semester],"year"=>$a[class_year]);
			}
		}
	}
//	print "<pre>$time";print_r($classes);
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
	ereg("([a-zA-Z]{0,})([0-9]{1,})([a-zA-Z]{0,})-([lsfw]|bl{1})([0-9]{2})",$code,$r);
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
	global $cfg;
	db_connect($cfg[coursefolders_host],$cfg[coursefolders_username],$cfg[coursefolders_password],$cfg[coursefolders_db]);
	if (ereg("([a-zA-Z]{2})([0-9]{3})([a-zA-Z]{0,1})-([lsfw]{1})([0-9]{2})",$cl,$regs)) {
		$class = $regs[1].$regs[2].$regs[3];

		$curr_semester = $regs[4];	
		if ($curr_semester == "f") {
			$semester = "Fall";
		} else if ($curr_semester == "s"){
			$semester = "Spring";
		} else if ($curr_semester == "w"){
			$semester = "Winter";
		} else if ($curr_semester == "l"){
			$semester = "Summer";
		} 
					
		$curr_year = $regs[5];
		if ($curr_year > 95) {
			$year = "19".$curr_year;
		} else {
			$year = "20".$curr_year;
		}	
	}
	$query = ("select * from ".$cfg[coursefolders_table]." where ".$cfg[coursefolders_coursecode_column]." = '$class' and ".$cfg[coursefolders_semester_column]." = '$semester' and ".$cfg[coursefolders_year_column]." = '$year'");
	$r = db_query($query);	
	if (db_num_rows($r)) {
		$a = db_fetch_assoc($r);
		$title = $a[$cfg[coursefolders_title_column]];
		$url = $a[$cfg[coursefolders_url_column]];
		$site_info = array('title' => $title, 'url' => $url);
		return $site_info;
	}	
}

function ldapfname($uname) {
	$uname = strtolower($uname);
	if (isgroup($uname)) return "Students in group";
	if (isclass($uname)) return "Students in class";
	if ($fname = db_get_value("user","user_fname","user_uname='$uname'")) return $fname;
	$r = userlookup($uname,LDAP_USER,LDAP_EXACT,LDAP_LASTNAME,1);
	return $r[$uname];
}

define("LDAP_USER",1);
define("LDAP_FNAME",2);
define("LDAP_BOTH",3);
define("LDAP_WILD",1);
define("LDAP_EXACT",0);
define("LDAP_LASTNAME",0);
define("LDAP_FIRSTNAME",1);

function userlookup($name,$type=LDAP_BOTH,$wild=LDAP_WILD,$n=LDAP_LASTNAME,$lc=0,$extra=false) {
	$name = strtolower($name);
	global $cfg;
	$ldap_user = $cfg[ldap_voadmin_user_dn];
	$ldap_pass = $cfg[ldap_voadmin_pass];
	
	$wc = ($wild==LDAP_WILD)?"*":"";
	
	$c = ldap_connect($cfg[ldap_server]);
	$r = ldap_bind($c,$ldap_user,$ldap_pass);
	if ($r) {
		$return = array(
			$cfg[ldap_username_attribute], 
			$cfg[ldap_fullname_attribute]
		);
		
		if ($extra) {
			$return[] = $cfg[ldap_email_attribute]; 
			$return[] = $cfg[ldap_group_attribute]; 
		}
		$dn = (($cfg[ldap_user_dn])?$cfg[ldap_user_dn].",":"").$cfg[ldap_base_dn];
		if ($type == LDAP_USER) $filter = $cfg[ldap_username_attribute]."=$wc$name$wc";
		if ($type == LDAP_FNAME) $filter = $cfg[ldap_fullname_attribute]."=$wc$name$wc";
		if ($type == LDAP_BOTH) $filter = "(|(".$cfg[ldap_username_attribute]."=$wc$name$wc)(".$cfg[ldap_fullname_attribute]."=$wc$name$wc))";
		
		$sr = ldap_search($c,$dn,$filter,$return);
		$res = ldap_get_entries($c,$sr);
		if ($res['count']) {
			$res[0] = array_change_key_case($res[0], CASE_LOWER);
			$num = ldap_count_entries($c,$sr);
			ldap_close($c);
	/* 		print "<pre>"; */
	/* 		print_r($res); */
	/* 		print "</pre>"; */
			if ($num) {
				$usernames = array();
				for ($i = 0; $i<$res['count'];$i++) {
					$uid = $res[$i][strtolower($cfg[ldap_username_attribute])][0];
					$fname = $res[$i][strtolower($cfg[ldap_fullname_attribute])][0];
					if (!ereg(",",$fname) && !$n) {
						$vars = split(" ",$fname);
						if (count($vars) == 2)
							$fname = $vars[1] . ", " . $vars[0];
						if (count($vars) == 3)
							$fname = $vars[2] . ", " . $vars[0] . " " . $vars[1]; // for Gabriel B. Schine names
					}
	//				$res[$i]['cn'][0] = $fname;
					if ($extra) {
						// we must find out if they are a professor or a student.
						$areprof = false;
						if (is_array($res[0][strtolower($cfg[ldap_group_attribute])])) {
							$isProfSearchString = implode("|", $cfg[ldap_prof_groups]);
							foreach ($results[0][strtolower($cfg[ldap_group_attribute])] as $item) {
								if (eregi($isProfSearchString,$item)) {
									$areprof=1;
								}
							}
						}
						$userType = ($areprof)?"prof":"stud";
						$usernames[strtolower($uid)] = array($fname,$res[$i][strtolower($cfg[ldap_email_attribute])][0],$userType);
					}
					else $usernames[strtolower($uid)] = $fname;
				}
			}
		}
	}

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
