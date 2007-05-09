<? /* $Id$ */

function makedownloadbar($o) {
	global $site,$section,$page,$uploaddir,$uploadurl;
	if ($o->getField("type")!='file') return;
	
	$b = db_get_line("media INNER JOIN slot ON media.FK_site=slot.FK_site","media_id='".addslashes($o->getField("longertext"))."'");
	
	ob_start();
	print "\n";
	print "\n<div class='downloadbar' style='margin-bottom: 10px'>";
	print "\n\t<table width='100%' cellpadding='0' cellspacing='0'>\n\t<tr>\n\t\t<td>";
	if ($o->getField("title")) {
		print "\n\t\t\t<strong><a href='index.php?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page&amp;story=".$o->id."&amp;detail=".$o->id."'>";
		print "".spchars($o->getField("title"))."</a></strong>\n\t\t\t<br />";
	}
	
	printDownloadLink($b);
	
	print "\n\t\t\t\t\t<div style='font-size: smaller; margin-bottom: 10px;'>";
	printCitation($b);
	print "\n\t\t\t\t\t</div>";
	
//	print "<hr size='1' />";
	if ($o->getField("shorttext")) print "".stripslashes($o->getField("shorttext"));
	print "\n\t\t</td>\n\t</tr>\n\t</table>\n</div>\n";
	return ob_get_clean();
}

function printDownloadLink($mediaRow) {
	global $site,$section,$page,$uploaddir,$uploadurl;
	$filename = urldecode($mediaRow[media_tag]);
/* 	print $filename; */
	$dir = $mediaRow[slot_name];
	$size =$mediaRow[media_size];
	$fileurl = "$uploadurl/$dir/$filename";
	$filepath = "$uploaddir/$dir/$filename";
	$filesize = convertfilesize($size);
	
	print "\n\t\t\t<table width='100%' cellpadding='0' cellspacing='0' style='margin: 0px; border: 0px;'>\n\t\t\t\t<tr>\n\t\t\t\t\t<td class='leftmargin' align='left'>\n\t\t\t\t\t\t<a href='$fileurl' target='new_window'>\n\t\t\t\t\t\t\t<img src='downarrow.gif' border='0' width='15' height='15' align='middle' alt='Download Arrow' />\n\t\t\t\t\t\t\t $filename\n\t\t\t\t\t\t</a>\n\t\t\t\t\t</td>\n\t\t\t\t\t<td align='right' style='padding-right: 10px;'><b>$filesize</b></td>\n\t\t\t\t</tr>\n\t\t\t</table>";
}

/**
 * Print a citation from a row of the media table
 * 
 * @param array $mediaRow
 * @return <##>
 * @access public
 * @since 10/18/06
 */
function printCitation ($mediaRow) {
	// Citation
	
	ob_start();
	if ($mediaRow['author'])
		print $mediaRow['author'].". ";
	
	if ($mediaRow['title_part'])
		print '"'.$mediaRow['title_part'].'"';
	
	if ($mediaRow['title_part'] && $mediaRow['title_whole'])
		print " ";
	else if ($mediaRow['title_part'])
		print ". ";
	
	if ($mediaRow['title_whole'])
		print '<em>'.$mediaRow['title_whole'].'</em>. ';
		
	if ($mediaRow['publisher'])
		print $mediaRow['publisher'];
	
	if ($mediaRow['publisher'] && $mediaRow['pubyear'])
		print ", ";
	else if ($mediaRow['publisher'])
		print ". ";
	
	if ($mediaRow['pubyear'])
		print $mediaRow['pubyear'].". ";
		
	if ($mediaRow['pagerange'])
		print "(".$mediaRow['pagerange'].") ";
	
// 	if ($mediaRow['is_published'])
// 		print " &copy; ";
	
	print trim(ob_get_clean());
}
	
function mkfilesize($filename) {
	$j = 0;
	$ext = array("B","KB","MB","GB","TB");
//	$filename = ereg_replace(" ","\\ ",$filename);
//	print "<br /><br />$filename<br /><br />";
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
	$query = "SELECT FK_site FROM slot WHERE slot_name='".addslashes($site)."'";
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
//	print "$extn <br />";
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
	
//	print "$userdir/$file[name]<br />";
	if (!is_dir($userdir)) {
		mkdir($userdir,0700); 
		chmod($userdir,0700); 
	}
	
	if ($replace) {
		$unlink = unlink($userdir."/".$name);
		/* print "unlink: $unlink"; */
	}
	
	if (!is_writeable($userdir)) {
		print "<strong>Can not write to '".$userdir."'. <br />Please contact your system administrator with the message above to fix this problem.</strong> <br />";
		return "ERROR";
	}
	if (file_exists($userdir."/".$name) && !is_writeable($userdir."/".$name)) {
		print "<strong>Can not write to '".$userdir."/".$name."'. <br />Please contact your system administrator with the message above to fix this problem.</strong> <br />";
		return "ERROR";
	}
	
	if ($allreadyuploaded) {
		$r = copy($file[tmp_name],"$userdir/".$name);
	} else {
/* 		print "move uploaded file ($file[tmp_name], $userdir/$file[name])<br />"; */
		$r=move_uploaded_file($file['tmp_name'],$userdir."/".$name);
	}
	if (!$r) {
		print "Upload file error!<br />";
		log_entry("media_error","File upload attempt by $_SESSION[auser] in site $site failed.",$site,$siteid,"site");
		return "ERROR";
	} else if ($replace) {
		$size = filesize($userdir."/".$name);
		$query = "UPDATE media SET media_updated_tstamp=NOW(),FK_updatedby='".addslashes($_SESSION[aid])."',media_size='".addslashes($size)."' WHERE media_id='".addslashes($replace_id)."'";
		/* print $query."<br />"; */
		db_query($query);
		print mysql_error()."<br />";
		
		$media_id = $replace_id;
		
		log_entry("media_upload","$_SESSION[auser] updated file: $name, id: $media_id, in site $site",$site,$siteid,"site");
		return $media_id;
	} else {
		$size = filesize($userdir."/".$name);
		$query = "INSERT INTO media SET media_tag='".addslashes($name)."',FK_site='".addslashes($siteid)."',FK_createdby='".addslashes($_SESSION[aid])."',FK_updatedby='".addslashes($_SESSION[aid])."',media_type='".addslashes($type)."',media_size='".addslashes($size)."'";
//		print $query."<br />";
		db_query($query);
//		print mysql_error()."<br />";
		
		$media_id = lastid();
		log_entry("media_upload","$_SESSION[auser] uploaded file: $name, id: $media_id, to site $site",$site,$siteid,"site");
		return $media_id;
	}
}

function copy_media($id,$newsitename) {
	global $uploaddir;
	$oldsitename = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","slot_name","media_id='".addslashes($id)."'");
	$file_name = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","media_tag","media_id='".addslashes($id)."'");
	$sourcedir  = "$uploaddir/$oldsitename";
	$destdir = "$uploaddir/$newsitename";
	$old_file_path = $sourcedir."/".$file_name;
	$new_file_path = $destdir."/".$file_name;
	if (!is_dir($destdir)) {
		mkdir($destdir,0700); 
		chmod($destdir,0700); 
	}
	if (file_exists($new_file_path)) {
		$newid = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","media_id","slot_name='".addslashes($newsitename)."' && media_tag='".addslashes($file_name)."'");
	} else {
		$file = array();
		$file[name] = $file_name;
		$file[tmp_name] = $old_file_path;
//		print_r ($file);
//		print "<br />";
		$newid = copyuserfile($file,$newsitename,0,0,1);
	}
	return $newid;
}

