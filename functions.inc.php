<? /* $Id$ */

function makedownloadbar($o) {
	global $site,$uploaddir,$uploadurl;
	if ($o->getField("type")!='file') return;
	
	$b = db_get_line("media","media_id=".$o->getField("longertext"));
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
		log_entry("media_error","File upload attempt by $auser in site $site failed.",$site);
		return "ERROR";
	} else if ($replace) {
		$size = filesize($userdir."/".$name);
// !!!!!!!!!!!!!!!!! UPDATE THIS QUERY
		$query = "UPDATE media SET media_updated_tstamp=NOW(),addedby='$auser',media_size='$size' WHERE media_id='$replace_id'";
		/* print $query."<br>"; */
		db_query($query);
		print mysql_error()."<br>";
		
		$media_id = $replace_id;
		
		log_entry("media_update","$auser updated file: $name, id: $media_id, in site $site",$site);
		return $media_id;
	} else {
		$size = filesize($userdir."/".$name);
// !!!!!!!!!!!!!!!!! UPDATE THIS QUERY		
		$query = "insert into media set name='$name',site_id='$site',addedtimestamp=NOW(),addedby='$auser',type='$type',size='$size'";
//		print $query."<br>";
		db_query($query);
//		print mysql_error()."<br>";
		
		$media_id = lastid();
		log_entry("media_upload","$auser uploaded file: $name, id: $media_id, to site $site",$site);
		return $media_id;
	}
}

function copy_media($id,$newsitename) {
	global $uploaddir;
// !!!!!!!!!!!!!!!!! UPDATE THIS QUERY
	$oldsitename = db_get_value("media","site_id","id=$id");
// !!!!!!!!!!!!!!!!! UPDATE THIS QUERY	
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
	$file_path = $uploaddir."/".$a[site_id]."/".$a[name];
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
			log_entry("media_delete","$auser deleted file: ".$a[name].", id: $fileid, from site ".$a[site_id],$a[site_id]);
		} else {
			log_entry("media_error","Delete failed of file: ".$a[name].", id: $fileid, from site ".$a[site_id]." by $auser",$a[site_id]);
			error("File could not be Deleted");
		}
	} else {
		log_entry("media_error","Delete failed of file: ".$a[name].", id: $fileid, from site ".$a[site_id]." by $auser. File does not exist. Removed entry.",$a[site_id]);
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

function log_entry($type,$content,$site="",$section="",$page="",$story="") {
	global $dbhost, $dbuser,$dbpass, $dbdb, $auser, $luser;
	db_connect($dbhost,$dbuser,$dbpass, $dbdb);
	// see, what kind of site unit it is
	$siteunit_type = $story ? "story" : ($page ? "page" : ($section ? "section" : ($site ? "site" : NULL)));
	switch ($siteunit_type) {
		case "story"   : $siteunit = $story; break;
		case "page"    : $siteunit = $page; break;
		case "section" : $siteunit = $section; break;
		case "site"    : $siteunit = $site; break;
		default        : $siteunit = NULL;
	}
	$q = "INSERT INTO log SET log_type='$type',log_desc='$content',FK_luser=$lid,FK_auser=$aid,FK_siteunit=".($siteunit?$siteunit:"NULL").",log_siteunit_type=".($siteunit_type?$siteunit_type:"NULL");
	db_query($q);
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
	if ($atype == 'prof' && db_line_exists("classgroups","name='$name' and owner='$auser'")) return 1;
	
	return 0;
*/
	return 1;
}

function insite($site,$section,$page=0,$story=0) {
	$ok=1;
// !!!!!!!!!!!!!!!!! UPDATE THIS QUERY	
	if (!in_array($section,decode_array(db_get_value("sites","sections","name='$site'")))) $ok=0;
	if ($page && !in_array($page,decode_array(db_get_value("sections","pages","id=$section")))) $ok=0;
	if ($story && !in_array($story,decode_array(db_get_value("pages","stories","id=$page")))) $ok=0;
	return $ok;
}

function isgroup ($group) {
	global $auser;
// !!!!!!!!!!!!!!!!! UPDATE THIS QUERY	
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
	if ($semester == "w") $order = 1;
	else if ($semester == "s") $order = 2;
	else if ($semester == "ls")	$order = 3;
	else if ($semester == "f") $order = 4;
	return $order;
}

function inclassgroup($class) {
// !!!!!!!!!!!!!!!!! UPDATE THIS QUERY
	$query = "select * from classgroups where classes like '%$class%'";
	$r = db_query($query);
	if (db_num_rows($r)) { $a = db_fetch_assoc($r); return $a[name]; }
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
