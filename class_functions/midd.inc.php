<? /* $Id$ */

function isclass ($class) {
	global $auser;
	$auser = strtolower($auser);
	$v = ereg("([a-zA-Z]{2})([0-9]{3})([a-zA-Z]{0,1})-([lsfw]{1})([0-9]{2})",$class);
	if (!$v && db_line_exists("classgroups","name='$class'")) $v = 1;
	return $v;
}

function getuserclasses($user,$time="now") {
	$user = strtolower($user);
	global $ldap_voadmin_user, $ldap_voadmin_pass,$ldapserver;
	$ldap_user = "cn=$ldap_voadmin_user,cn=Recipients,ou=Midd,o=MC";
	$ldap_pass = $ldap_voadmin_pass;
	$classes = array();
	
	$c = ldap_connect($ldapserver);
	$r = @ldap_bind($c,$ldap_user,$ldap_pass);
	if ($r) {		// connected & logged in
		$return = array("uid","cn","memberof");
		$base_dn = "ou=Midd,o=MC";
		$filter = "uid=$user";
		
		$sr = ldap_search($c,$base_dn,$filter,$return);
		$res = ldap_get_entries($c,$sr);
//		print "<pre>";print_r($res);print"</pre>";
		$num = ldap_count_entries($c,$sr);
//		print "num: $num<br>";
		ldap_close($c);
		if ($num) {
//			print "memberof num: ".$res[0]['memberof']['count']."<br>";
//			print "or ".count($res[0][memberof])."<br>";
			for ($i = 0; $i<$res[0]['memberof']['count']; $i++) {
				$f = $res[0]['memberof'][$i];
//				print "$f<br>";
				$parts = explode(",",$f);
				foreach ($parts as $p) {
					if (ereg("cn=([a-zA-Z]{2})([0-9]{3})([a-zA-Z]{0,1})-([lsfw]{1})([0-9]{2})",$p,$r)) {
//						print "goood!";
						$semester = currentsemester ();
						if ($time == "now" && $r[5] == date('y') && $r[4] == $semester) {
							$class = $r[1].$r[2].$r[3]."-".$r[4].$r[5];					
							$classes[$class] = array("code"=>"$r[1]$r[2]","sect"=>$r[3],"sem"=>$r[4],"year"=>$r[5]);
							$classinfo = db_get_line("classes","name='$class'");
							if (!$classinfo) {
								$query = "insert into classes set name='$class', uname='$user'";
								db_query($query);
							}	
							
						} else if ($time == "past" && ($r[5] < date('y') || semorder($r[4]) < semorder($semester))) {
							$classes[$r[1].$r[2].$r[3]."-".$r[4].$r[5]] = array("code"=>"$r[1]$r[2]","sect"=>$r[3],"sem"=>$r[4],"year"=>$r[5]);
						} else if ($time == "future" && ($r[5] > date('y') || semorder($r[4]) > semorder($semester))) {
							$classes[$r[1].$r[2].$r[3]."-".$r[4].$r[5]] = array("code"=>"$r[1]$r[2]","sect"=>$r[3],"sem"=>$r[4],"year"=>$r[5]);
						}
					}
				}
			}
		}
	}
	return $classes;
}

//This function checks for non-Segue sites (those in web courses database created in course folders)
function coursefoldersite($cl) {
	db_connect("et.middlebury.edu","httpd","httpd","it");
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
	$query = ("select * from courses where code = '$class' and semester = '$semester' and year = '$year'");
	$r = db_query($query);	
	if (db_num_rows($r)) {
		$a = db_fetch_assoc($r);
		$title = $a['title'];
		$url = $a['url'];
		$site_info = array('title' => $title, 'url' => $url);
		return $site_info;
	}	
}

function ldapfname($uname) {
	$uname = strtolower($uname);
	if (isgroup($uname)) return "Students in group";
	if (isclass($uname)) return "Students in class";
	if ($fname = db_get_value("users","fname","uname='$uname'")) return $fname;
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

function userlookup($name,$type=LDAP_BOTH,$wild=LDAP_WILD,$n=LDAP_LASTNAME,$lc=0) {
	$name = strtolower($name);
	global $ldap_voadmin_pass,$ldap_voadmin_user,$ldapserver;
	$ldap_user = "cn=$ldap_voadmin_user,cn=Recipients,ou=Midd,o=MC";
	$ldap_pass = $ldap_voadmin_pass;
	
	$wc = ($wild==LDAP_WILD)?"*":"";
	
	$c = ldap_connect($ldapserver);
	$r = ldap_bind($c,$ldap_user,$ldap_pass);
	if ($r) {
		$return = array("uid","cn");
		$base_dn = "ou=Midd,o=MC";
//		$filter = "cn=*$name*";
		if ($type == LDAP_USER) $filter = "uid=$wc$name$wc";
		if ($type == LDAP_FNAME) $filter = "cn=$wc$name$wc";
		if ($type == LDAP_BOTH) $filter = "(|(cn=$wc$name$wc)(uid=$wc$name$wc))";
//		print $filter."<BR>";
		
		$sr = ldap_search($c,$base_dn,$filter,$return);
		$res = ldap_get_entries($c,$sr);
		$num = ldap_count_entries($c,$sr);
		ldap_close($c);
/* 		print "<pre>"; */
/* 		print_r($res); */
/* 		print "</pre>"; */
		if ($num) {
			$usernames = array();
			for ($i = 0; $i<$res['count'];$i++) {
				$uid = $res[$i]['uid'][0];
				$fname = $res[$i]['cn'][0];
				if (!ereg(",",$fname) && !$n) {
					$vars = split(" ",$fname);
					if (count($vars) == 2)
						$fname = $vars[1] . ", " . $vars[0];
					if (count($vars) == 3)
						$fname = $vars[2] . ", " . $vars[0] . " " . $vars[1]; // for Gabriel B. Schine names
				}
//				$res[$i]['cn'][0] = $fname;
				$usernames[$uid] = $fname;
			}
		}
	}
	if ($lc && $usernames) {
		foreach ($usernames as $u=>$f)
			$usernames[strtoupper($u)] = $usernames[strtolower($u)] = $f;
	}
	return $usernames;
}
