<? /* $Id$ */

function makedownloadbar($o) {
	global $site,$uploaddir,$uploadurl;
	if ($o->getField("type")!='file') return;
	
	$b = db_get_line("media INNER JOIN slot ON media.FK_site=slot.FK_site","media_id=".$o->getField("longertext"));
	$filename = urldecode($b[media_tag]);
/* 	print $filename; */
	$dir = $b[slot_name];
	$size = $b[media_size];
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
	global $uploaddir;
	
	$sitename = $site;
	$query = "SELECT FK_site FROM slot WHERE slot_name='$site'";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$siteid = $a[FK_site];
	
	if (!$file[name]) {
		print "No File";
		return "ERROR";
	}
	
	$siteObj =& new site($site);
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
		log_entry("media_error","File upload attempt by $_SESSION[auser] in site $site failed.",$site,$siteid,"site");
		return "ERROR";
	} else if ($replace) {
		$size = filesize($userdir."/".$name);
		$query = "UPDATE media SET media_updated_tstamp=NOW(),FK_updatedby='".$_SESSION[aid]."',media_size='$size' WHERE media_id='$replace_id'";
		/* print $query."<br>"; */
		db_query($query);
		print mysql_error()."<br>";
		
		$media_id = $replace_id;
		
		log_entry("media_update","$_SESSION[auser] updated file: $name, id: $media_id, in site $site",$site,$siteid,"site");
		return $media_id;
	} else {
		$size = filesize($userdir."/".$name);
		$query = "INSERT INTO media SET media_tag='$name',FK_site='$siteid',FK_createdby='".$_SESSION[aid]."',FK_updatedby='".$_SESSION[aid]."',media_type='$type',media_size='$size'";
//		print $query."<br>";
		db_query($query);
//		print mysql_error()."<br>";
		
		$media_id = lastid();
		log_entry("media_upload","$_SESSION[auser] uploaded file: $name, id: $media_id, to site $site",$site,$siteid,"site");
		return $media_id;
	}
}

