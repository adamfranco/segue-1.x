<? // functions.inc.php -- random functions that will be useful

function makedownloadbar($a) {
	global $site,$uploaddir,$uploadurl;
	if ($a[type]!='file') return;
	$name = urldecode($a[longertext]);
	$filepath = "$uploaddir/$site/$a[id]/$name";
	$fileurl = "$uploadurl/$site/$a[id]/$name";
//	print $filepath;
	$filesize = mkfilesize($filepath);
	$t = '';
	$t .= "<div class=downloadbar style='margin-bottom: 10px'>";
	if ($a[title]) $t.="<b>".spchars($a[title])."</b><br>";
	$t .= "<table width=70% cellpadding=0 cellspacing=0 style='margin: 0px'><tr><td class=leftmargin align=left><a href='$fileurl'><img src='downarrow.gif' border=0 width=15 height=15 align=absmiddle> $name</a></td><td align=right><b>$filesize</b></td></tr></table>";
	if ($a[shorttext]) $t .= "".stripslashes(urldecode($a[shorttext]));
	$t.="</div>";
	return $t;
}
	
function mkfilesize($filename) {
	$j = 0;
	$ext = array("B","KB","MB","GB","TB");
//	$filename = ereg_replace(" ","\\ ",$filename);
//	print "<br><br>$filename<br><br>";
	$file_size = filesize($filename);
	while ($file_size >= pow(1024,$j)) ++$j;
	$file_size = round($file_size / pow(1024,$j-1) * 100) / 100 . $ext[$j-1];
	return $file_size;
}

function copyuserfile($id,$file) {
	global $uploaddir, $auser, $site;
	if (!$file[name]) return 0;
	$userdir = "$uploaddir/$site";
	$fdir = "$userdir/$id";
	print "$userdir - $fdir<br>";
	if (!is_dir($userdir)) { mkdir($userdir,0777); chmod($userdir,0775); }
	if (!is_dir($fdir)) { mkdir($fdir,0777); chmod($fdir,0775); }
	$r=move_uploaded_file($file['tmp_name'],"$fdir/".$file['name']);
	if ($r) print "Upload file error!";
	return 1;
}

function deleteuserfile($id,$file) {
	global $uploaddir, $auser, $site;
	if (!$file[name]) return 0;
	$userdir = "$uploaddir/$site";
	$fdir = "$userdir/$id";
	$f = "$fdir/$file";
//	print "$userdir - $fdir<br>";
	if (file_exists($f)) unlink($f);
	if (is_dir($fdir)) rmdir($fdir);
	return 1;
}

function decode_array($string) {
	$array = array();
	if ($string) {
		$string = urldecode($string);
		$array = unserialize($string);
	}
	return $array;
}

function encode_array($array) {
	if (!$array || $array=='') return '';
	return urlencode(serialize($array));
}

// adds a link entry onto topnav, leftnav or rightnav
function add_link($array,$name="",$url="",$extra='',$id=0,$target='_self') {
	global $$array;
	$type = 'normal';
	if ($url=='') $type='heading';
	if ($name=='') $type='divider';
	if (!$target) $target='_self';
	array_push($$array,array("type"=>$type,"name"=>$name,"url"=>$url,"extra"=>$extra,"id"=>$id,"target"=>$target));
//	return $array;
}

// makelink($i,$samepage,$e)
// $i = single entry of a navbar variable (made with add_link)
// $samepage = are we on the page this link describes?
// $e = extra HTML for the link
function makelink($i,$samepage=0,$e='',$newline=0) {
	$s = '';
	$s=(!$samepage&&$i[url])?"<a href='$i[url]' target='$i[target]'".(($e)?" $e":"").">":"";
	$s.=$i[name];
	$s.=(!$samepage&&$i[url])?"</a>":"";
	$s.=($i[extra])?(($newline)?"<div align=right>":" ").$i[extra].(($newline)?"</div>":""):"";
	return $s;
}

function printc($string) {
	global $content;
//	print "printc called...<BR>";
//	$content .= $string . "\n";
	$content .= $string;
}

