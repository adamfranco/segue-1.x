<? /* $Id$ */

// include the common class functions
require_once(dirname(__FILE__)."/common.inc.php");

function isclass ($class) {
	global $_isclass_cache;
	
	if (isset($_isclass_cache[$class])) 
		return $_isclass_cache[$class];
	
	// Check to see if the slot is a class-type slot
	$SlotType = db_get_value("slot", "slot_type", "slot_name ='".addslashes($class)."'");
//	printpre($SlotType);
	
	// Check the name against the form of our class codes
	$isClass = ereg("^(([a-zA-Z]{0,})([0-9]{1,})([a-zA-Z]{0,})-([a-zA-Z]{1,})([0-9]{2}))$",$class);
		
	// If this isn't a class, but itself, but is a class group, then it is a class.
	if (!$isClass && isgroup($class) && $SlotType == "class") 
		$isClass = TRUE;
	
	$_isclass_cache[$class] = $isClass;
	return $isClass;
}

/******************************************************************************
 * getclassstudents queries LDAP for all the students in a class
 ******************************************************************************/

function getclassstudents($class_id) {
	global $cfg;
		
	$whereClassParts = getClassWhereClauseForSitename($class_id);	
		
	$classes = array();
	$query = "
		SELECT
			ugroup_name,
			class_external_id,
			class_department,
			class_number,
			class_section,
			class_semester,
			class_year,
			class.FK_owner AS class_owner_id,
			classgroup.FK_owner AS classgroup_owner_id
		FROM 
			class
				LEFT JOIN 
			classgroup ON FK_classgroup = classgroup_id
				LEFT JOIN
			ugroup ON FK_ugroup = ugroup_id
		WHERE 
			classgroup_name = '".addslashes($class_id)."'
			OR class_external_id = '".addslashes($class_id)."'
			OR $whereClassParts
	";
	
	$r = db_query($query);
	while ($resultArray = db_fetch_assoc($r)) {
		$classes[] = array (
			'class_owner_id' => $resultArray['class_owner_id'],
			'classgroup_owner_id' => $resultArray['classgroup_owner_id'],
			'class_id' => $resultArray['ugroup_name']
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
		$participant = array();	
		$db_participants = array();
		$external_memberlist_participants = array();
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
				FK_ugroup = '".addslashes($ugroup_id)."'
			ORDER BY
				user_type DESC, user_uname
		";
		
//		printpre($query);
		$r = db_query($query);
			
		while ($a = db_fetch_assoc($r)) {
			
			$participant[id] = $a[user_id];
			$participant[fname] = $a[user_fname];
			$participant[uname] = $a[user_uname];
			$participant[email] = $a[user_email];
			$participant[type] = $a[user_type];
			$participant[memberlist] = "db";
			$db_participants[$participant[id]]= $participant;
		}

		/******************************************************************************
		 *  External member list source (e.g. LDAP group member
		 *  LDAP Class info: queries LDAP for users in class group
		 ******************************************************************************/
	
			
		ereg("([a-zA-Z]{0,})([0-9]{1,})([a-zA-Z]{0,})-([a-zA-Z]{1,})([0-9]{2})",$class_id,$r);
		$department = $r[1];
		$number = $r[2];
		$section = $r[3];
		$semester = $r[4];
		$year = $r[5];
		
		if ($semester == "f") {
			$semester = "Fall";
		} else if ($semester == "s"){
			$semester = "Spring";
		} else if ($semester == "w"){
			$semester = "Winter";
		} else if ($semester == "l"){
			$semester = "Summer";
		} 
	
		/******************************************************************************
		 * create class search dn with appropriate semester information
		 ******************************************************************************/
	
		$ldap_search_semester = "ou=".$semester.$year.",ou=classes,ou=groups,";
		//printpre ($ldap_search_semester);
		
		$ldap_user = $cfg[ldap_voadmin_user_dn];
		$ldap_pass = $cfg[ldap_voadmin_pass];
	
		$c = ldap_connect($cfg[ldap_server]);
		$r = @ldap_bind($c,$ldap_user,$ldap_pass);
		if ($r && true) {		// connected & logged in
	
			$return = array(
				$cfg[ldap_groupmember_attribute]
			);
			
			/******************************************************************************
			 * create class search dn and filter
			 * needs to search semester, classes, groups within base domain
			 ******************************************************************************/
			
			$classSearchDN = $ldap_search_semester.$cfg[ldap_base_dn];
			//printpre ("classSearchDN: ".$classSearchDN);
			$classSearchFilter = "(".$cfg[ldap_groupname_attribute]."=".$class_id.")";
			//printpre ("classSearchFilter:".$classSearchFilter);
			
			/******************************************************************************
			 * search ldap with search dn and filter, get results, close ldap connection
			 * results will be list of members of group within a class within a semester
			 ******************************************************************************/
			$sr = ldap_search($c,$classSearchDN,$classSearchFilter,$return);
			$res = ldap_get_entries($c,$sr);
			if ($res['count']) {
				$res[0] = array_change_key_case($res[0], CASE_LOWER);
		//		print "<pre>";print_r($res);print"</pre>";
				$num = ldap_count_entries($c,$sr);
		//		print "num: $num<br />";
				ldap_close($c);
				
				/******************************************************************************
				 * if class found, then get groupmember attributes
				 * these will be list of students in class
				 ******************************************************************************/
	
				if ($num) {
					//$groupmembers = array();
					for ($i = 0; $i<$res[0][strtolower($cfg[ldap_groupmember_attribute])]['count']; $i++) {
						$nextmember = $res[0][strtolower($cfg[ldap_groupmember_attribute])][$i];
						
						/******************************************************************************
						 * for each member (ie student) found, search ldap for their attributes
						 * need, username, fullname at least
						 * (could add group attributes which would list groups they are members of)
						 ******************************************************************************/
	
						$c = ldap_connect($cfg[ldap_server]);
						$r = @ldap_bind($c,$ldap_user,$ldap_pass);
						if ($r && true) {		// connected & logged in
						
							$return2 = array (
								$cfg[ldap_username_attribute], 
								$cfg[ldap_fullname_attribute],
								$cfg[ldap_email_attribute], 
								$cfg[ldap_group_attribute]
							);
							
							$userSearchDN = (($cfg[ldap_user_dn])?$cfg[ldap_user_dn].",":"").$cfg[ldap_base_dn];
							//printpre ("userSearchDN: ".$userSearchDN);
							
							//not sure user search filter below will work
							//search filter user in ldap.inc.php is
							//$searchFilter = "(".$cfg[ldap_username_attribute]."=".$name.")";
							
							$userSearchFilter = "(".$nextmember.")";
							$userSearchFilter = eregi_replace("(,)\s?".$userSearchDN,"", $userSearchFilter);
							$userSearchFilter = str_replace("\\", "", $userSearchFilter);
							//printpre($userSearchFilter);
							// search ldap with filter set to full name...
							//$sr2 = ldap_search($c,$userSearchDN,$userSearchFilter,$return2);
							//print "<hr />";
							//printpre("$sr2 = ldap_search($c :: $userSearchDN :: $userSearchFilter :: $return2);");
							//printpre($return2);
							$sr2 = ldap_search($c,$userSearchDN,$userSearchFilter,$return2);
							$res2 = ldap_get_entries($c,$sr2);
							//printpre($res2);
							$res2[0] = array_change_key_case($res2[0], CASE_LOWER);
							//printpre($cfg[ldap_fullname_attribute]);
							$num = ldap_count_entries($c,$sr);
							ldap_close($c);
							$participant = array();
							if ($num) {	
								//printpre($cfg[ldap_username_attribute]);	
								$participant[id] = 0;					
								$participant[fname] = $res2[0][strtolower($cfg[ldap_fullname_attribute])][0];
								$participant[uname] = $res2[0][strtolower($cfg[ldap_username_attribute])][0];
								$participant[email] = $res2[0][strtolower($cfg[ldap_email_attribute])][0];
								$participant[memberlist] = "external";
	// 							printpre("uname: ".$participant[uname]);
								$areprof = 0;
								if (is_array($res2[0][strtolower($cfg[ldap_group_attribute])])) {
								$isProfSearchString = implode("|", $cfg[ldap_prof_groups]);
									foreach ($res2[0][strtolower($cfg[ldap_group_attribute])] as $item) {
										if (eregi($isProfSearchString,$item)) {
											$areprof=1;
										}
									}
								}
								$participant[type] = ($areprof)?"prof":"stud";
	
								//$student[email] = $res2[0][strtolower($cfg[ldap_email_attribute])][0];
								//printpre("found ".$studentname);
							}	
						} // end if	
						$external_memberlist_participant_unames[]= $participant[uname];
						
						$external_memberlist_participants[$participant[uname]]= $participant;				
					} //end for loop
				} // end num 				
			}// end result count
							
		} // ends if bind
		$external_memberlist_participant_unames = array();
		$external_memberlist_participant_unames = array_unique($external_memberlist_participant_unames);
	
		/******************************************************************************
		 * Check to see if $external_memberlist_participant are already in database
		 * if not add them to database
		 ******************************************************************************/
		foreach (array_keys($external_memberlist_participants) as $key) {
			$student_uname = $external_memberlist_participants[$key][uname];
			//printpre ($cfg[auth_mods]);
			//$cfg[auth_mods]
			$valid = 0;
			foreach ($cfg[auth_mods] as $_auth) {
				$func = "_valid_".$_auth;
				//printpre ("<br />AUTH: trying ".$_auth ."..."); //debug
				if ($x = $func($student_uname,"",1)) {
					$valid = 1;
					break;
				}
			}
		}
	
		
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
	//		print "num: $num<br />";
			ldap_close($c);
			if ($num) {
				for ($i = 0; $i<$res[0][strtolower($cfg[ldap_group_attribute])]['count']; $i++) {
					$f = $res[0][strtolower($cfg[ldap_group_attribute])][$i];
	//				print "$f<br />";
					$parts = explode(",",$f);
					foreach ($parts as $p) {
						if (eregi($cfg[ldap_groupname_attribute]."=([a-zA-Z]{0,4})([0-9]{1,4})([a-zA-Z]{0,1})-([a-zA-Z]{1,})([0-9]{2})",$p,$r)) {
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
							$user_id = db_get_value("user","user_id","user_uname = '".addslashes($user)."'");
							$ugroup_id = db_get_value("ugroup","ugroup_id","ugroup_name='".addslashes($class)."'");
							$classinfo = db_get_line("class","
										class_department='".addslashes($r[1])."' AND
										class_number='".addslashes($r[2])."' AND
										class_section='".addslashes($r[3])."' AND
										class_semester='".addslashes($sem)."' AND
										class_year='20".addslashes($r[5])."'");
							
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
										class_department='".addslashes($r[1])."',
										class_number='".addslashes($r[2])."',
										class_section='".addslashes($r[3])."',
										class_semester='".addslashes($sem)."',
										class_year='20".addslashes($r[5])."',
										class_name='',
										FK_owner=NULL,
										FK_ugroup='".addslashes($ugroup_id)."'
								";
								
								db_query($query);
							}
							
							$ugroup_userinfo = db_get_line("ugroup_user","FK_ugroup='".addslashes($ugroup_id)."' AND FK_user='".addslashes($user_id)."'");
	
							if (!$ugroup_userinfo) {
								$query = "
									INSERT INTO
										ugroup_user
									SET
										FK_ugroup = '".addslashes($ugroup_id)."',
										FK_user = '".addslashes($user_id)."'
								";
								
								db_query($query);
							}
							
							/******************************************************************************
							 * end update
							 ******************************************************************************/
							
							
							if ($time == "now" && isSemesterNow($r[4], $r[5])) {
								$classes[$class] = array("code"=>"$r[1]$r[2]","sect"=>$r[3],"sem"=>$r[4],"year"=>$r[5]);
								
							} else if ($time == "past" && isSemesterPast($r[4], $r[5])) {
								$classes[$r[1].$r[2].$r[3]."-".$r[4].$r[5]] = array("code"=>"$r[1]$r[2]","sect"=>$r[3],"sem"=>$r[4],"year"=>$r[5]);
								
							} else if ($time == "future" && isSemesterFuture($r[4], $r[5])) {
								$classes[$r[1].$r[2].$r[3]."-".$r[4].$r[5]] = array("code"=>"$r[1]$r[2]","sect"=>$r[3],"sem"=>$r[4],"year"=>$r[5]);
								
							} else if ($time == "all") {
								$classes[$r[1].$r[2].$r[3]."-".$r[4].$r[5]] = array("code"=>"$r[1]$r[2]","sect"=>$r[3],"sem"=>$r[4],"year"=>$r[5]);
							}
						
						/******************************************************************************
						 * if not a class group then get group name and add to ugroup table
						 ******************************************************************************/
						
						} else if (eregi('^'.$cfg[ldap_groupname_attribute].'=(.+)$', $p, $matches)) {
							$group_name = $matches[1];
							
							$user_id = db_get_value("user","user_id","user_uname = '".addslashes($user)."'");
							$ugroup_id = db_get_value("ugroup","ugroup_id","ugroup_name='".addslashes($group_name)."'");

							/******************************************************************************
							 * insert group_name into ugroup table with group if not already in table
							 ******************************************************************************/
							
							if (!$ugroup_id) {
															
								$query = "
									INSERT INTO
										ugroup
									SET
										ugroup_name = '".addslashes($group_name)."',
										ugroup_type = 'other'
								";
								
								//printpre($query);
								
								db_query($query);
								$ugroup_id = lastid();
							}
								
							/******************************************************************************
							 * if user not part of group then add to ugroup_user table
							 ******************************************************************************/
						 
							$ugroup_userinfo = db_get_line("ugroup_user","FK_ugroup='".addslashes($ugroup_id)."' AND FK_user='".addslashes($user_id)."'");
	
							if (!$ugroup_userinfo) {
								$query = "
									INSERT INTO
										ugroup_user
									SET
										FK_ugroup = '".addslashes($ugroup_id)."',
										FK_user = '".addslashes($user_id)."'
								";
								
								//printpre($query);
								db_query($query);
							}

						/******************************************************************************
						 * get other members of this ugroup and add to ugroup_user table
						 * (this may not be necessary since users will be added when they log in...)
						 ******************************************************************************/
						
							
							
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
			user_uname = '".addslashes($user)."'
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

function getusergroups($user) {
	global $cfg;
	$user = strtolower($user);
	//printpre($user);
	if (!$user)
		return array();

	$groups = array();

	$user_id = db_get_value("user","user_id","user_uname = '".addslashes($user)."'");
	$query = "
		SELECT
			ugroup_id, ugroup_name
		FROM
			ugroup
				INNER JOIN
			ugroup_user
				ON
			FK_ugroup = ugroup_id
		WHERE
			FK_user = '".addslashes($user_id)."'
	";
	//printpre($query);
	$r = db_query($query);
	
	while ($a = db_fetch_assoc($r)) {
		$groups[] = $a[ugroup_name];
	}
		
	return $groups;
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
			class_id = '".addslashes($id)."'
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
	ereg("([a-zA-Z]{0,})([0-9]{1,})([a-zA-Z]{0,})-([a-zA-Z]{1,})([0-9]{2})",$code,$r);
	$department = $r[1];
	$number = $r[2];
	$section = $r[3];
	$semester = $r[4];
	$year = "20".$r[5];
	
	$terms = "
		class_department='".addslashes($department)."' AND
		class_number='".addslashes($number)."' AND
		class_section='".addslashes($section)."' AND
		class_semester='".addslashes($semester)."' AND
		class_year='".addslashes($year)."'
	";
	return $terms;
}

//This function checks for non-Segue sites (those in web courses database created in course folders)
function coursefoldersite($cl) {
	global $cfg;
	
	if (!$cfg[coursefolders_host])
		return null;
	db_connect($cfg[coursefolders_host],$cfg[coursefolders_username],$cfg[coursefolders_password],$cfg[coursefolders_db]);
	if (ereg("([a-zA-Z]{2})([0-9]{3})([a-zA-Z]{0,1})-([a-zA-Z]{1,})([0-9]{2})",$cl,$regs)) {
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
	$query = ("
		select 
			* 
		from 
			".$cfg[coursefolders_table]." 
		where 
			".$cfg[coursefolders_coursecode_column]." = '".addslashes($class)."' 
			and ".$cfg[coursefolders_semester_column]." = '".addslashes($semester)."' 
			and ".$cfg[coursefolders_year_column]." = '".addslashes($year)."'"
		);
	
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
	if ($fname = db_get_value("user","user_fname","user_uname='".addslashes($uname)."'")) return $fname;
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
							foreach ($res[0][strtolower($cfg[ldap_group_attribute])] as $item) {
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
			user_uname LIKE '%".addslashes($name)."%'
				OR
			user_fname LIKE '%".addslashes($name)."%'
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
			ugroup_name LIKE '%".addslashes($name)."%'
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