function copy_media($id,$newsitename) {
	global $uploaddir;
	$oldsitename = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","slot_name","media_id=$id");
	$file_name = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","media_tag","media_id=$id");
	$sourcedir  = "$uploaddir/$oldsitename";
	$destdir = "$uploaddir/$newsitename";
	$old_file_path = $sourcedir."/".$file_name;
	$new_file_path = $destdir."/".$file_name;
	if (!is_dir($destdir)) {
		mkdir($destdir,0777); 
		chmod($destdir,0775); 
	}
	if (file_exists($new_file_path)) {
		$newid = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","media_id","slot_name='$newsitename' && media_tag='$file_name'");
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
	global $uploaddir, $site, $settings;
	$query = "
			SELECT 
				* 
			FROM 
				media 
					INNER JOIN
				slot
					ON
				media.FK_site = slot.FK_site
			WHERE 
				media_id='$fileid'
	";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$a[media_tag] = urldecode($a[media_tag]);
	$siteObj =& new site($a[slot_name]);
	$file_path = $uploaddir."/".$siteObj->getField("name")."/".$a[media_tag];
//	$file_path = "../segue_userfiles/afranco/close2.gif";
//	print "file = \"$file_path\" <br>";
	if (file_exists($file_path)) {
//		$exists = file_exists($file_path);
//		print "fileexists = $exists $file_path<br> ";
		$success = unlink($file_path);
//		print "success = $success <br>";
		if ($success) {
			$query = "DELETE FROM media WHERE media_id='$fileid' LIMIT 1";
			db_query($query);
			log_entry("media_delete","$_SESSION[auser] deleted file: ".$a[media_tag].", id: $fileid, from site ".$siteObj->getField("name"),$siteObj->name,$siteObj->id,"site");
		} else {
			log_entry("media_error","Delete failed of file: ".$a[media_tag].", id: $fileid, from site ".$siteObj->getField("name")." by $_SESSION[auser]",$siteObj->name,$siteObj->id,"site");
			error("File could not be Deleted");
		}
	} else {
		log_entry("media_error","Delete failed of file: ".$a[media_tag].", id: $fileid, from site ".$siteObj->getField("name")." by $_SESSION[auser]. File does not exist. Removed entry.",$siteObj->name,$siteObj->id,"site");
		error("File does not exist. Its Entry was deleted");
		$query = "DELETE FROM media WHERE media_id='$fileid' LIMIT 1";
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

function deletePath($path) {
	// posted by georg@spieleflut.de on PHP.net 24-Dec-2001 10:28
	// This function will completely delete even a non-empty directory.
	chmod($path,0777);
	if (is_dir($path)) {
		$handle = opendir($path);
 		while($filename = readdir($handle)) {
  			if ($filename != "." && $filename != "..") {
  				deletePath($path."/".$filename);
  			}
 		}
		closedir($handle);
 		rmdir($path);
	} else {
		unlink($path);
	}
}


function dir_copy ($from_path, $to_path)
{
	// posted by dallask at sbcglobal dot net 21-Oct-2002 10:14
	// on PHP.net
	// Recursively copies a directory.
   $this_path = getcwd();
   if(!is_dir($to_path))
   {    mkdir($to_path, 0775);
   }
  
   if (is_dir($from_path))
   {
       chdir($from_path);
       $handle=opendir('.');
       while (($file = readdir($handle))!==false)
       {
           if (($file != ".") && ($file != ".."))
           {
               if (is_dir($file))
               {
                   chdir($this_path);
                   dir_copy ($from_path.$file."/", $to_path.$file."/");
                   chdir($this_path);
                   chdir($from_path);
               }
               if (is_file($file))
               {
                   chdir($this_path);
                   copy($from_path.$file, $to_path.$file);
                   chdir($from_path);
               }
           }
       }
       closedir($handle);
   }
   chdir($this_path);
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
// $bold = if on story detail then bold link
function makelink($i,$samepage=0,$e='',$newline=0,$bold=0) {
	$s = '';
	$s=(!$samepage&&$i[url])?"<a href='$i[url]' target='$i[target]'".(($e)?" $e":"").">":"";
	
	if (!$bold) {
		$s.=$i[name];
	} else {
		$s.="<b>".$i[name]."</b>";
	}
	
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

function log_entry($type,$content,$site=0,$siteunit=0,$siteunit_type="site") {
	global $dbhost, $dbuser,$dbpass, $dbdb;
	
	if ($site) {
		$query = " 
			SELECT 
				slot_id
			FROM
				slot
			WHERE
				slot_name = '$site'
		";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		$slot_id = "'".$a[slot_id]."'";
	} else {
		$slot_id = "NULL";
	}
	
	db_connect($dbhost,$dbuser,$dbpass, $dbdb);
	db_query("insert into log set 
		log_type='$type',
		log_desc='$content',
		FK_luser='".$_SESSION[lid]."',
		FK_auser='".$_SESSION[aid]."',
		FK_slot=$slot_id,
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
	
/*	global $_SESSION[auser],$atype,$classes,$ltype, $settings;
	$_SESSION[auser] = strtolower($_SESSION[auser]);
	$name = strtolower($name);
	if ($name == $_SESSION[auser]) return 1;
	if ($ltype=='admin') return 1;
	// look at the classes list.. if the site is in the classes list, then it's valid
// 	print "$atype -- $name"; 
// 	print_r($classes); 
	if ($settings[type]=="other" && $_SESSION[auser]==$settings[addedby]) return 1; 
	if ($atype == 'prof' && is_array($classes[$name])) return 1;
	if ($atype == 'prof') {
		$query = "
SELECT
	classgroup_id
FROM
	classgroup
		INNER JOIN
	user
		ON FK_owner = user_id AND user_uname = '$_SESSION[auser]'
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


$_isgroup_cache = array();
function isgroup ($group) {
	global $_isgroup_cache;
	if (isset($_isgroup_cache[$group])) return $_isgroup_cache[$group];
	$query = ("SELECT classgroup_id FROM classgroup WHERE classgroup_name='$group'");
	$r = db_query($query);
	if (db_num_rows($r)) {
		$a = db_fetch_assoc($r);
		$query = "SELECT class_id FROM class INNER JOIN classgroup ON classgroup_id = ".$a[classgroup_id];
		$r = db_query($query);
		$temp_c = array();
		while ($a = db_fetch_assoc($r))
			$temp_c[] = generateCourseCode($a[class_id]);
		$_isgroup_cache[$group] = true;
		return $temp_c;
	}
	$_isgroup_cache[$group] = false;
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
	$semester='l';
	}else{
	$semester='f';
	}
	return $semester;
}

function semorder($semester) {
	if ($semester == "w") $order = 1;
	else if ($semester == "s") $order = 2;
	else if ($semester == "l")	$order = 3;
	else if ($semester == "bl") $order = 3;
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
	class ON classgroup_id = FK_classgroup AND ".generateTermsFromCode($class)."
";
	$r = db_query($query);
	if (db_num_rows($r)) { 
		$a = db_fetch_assoc($r); 
		return $a[classgroup_name]; 
	}
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
	printc("</select>\n");
	printc("<select name='startmonth'>");
	for ($i=0; $i<12; $i++)
		printc("<option value=".($i+1). (($startmonth == $i+1)?" selected":"") . ">$months[$i]\n");
	
	printc("</select>\n<select name='startyear'>");
	$curryear = date("Y");
	for ($i=$curryear-10; $i <= ($curryear); $i++) {
		printc("<option" . (($startyear == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>");
//	printc("<br>");
	printc(" to <select name='endday'>");
	for ($i=1;$i<=31;$i++) {
		printc("<option" . (($endday == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>\n");
	printc("<select name='endmonth'>");
	for ($i=0; $i<12; $i++) {
		printc("<option value=".($i+1) . (($endmonth == $i+1)?" selected":"") . ">$months[$i]\n");
	}
	printc("</select>\n<select name='endyear'>");
	for ($i=$curryear; $i <= ($curryear+5); $i++) {
		printc("<option" . (($endyear == $i)?" selected":"") . ">$i\n");
	}
	printc("</select>");
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

function printpre($array, $return=FALSE) {
	$string = "\n<pre>";
	$string .= print_r($array, TRUE);
	$string .= "\n</pre>";
	
	if ($return)
		return $string;
	else
		print $string;
}