function preprintc($string) {
	global $content;
	$content = $string . $content;
}

function spchars($string) {
	return htmlspecialchars(stripslashes($string),ENT_QUOTES);
}

function log_entry($type,$site,$section,$page,$content) {
	global $dbhost, $dbuser,$dbpass, $dbdb, $auser, $luser;
	db_connect($dbhost,$dbuser,$dbpass, $dbdb);
	db_query("insert into logs set type='$type',content='$content',luser='$luser',auser='$auser',site='$site',section='$section',page='$page'");
}

function htmlbr($string) {
	return ereg_replace("\n","<br>\n",$string);
}

function sitenamevalid($name) {
	global $auser,$atype,$classes,$ltype;
	$auser = strtolower($auser);
	$name = strtolower($name);
	if ($name == $auser) return 1;
	if ($ltype=='admin') return 1;
	// look at the classes list.. if the site is in the classes list, then it's valid
/* 	print "$atype -- $name"; */
/* 	print_r($classes); */
	if ($atype == 'prof' && is_array($classes[$name])) return 1;
	if ($atype == 'prof' && db_line_exists("classgroups","name='$name' and owner='$auser'")) return 1;
	
	return 0;
}

function insite($site,$section,$page=0,$story=0) {
	$ok=1;
	if (!in_array($section,decode_array(db_get_value("sites","sections","name='$site'")))) $ok=0;
	if ($page && !in_array($page,decode_array(db_get_value("sections","pages","id=$section")))) $ok=0;
	if ($story && !in_array($story,decode_array(db_get_value("pages","stories","id=$page")))) $ok=0;
	return $ok;
}

function isclass ($class) {
	global $auser;
	$auser = strtolower($auser);
	$v = ereg("([a-zA-Z]{2})([0-9]{3})([a-zA-Z]{0,1})-([lsfw]{1})([0-9]{2})",$class);
	if (!$v && db_line_exists("classgroups","name='$class'")) $v = 1;
	return $v;
}

function isgroup ($group) {
	global $auser;
	$r = db_query("select * from classgroups where name='$group'");
	if (db_num_rows($r)) {
		$a = db_fetch_assoc($r);
		return explode(",",$a[classes]);
	}
	return 0;
}

function currentsemester () {
	$currentyear = date(y);
	$year=2000+$currentyear;
	$currmonth = date(m);
	
	if ($currmonth<2){
	$semester='w';
	}elseif (($currmonth>1)&&($currmonth<6)){
	$semester='s';
	}elseif (($currmonth>5)&&($currmonth<9)){
	$semester='ls';
	}else{
	$semester='f';
	}
	return $semester;
}

