<? /* $Id$ */

function makedownloadbar($o) {
	global $site,$uploaddir,$uploadurl;
	if ($o->getField("type")!='file') return;
	
	$b = db_get_line("media","id=".$o->getField("longertext"));
	$filename = urldecode($b[name]);
	print $filename;
	$dir = $b[site_id];
	$size = $b[size];
	$fileurl = "$uploadurl/$dir/$filename";
	$filepath = "$uploaddir/$dir/$filename";
	$filesize = convertfilesize($size);
	$t = '';
	$t .= "<div class=downloadbar style='margin-bottom: 10px'>";
	if ($o->getField("title")) $t.="<b>".spchars($o->getField("title"))."</b><br>";
	$t .= "<table width=70% cellpadding=0 cellspacing=0 style='margin: 0px; border: 0px;'><tr><td class=leftmargin align=left><a href='$fileurl' target='new_window'><img src='downarrow.gif' border=0 width=15 height=15 align=absmiddle> $filename</a></td><td align=right><b>$filesize</b></td></tr></table>";
	if ($o->getField("shorttext")) $t .= "".stripslashes($o->getField("shorttext"));
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

function convertfilesize($size) {
	$j = 0;
	$ext = array("B","KB","MB","GB","TB");
	while ($size >= pow(1024,$j)) ++$j;
	$size = round($size / pow(1024,$j-1) * 100) / 100 . $ext[$j-1];
	return $size;
}

function get_size($pic) {
	$imageInfo = GetImageSize($pic);
	$size[x]=$imageInfo[0];
	$size[y]=$imageInfo[1];
	
	return $size;
}

function get_sizes($pic,$maxsize) {
	//-----This function takes an image and a maximum dimension and returns scaled dimensions 
	$imageInfo=getimagesize($pic);
	$iwidth=$imageInfo[0];
	$iheight=$imageInfo[1];
	
	if ($iwidth > $maxsize || $iheight > $maxsize) {
	       	$winX = $maxsize;
		$winY = $maxsize;

		if ($iwidth > $iheight) {
	    		$winY = $winX*$iheight/$iwidth;
	    	} else {
	   		$winX = $winY*$iwidth/$iheight;
	   	}
	} else {
		$winX = $iwidth;
		$winY = $iheight;
	}
	
	$size = array();
	$size['x'] = $winX;
	$size['y'] = $winY;

	return $size;
}

function copyuserfile($file,$site,$replace,$replace_id,$allreadyuploaded=0) {
	global $uploaddir, $auser;
	if (!$file[name]) {
		print "No File";
		return "ERROR";
	}
	
	$siteObj = new site($site);
	$userdir = "$uploaddir/$site";
	
	$name = ereg_replace("[\x27\x22]",'',stripslashes(trim($file['name'])));
	$extn = explode(".",$name);
	$last = count($extn)-1;
	$extn = strtolower($extn[$last]);
//	print "$extn <br>";
	$image_extns = array(
		"jpeg",
		"jpg",
		"gif",
		"bmp",
		"png",
		"tiff"
	);
	if (in_array($extn, $image_extns)) $type = "image";
	else $type = "file";
	
//	print "$userdir/$file[name]<br>";
	if (!is_dir($userdir)) {
		mkdir($userdir,0777); 
		chmod($userdir,0775); 
	}
	
	if ($replace) {
		$unlink = unlink($userdir."/".$name);
		/* print "unlink: $unlink"; */
	}
	
	if ($allreadyuploaded) {
		$r = copy($file[tmp_name],"$userdir/".$name);
	} else {
/* 		print "move uploaded file ($file[tmp_name], $userdir/$file[name])<br>"; */
		$r=move_uploaded_file($file['tmp_name'],"$userdir/".$name);
	}
	if (!$r) {
		print "Upload file error!<br>";
		log_entry("media_error","File upload attempt by $auser in site $site failed.",$siteObj->id,"site");
		return "ERROR";
	} else if ($replace) {
		$size = filesize($userdir."/".$name);
		$query = "update media set addedtimestamp=NOW(),addedby='$auser',size='$size' where id='$replace_id'";
		/* print $query."<br>"; */
		db_query($query);
		print mysql_error()."<br>";
		
		$media_id = $replace_id;
		
		log_entry("media_update","$auser updated file: $name, id: $media_id, in site $site",$siteObj->id,"site");
		return $media_id;
	} else {
		$size = filesize($userdir."/".$name);
		$query = "insert into media set name='$name',site_id='$site',addedtimestamp=NOW(),addedby='$auser',type='$type',size='$size'";
//		print $query."<br>";
		db_query($query);
//		print mysql_error()."<br>";
		
		$media_id = lastid();
		log_entry("media_upload","$auser uploaded file: $name, id: $media_id, to site $site",$siteObj->id,"site");
		return $media_id;
	}
}

function copy_media($id,$newsitename) {
	global $uploaddir;
	$oldsitename = db_get_value("media","site_id","id=$id");
	$file_name = db_get_value("media","name","id=$id");
	$sourcedir  = "$uploaddir/$oldsitename";
	$destdir = "$uploaddir/$newsitename";
	$old_file_path = $sourcedir."/".$file_name;
	$new_file_path = $destdir."/".$file_name;
	if (!is_dir($destdir)) {
		mkdir($destdir,0777); 
		chmod($destdir,0775); 
	}
	if (file_exists($new_file_path)) {
		$newid = db_get_value("media","id","site_id='$newsitename' && name='$file_name'");
	} else {
		$file = array();
		$file[name] = $file_name;
		$file[tmp_name] = $old_file_path;
//		print_r ($file);
//		print "<br>";
		$newid = copyuserfile($file,$newsitename,0,0,1);
	}
	return $newid;
}

function deleteuserfile($fileid) {
	global $uploaddir, $auser, $site, $settings;
	$query = "select * from media where id='$fileid'";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$a[name] = urldecode($a[name]);
	$siteObj = new site($a[FK_site]);
	$file_path = $uploaddir."/".$siteObj->getField("name")."/".$a[name];
//	$file_path = "../segue_userfiles/afranco/close2.gif";
//	print "file = \"$file_path\" <br>";
	if (file_exists($file_path)) {
//		$exists = file_exists($file_path);
//		print "fileexists = $exists $file_path<br> ";
		$success = unlink($file_path);
//		print "success = $success <br>";
		if ($success) {
			$query = "DELETE FROM media WHERE id='$fileid' LIMIT 1";
			db_query($query);
			log_entry("media_delete","$auser deleted file: ".$a[name].", id: $fileid, from site ".$siteObj->getField("name"),$siteObj->id,"site");
		} else {
			log_entry("media_error","Delete failed of file: ".$a[name].", id: $fileid, from site ".$siteObj->getField("name")." by $auser",$siteObj->id,"site");
			error("File could not be Deleted");
		}
	} else {
		log_entry("media_error","Delete failed of file: ".$a[name].", id: $fileid, from site ".$siteObj->getField("name")." by $auser. File does not exist. Removed entry.",$siteObj->id,"site");
		error("File does not exist. Its Entry was deleted");
		$query = "DELETE FROM media WHERE id='$fileid' LIMIT 1";
		db_query($query);
	}
}

function deleteComplete($file) {
	// posted by georg@spieleflut.de on PHP.net 24-Dec-2001 10:28
	// This function will completely delete even a non-empty directory.
	global $uploaddir;
	$uploadDirs = array(
		$uploaddir,
		$uploaddir."/",
		$uploaddir."//",
		$uploaddir."///"
	);
	if (in_array($file, $uploadDirs)) return false;
	chmod($file,0777);
	if (is_dir($file)) {
		$handle = opendir($file);
 		while($filename = readdir($handle)) {
  			if ($filename != "." && $filename != "..") {
  				deleteComplete($file."/".$filename);
  			}
 		}
		closedir($handle);
 		rmdir($file);
	} else {
		unlink($file);
	}
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

function log_entry($type,$content,$siteunit=0,$siteunit_type="site") {
	global $dbhost, $dbuser,$dbpass, $dbdb, $auser, $luser;
	
	db_connect($dbhost,$dbuser,$dbpass, $dbdb);
	db_query("insert into log set 
		log_type='$type',
		log_desc='$content',
		FK_luser='".$_SESSION[lid]."',
		FK_auser='".$_SESSION[aid]."',
		FK_siteunit='$siteunit',
		log_siteunit_type='$siteunit_type'
	");
}

function htmlbr($string) {
	return ereg_replace("\n","<br>\n",$string);
}

function sitenamevalid($name) {
	// sitenamevalid doen't really check if the sitename is valid and throws errors.
	// its purpose needs to be clarified and the function rewritten
	
/*	global $auser,$atype,$classes,$ltype, $settings;
	$auser = strtolower($auser);
	$name = strtolower($name);
	if ($name == $auser) return 1;
	if ($ltype=='admin') return 1;
	// look at the classes list.. if the site is in the classes list, then it's valid
// 	print "$atype -- $name"; 
// 	print_r($classes); 
	if ($settings[type]=="other" && $auser==$settings[addedby]) return 1; 
	if ($atype == 'prof' && is_array($classes[$name])) return 1;
	if ($atype == 'prof') {
		$query = "
SELECT
	classgroup_id
FROM
	classgroup
		INNER JOIN
	user
		ON FK_owner = user_id AND user_uname = '$auser'
WHERE
	classgroup_name = '$name'
";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		if (count($a) > 0)
			return 1;			
	 }
	
	return 0;
*/
	return 1;
}

function insite($site,$section,$page=0,$story=0) {
	$ok=1;
	if (!in_array($section,decode_array(db_get_value("sites","sections","name='$site'")))) $ok=0;
	if ($page && !in_array($page,decode_array(db_get_value("sections","pages","id=$section")))) $ok=0;
	if ($story && !in_array($story,decode_array(db_get_value("pages","stories","id=$page")))) $ok=0;
	return $ok;
}

function isgroup ($group) {
	global $auser;
	$query = ("SELECT classgroup_id FROM classgroup WHERE classgroup_name='$group'");
	$r = db_query($query);
	if (db_num_rows($r)) {
		$a = db_fetch_assoc($r);
		$query = "SELECT class_code FROM class INNER JOIN classgroup ON classgroup_id = ".$a[classgroup_id];
		$r = db_query($query);
		$temp_c = array();
		while ($a = db_fetch_assoc($r))
			$temp_c[] = $a[class_code];
		return $temp_c;
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
	if ($semester == "w") $order = 1;
	else if ($semester == "s") $order = 2;
	else if ($semester == "ls")	$order = 3;
	else if ($semester == "f") $order = 4;
	return $order;
}

function inclassgroup($class) {
	$query = "
SELECT
	classgroup_name
FROM
	classgroup
		INNER JOIN
	class ON classgroup_id = FK_classgroup AND class_code = '$class'
";
	$r = db_query($query);
	if (db_num_rows($r)) { $a = db_fetch_assoc($r); return $a[classgruop_name]; }
	return 0;
}

/******************************************************************************
 * canview - to be phased out by $obj->canview($user)
 ******************************************************************************/

function canview($a,$type=SITE) {
//	if (!$a[type]=='page'&&!$a[type]=='section'&&!$a[theme]) return 0;
	if ($a[type] == 'heading' || $a[type] == 'divider') return 1;
	if ($type == SITE || $type == SECTION || $type==PAGE) {
		if (!$a[active]) return 0;
	}
	if (!indaterange($a[activatedate],$a[deactivatedate])) return 0;
	return 1;
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
// 			$week = date("W",$t-(date("w",$t)*86400)); 
//
// 			if ($startyear == $year && $startweek == $week) 
// 				$newstories[] = $s; 
// 		if ((!$usestart || $start < $t) && (!$useend || $t < $end)) { 
		if (($start < $t) && ($t < $end) || false) {
			$newstories[$s] = $t;
		}
	
	}
// 	print_r($newstories); 
	arsort($newstories,SORT_NUMERIC);
// 	print_r($newstories); 
	$newstories = array_keys($newstories);
	printc("<b>Content ranging from $txtstart to $txtend.</b><br><BR>");
	return $newstories;
}

function handlestoryorder($stories,$order) {
	// reorders the stories array passed to it depending on the order specified.
	// Orders: addedesc, addedasc, editeddesc, editedasc, author, editor, category, titledesc, titleasc
	return $stories;
}

/******************************************************************************
 * copyPart - holy god... um, re-write to use objects... should be substantially shorter
 ******************************************************************************/
/*  */
/* function copyPart($action,$parttype,$id,$newparentid,$isSubCall=0) { */
/* 	// $action can have value MOVE or COPY */
/* 	global $auser; */
/* 	$action = strtolower($action); */
/* //	print "--------------------------<br>"; */
/* //	print "action = $action<br>parttype = $parttype<br>id = $id<br>newparentid = $newparentid<br>isSubCall = $isSubCall<br>"; */
/* 	 */
/* 	// Get part and newparent info */
/* 	if ($parttype == 'story') { */
/* 		$parttable = "stories"; */
/* 		$partarray = "stories"; */
/* 		$parenttable = "pages"; */
/* 		$parenttype = "page"; */
/* 	} else if ($parttype == 'page') { */
/* 		$parttable = "pages"; */
/* 		$partarray = "pages"; */
/* 		$parenttable = "sections"; */
/* 		$parenttype = "section"; */
/* 		$childarray = "stories"; */
/* 		$childtype = "story"; */
/* 	} else if ($parttype == 'section') { */
/* 		$parttable = "sections"; */
/* 		$partarray = "sections"; */
/* 		$parenttable = "sites"; */
/* 		$parenttype = "site"; */
/* 		$childarray = "pages"; */
/* 		$childtype = "page"; */
/* 	} */
/* 	 */
/* 	$part = db_get_line($parttable,"id=$id"); */
/* //	print_r($part); */
/* //	print "<br>"; */
/* 	if ($parenttype == "site") */
/* 		$newparent = db_get_line($parenttable,"name='$newparentid'"); */
/* 	else	 */
/* 		$newparent = db_get_line($parenttable,"id='$newparentid'"); */
/*  */
/* 	// Log the move if this is not part of a larger move call */
/* 	if (!$isSubCall) { */
/* 		if ($parttype == 'story') log_entry("$action_story",$newparent[site_id],$newparent[section_id],$newparentid,"$auser MOVED story $id FROM site $part[site_id], section $part[section_id], page $part[page_id]  TO site $newparent[site_id], section $newparent[section_id], page $newparentid"); */
/* 		if ($parttype == 'page') log_entry("$action_page",$newparent[site_id],$newparent[section_id],$id,"$auser MOVED page $id FROM site $part[site_id], section $part[section_id]  TO site $newparent[site_id], section $newparent[section_id]"); */
/* 		if ($parttype == 'section') log_entry("$action_section",$newparent[site_id],$id,"","$auser MOVED section $id FROM site $part[site_id] TO site $newparent[site_id]"); */
/* 	} */
/* 	 */
/* 	// if MOVING remove the reference to the part in the old parent's array */
/* 	if ($action == "move" && !$isSubCall) { */
/* 		$tmp = $parenttype."_id"; */
/* 		$parentid = $part[$tmp]; */
/* 		if ($parenttype == "site") */
/* 			$parentparts = decode_array(db_get_value($parenttable,$partarray,"name='$parentid'")); */
/* 		else */
/* 			$parentparts = decode_array(db_get_value($parenttable,$partarray,"id=$parentid")); */
/* 		$parentnewparts = array(); */
/* 		foreach ($parentparts as $p) { */
/* 			if ($p != $id) array_push($parentnewparts,$p); */
/* 		} */
/* 		$parentparts = encode_array($parentnewparts); */
/* 		if ($parenttype == "site") */
/* 			$query = "update $parenttable set $partarray='$parentparts' where name='$parentid'"; */
/* 		else */
/* 			$query = "update $parenttable set $partarray='$parentparts' where id='$parentid'"; */
/* 		db_query($query); */
/* //		print "$query<br>"; */
/* 	} */
/* 	 */
/* 	// Copy any associated images */
/* 	if ($parttype == "story" && $part[site_id] != $newparent[site_id]) { */
/* 		// If we're moving a story to another site. */
/* 		$images = array(); */
/* 		if ($part[type] == "image" || $part[type] == "file") { */
/* 			$media_id = $part[longertext]; */
/* 			$part[longertext] = copy_media($media_id,$newparent[site_id]); */
/* 		} else if ($part[type] == "story") { */
/* 			$st = stripslashes(urldecode($part[shorttext])); */
/* 			$st = str_replace("src='####","####",$st); */
/* 			$st = str_replace("src=####","####",$st); */
/* 			$st = str_replace("####'","####",$st); */
/* 			$textarray1 = explode("####", $st); */
/* 			if (count($textarray1) > 1) { */
/* 				for ($i=1; $i<count($textarray1); $i=$i+2) { */
/* 					$id = $textarray1[$i]; */
/* 					$newid = copy_media($id,$newparent[site_id]); */
/* 					$textarray1[$i] = "src='####".$newid."####'"; */
/* 				}		 */
/* 				$st = implode("",$textarray1); */
/* 				$part[shorttext] = urlencode(addslashes($st)); */
/* 			} */
/* 			$st = stripslashes(urldecode($part[longertext])); */
/* 			$st = str_replace("src='####","####",$st); */
/* 			$st = str_replace("src=####","####",$st); */
/* 			$st = str_replace("####'","####",$st); */
/* 			$textarray1 = explode("####", $st); */
/* 			if (count($textarray1) > 1) { */
/* 				for ($i=1; $i<count($textarray1); $i=$i+2) { */
/* 					$id = $textarray1[$i]; */
/* 					$newid = copy_media($id,$newparent[site_id]); */
/* 					$textarray1[$i] = "src='####".$newid."####'"; */
/* 				}		 */
/* 				$st = implode("",$textarray1); */
/* 				$part[longertext] = urlencode(addslashes($st)); */
/* 			} */
/* 		} */
/* 	} */
/* 	 */
/* 	// Update the part's fields if MOVING, if COPYING insert into new row */
/* 	if ($action == "move") {	// MOVE */
/* 		$query = "update $parttable set editedby='$auser',";  */
/* 		$where = " where id='$id'"; */
/* 	} else {	// COPY */
/* 		$query = "insert into $parttable set addedby='$auser',addedtimestamp=NOW(),";  */
/* 		$where = ""; */
/* 	} */
/* 			 */
/* 	$chg = array(); */
/* 	if ($parttype == 'story') { */
/* 		$chg[] = "site_id='$newparent[site_id]'"; */
/* 		$chg[] = "section_id='$newparent[section_id]'"; */
/* 		$chg[] = "page_id='$newparentid'"; */
/* 		$chg[] = "permissions='$newparent[permissions]'"; */
/* 		if ($action == "copy") { */
/* 			$chg[] = "discuss='$part[discuss]'"; */
/* 			$chg[] = "discusspermissions='$part[discusspermissions]'"; */
/* 			$chg[] = "texttype='$part[texttype]'"; */
/* 			$chg[] = "category='$part[category]'"; */
/* 			$chg[] = "shorttext='$part[shorttext]'"; */
/* 			$chg[] = "longertext='$part[longertext]'"; */
/* 			$chg[] = "url='$part[url]'"; */
/* 			$chg[] = "type='$part[type]'"; */
/* 			$chg[] = "title='$part[title]'"; */
/* 			$chg[] = "locked=$part[locked]"; */
/* 			$chg[] = "activatedate='$part[activatedate]'"; */
/* 			$chg[] = "deactivatedate='$part[deactivatedate]'"; */
/* 		} */
/* 	} */
/* 	if ($parttype == 'page') { */
/* 		$chg[] = "site_id='$newparent[site_id]'"; */
/* 		$chg[] = "section_id='$newparentid'"; */
/* 		$chg[] = "permissions='$newparent[permissions]'"; */
/* 		if ($action == "copy") { */
/* 			$chg[] = "ediscussion=$part[ediscussion]"; */
/* 			$chg[] = "archiveby='$part[archiveby]'"; */
/* 			$chg[] = "url='$part[url]'"; */
/* 			$chg[] = "type='$part[type]'"; */
/* 			$chg[] = "title='$part[title]'"; */
/* 			$chg[] = "showcreator=$part[showcreator]"; */
/* 			$chg[] = "showdate=$part[showdate]"; */
/* 			$chg[] = "showhr=$part[showhr]"; */
/* 			$chg[] = "locked=$part[locked]"; */
/* 			$chg[] = "activatedate='$part[activatedate]'"; */
/* 			$chg[] = "deactivatedate='$part[deactivatedate]'"; */
/* 			$chg[] = "active=$part[active]"; */
/* 			$chg[] = "storyorder='$part[storyorder]'"; */
/* 		} */
/* 	} */
/* 	if ($parttype == 'section') { */
/* 		$chg[] = "site_id='$newparentid'"; */
/* 		$chg[] = "permissions='$newparent[permissions]'"; */
/* 		if ($action == "copy") { */
/* 			$chg[] = "url='$part[url]'"; */
/* 			$chg[] = "type='$part[type]'"; */
/* 			$chg[] = "title='$part[title]'"; */
/* 			$chg[] = "locked=$part[locked]"; */
/* 			$chg[] = "activatedate='$part[activatedate]'"; */
/* 			$chg[] = "deactivatedate='$part[deactivatedate]'"; */
/* 			$chg[] = "active=$part[active]"; */
/* 		} */
/* 	} */
/*  */
/* 	$query .= implode(",",$chg); */
/* 	if (count($chg)) db_query($query.$where); */
/* //	print $query.$where."BR>"; */
/* //	print mysql_error()."<br>"; */
/*  */
/* 	// Make sure that we have the correct new ID */
/* 	if ($action == "copy") $newid = lastid(); */
/* 	else $newid = $id; */
/* 	 */
/* 	// Update the stories array in the newparent if not a move sub call. */
/* 	if (!($action == "move" && $isSubCall)) { */
/* 		$newparentparts = decode_array($newparent[$partarray]); */
/* 		array_push($newparentparts,$newid); */
/* 		$newparentparts = encode_array($newparentparts); */
/* 		if ($parenttype == "site") */
/* 			$query = "update $parenttable set $partarray='$newparentparts' where name='$newparentid'"; */
/* 		else */
/* 			$query = "update $parenttable set $partarray='$newparentparts' where id='$newparentid'"; */
/* 		db_query($query); */
/* //		print "$query<br>"; */
/* 	} */
/* 	 */
/* 	// Update the appropriate ids and permissions of pages lower in the hierarchy. */
/* 	if ($parttype != "story") { */
/* 		$children = decode_array($part[$childarray]); */
/* 		if ($action == "move") { */
/* 			// update the foreign keys and permissions for the entry */
/* 			foreach ($children as $childid) { */
/* 				copyPart(MOVE,$childtype,$childid,$newid,1); */
/* 			} */
/* 		} else { // copy */
/* 			// recursively copy all of the lower parts. */
/* 			foreach ($children as $childid) { */
/* 				copyPart(COPY,$childtype,$childid,$newid,1); */
/* 			} */
/* 		} */
/* 	} */
/* 	 */
/* 	if (!$isSubCall) return $newid; */
/* } */

/******************************************************************************
 * copySite - to be re-written to use objects... should be much shorter
 ******************************************************************************/
/* function copySite($orig,$dest) { */
/* 	// This function does not support the copying of userfiles in this implementation. */
/*  */
/* 	global $auser; */
/* 	$sections = decode_array(db_get_value("sites","sections","name='$orig'")); */
/* 	$nsections = array(); */
/* 	foreach ($sections as $s) { */
/* 		$sa = db_get_line("sections","id=$s"); */
/* 		$squery = "insert into sections set addedby='$auser', addedtimestamp=NOW(),"; */
/* 		$schg = array(); */
/* 		$schg[] = "site_id='$dest'"; */
/* 		$schg[] = "title='$sa[title]'"; */
/* 		$schg[] = "url='$sa[url]'"; */
/* 		$schg[] = "type='$sa[type]'"; */
/* 		$schg[] = "locked=$sa[locked]"; */
/* 		$schg[] = "active=$sa[active]"; */
/* //		$schg[] = "activatedate='$sa[activatedate]'"; */
/* //		$schg[] = "deactivatedate='$sa[deactivatedate]'"; */
/* //		$schg[] = "permissions='$sa[permissions]'"; */
/*  */
/* 		$squery .= implode(",",$schg); */
/* 		db_query($squery); */
/* 		print " &nbsp; &nbsp; &nbsp; ".$squery."<BR>"; */
/* 		print " &nbsp; &nbsp; &nbsp; ".mysql_error()."<BR>"; */
/*  */
/* 		$section = lastid(); */
/* 		$nsections[] = lastid(); */
/* 		 */
/* 		$pages = decode_array($sa[pages]); */
/* 		$npages = array(); */
/* 		foreach ($pages as $p) { */
/* 			$pa = db_get_line("pages","id=$p"); */
/* 			$pquery = "insert into pages set addedby='$auser', addedtimestamp=NOW(),"; */
/* 			$pchg = array(); */
/* 			$pchg[] = "site_id='$dest'"; */
/* 			$pchg[] = "section_id='$section'"; */
/* 			$pchg[] = "ediscussion=1";  */
/* 			$pchg[] = "archiveby='$pa[archiveby]'"; */
/* 			$pchg[] = "url='$pa[url]'"; */
/* 			$pchg[] = "type='$pa[type]'"; */
/* 			$pchg[] = "title='$pa[title]'"; */
/* 			$pchg[] = "showcreator=$pa[showcreator]"; */
/* 			$pchg[] = "showdate=$pa[showdate]"; */
/* 			$pchg[] = "locked=$pa[locked]"; */
/* 			$pchg[] = "active=$pa[active]"; */
/* //			$pchg[] = "activatedate='$pa[activatedate]'"; */
/* //			$pchg[] = "deactivatedate='$pa[deactivatedate]'"; */
/* //			$pchg[] = "permissions='$pa[permissions]'"; */
/* 			 */
/* 			$pquery .= implode(",",$pchg); */
/* 			print " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  ".$pquery."<BR>"; */
/* 			db_query($pquery); */
/* 			print " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  ".mysql_error()."<BR>"; */
/* 			$page=lastid(); */
/* 			$npages[]=lastid(); */
/* 		 */
/* 			$stories = decode_array($pa[stories]); */
/* 			$nstories = array(); */
/* 			foreach ($stories as $st) { */
/* 				$sta = db_get_line("stories","id=$st"); */
/* 				$stquery = "insert into stories set addedby='$auser', addedtimestamp=NOW(),"; */
/* 				$stchg = array(); */
/* 				$stchg[] = "site_id='$dest'"; */
/* 				$stchg[] = "section_id='$section'"; */
/* 				$stchg[] = "page_id='$page'"; */
/* 				$stchg[] = "discuss='$sta[discuss]'"; */
/* 				$stchg[] = "discusspermissions='$sta[discusspermissions]'"; */
/* 				$stchg[] = "texttype='$sta[texttype]'"; */
/* 				$stchg[] = "category='$sta[category]'"; */
/* 				$stchg[] = "shorttext='$sta[shorttext]'"; */
/* 				$stchg[] = "longertext='$sta[longertext]'"; */
/* 				$stchg[] = "url='$sta[url]'"; */
/* 				$stchg[] = "type='$sta[type]'"; */
/* 				$stchg[] = "title='$sta[title]'"; */
/* 				$stchg[] = "locked=$sta[locked]"; */
/* //				$stchg[] = "activatedate='$sta[activatedate]'"; */
/* //				$stchg[] = "deactivatedate='$sta[deactivatedate]'"; */
/* //				$stchg[] = "permissions='$sta[permissions]'"; */
/* 	 */
/* 				$stquery .= implode(",",$stchg); */
/* 				print " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ".$stquery."<BR>"; */
/* 				db_query($stquery); */
/* 				print " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ".mysql_error()."<BR>"; */
/*  */
/* 				$nstories[] = lastid(); */
/* 			} */
/* 			$stories = encode_array($nstories); */
/* 			$pquery = "update pages set stories='$stories' where id='$page'"; */
/* 			print " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  ".$pquery."<BR>"; */
/* 			db_query($pquery); */
/* 			print " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  ".mysql_error()."<BR>"; */
/* 		} */
/* 		$pages = encode_array($npages); */
/* 		$squery = "update sections set pages='$pages' where id='$section'"; */
/* 		db_query($squery); */
/* 		print " &nbsp; &nbsp; &nbsp; ".$squery."<BR>"; */
/* 		print " &nbsp; &nbsp; &nbsp; ".mysql_error()."<BR>"; */
/* 	} */
/* 	$sections = encode_array($nsections); */
/* 	$query = "update sites set sections='$sections' where name='$dest'"; */
/* 	db_query($query); */
/* 	print $query."<BR>"; */
/* } */