function copy_media_with_fname($fname, $oldsitename, $newsitename) {
	$mediaid = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","media_id","slot_name='".addslashes($oldsitename)."' AND media_tag='".addslashes($fname)."'");
	return copy_media($mediaid, $newsitename);
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
				media_id='".addslashes($fileid)."'
	";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$a[media_tag] = urldecode($a[media_tag]);
	$siteObj =& new site($a[slot_name]);
	$file_path = $uploaddir."/".$siteObj->getField("name")."/".$a[media_tag];
//	$file_path = "../segue_userfiles/afranco/close2.gif";
//	print "file = \"$file_path\" <br />";
	if (file_exists($file_path)) {
//		$exists = file_exists($file_path);
//		print "fileexists = $exists $file_path<br /> ";
		$success = unlink($file_path);
//		print "success = $success <br />";
		if ($success) {
			$query = "DELETE FROM media WHERE media_id='".addslashes($fileid)."' LIMIT 1";
			db_query($query);
			log_entry("media_delete","$_SESSION[auser] deleted file: ".$a[media_tag].", id: $fileid, from site ".$siteObj->getField("name"),$siteObj->name,$siteObj->id,"site");
		} else {
			log_entry("media_error","Delete failed of file: ".$a[media_tag].", id: $fileid, from site ".$siteObj->getField("name")." by $_SESSION[auser]",$siteObj->name,$siteObj->id,"site");
			error("File could not be Deleted");
		}
	} else {
		log_entry("media_error","Delete failed of file: ".$a[media_tag].", id: $fileid, from site ".$siteObj->getField("name")." by $_SESSION[auser]. File does not exist. Removed entry.",$siteObj->name,$siteObj->id,"site");
		error("File does not exist. Its Entry was deleted");
		$query = "DELETE FROM media WHERE media_id='".addslashes($fileid)."' LIMIT 1";
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
function add_link($array,$name="",$url="",$extra='',$id=0,$target="_self",$type='normal',$content='') {
	global $$array;
	if ($type == "page" || $type == "link") {
		$type = 'normal';
	}
	array_push($$array,array("type"=>$type,"name"=>$name,"url"=>$url,"extra"=>$extra,"id"=>$id,"target"=>$target,"content"=>$content));
//	return $array;
}

// makelink($i,$samepage,$e)
// $i = single entry of a navbar variable (made with add_link)
// $samepage = are we on the page this link describes?
// $e = extra HTML for the link
// $bold = if on story detail then bold link
function makelink($i,$samepage=0,$e='',$newline=0,$bold=0) {
	$s = '';
	$s=(!$samepage&&$i[url])?"<a href='$i[url]'".(($i['target'])?" target='$i[target]'":"").(($e)?" $e":"").">":"";
	
	if (!$bold) {
		$s.=$i[name];
	} else {
		$s.="<b>".$i[name]."</b>";
	}
	
	$s.=(!$samepage&&$i[url])?"</a>\n":"";
	$s.=($i[extra])?(($newline)?"<div class='nav_extras'>":" ").$i[extra].(($newline)?"</div>\n":""):"";
	return $s;
}

function printc($string) {
	global $content;
//	print "printc called...<br />";
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
				slot_name = '".addslashes($site)."'
		";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		$slot_id = "'".addslashes($a[slot_id])."'";
	} else {
		$slot_id = "NULL";
	}
	
	db_connect($dbhost,$dbuser,$dbpass, $dbdb);
	db_query("insert into log set 
		log_type='".addslashes($type)."',
		log_desc='".addslashes($content)."',
		FK_luser='".addslashes($_SESSION[lid])."',
		FK_auser='".addslashes($_SESSION[aid])."',
		FK_slot=".$slot_id.",
		FK_siteunit='".addslashes($siteunit)."',
		log_siteunit_type='".addslashes($siteunit_type)."'
	");
}

/******************************************************************************
 * Get all tags for a given site (or section or page...)
 ******************************************************************************/

function get_tags($site,$section,$page, $record_type="story") {
	global $dbhost, $dbuser,$dbpass, $dbdb;
	$tags = array();
	// Validate that the record type is safe.
	if (!preg_match("/^[a-z]+$/i", $record_type))
		die("Invalid record type - line: ".__LINE__." in  ".__FILE__);

	$record_type_id = $record_type."_id";
	
	if ($site && $record_type == "story") {	
		$query = " 
			SELECT
				DISTINCT record_tag
			FROM
				tags
			INNER JOIN
				$record_type
				ON FK_record_id = $record_type_id
			INNER JOIN
				page
				ON FK_page = page_id
			INNER JOIN
				section
				ON FK_section = section_id
			INNER JOIN
				site
				ON section.FK_site = site.site_id
			INNER JOIN
			 slot
				ON site.site_id = slot.FK_site
			WHERE
				slot_name = '".addslashes($site)."'
			ORDER BY
				record_tag ASC
		";
	
		$r = db_query($query);
		
		while ($a = db_fetch_assoc($r)) {
			$record_tag = $a[record_tag];
			$query2 = " 
			SELECT
				COUNT(*) AS num_stories
			FROM
				tags
			INNER JOIN
				$record_type
				ON FK_record_id = $record_type_id
			INNER JOIN
				page
				ON FK_page = page_id
			INNER JOIN
				section
				ON FK_section = section_id
			INNER JOIN
				site
				ON section.FK_site = site.site_id
			INNER JOIN
				slot
				ON site.site_id = slot.FK_site
			WHERE
				slot_name = '".addslashes($site)."'
			AND
				record_tag = '".addslashes($record_tag)."'
			";
			$r2 = db_query($query2);
			$a2 = db_fetch_assoc($r2);
			$tags[$a[record_tag]] = $a2[num_stories];

		}
	}
//	printpre($tags);
//	printpre($tags[tag]);
	//exit();
	return $tags;
}

/******************************************************************************
 * Gets all tags for a given record (story or discussion or...)
 ******************************************************************************/

function get_record_tags($record_id) {
	global $dbhost, $dbuser,$dbpass, $dbdb;

	$tags = array();
	$query = " 
		SELECT
			record_tag
		FROM
			tags
		WHERE
			FK_record_id = '".addslashes($record_id)."'
		";
	$r = db_query($query);
	while ($a = db_fetch_assoc($r)) {
		$tags[]= $a[record_tag];
	}

	return $tags;
}

/******************************************************************************
 * deletes all tags for a given record (story or discussion or...)
 ******************************************************************************/

function delete_record_tags($site,$record_id,$record_type,$user_id='') {
	global $dbhost, $dbuser,$dbpass, $dbdb;

	if ($site && $record_id) {
		$query = " 
		DELETE
		FROM
			tags
		WHERE
			FK_record_id = '".addslashes( $record_id)."'
		AND
			record_type = '".addslashes($record_type)."'
		";
		$r = db_query($query);
	}
}

/******************************************************************************
 * saves all tags for a given record (story or discussion or...)
 * save_record_tags = urlencoded array of tags to save
 * delete_record_tags = urlencoded array of tags to delete
 * record_type = story or discussions or...
 * record_id = story_id or discussion_id or ...
 ******************************************************************************/

function save_record_tags($save_record_tags,$delete_record_tags,$record_id,$user_id,$record_type) {
	global $dbhost, $dbuser,$dbpass, $dbdb;

	if ($record_id) {
	
		//check if record_tag to be saved already exists...
		if (is_array($save_record_tags)) {
			foreach ($save_record_tags as $record_tag) {
				$query = "
				SELECT
					record_tag
				FROM
					tags
				WHERE
					FK_record_id = '".addslashes($record_id)."'
				AND
					record_tag = '".addslashes($record_tag)."'				
				";
				$r = db_query($query);
				//printpre($query);
	
				//if record tag doesn't exist then add
				if (db_num_rows($r) == 0) {
					$query = " 
					INSERT INTO
						tags
					SET
						record_type = '".addslashes($record_type)."', 
						FK_record_id = '".addslashes($record_id)."', 
						FK_user_id = '".addslashes($user_id)."', 
						record_tag = '".addslashes($record_tag)."', 
						record_tag_added = NOW();
					";
					$r = db_query($query);
				}
		
			}
		}
//		printpre(count($delete_record_tags));
//		printpre(count($save_record_tags));

		//check again that record tag to be deleted exists	
		if (is_array($delete_record_tags)) {
			foreach ($delete_record_tags as $record_tag) {
				$query = "
				SELECT
					record_tag
				FROM
					tags
				WHERE
					FK_record_id = '".addslashes($record_id)."'
				AND
					record_tag = '".addslashes($record_tag)."'				
				";
				$r = db_query($query);
				
				// if record tag does exist, then delete
				if (db_num_rows($r)) {
					$query = " 
					DELETE FROM
						tags
					WHERE
						record_type = '".addslashes($record_type)."'
					AND
						FK_record_id = '".addslashes($record_id)."'
					AND
						FK_user_id = '".addslashes($user_id)."' 
					AND
						record_tag = '".addslashes($record_tag)."' 
					";
					$r = db_query($query);
				}
		
			}
		}
		
		
	}
}

/******************************************************************************
 * Saves previous version to version table
 ******************************************************************************/
function save_version ($version_short, $version_long, $story_id, $version_comments) {
	global $dbhost, $dbuser,$dbpass, $dbdb;

	// make sure number of versions is within limit
// 	$num_versions = count(get_versions($story_id));
//	printpre($num_versions);
	//exit;
// 	if ($num_versions > 30) {
// 		$query = "
// 			SELECT
// 				version_order
// 			FROM
// 				version
// 			WHERE
// 				FK_parent = '".addslashes($story_id)."'
// 			ORDER BY
// 				version_created_tstamp ASC
// 			LIMIT
// 				0,1
// 		";
// 		$r = db_query($query);
// 		$a = db_fetch_assoc($r);
// 		$last_version_num = $a['version_order'];
// 	
// 		$query = "
// 			DELETE FROM
// 				version
// 			WHERE
// 				FK_parent = '".addslashes($story_id)."'
// 				AND
// 				version_order = '".addslashes($last_version_num)."'		
// 		";
// 	
// 		$r = db_query($query);
// 	}
	
	// get the last version number
	$query = "
		SELECT
			version_order, version_text_short, version_text_long
		FROM
			version
		WHERE
			FK_parent = '".addslashes($story_id)."'
		ORDER BY
			version_order DESC
		LIMIT
			0,1
	";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	$last_version_num = $a['version_order'];
	$last_version_text_short = urldecode($a['version_text_short']);
	$last_version_text_long = urldecode($a['version_text_long']);
// 	printpre(htmlspecialchars($last_version_text_short));
// 	printpre(htmlspecialchars($version_short));
//	printpre("versioncommment: ".$version_comments);
//	 exit;
		
	$version_number = $last_version_num + 1;
	
	$lastLines = explode("\n", $last_version_text_short);
	$newLines = explode("\n", $version_short);
	for ($i =0; $i < max(count($newLines), count($lastLines)); $i++) {
		if ($lastLines[$i] != $newLines[$i]) {
// 			printpre("<strong>Lines $i are not equal:</strong>");	
// 			printpre("Old: ".htmlspecialchars($lastLines[$i]));
// 			printpre("New: ".htmlspecialchars($newLines[$i]));
		}
	}

	if ($last_version_text_short != $version_short) {
//		printpre("not equal...");
// 		exit;
		$query = " 
		INSERT INTO
			version
		SET
			FK_parent = '".addslashes($story_id)."', 
			FK_createdby ='".addslashes($_SESSION[aid])."',
			version_order = '".addslashes($version_number)."', 
			version_created_tstamp = NOW(), 
			version_text_short = '".addslashes(urlencode($version_short))."', 
			version_text_long = '".addslashes(urlencode($version_long))."',
			version_comments = '".addslashes($version_comments)."'
		";
	//	printpre($query);
	//	exit;
		$r = db_query($query);
	}

}

/******************************************************************************
 * Gets previous version(s) and returns a list
 * if no version_id is passed then gets all versions
 ******************************************************************************/
function get_versions ($story_id, $version_num = 0) {
	global $dbhost, $dbuser,$dbpass, $dbdb;
	
	$versions = array();
	
	if ($version_num == 0) {	
		$where = "FK_parent = '".addslashes($story_id)."'";
	} else {
		$where = "
			FK_parent = '".addslashes($story_id)."'
			AND
			version_order = '".addslashes($version_num)."'
		";
	}
	$query = "
		SELECT
			*
		FROM
			version
		WHERE
			$where	
		ORDER BY
			version_created_tstamp DESC
	";
//	printpre($query);
	//exit;
	$r = db_query($query);
	
	while ($a = db_fetch_assoc($r)) {
		$version_author = $a[FK_createdby];
		$version_authorname = db_get_value("user", "user_fname", "user_id = '".addslashes($version_author)."'");
		$version_created = timestamp2usdate($a[version_created_tstamp]);
		$version[FK_createdby]= $version_authorname;
		$version[version_order]= $a[version_order];
		$version[version_created_tstamp]= $version_created;
		$version[version_text_short]= $a[version_text_short];
		$version[version_text_long]= $a[version_text_long];
		$version[version_comments]= $a[version_comments];
		$version[version_id]= $a[version_id];
		$versions[] = $version;
	}
	return $versions;

}


/******************************************************************************
 * Get all records with a given tag
 * returns array with story, page and section ids
 ******************************************************************************/

function get_tagged_stories ($site,$section,$page,$tag,$record_type="story") {
	global $dbhost, $dbuser,$dbpass, $dbdb;
	$tagged_stories = array();
	$story_id = array();
	$page_id = array();
	$section_id = array();
	
	// Validate that the record type is safe.
	if (!preg_match("/^[a-z]+$/i", $record_type))
		die("Invalid record type - line: ".__LINE__." in  ".__FILE__);
		
	$record_type_id = $record_type."_id";
	
	if ($site) {
		$query = " 
		SELECT
			story_id, page_id, section_id
		FROM
			tags
		INNER JOIN
			$record_type
			ON FK_record_id = $record_type_id
		INNER JOIN
			page
			ON FK_page = page_id
		INNER JOIN
		 	section
			ON FK_section = section_id
		INNER JOIN
			site
			ON section.FK_site = site.site_id
		INNER JOIN
		 slot
			ON site.site_id = slot.FK_site
		WHERE
			slot_name = '".addslashes($site)."'
		AND
			record_tag = '".addslashes($tag)."'
		ORDER BY
			record_tag_added DESC
		";
		
	//	printpre($query);
		$r = db_query($query);
		while ($a = db_fetch_assoc($r)) {
			$tagged_stories[story_id][]= $a[story_id];
			$tagged_stories[page_id][]= $a[page_id];
			$tagged_stories[section_id][]= $a[section_id];
		}
	}
	return $tagged_stories;
}


function htmlbr($string) {
	// It seems that Safari (at least) submits line returns that are \r\n instead
	// of just \n. This was causing a doubling of line-returns.
	return preg_replace("/((\r?)\n(\r?))/","<br />",$string);
}

function sitenamevalid($name) {
	/******************************************************************************
	 * sitenamevalid checks to see if $name has a value
	 * it is possible for $name to become null and a no name site to be created
	 * when a user creates a site and then immediately uses the browser back
	 * to tweak the appearance...
	 ******************************************************************************/	
	if ($name =='') { 
		return 0; 
	} else { 
		return 1; 
	} 
}

function insite($site,$section,$page=0,$story=0) {
	$ok=1;
	if (!in_array($section,decode_array(db_get_value("sites","sections","name='".addslashes($site)."'")))) $ok=0;
	if ($page && !in_array($page,decode_array(db_get_value("sections","pages","id='".addslashes($section)."'")))) $ok=0;
	if ($story && !in_array($story,decode_array(db_get_value("pages","stories","id='".addslashes($page)."'")))) $ok=0;
	return $ok;
}


$_isgroup_cache = array();
function isgroup ($group) {
	global $_isgroup_cache;
	if (isset($_isgroup_cache[$group])) 
		return $_isgroup_cache[$group];
	
	$query = "SELECT class_id FROM class INNER JOIN classgroup ON FK_classgroup = classgroup_id WHERE classgroup_name='".addslashes($group)."'";
	$r = db_query($query);
	
	if (db_num_rows($r)) {
		$temp_c = array();
		while ($a = db_fetch_assoc($r)) {
			$temp_c[] = generateCourseCode($a[class_id]);
		}
		$_isgroup_cache[$group] = true;
		return $temp_c;
	}
	$_isgroup_cache[$group] = false;
	return 0;
}

/**
 * Return the current semester, 
 * 
 * @return string, the semester key of the current semester.
 * @access public
 * @date 9/7/04
 */
function currentsemester () {
	global $cfg;
	
	$currDay = date("z");
	
	foreach (array_keys($cfg['semesters']) as $semesterKey) {
		
		$startDay = date("z", 
						strtotime(				
							$cfg['semesters'][$semesterKey]['start_month']
							."/".$cfg['semesters'][$semesterKey]['start_day']
						)
					);
		$endDay = date("z", 
						strtotime(				
							$cfg['semesters'][$semesterKey]['end_month']
							."/".$cfg['semesters'][$semesterKey]['end_day']
						)
					);
		
		// Process if the semester doesn't go across the new year
		if (($endDay - $startDay) >= 0) {
			if ($currDay >= $startDay && $currDay <= $endDay)
				return $semesterKey;
		} 
		// Process if the semester goes accross the new year
		else {
			if ($currDay >= $startDay || $currDay <= $endDay)
				return $semesterKey;
		}
		
	}
		
	return $semester;
}


/**
 * The semorder function returns what order througout the calender year the 
 * semesters occur, this information can then be used to determine if
 * a semester has yet to occur 
 * 
 * @param string $semester
 * @return integer, the order of the semester
 * @access public
 * @date 9/8/04
 */
function semorder($semester) {
	global $cfg;
	$order = 1;
	
	foreach (array_keys($cfg['semesters']) as $semesterKey) {
		if ($semester == $semesterKey) {
			return $order;
		} else
			$order++;
	}
	
	printerr("Semester, '$semester', is not specified in the config.");
}

function inclassgroup($class) {
	$query = "
		SELECT
			classgroup_name
		FROM
			classgroup
				INNER JOIN
			class ON classgroup_id = FK_classgroup AND ".addslashes(generateTermsFromCode($class))."
	";
	$r = db_query($query);
	if (db_num_rows($r)) { 
		$a = db_fetch_assoc($r); 
		return $a[classgroup_name]; 
	}
	return 0;
}


/**
 * Determin if the semester and year specified are the current one
 * 
 * @param string $semester The semester in question.
 * @param integer $year		The year of the semester in question.
 * @return boolean
 * @access public
 * @date 9/9/04
 */
function isSemesterNow ($semester, $year) {
	global $cfg;
	
	// If we aren't in the semester that is specified, don't bother
	// checking the year.
	if ($semester != currentSemester()) {
		return FALSE;
	}
	
	// Make sure we have a 4-digit year.
	if (strlen($year) == 2) {
		$year = $year + 2000;
	}
	
	// We are good if we are in the same year as asked for.
	if (date("Y") == $year) {
		return TRUE;
	}
	
	// If we aren't in the same year, then we need to check if we are in a
	// year beyond the specified one if the semester in question spans the 
	// NewYear.
	
	$startDay = date("z", 
					strtotime(				
						$cfg['semesters'][$semester]['start_month']
						."/".$cfg['semesters'][$semester]['start_day']
					)
				);
	$endDay = date("z", 
					strtotime(				
						$cfg['semesters'][$semester]['end_month']
						."/".$cfg['semesters'][$semester]['end_day']
					)
				);
	
	// If the semester in question doesn't go across the new year,
	// then we are definately not in it. since we checked before
	// if our year was the same as the semester in question.
	if (($endDay - $startDay) >= 0) {
			return FALSE;
	} 
	
	// If the semester in question goes accross the new year, 
	// make sure that we are just a year beyond the specified
	// semester and still before the end day of the semester.
	else {
		if (date("Y") == ($year+1) && date("z") <= $endDay)
			return TRUE;
		else
			return FALSE;
	}
}

/**
 * Determin if the semester and year specified are in the past
 * 
 * @param string $semester The semester in question.
 * @param integer $year		The year of the semester in question.
 * @return boolean
 * @access public
 * @date 9/9/04
 */
function isSemesterPast ($semester, $year) {
	global $cfg;
	
	// If we are in the semester that is specified, don't bother
	// continuing. Since we aren't past yet.
	if (isSemesterNow($semester, $year)) {
		return FALSE;
	}
	
	// Make sure we have a 4-digit year.
	if (strlen($year) == 2) {
		$year = $year + 2000;
	}
	
	// If our year is greater than the current one, then we definately
	// aren't past yet.
	if ($year > date("Y"))
		return FALSE;
		
	// If our year is less than the current one, then we definately
	// are past.
	if ($year < date("Y"))
		return TRUE;
	
	
	// If we are in the same year, then we need to check if we are before or
	// after the semester in question.
	$currDay = date("z");
	
	$startDay = date("z", 
					strtotime(				
						$cfg['semesters'][$semester]['start_month']
						."/".$cfg['semesters'][$semester]['start_day']
					)
				);
	
	
	// If our day of the year is after the start of the semester
	// (and we've already checked that we are not in this semester),
	// then the semester is past. Otherwise it is future.
	if ($currDay > $startDay)
		return TRUE;
	else
		return FALSE;
}

/**
 * Determin if the semester and year specified are in the future
 * 
 * @param string $semester The semester in question.
 * @param integer $year		The year of the semester in question.
 * @return boolean
 * @access public
 * @date 9/9/04
 */
function isSemesterFuture ($semester, $year) {
	
	// If we are in the semester that is specified or it is past, don't bother
	// continuing.
	if (isSemesterNow($semester, $year) || isSemesterPast($semester, $year)) {
		return FALSE;
	} 
	// If the semester in question is not the current one or in the past,
	// then it must be in the future.
	else {
		return TRUE;
	}
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

/******************************************************************************
 * the reorder function -- recieves an array, id and a direction... and then returns the new array
 ******************************************************************************/
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
	printc("<form action='$PHP_SELF?$sid&amp;action=site&amp;site=$site&amp;section=$section&amp;page=$page' method='post'>");
	printc("<input type='hidden' name='usesearch' value='1' />");

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
//	printc("<br />");
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
	printc(" <input type='submit' class='button' value='go' />");
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
		$a = db_get_line("stories","id='".addslashes($s)."'");
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
	printc("<b>Content ranging from $txtstart to $txtend.</b><br /><br />");
	return $newstories;
}

function handlestoryorder($stories,$order) {
	// reorders the stories array passed to it depending on the order specified.
	// Orders: addedesc, addedasc, editeddesc, editedasc, author, editor, category, titledesc, titleasc
	return $stories;
}

function printpre($array, $return=FALSE) {
	ob_start();
	print "\n<pre>";
	print_r($array);
	print "\n</pre>";
	
	if ($return)
		return ob_get_clean();
	else
		ob_end_flush();
}

/**
 * Var dump a variable inside of <pre> tags.
 * 
 * @param <##>
 * @return <##>
 * @access public
 * @since 10/24/06
 */
function var_dumpPre($array, $return=FALSE) {
	ob_start();
	print "\n<pre>";
	var_dump($array);
	print "\n</pre>";
	
	if ($return)
		return ob_get_clean();
	else
		ob_end_flush();
}

/**
 * print a string outside of any output buffers
 * 
 * @param string $string
 * @return void
 * @access public
 * @since 10/24/06
 */
function printOb0 ($string) {
	// move the output buffers out of the way
	$outputBuffers = array();
	$level = ob_get_level();
	while ($level > 0) {
		$outputBuffers[$level] = ob_get_clean();
		$level = ob_get_level();
	}
	
	// Print out the string
	print $string;
	flush();
	
	// rebuild the output buffers
	ksort($outputBuffers);
	foreach ($outputBuffers as $level => $data) {
		ob_start();
		print $data;
		unset($outputBuffers[$level]);
	}
}


function _error_handler($num, $str, $file, $line, $context) {
	if ($num & E_NOTICE) return;
	print "ERROR! ($num) $str<br/>";
	print "in $file:$line  --> ";
	var_dump($context);
	print "<p>";
	printpre(print_r(debug_backtrace(),true));
}

//set_error_handler("_error_handler");

/**
 * Order an array of classes. The input array should contain elements
 * which are arrays containing the following fields: sem, year, code
 * 
 * @param array $classes The classes array to sort
 * @param optional const $direction One of the directions to pass to array_multisort().
 * @return array The sorted array of classes
 * @access public
 * @date 9/9/04
 */
function sortClasses ( $classes, $direction=SORT_DESC) {
	global $cfg;
	
	
	$years = array();
	
	$semesterOrder = array_keys($cfg['semesters']);
	$semesters = array();
	
	$codes = array();
	$sections = array();
	
	foreach($classes as $key => $class) {
		// get the year-classkey relation.
		if ($class['year'] < 100)
			$years[$key] = '20'.$class['year'];
		else
			$years[$key] = $class['year'];
		
		// get the semesterorder-classkey relation
		$semesters[$key] = array_search($class['sem'], $semesterOrder);
		
		// get the code-classkey relation.
		$codes[$key] = $class['code'];
		
		$sections[$key] = $class['sect'];
	}
	
// 	print "<hr />";
// 	printpre($classes);
// 	printpre($years);
// 	printpre($semesters);
// 	printpre($codes);

	array_multisort($years, $direction, SORT_NUMERIC, $semesters, $direction, SORT_NUMERIC, $codes, SORT_ASC, SORT_STRING, $sections, SORT_ASC, SORT_STRING, $classes);
	
// 	printpre($classes);

	return $classes;
}

/**
 * Convert all the links in $sitename that point to other parts of this segue site, or
 * to this site's media, to placeholder tags for storage. This allows those
 * tags to still be valid if the segue server url changes.
 * 
 * @param string $sitename The site search for links.
 * @return void
 * @access public
 * @date 9/15/04
 */
function convertAllInteralLinksToTags ($sitename) {
	global $cfg;
	
	if (!$sitename)
		printError("convertInteralLinksToTags: no sitename passed!");
	
	$site =& new site ($sitename);
	$site->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen();
	
	// Start with the site level text
	$site->setField("header", 
		convertInteralLinksToTags($sitename, $site->getField('header')));
			
	$site->setField("footer", 
		convertInteralLinksToTags($sitename, $site->getField('footer')));
	
	// Do the sections
	foreach (array_keys($site->sections) as $sectionId) {
		$section =& $site->sections[$sectionId];
		
		$section->setField("url", 
			convertInteralLinksToTags($sitename, 	$section->getField('url')));
		
		// Do the Pages
		foreach (array_keys($section->pages) as $pageId) {
			$page =& $section->pages[$pageId];
			
			$page->setField("url", 
				convertInteralLinksToTags($sitename, $page->getField('url')));
		
			// Do the Stories
			foreach (array_keys($page->stories) as $storyId) {
				$story =& $page->stories[$storyId];
				
				$story->setField("url", 
					convertInteralLinksToTags($sitename, $story->getField('url')));
				
				$story->setField("shorttext", 
					convertInteralLinksToTags($sitename, $story->getField('shorttext')));
				
				$story->setField("longertext", 
					convertInteralLinksToTags($sitename, $story->getField('longertext')));
			}
		}
	}
	
	$site->updatedb(1,1,1);
}


/******************************************************************************
 * Gets pages titles 
 * @param string $section the id of section with pages.
 * return array of page titles
 ******************************************************************************/

function getPageTitles ($section) {	
	$page_titles = array();
	
	$query = "
	SELECT 
		page_title, page_id
	FROM 
		page 
	WHERE 
		FK_section ='".addslashes($section)."'
	";
	$r = db_query($query);
	
	while ($a = db_fetch_assoc($r)) {
		$page_titles[$a[page_title]] = $a[page_id];
	}
	return $page_titles;
}

/******************************************************************************
 * Gets section titles 
 * @param string $section the id of site with sections.
 * return array of site titles
 ******************************************************************************/

//function getSectionTitles ($site) {	
//	$site_id = db_get_value("slot", "FK_site", "slot_name='".$site."'");
//	$section_titles = array();
//	
//	$query = "
//	SELECT 
//		section_title, section_id
//	FROM 
//		section 
//	WHERE 
//		FK_site ='".addslashes($site_id)."'
//	";
//	$r = db_query($query);
//	
//	while ($a = db_fetch_assoc($r)) {
//		$section_titles[$a[section_title]] = $a[section_id];
//	}
//	
//	return $section_titles;
//}
	
/******************************************************************************
 * Gets story titles 
 * @param string $page the id of page with stories on it.
 * return array of site titles
 ******************************************************************************/

//function getStoryTitles ($page) {	
//
//	$story_titles = array();
//	
//	$query = "
//	SELECT 
//		story_title, story_id
//	FROM 
//		story 
//	WHERE 
//		FK_page ='".addslashes($page)."'
//	";
//	$r = db_query($query);
//	
//	while ($a = db_fetch_assoc($r)) {
//		$story_titles[$a[story_title]] = $a[story_id];
//	}
//
//	return $story_titles;
//}

/******************************************************************************
 * Gets all story titles from site
 * @param string $page the id of page with stories on it.
 * return array of site titles
 ******************************************************************************/

//function getAllStoryTitles ($site) {	
//	$site_id = db_get_value("slot", "FK_site", "slot_name='".$site."'");
//	$story_titles = array();
//	
//	$query = "
//	SELECT 
//		story_title, story_id
//	FROM
//		story
//		LEFT JOIN
//		page
//		ON FK_page = page_id
//		LEFT JOIN
//		section
//		ON FK_section = section_id
//		LEFT JOIN
//		site
//		ON section.FK_site = site_id
//		LEFT JOIN
//		slot
//		ON site_id = slot.FK_site
//	WHERE 
//		slot.FK_site ='".addslashes($site_id)."'	
//	";
//	$r = db_query($query);
//	
//	while ($a = db_fetch_assoc($r)) {
//		$story_titles[$a[story_title]] = $a[story_id];
//	}
//
//	return $story_titles;
//}

/******************************************************************************
 * Gets all page titles from site
 * @param string $page the id of page with stories on it.
 * return array of site titles
 ******************************************************************************/


//function getAllPageTitles ($site) {	
//	$site_id = db_get_value("slot", "FK_site", "slot_name='".$site."'");
//	$page_titles = array();
//	
//	$query = "
//	SELECT 
//		page_title, page_id, section_id
//	FROM
//		page
//		LEFT JOIN
//		section
//		ON FK_section = section_id
//		LEFT JOIN
//		site
//		ON section.FK_site = site_id
//		LEFT JOIN
//		slot
//		ON site_id = slot.FK_site
//	WHERE 
//		slot.FK_site ='".addslashes($site_id)."'
//	";
//	$r = db_query($query);
//	
//	while ($a = db_fetch_assoc($r)) {
//		$page_titles[$a[page_title]] = $a[page_id];
//	}
////	printpre($page_titles);
//	return $page_titles;
//}


/******************************************************************************
 * creates a page in a given section 
 * @param string $section the id of section with pages.
 * return array of page titles
 ******************************************************************************/

//function createPage ($site, $section, $linked_title) {
//	$siteObj =& new site($site);
//	$sectionObj =& new section($site, $section, $siteObj);
//	$pageObj =& new page($site, $section, 0, $sectionObj);
//	
//	$pageObj->setField("title", $linked_title);
//	//$pageObj->setField("addedby", $linked_title);
//	
//
//	$pageObj->setPermissions($sectionObj->getPermissions());
//	$pageObj->insertDB();
//	log_entry("add_page","$_SESSION[auser] added page id ".$pageObj->id." in site ".$pageObj->owning_site.", section ".$pageObj->owning_section,$pageObj->owning_site,$pageObj->id,"page");
//	
//	$page_id = $pageObj->id;
//	return $page_id;
//}



/******************************************************************************
 * Converts wiki markup to internal links
 * if no page with title = markup title call createPage function
 * @param string $section the id of section with pages.
 * return $text
 ******************************************************************************/

//function convertWikiMarkupToLinks($site, $section, $page_id, $story_id, $page_title=0, $text) {
//	global $cfg;
//	
//	$linked_titles = array();
//	$links = array();
//	
//	$linkpattern = "/(\[\[)([^\[]*)(\]\])/";
//	
//	preg_match_all($linkpattern, $text, $matches);
//	$linked_titles = $matches[2];
//	//$wikiLinks = $matches[0];
//	
//	if (count($linked_titles) != 0) {
//	
//		$current_story_titles = getStoryTitles ($page_id);
//		$current_page_titles = getPageTitles ($section);
//		$section_titles = getSectionTitles ($site);
//		$all_page_titles = getAllPageTitles($site);
//		$all_story_titles = getAllStoryTitles($site);
//		//printpre($current_story_titles);
//	
//		foreach ($linked_titles as $linked_title) {
//			
//			$links[$linked_title] = "<a href='";
//			if (in_array($linked_title, array_keys($current_story_titles))) {
//				$linked_story_id = $current_story_titles[$linked_title];
//				$links[$linked_title] .= $cfg['full_uri']."/index.php?&action=site"."&site=".$site."&section=".$section."&page=".$page_id."&story=".$linked_story_id."&detail=".$linked_story_id;
//				$links[$linked_title] .= "'>".$linked_title."</a>";
//			} else if (in_array($linked_title, array_keys($current_page_titles))) {
//				$linked_page_id = $current_page_titles[$linked_title];
//				$links[$linked_title] .= $cfg['full_uri']."/index.php?&action=site"."&site=".$site."&section=".$section."&page=".$linked_page_id;
//				$links[$linked_title] .= "'>".$linked_title."</a>";
//			} else if (in_array($linked_title, array_keys($section_titles))) {
//				$linked_section_id = $section_titles[$linked_title];
//				$links[$linked_title] .= $cfg['full_uri']."/index.php?&action=site"."&site=".$site."&section=".$linked_section_id;
//				$links[$linked_title] .= "'>".$linked_title."</a>";
//			} else if (in_array($linked_title, array_keys($all_page_titles))) {
//				$linked_page_id = $all_page_titles[$linked_title];
//				$linked_section_id = db_get_value("page", "FK_section", "page_id='".$linked_page_id."'");
//				$links[$linked_title] .= $cfg['full_uri']."/index.php?&action=site"."&site=".$site."&section=".$linked_section_id."&page=".$linked_page_id;
//				$links[$linked_title] .= "'>".$linked_title."</a>";
//			} else if (in_array($linked_title, array_keys($all_story_titles))) {
//				$linked_story_id = $all_story_titles[$linked_title];
//				$linked_page_id = db_get_value("story", "FK_page", "story_id='".$linked_story_id."'");
//				$linked_section_id = db_get_value("page", "FK_section", "page_id='".$linked_page_id."'");
//				$links[$linked_title] .= $cfg['full_uri']."/index.php?&action=site"."&site=".$site."&section=".$linked_section_id."&page=".$linked_page_id."&story=".$linked_story_id."&detail=".$linked_story_id;				
//				$links[$linked_title] .= "'>".$linked_title."</a>";
//			} else {
//				//$linked_page_id = createPage($site, $section, $linked_title);
//				$links[$linked_title] .= $cfg['full_uri']."/index.php?&action=add_node"."&site=".$site."&section=".$section."&page=".$page_id."&story=".$story_id."&title=".$linked_title;
//				$links[$linked_title] .= "'>".$linked_title."?</a>";
//			}
//		}
//	}
//	
////	printpre($links);
////	exit;
//	foreach ($links as $title=>$link) {
//		$wikiLink = "[[".$title."]]";
//		$text = str_replace($wikiLink, $link, $text);
//	}
//	
//	return $text;
//
//}

/******************************************************************************
 * Converts links with action=add_node to links with action=site
 * if no page with title = markup title call createPage function
 * @param string $section the id of section with pages.
 * return $text
 ******************************************************************************/

//function convertAddNodeLinks($site, $section, $source_story_id, $title, $page=0, $story=0) {
//	global $cfg;
//	//$page_titles = getPageTitles ($section);
//	
//	// get source story text
//	$shorttext = db_get_value("story", "story_text_short", "story_id=".$source_story_id);
//	$shorttext = stripslashes(urldecode($shorttext));
//	$shorttext = convertTagsToInteralLinks ($site, $shorttext);
//	
//	$longertext = db_get_value("story", "story_text_long", "story_id=".$source_story_id);
//	$longertext = stripslashes(urldecode($longertext));
//	$longertext = convertTagsToInteralLinks ($site, $longertext);
//
//	printpre($shorttext);
//	
//	
//	// find all links with action = add_node and title = title
//		
//
//	$linked_titles = array();
//	$links = array();
//	
//	$linkpattern = "/action=(add_node).*?site=([^&]*).*?section=([0-9]*).*?page=([0-9]*).*?story=([0-9]*).*?title=([^'>]*)/";	
//	
//	preg_match_all($linkpattern, $shorttext, $matches);
//	$links[action] = $matches[1];
//	$links[site] = $matches[2];
//	$links[section] = $matches[3];
//	$links[page] = $matches[4];
//	$links[story] = $matches[5];
//	$links[title] = $matches[6];
//	
//	printpre($links);
////	exit;
//
//	// replace in found items add_node with site and section, page and story with new values
//	
//	for ($i=0; $i<=count($links[action]); $i++) {
//		printpre($links[title][$i]);
//		if ($links[title][$i] == $title) {
//			$oldlink = "action=add_node&site=".$links[site][$i]."&section=".$links[section][$i]."&page=".$links[page][$i]."&story=".$links[story][$i]."&title=".$links[title][$i]."'>".$links[title][$i]."?";
//			$newlink = "action=site&site=".$site."&section=".$section;
//			printpre($oldlink);
//			if ($page != 0) $newlink .= "&page=".$page;
//			if ($story != 0) $newlink .= "&story=".$story;
//			$newlink .= "'>".$links[title][$i];
//			printpre($newlink);
//			$shorttext = str_replace($oldlink, $newlink, $shorttext);
//		}
//	}
//	
//	preg_match_all($linkpattern, $longertext, $matches);
//	$links[action] = $matches[1];
//	$links[site] = $matches[2];
//	$links[section] = $matches[3];
//	$links[page] = $matches[4];
//	$links[story] = $matches[5];
//	$links[title] = $matches[6];
//	
//	printpre($links);
////	exit;
//
//	// replace in found items add_node with site and section, page and story with new values
//	
//	for ($i=0; $i<=count($links[action]); $i++) {
//		printpre($links[title][$i]);
//		if ($links[title][$i] == $title) {
//			$oldlink = "action=add_node&site=".$links[site][$i]."&section=".$links[section][$i]."&page=".$links[page][$i]."&story=".$links[story][$i]."&title=".$links[title][$i]."'>".$links[title][$i]."?";
//			$newlink = "action=site&site=".$site."&section=".$section;
//			printpre($oldlink);
//			if ($page != 0) $newlink .= "&page=".$page;
//			if ($story != 0) $newlink .= "&story=".$story;
//			$newlink .= "'>".$links[title][$i];
//			printpre($newlink);
//			$longertext = str_replace($oldlink, $newlink, $longertext);
//		}
//	}
//	
//	
//	printpre($shorttext);
//	//exit;
//	// save updated story text
//
//	$query = "UPDATE
//				story
//			SET 
//				story_text_short ='".addslashes($shorttext)."',
//				story_text_long ='".addslashes($longertext)."' 
//			WHERE
//				story_id ='".addslashes($source_story_id)."'
//			";
//							
//	db_query($query);
//	
//
//}



function getLinkingPages($site, $section, $page) {
	global $cfg;
	
	$links = array();
	
	$query = "
	SELECT 
		source_id, source_type
	FROM 
		links 
	WHERE 
		target_id ='".addslashes($page)."'
	AND
		target_type = 'page'
	";
	//printpre($query);
	//exit;
	
	$r = db_query($query);
	
	
	
	while ($a = db_fetch_assoc($r)) {	
		$linkingpage = $a['source_id'];		

		$linkingpagetitle = db_get_value("page", "page_title", "page_id=".$linkingpage);
		if ($linkingpagetitle) {
			$linkingsection = db_get_value("page", "FK_section", "page_id=".$linkingpage);
			$linkingsite = db_get_value("section", "FK_site", "section_id=".$linkingsection);
			$linkingsite = db_get_value("slot", "slot_name", "FK_site=".$linkingsite);
		
			$links[$linkingpagetitle] = $cfg['full_uri']."/index.php?action=site"."&site=".$linkingsite."&section=".$linkingsection."&page=".$a['source_id'];		
		}
	}
	
//	printpre($links);
	return $links;
	//exit;

}

/******************************************************************************
 * Finds all links to a given node in all other content blocks in site
 * Add internal link to link table
 * @param string $section the id of section with pages.
 * return $text
 ******************************************************************************/

//function findInternalLinks ($site, $section, $page_id, $page_title, $text) {
//	global $cfg;
////	exit;
//	$patterns = array();
//	$replacements = array();
//		
//}


/******************************************************************************
 * records all internal links
 * Add internal link to link table
 * @param string $section the id of section with pages.
 * return $text
 ******************************************************************************/

//function recordInternalLinks ($site, $section, $page_id, $story_id, $text) {
//	global $cfg;
////	exit;
//	$patterns = array();
//	$replacements = array();
//		
//	
//	printpre($text);
//	
//	
//	//$linkpattern = "/site=([^&]*).*?section=([0-9]*).*?.*?page=([0-9]*).*?story=([0-9]*)/";	
//
//	//find wiki links
//	$linkpattern = "/(\[\[)([^\[]*)(\]\])/";
//	preg_match_all($linkpattern, $text, $matches);
//	$linked_wiki = $matches[1];	
//	printpre($linked_wiki);
//
//	
//	//find links to stories
//	$linkpattern = "/site=[^&]*.*?section=[0-9]*.*?page=([0-9]*).*?story=([0-9]*)/";	
//	preg_match_all($linkpattern, $text, $matches);
//	$linked_story = $matches[1];	
//	printpre($linked_story);
//	
//	//find links to pages
//	$linkpattern = "/site=[^&]*.*?section=[0-9]*.*?page=([0-9]*)/";	
//	preg_match_all($linkpattern, $text, $matches);
//	$linked_page = $matches[1];	
//	printpre($linked_page);
//	
//	//find links to sections
//	$linkpattern = "/site=[^&]*.*?section=([0-9]*)[&][^page+]/";	
//	preg_match_all($linkpattern, $text, $matches);
//	$linked_section = $matches[1];	
////	printpre($linked_section);
//
//	//find links to sites
//	$linkpattern = "/site=([^&]*)[&][^(?:section)]/";
//	preg_match_all($linkpattern, $text, $matches);
//	$linked_site = $matches;	
//	//printpre($linked_site);
//
//	//check if linked page exists and if not delete from links table
//	foreach ($linked_page as $target_id) {
//		$query = "
//		SELECT 
//			page_id
//		FROM 
//			page 
//		WHERE 
//			page_id ='".addslashes($target_id)."'
//		";
//	//	printpre($query);		
//		$r = db_query($query);
//		
//		
//		if (!db_num_rows($r)) {
//			$query = "
//			DELETE FROM 
//				links
//			WHERE 
//				target_id ='".addslashes($target_id)."'
//			AND
//				target_type = 'page'
//			";
//		//	printpre($query);		
//			$r = db_query($query);	
//
//		// check if record of source page linking to link page exists
//		// and if not create record			
//		} else {
//		
//			$query = "
//			SELECT 
//				target_id, target_type
//			FROM 
//				links 
//			WHERE 
//				source_id ='".addslashes($page_id)."'
//			AND
//				target_id ='".addslashes($target_id)."'
//			AND
//				target_type = 'page'
//			";
//		//	printpre($query);		
//			$r = db_query($query);
//			
//			if (!db_num_rows($r)) {
//				$query = "
//				INSERT INTO 
//					links
//				SET
//					source_id ='".addslashes($page_id)."',
//					source_type = 'page',
//					target_id ='".addslashes($target_id)."',
//					target_type = 'page',
//					link_tstamp = NOW(),
//					FK_luser='".addslashes($_SESSION[lid])."',
//					FK_auser='".addslashes($_SESSION[aid])."'				
//				";
//			//	printpre($query);		
//				$r = db_query($query);					
//			}
//		}	
//	}
//	
////	exit;	
//	return $text;
//}



/**
 * Convert links in $text that point to other parts of this segue site, or
 * to this site's media, to placeholder tags for storage. This allows those
 * tags to still be valid if the segue server url changes.
 * 
 * @param string $text The text to parse for links.
 * @return string The text with links converted to tags.
 * @access public
 * @date 9/15/04
 */
function convertInteralLinksToTags ($sitename, $text) {
	global $cfg;
	
	if (!$sitename)
		printError("convertInteralLinksToTags: no sitename passed!");
	
	$patterns = array();
	$replacements = array();
	
	// replace internal link urls with constant [[linkpath]]
	$patterns[] = $cfg['full_uri'];
	$replacements[] = "\[\[linkpath\]\]";
	if ($cfg['personalsitesurl']) {
		$patterns[] = $cfg['personalsitesurl'];
		$replacements[] = "\[\[linkpath\]\]";
	}
	if ($cfg['classsitesurl']) {
		$patterns[] = $cfg['classsitesurl'];
		$replacements[] = "\[\[linkpath\]\]";
	}
	
	// replace specific site reference with general
	$patterns[] = "site=".$sitename;
	$replacements[] = "site=\[\[site\]\]";

	// replace internal links to edit mode (action=viewsite)
	// with internal links to non-edit mode (action=site)
	$patterns[] = "action=viewsite";
	$replacements[] = "action=site";
	
	// replace upload url path with constant [[mediapath]]
	$patterns[]  = $cfg[uploadurl]."/".$sitename;
	$replacements[] = "\[\[mediapath\]\]";
	
	// Remove any PHPSESSID components from the URL
	$patterns[]  = "&?PHPSESSID=[a-zA-Z0-9]+";
	$replacements[] = "";
	
	for ($i=0; $i < count($patterns); $i++) {
		$text = eregi_replace($patterns[$i], $replacements[$i], $text);
	}
	
	return $text;
}

/**
 * Convert placeholder tags for storage to links in $text that point to other parts of this segue site, or
 * to this site's media. This allows those
 * tags to still be valid if the segue server url changes.
 * 
 * @param string $text The text to parse for tags.
 * @return string The text with tags converted to links.
 * @access public
 * @date 9/15/04
 */
function convertTagsToInteralLinks ($sitename, $text) {
	global $cfg;

	if (!$sitename)
		printError("convertTagsToInternalLinks: no sitename passed!");
	
	$patterns = array();
	$replacements = array();
	
	// replace internal link urls with constant [[linkpath]]
	$patterns[] = "\[\[linkpath\]\]";
	$replacements[] = $cfg[full_uri];
	
	// replace specific site reference with general
	$patterns[] = "site=\[\[site\]\]";
	$replacements[] = "site=".$sitename;
	
	// replace upload url path with constant [[mediapath]]
	$patterns[] = "\[\[mediapath\]\]";
	$replacements[]  = $cfg[uploadurl]."/".$sitename;
	
	for ($i=0; $i < count($patterns); $i++) {
		$text = eregi_replace($patterns[$i], $replacements[$i], $text);
	}

	return $text;
}

/**
 * Make a global hash with all of the current Ids as the keys. When a site
 * and its parts are being copied (inserted again with the 'copy' flag), 
 * their new ids will be added to the global hash as the values.
 * 
 * @param object site $site The site to make the cache for.
 * @return void
 * @access public
 * @date 9/16/04
 */
function makeSiteHash (& $site) {
	$GLOBALS['__site_hash'] = array();
	$GLOBALS['__site_hash']['site'] = array();
	$GLOBALS['__site_hash']['sections'] = array();
	$GLOBALS['__site_hash']['pages'] = array();
	$GLOBALS['__site_hash']['stories'] = array();
// Currently, Segue doesn't copy discussions, so don't bother.				
//	$GLOBALS['__site_hash']['discussions'] = array();
	
	$GLOBALS['__site_hash']['site'][$site->name] = 'NEXT';
	
	// Sections
	foreach (array_keys($site->sections) as $sectionId) {
		$GLOBALS['__site_hash']['sections'][$sectionId] = NULL;
		
		$section =& $site->sections[$sectionId];
		
		foreach (array_keys($section->pages) as $pageId) {
			$GLOBALS['__site_hash']['pages'][$pageId] = NULL;
			
			$page =& $section->pages[$pageId];
			
			foreach (array_keys($page->stories) as $storyId) {
				$GLOBALS['__site_hash']['stories'][$storyId] = NULL;

// Currently, Segue doesn't copy discussions, so don't bother.				
// 				$story =& $page->stories[$storyId];
// 				
// 				foreach ($story->data['discussions'] as $discussionId) {
// 					$GLOBALS['__site_hash']['discussions'][$discussionId] = NULL;
// 				}
			}
		}
	}
}

/**
 * Take a global hash with all of the old Ids as the keys and the new Ids as 
 * the values and parse through the whole site, updating all links that point
 * to the old keys, to the new values.
 * 
 * @param object site $site The site to convert the links for.
 * @return void
 * @access public
 * @date 9/16/04
 */
function updateSiteLinksFromHash (& $site, & $nodeToStartOn) {
//  	printpre($GLOBALS['__site_hash']);
	
	//*****************************************************************
	// Lets build our search terms for any links to site parts that are 
	// in the text and replace them with the appropriate new value from 
	// the hash.
	//*****************************************************************
	
	// Build our pattern and replacement arrays
	$patterns = array();
	$replacements = array();
	
	// Add all the sections that we have matches for.
	foreach ($GLOBALS['__site_hash']['sections'] as $oldId => $newId) {
		if ($oldId && $newId) {
			$patterns[] = "/&amp;section=".$oldId."/";
			$replacements[] = "&amp;section=".$newId;
		}
	}
	
	// Add all the pages that we have matches for.
	foreach ($GLOBALS['__site_hash']['pages'] as $oldId => $newId) {
		if ($oldId && $newId) {
			$patterns[] = "/&amp;page=".$oldId."/";
			$replacements[] = "&amp;page=".$newId;
		}
	}
	
	// Add all the pages that we have matches for.
	foreach ($GLOBALS['__site_hash']['stories'] as $oldId => $newId) {
		if ($oldId && $newId) {
			$patterns[] = "/&amp;story=".$oldId."/";
			$replacements[] = "&amp;story=".$newId;
		}
	}
	
	// Get the old site name.
	$siteArray = array_keys ($GLOBALS['__site_hash']['site']);
	$oldSitename = $siteArray[0];
	
	
// 	print "\n<br />Old Sitename=".$oldSitename;
// 	printpre($patterns);
// 	printpre($replacements);

	if (!$nodeToStartOn ||  get_class($nodeToStartOn) == 'site') {
			
		// Start with the site level text
		$site->setField("header", 
			updateLinksToNewSite($oldSitename, $patterns, $replacements,
				$site->getField('header')));
				
		$site->setField("footer", 
			updateLinksToNewSite($oldSitename, $patterns, $replacements,
				$site->getField('footer')));
		
		// Do the sections
		foreach (array_keys($site->sections) as $sectionId) {
			$section =& $site->sections[$sectionId];
			
			$section->setField("url", 
				updateLinksToNewSite($oldSitename, $patterns, $replacements,
					$section->getField('url')));
			
			// Do the Pages
			foreach (array_keys($section->pages) as $pageId) {
				$page =& $section->pages[$pageId];
				
				$page->setField("url", 
					updateLinksToNewSite($oldSitename, $patterns, $replacements,
						$page->getField('url')));
			
				// Do the Stories
				foreach (array_keys($page->stories) as $storyId) {
					$story =& $page->stories[$storyId];
					
					$story->setField("url", 
						updateLinksToNewSite($oldSitename, $patterns, $replacements,
							$story->getField('url')));
					
					$story->setField("shorttext", 
						updateLinksToNewSite($oldSitename, $patterns, $replacements,
							$story->getField('shorttext')));
					
					$story->setField("longertext", 
						updateLinksToNewSite($oldSitename, $patterns, $replacements,
							$story->getField('longertext')));
				}
			}
		}
	} else {
		$startType = get_class($nodeToStartOn);
		
		if ($startType == 'section') {
			$section =& $nodeToStartOn;
			
			$section->setField("url", 
				updateLinksToNewSite($oldSitename, $patterns, $replacements,
					$section->getField('url')));
			
			// Do the Pages
			foreach (array_keys($section->pages) as $pageId) {
				$page =& $section->pages[$pageId];
				
				$page->setField("url", 
					updateLinksToNewSite($oldSitename, $patterns, $replacements,
						$page->getField('url')));
			
				// Do the Stories
				foreach (array_keys($page->stories) as $storyId) {
					$story =& $page->stories[$storyId];
					
					$story->setField("url", 
						updateLinksToNewSite($oldSitename, $patterns, $replacements,
							$story->getField('url')));
					
					$story->setField("shorttext", 
						updateLinksToNewSite($oldSitename, $patterns, $replacements,
							$story->getField('shorttext')));
					
					$story->setField("longertext", 
						updateLinksToNewSite($oldSitename, $patterns, $replacements,
							$story->getField('longertext')));
				}
			}
		} else if ($startType == 'page') {
			$page =& $nodeToStartOn;
				
			$page->setField("url", 
				updateLinksToNewSite($oldSitename, $patterns, $replacements,
					$page->getField('url')));
		
			// Do the Stories
			foreach (array_keys($page->stories) as $storyId) {
				$story =& $page->stories[$storyId];
				
				$story->setField("url", 
					updateLinksToNewSite($oldSitename, $patterns, $replacements,
						$story->getField('url')));
				
				$story->setField("shorttext", 
					updateLinksToNewSite($oldSitename, $patterns, $replacements,
						$story->getField('shorttext')));
				
				$story->setField("longertext", 
					updateLinksToNewSite($oldSitename, $patterns, $replacements,
						$story->getField('longertext')));
			}
		} else if ($startType == 'story') {
			$story =& $nodeToStartOn;
			
			$story->setField("url", 
				updateLinksToNewSite($oldSitename, $patterns, $replacements,
					$story->getField('url')));
			
			$story->setField("shorttext", 
				updateLinksToNewSite($oldSitename, $patterns, $replacements,
					$story->getField('shorttext')));
			
			$story->setField("longertext", 
				updateLinksToNewSite($oldSitename, $patterns, $replacements,
					$story->getField('longertext')));
		}
	}
}

/**
 * Convert the link in the passed string to the new ids from the global site hash.
 * 
 * @param string $oldSitename The name of the old site to search for.
 * @param array $patterns The indexed array of patterns to send to preg_replace.
 * @param array $replacements The indexed array of replacements to send to preg_replace.
 * @param string $text The text to search for links.
 * @return string The text with the links converted.
 * @access public
 * @date 9/16/04
 */
function updateLinksToNewSite ($oldSitename, $patterns, $replacements, $text) {

	// First, lets make sure that all the links were converted to tags.
	// This should get rid of any references to our site.
	$text = convertInteralLinksToTags($oldSitename, $text);
	
	// Replace the link ids.
	$text = preg_replace($patterns, $replacements, $text);
	
	return $text;
}

function nameMatches($filename, $anArrayOfRegExs) {
	ereg("\.([^\.]+)$", $filename, $filenameParts);
	$extension = $filenameParts[1];

	foreach ($anArrayOfRegExs as $expression) {
		if (eregi('^'.$expression.'$', $extension)) {
			return TRUE;
		}
	}
	return FALSE;
}

function associatedSiteExists($uname, $class_id) {
	$assoc_site = "false";
	$slotname = $class_id."-".$uname;
	
	$query = "
	SELECT 
		slot.slot_name AS name,
		user.user_uname AS owner,
		slot.slot_type AS type,
		assocsite.slot_name AS assocsite_name,
		slot.FK_site as inuse,
		slot.slot_uploadlimit AS uploadlimit
	FROM 
		slot
			LEFT JOIN
		user
			ON
				slot.FK_owner = user_id
			LEFT JOIN
				slot AS assocsite
			ON
				slot.FK_assocsite = assocsite.slot_id
	WHERE
		slot.slot_name = '".addslashes($slotname)."'
	";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	
	// if associated site slot  exists, print add to array
	if (db_num_rows($r)) {
		$assoc_site = "true";
		return $assoc_site;
	}
}

function participantContributions($uname, $site) {

	$query = "
		SELECT
			version_id, section_id, page_id, story_id,
			story_created_tstamp, story_title, version_created_tstamp, 
			version_text_short, version_text_long, version_comments 
		FROM
			version
				INNER JOIN
			story
				ON FK_parent = story_id
				INNER JOIN
			page
				ON FK_page = page_id
				INNER JOIN
			section
				ON FK_section = section_id
				INNER JOIN
			site
				ON section.FK_site = site_id
				INNER JOIN
			slot
				ON site_id = slot.FK_site
				INNER JOIN
			user
				ON story.FK_createdby = user_id
		WHERE
			slot_name = '".addslashes($site)."'
		AND
			user_uname = '".addslashes($uname)."'
		Order BY
			version_created_tstamp  DESC
	";
	$r = db_query($query);
	$a = db_fetch_assoc($r);
	
	// if contributions exist put into an array
	if (db_num_rows($r)) {
	
		$contributions = array();
		while ($a = db_fetch_assoc($r)) {
			$contributions[story_title] = $a['story_title'];
			$contributions[section_id] = $a['section_id'];
			$contributions[page_id] = $a['page_id'];
			$contributions[version_id] = $a['version_id'];
			$contributions[version_created_tstamp] = $a['version_created_tstamp'];			
		}
		return $contributions;
	}
}

function participantDiscussions($uname, $site) {
	
	$query = "
		SELECT
			discussion_tstamp, discussion_subject, discussion_id, user_fname, slot_name, site_title,
			story_id, story_title, page_id, section_id, FK_author, user_uname
		FROM
			discussion
				INNER JOIN
			story
				ON FK_story = story_id
				INNER JOIN
			page
				ON FK_page = page_id
				INNER JOIN
			section
				ON FK_section = section_id
				INNER JOIN
			site
				ON section.FK_site = site_id
				INNER JOIN
			slot
				ON site_id = slot.FK_site
				INNER JOIN
			user
				ON discussion.FK_author = user_id
 
			WHERE
				discussion.FK_author = '".addslashes($uname)."'
			AND
				slot_name = '".addslashes($site)."'
			Order BY
				discussion_tstamp DESC
		";
	
	$r = db_query($query);
	$a = db_fetch_assoc($r);
//	printpre($query);
	
	// if discussion posts by this user exist put into an array
	if (db_num_rows($r)) {
		$discussions = array();
		while ($a = db_fetch_assoc($r)) {
			$discussions[discussion_subject] = $a['discussion_subject'];
			$discussions[story_id] = $a['story_id'];
			$discussions[section_id] = $a['section_id'];
			$discussions[page_id] = $a['page_id'];
			$discussions[discussion_id] = $a['discussion_id'];
			$discussions[discussion_tstamp] = $a['discussion_tstamp'];			
		}
		return $discussions;
	}
}

function get_story_title($story_id) {
	$story_title = db_get_value("story", "story_title", "story_id ='".addslashes($story_id)."'");
	return $story_title;
}
		
function recent_site_edits($site) {
	$query = "		
		SELECT
			slot_name, user_fname, site_title, section_id, page_id, story_id,
			story_created_tstamp, story_title, page_title, user_fname, user_uname, 
			story_text_short, user_email, story_display_type, story_text_long
		FROM
			story
				INNER JOIN
			page
				ON FK_page = page_id
				INNER JOIN
			section
				ON FK_section = section_id
				INNER JOIN
			site
				ON section.FK_site = site_id
				INNER JOIN
			slot
				ON site_id = slot.FK_site
				INNER JOIN
			user
				ON story.FK_createdby = user_id
		WHERE
			slot_name = '".addslashes($site)."'
		Order BY
			story_created_tstamp  DESC
		LIMIT 0,20
	";		
	$recent_edits = db_query($query); 	
	return $recent_edits;
}

function recent_discussion($site) {
	$query = "
		SELECT
			discussion_tstamp, discussion_subject, discussion_id, user_fname, user_uname,
			slot_name, site_title, story_id, story_title, page_id, section_id, 
			FK_author, discussion_content, user_email
		FROM
			discussion
				INNER JOIN
			story
				ON FK_story = story_id
				INNER JOIN
			page
				ON FK_page = page_id
				INNER JOIN
			section
				ON FK_section = section_id
				INNER JOIN
			site
				ON section.FK_site = site_id
				INNER JOIN
			slot
				ON site_id = slot.FK_site
				INNER JOIN
			user
				ON discussion.FK_author = user_id
		WHERE
			slot_name = '".addslashes($site)."'
		Order BY
			discussion_tstamp DESC
		LIMIT 0,20
	";
	
	$recent_discussions = db_query($query); 
	return $recent_discussions;
}

function recent_edited_stories($limit,$user_id) {
	
	$query = "		
		SELECT
			slot_name, site_title,
			MAX(story_updated_tstamp) AS most_recent_tstamp, 
			SUBSTR(MAX(CONCAT(story_updated_tstamp,'@',story_id)), LENGTH(MAX(story_updated_tstamp))+2) AS mr_story_id,
			SUBSTR(MAX(CONCAT(story_updated_tstamp,'@',story_title)), LENGTH(MAX(story_updated_tstamp))+2) AS mr_story_title,
			SUBSTR(MAX(CONCAT(story_updated_tstamp,'@',page_id)), LENGTH(MAX(story_updated_tstamp))+2) AS mr_page_id,
			SUBSTR(MAX(CONCAT(story_updated_tstamp,'@',page_title)), LENGTH(MAX(story_updated_tstamp))+2) AS mr_page_title,
			SUBSTR(MAX(CONCAT(story_updated_tstamp,'@',section_id)), LENGTH(MAX(story_updated_tstamp))+2) AS mr_section_id,
			SUBSTR(MAX(CONCAT(story_updated_tstamp,'@',section_title)), LENGTH(MAX(story_updated_tstamp))+2) AS mr_section_title
		FROM
			story
				INNER JOIN				
			page
				ON FK_page = page_id
				INNER JOIN
			section
				ON FK_section = section_id
				INNER JOIN
			site
				ON section.FK_site = site_id
				INNER JOIN
			slot
				ON site_id = slot.FK_site		
			WHERE
				story.FK_updatedby = $user_id
			group BY
				slot_name
			Order BY
				most_recent_tstamp  DESC
			LIMIT 0,10
	";
	//printpre($query);
	$recent_sites = db_query($query); 
	return $recent_sites;
}

function recent_edited_pages($limit,$user_id) {
	
	$query = "		
		SELECT
			slot_name, site_title,
			MAX(page_updated_tstamp) AS most_recent_tstamp, 
			SUBSTR(MAX(CONCAT(page_updated_tstamp,'@',page_id)), LENGTH(MAX(page_updated_tstamp))+2) AS mr_page_id,
			SUBSTR(MAX(CONCAT(page_updated_tstamp,'@',page_title)), LENGTH(MAX(page_updated_tstamp))+2) AS mr_page_title,
			SUBSTR(MAX(CONCAT(page_updated_tstamp,'@',section_id)), LENGTH(MAX(page_updated_tstamp))+2) AS mr_section_id,
			SUBSTR(MAX(CONCAT(page_updated_tstamp,'@',section_title)), LENGTH(MAX(page_updated_tstamp))+2) AS mr_section_title
		FROM				
			page
				INNER JOIN
			section
				ON FK_section = section_id
				INNER JOIN
			site
				ON section.FK_site = site_id
				INNER JOIN
			slot
				ON site_id = slot.FK_site		
			WHERE
				page.FK_updatedby = $user_id
			group BY
				slot_name
			Order BY
				most_recent_tstamp  DESC
			LIMIT 0,10
	";
	//printpre($query);
	$recent_sites = db_query($query); 
	return $recent_sites;
}

function recent_edited_sections($limit,$user_id) {
	
	$query = "		
		SELECT
			slot_name, site_title,
			MAX(section_updated_tstamp) AS most_recent_tstamp, 
			SUBSTR(MAX(CONCAT(section_updated_tstamp,'@',section_id)), LENGTH(MAX(section_updated_tstamp))+2) AS mr_section_id,
			SUBSTR(MAX(CONCAT(section_updated_tstamp,'@',section_title)), LENGTH(MAX(section_updated_tstamp))+2) AS mr_section_title
		FROM
			section
				INNER JOIN
			site
				ON section.FK_site = site_id
				INNER JOIN
			slot
				ON site_id = slot.FK_site		
			WHERE
				section.FK_updatedby = $user_id
			group BY
				slot_name
			Order BY
				most_recent_tstamp  DESC
			LIMIT 0,10
	";
	//printpre($query);
	$recent_sites = db_query($query); 
	return $recent_sites;
}

function recent_edited_sites($limit,$user_id) {
	
	$query = "		
		SELECT
			slot_name, site_title,
			site_updated_tstamp AS most_recent_tstamp
		FROM
			site
				INNER JOIN
			slot
				ON site_id = slot.FK_site		
			WHERE
				site.FK_updatedby = $user_id
			group BY
				slot_name
			Order BY
				most_recent_tstamp  DESC
			LIMIT 0,10
	";
	//printpre($query);
	$recent_sites = db_query($query); 
	return $recent_sites;
}

function recent_edited_components($limit, $user_id) {
	$recentSites = array();
	
	// Add Sites
	$componentsResult = recent_edited_sites($limit, $user_id);
	while ($a = db_fetch_assoc($componentsResult)) {
		$recentSites[$a['slot_name']] = $a;
	}
	mysql_free_result($componentsResult);
	
	// Add Sections
	$componentsResult = recent_edited_sections($limit, $user_id);
	while ($a = db_fetch_assoc($componentsResult)) {
		if(!isset($recentSites[$a['slot_name']])
			|| $a['most_recent_tstamp'] > $recentSites[$a['slot_name']]['most_recent_tstamp']) 
		{
			$recentSites[$a['slot_name']] = $a;
		}
	}
	mysql_free_result($componentsResult);
	

	// Add Pages
	$componentsResult = recent_edited_pages($limit, $user_id);
	while ($a = db_fetch_assoc($componentsResult)) {
		if(!isset($recentSites[$a['slot_name']])
			|| $a['most_recent_tstamp'] > $recentSites[$a['slot_name']]['most_recent_tstamp']) 
		{
			$recentSites[$a['slot_name']] = $a;
		}
	}
	mysql_free_result($componentsResult);
	
	// Add Stories
	$componentsResult = recent_edited_stories($limit, $user_id);
	while ($a = db_fetch_assoc($componentsResult)) {
		if(!isset($recentSites[$a['slot_name']])
			|| $a['most_recent_tstamp'] > $recentSites[$a['slot_name']]['most_recent_tstamp']) 
		{
			$recentSites[$a['slot_name']] = $a;
		}
	}
	mysql_free_result($componentsResult);
	
	// Sort the sites based on the timestamp
	$tstamps = array();
	foreach ($recentSites as $name => $array) {
		$tstamps[$name] = $array['most_recent_tstamp'];
	}
	array_multisort($tstamps, SORT_DESC, $recentSites);
	
	// remove any extras
	while (count($recentSites) > $limit)
		array_pop($recentSites);
	
	
	return $recentSites;
}

function recent_discussions($start, $max, $user_id) {
	
	$query = "
		SELECT
			discussion_tstamp, discussion_subject, discussion_id, user_fname, slot_name, site_title,
			story_id, story_title, page_id, section_id, FK_author, user_uname
		FROM
			discussion
				INNER JOIN
			story
				ON FK_story = story_id
				INNER JOIN
			page
				ON FK_page = page_id
				INNER JOIN
			section
				ON FK_section = section_id
				INNER JOIN
			site
				ON section.FK_site = site_id
				INNER JOIN
			slot
				ON site_id = slot.FK_site
				INNER JOIN
			user
				ON discussion.FK_author = user_id
 
			WHERE
				discussion.FK_author = $user_id
			OR
				story.FK_createdby = $user_id
			Order BY
				discussion_tstamp DESC
			LIMIT $start,$max
		";
	
	$recent_discussions = db_query($query); 
	//printpre($query);
	return $recent_discussions;
}