function semorder($semester) {
	if ($semester == "w") {
		$order = 1;
	} else if ($semester == "s") {
		$order = 2;
	} else if ($semester == "ls") {
		$order = 3;	} 
	else if ($semester == "f") {
		$order = 4;
	}
	return $order;
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
							
						} else if ($time == "past" && ($r[5] < date('y') || $r[4] != $semester)) {
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

//This function checks for non-SitesDB sites (those in web courses database created in course folders)
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

function inclassgroup($class) {
	$query = "select * from classgroups where classes like '%$class%'";
	$r = db_query($query);
	if (db_num_rows($r)) { $a = db_fetch_assoc($r); return $a[name]; }
	return 0;
}


function canview($a,$type=SITE) {
//	if (!$a[type]=='page'&&!$a[type]=='section'&&!$a[theme]) return 0;
	if ($a[type] == 'heading' || $a[type] == 'divider') return 1;
	if ($type == SITE || $type == SECTION || $type==PAGE) {
		if (!$a[active]) return 0;
	}
	if (!indaterange($a[activatedate],$a[deactivatedate])) return 0;
	return 1;
}

function ldapfname($uname) {
	$uname = strtolower($uname);
	if (isgroup($uname)) return "Students in group";
	if (isclass($uname)) return "Students in class";
	if ($fname = db_get_value("users","fname","uname='$uname'")) return $fname;
	$r = ldaplookup($uname,LDAP_USER,LDAP_EXACT,LDAP_LASTNAME,1);
	return $r[$uname];
}

define("LDAP_USER",1);
define("LDAP_FNAME",2);
define("LDAP_BOTH",3);
define("LDAP_WILD",1);
define("LDAP_EXACT",0);
define("LDAP_LASTNAME",0);
define("LDAP_FIRSTNAME",1);

function ldaplookup($name,$type=LDAP_BOTH,$wild=LDAP_WILD,$n=LDAP_LASTNAME,$lc=0) {
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

// the reorder function -- recieves an array, id and a direction... and then returns the new array
function reorder($array,$id,$d) {
	$num = count($array)-1;
	for ($i=0; $i<=$num; $i++) {
		if ($array[$i] == $id) {
			if ($d == 'down') {
				if ($i != $num) {
					$array[$i]=$array[$i+1];
					$array[$i+1]=$id;
					break;
				}
			}
			if ($d == 'up') {
				if ($i != 0) {
					$array[$i]=$array[$i-1];
					$array[$i-1] = $id;
					break;
				}
			}
		}
	}
	return $array;
}

/* function handlearchive($stories,$pa) { */
/* 	global $startweek,$startmonth,$startyear; */
/* 	$newstories = array(); */
/* 	if ($pa == 'week') { */
/* 		if (!$startweek) $startweek = date("W",time()-(date("w")*86400)); */
/* 		if (!$startyear) $startyear = date("Y"); */
/* 		foreach ($stories as $s) { */
/* 			$a = db_fetch_line("stories","id=$s"); */
/* 			$added = $a[addedtimestamp]; */
/* 			ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})",$added,$regs); */
/* 			$year = (integer)$regs[1]; */
/* 			$month = (integer)$regs[2]; */
/* 			$day = (integer)$regs[3]; */
/* 			$t = mktime(1,1,$month,$day,$year); */
/* 			$week = date("W",$t-(date("w",$t)*86400)); */
/* 			 */
/* 			if ($startyear == $year && $startweek == $week) */
/* 				$newstories[] = $s; */
/* 		 */
/* 		} */
/* 	} */
/* } */

function handlearchive($stories,$pa) {
	global $startday,$startmonth,$startyear,$endday,$endmonth,$endyear,$usestart,$useend,$months;
	global $usesearch;
	global $site,$section,$page;
	$newstories = array();
	
	if (!$usesearch) {
		$endyear = date("Y");
		$endmonth = date("n");
		$endday = date("j");
	}	
	printc("<div>");
//	printc("<b>Search:</b> ");
	printc("Display content in date rage: ");
	printc("<form action='$PHP_SELF?$sid&action=site&site=$site&section=$section&page=$page' method=post>");
	printc("<input type=hidden name=usesearch value=1>");

	printc("<select name='startday'>");
	for ($i=1;$i<=31;$i++) {
		printc("<option" . (($startday == $i)?" selected":"") . ">$i\n");
	}
	printc("/select>\n");
	printc("<select name='startmonth'>");
	for ($i=0; $i<12; $i++)
		printc("<option value=".($i+1). (($startmonth == $i+1)?" selected":"") . ">$months[$i]\n");
	
	printc("</select>\n<select name='startyear'>");
	$curryear = date("Y");
	for ($i=$curryear-10; $i <= ($curryear); $i++) {
		printc("<option" . (($startyear == $i)?" selected":"") . ">$i\n");
	}
	printc("/select>");
//	printc("<br>");
	printc(" to <select name='endday'>");
	for ($i=1;$i<=31;$i++) {
		printc("<option" . (($endday == $i)?" selected":"") . ">$i\n");
	}
	printc("/select>\n");
	printc("<select name='endmonth'>");
	for ($i=0; $i<12; $i++) {
		printc("<option value=".($i+1) . (($endmonth == $i+1)?" selected":"") . ">$months[$i]\n");
	}
	printc("</select>\n<select name='endyear'>");
	for ($i=$curryear; $i <= ($curryear+5); $i++) {
		printc("<option" . (($endyear == $i)?" selected":"") . ">$i\n");
	}
	printc("/select>");
	printc(" <input type=submit class=button value='go'>");
	printc("</form></div>");
	
	$start = mktime(1,1,1,$startmonth,$startday,$startyear);
	$end = mktime(1,1,1,$endmonth,$endday,$endyear);
	if ($pa == 'week') {
		if (!$usesearch) {
			$start = mktime(0,0,0,date("n"),date('j')-7,date('Y'));
			$end = time();
		}
	}
	if ($pa == 'month') {
		if (!$usesearch) {
			$start = mktime(0,0,0,date("n")-1,date('j'),date("Y"));
			$end = time();
		}
	}
	if ($pa == 'year') {
		if (!$usesearch) {
			$start = mktime(0,0,0,date("n"),date('j'),date("Y")-1);
			$end = time();
		}
	}
	$txtstart = date("n/j/y",$start);
	$txtend = date("n/j/y",$end);
	foreach ($stories as $s) {
		$a = db_get_line("stories","id=$s");
		$added = $a[addedtimestamp];
		ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})",$added,$regs);
		$year = (integer)$regs[1];
		$month = (integer)$regs[2];
		$day = (integer)$regs[3];
		$t = mktime(0,0,0,$month,$day,$year);
/* 			$week = date("W",$t-(date("w",$t)*86400)); */
/* 			 */
/* 			if ($startyear == $year && $startweek == $week) */
/* 				$newstories[] = $s; */
/* 		if ((!$usestart || $start < $t) && (!$useend || $t < $end)) { */
		if (($start < $t) && ($t < $end) || false) {
			$newstories[$s] = $t;
		}
	
	}
/* 	print_r($newstories); */
	arsort($newstories,SORT_NUMERIC);
/* 	print_r($newstories); */
	$newstories = array_keys($newstories);
	printc("<b>Content ranging from $txtstart to $txtend.</b><br><BR>");
	return $newstories;
}

