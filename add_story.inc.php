<? // add_page.inc.php -- add a page
if (!$step) $step = 1;
if (!isset($mode)) $mode=0;


$add=$edit=0;
if ($action == 'add_story') $add=1;
if ($action == 'edit_story') $edit=1;

// first check if we are allowed to edit this site at all
if ($auser != $site_owner && !is_editor($auser,$site)) {
	error("You're not even an editor for this site! Bad person!");
	return;
}
if ($edit && $auser != $site_owner && db_get_value("stories","locked","id=$edit_story")) {
	error("This story is locked. You may not edit it.");
	return;
}
if ($edit && !permission($auser,PAGE,EDIT,$page)) {
	error("You don't have permission to edit this content. Nice try.");
	return;
}
if ($add && !permission($auser,PAGE,ADD,$page)) {
	error("You don't have permission to add content to this page. Nice try.");
	return;
}
if ($edit && !insite($site,$section,$page,$edit_story)) {
	error("Oh, you're good, but not good enough!");
	return;
}


$pagetitle=db_get_value("sites","title","name='$site'") . " > " . db_get_value("sections","title","id=$section") . " > " . db_get_value("pages","title","id=$page") . " > ";

if ($add) {
	$pagetitle .= "Add Content";
	if ($step==1) {
		$discusspermissions = 'anyone';
		if (!$type) $type='story';
	}
	if (!$url) $url = 'http://';
}
if ($edit) {
	$pagetitle .= "Edit Content";
	if ($step == 1) {
		$a = db_get_line("stories","id=$edit_story");
		foreach ($a as $n=>$v) $$n = $v;
		list($activateyear,$activatemonth,$activateday) = explode("-",$activatedate);
		list($deactivateyear,$deactivatemonth,$deactivateday) = explode("-",$deactivatedate);
		$activatemonth-=1;$deactivatemonth-=1;
		$activatedate=($activatedate=='0000-00-00')?0:1;
		$deactivatedate=($deactivatedate=='0000-00-00')?0:1;
		$shorttext = urldecode($shorttext);
		$longertext = urldecode($longertext);
		if (!$type) $type = 'story';
	}
}

if ($edit && $mode==0) {
	$a = db_get_line("stories","id=$edit_story");
	list($activateyear,$activatemonth,$activateday) = explode("-",$a[activatedate]);
	list($deactivateyear,$deactivatemonth,$deactivateday) = explode("-",$a[deactivatedate]);
	$activatemonth-=1;$deactivatemonth-=1;
	$activatedate=($a[activatedate]=='0000-00-00')?0:1;
	$deactivatedate=($a[deactivatedate]=='0000-00-00')?0:1;
}

if ($switchmode) {
	$mode = 1- $mode;
	$step = 1;
}

if ($typeswitch) $step = 1;

if ($step == 2) {
	$error = 0;
	// error checking
	if ($type == 'story') {
		if (!$shorttext || trim($shorttext)=='')
			error ("You must enter some story content.");
	}
	if ($type == 'file') {
		if (!$_FILES['file']['name'] || $_FILES['file']['name'] == '') 
			error("You must select a file to upload.");
	}
	if ($type == 'image') {
		if ($add) {
			if (!$_FILES['file']['name'] || $_FILES['file']['name'] == '') 
				error("You must select an image to upload.");
			if (!ereg("image",$_FILES['file']['type']))
				error("You must upload only images to display. If you would like to upload any file, choose <b>File for Download</b> instead.");
		}
	}
	if ($type == 'link') {
		if ($url =='' || !$url || $url == 'http://')
			error("You must enter a URL into the appropriate field.");
	}
	if (!$error) { // save it to the database
	
		if ($activatedate) $activatedate = $activateyear . "-" . ($activatemonth+1) . "-" . $activateday;
		else $activatedate = "0000-00-00";
		if ($deactivatedate) $deactivatedate = $deactivateyear . "-" . ($deactivatemonth+1) . "-" . $deactivateday;
		else $deactivatedate = "0000-00-00";
		$discuss = ($discuss)?1:0;
		$locked = ($locked)?1:0;
		
		if ($type == 'image' || $type == 'file') {
			$longertext = $_FILES['file']['name'];
			if ($edit) {
				$oldfilename = db_get_value("stories","longertext","id=$edit_story");
				if (!$longertext) {
					$longertext = $oldfilename;
				} else if ($oldfilename != $longertext) {
					// delete the old file
					deleteuserfile($edit_story,$oldfilename);
				}
			}
		}

		$shorttext=urlencode($shorttext);
		$longertext=urlencode($longertext);
		if ($newcategory) $category = $newcategory;
		
		$permissions = encode_array($permissions);
		if ($add) $query = "insert into stories set addedby='$auser',addedtimestamp=NOW(),";
		if ($edit) { $query = "update stories set editedby='$auser',"; $where = " where id=$edit_story";}
		$query.="type='$type',texttype='$texttype',category='$category',title='$title', discuss=$discuss, discusspermissions='$discusspermissions', shorttext='$shorttext', longertext='$longertext', locked=$locked, activatedate='$activatedate', deactivatedate='$deactivatedate', permissions='$permissions', url='$url'";
		db_query($query.$where) or print mysql_error();
		
		print $query.$where;
		
		// add the new section id to the sites table
		if ($add) {
			$newid = lastid();
			// move the file to the appropriate upload dir
			if ($type == 'image' || $type == 'file') {
				copyuserfile($newid,$_FILES['file']);
			}
			
			$stories = decode_array(db_get_value("pages","stories","id=$page"));
			array_push($stories,$newid);
			$stories = encode_array($stories);
			$query = "update pages set stories='$stories' where id=$page";
			db_query($query);
			log_entry("add_story","$auser added story id $newid to page id $page in site $site");
		}
		if ($edit) {
			log_entry("edit_story","$auser edited story id $edit_story in page id $page in site $site");
			if ($type == 'image' || $type == 'file') {
				copyuserfile($edit_story,$_FILES['file']);
			}
		}

		header("Location: index.php?$sid&action=viewsite&site=$site&section=$section&page=$page");
		
	} else $step = 1;
}

if ($step == 1) { // print out the add form
	include("add_story_form.inc");
}