function copySite($orig,$dest) {
	global $auser;
	$sections = decode_array(db_get_value("sites","sections","name='$orig'"));
	$nsections = array();
	foreach ($sections as $s) {
		$sa = db_get_line("sections","id=$s");
		$squery = "insert into sections set addedby='$auser', addedtimestamp=NOW()";
		$squery .= ",title='$sa[title]', active=$sa[active], type='$sa[type]', url='$sa[url]'";
		
		
		$pages = decode_array($sa[pages]);
		$npages = array();
		foreach ($pages as $p) {
			$pa = db_get_line("pages","id=$p");
			$pquery = "insert into pages set addedby='$auser', addedtimestamp=NOW()";
			$pquery .= ",ediscussion=1,archiveby='$pa[archiveby]',url='$pa[url]',type='$pa[type]',title='$pa[title]', showcreator=$pa[showcreator], showdate=$pa[showdate], locked=$pa[locked], active=$pa[active]";
			
			$stories = decode_array($pa[stories]);
			$nstories = array();
			foreach ($stories as $st) {
				$sta = db_get_line("stories","id=$st");
				$stquery = "insert into stories set addedby='$auser', addedtimestamp=NOW()";
				$stquery.=",type='$sta[type]',texttype='$sta[texttype]',category='$sta[category]',title='$sta[title]', discuss=$sta[discuss], discusspermissions='$sta[discusspermissions]', shorttext='$sta[shorttext]', longertext='$sta[longertext]', locked=$sta[locked], url='$sta[url]'";
				db_query($stquery);
//				print "$stquery<BR>";
				$nstories[] = lastid();
			}
			
			$stories = encode_array($nstories);
			$pquery.=",stories='$stories'";
			db_query($pquery);
			$npages[]=lastid();
//			print "$pquery<BR>";
		}
		
		$pages = encode_array($npages);
		$squery.=",pages='$pages'";
		db_query($squery);
		$nsections[] = lastid();
//		print "$squery<BR>";
	}
	$sections = encode_array($nsections);
	$query = "update sites set sections='$sections' where name='$dest'";
	db_query($query);
//	print "$query<BR>";
}