<? // add_section.inc.php -- add/edit a section

if (!$step) $step = 1;
if (!isset($mode)) $mode = 0;

$add = $edit = 0;
if ($action == "add_section") $add = 1; // for adding a section
if ($action == "edit_section") $edit = 1; // editing a section

// check first if we are even allowed to edit/add to this site
if ($auser != $site_owner && !is_editor($auser,$site)) {
	error("You're not even an editor for this site! Bad person!");
	return;
}
if ($edit && !permission($auser,SITE,EDIT,$site)) {
	error("You don't have permission to edit this section. Nice try.");
	return;
}
if ($add && !permission($auser,SITE,ADD,$site)) {
	error("You don't have permission to add sections to this site. Nice try.");
	return;
}
if ($edit && !insite($site,$edit_section)) {
	error("Oh, you're good, but not good enough!");
	return;
}

$pagetitle=db_get_value("sites","title","name='$site'") . " > ";
if ($add) {
	$pagetitle .= "Add Section";
	if ($step == 1) {
		if (!isset($permissions)) $permissions = decode_array(db_get_value("sites","permissions","name='$site'"));
		if (!isset($active)) $active=1;
		if (!isset($url)) $url = "http://";
		if (!$type) $type = 'section';
	}
}

if ($edit) {
	$pagetitle .= "Edit Section";
	if ($step ==1) {
		$a = db_get_line("sections","id=$edit_section");
		foreach ($a as $n=>$v) $$n = $v;
		$permissions = decode_array($permissions);
		list($activateyear,$activatemonth,$activateday) = explode("-",$activatedate);
		list($deactivateyear,$deactivatemonth,$deactivateday) = explode("-",$deactivatedate);
		$activatemonth-=1;$deactivatemonth-=1;
		$activatedate=($activatedate=='0000-00-00')?0:1;
		$deactivatedate=($deactivatedate=='0000-00-00')?0:1;
	}
}

if ($edit && $mode==0) {
	$a = db_get_line("sections","id=$edit_section");
	$name = $edit_site;
//	foreach ($a as $n=>$v) $$n=$v;
	$permissions = decode_array($a[permissions]);
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
	if (!$title || $title=='') error("You must enter a title for this section.");
	if ($type=='url' && ($url=='' || $url == "http://" || !$url)) error("You must enter a URL or choose a different section type (such as Content Section).");
	if (!$error) { // save it to the database
		if ($activatedate) $activatedate = $activateyear . "-" . ($activatemonth+1) . "-" . $activateday;
		else $activatedate = "0000-00-00";
		if ($deactivatedate) $deactivatedate = $deactivateyear . "-" . ($deactivatemonth+1) . "-" . $deactivateday;
		else $deactivatedate = "0000-00-00";
		$active = ($active)?1:0;
		$locked = ($locked)?1:0;
		
		if ($type == 'section') {
			// if the user is not the owner of the site, they should not have been able to change permissions
			if ($site_owner != $auser)
				$permissions = decode_array(db_get_value("sites","permissions","name='$site'"));
			
			// make sure that the permissions array represents all of the editors (giving them either permission (1) or not (0))
			$editors = db_get_value("sites","editors","name='$site'");
			if ($editors) {
				$edlist = explode(",",$editors);
				foreach ($edlist as $e) {
					for ($i=0;$i<3;$i++) {
						$permissions[$e][$i] = ($permissions[$e][$i])?1:0;
					}
				}
			}
			
			$permissions = encode_array($permissions);
		}
		
		if ($add) $query = "insert into sections set addedby='$auser', addedtimestamp=NOW()";
		if ($edit) $query = "update sections set editedby='$auser'";
		$where = '';
		if ($edit) $where = " where id=$edit_section";
		$query .= ",title='$title', activatedate='$activatedate', deactivatedate='$deactivatedate', active=$active, permissions='$permissions', type='$type', url='$url'";
		db_query($query.$where);
		
		print "$query$where<br>";
		
		// add the new section id to the sites table
//		$newid = db_get_value("sections","id","title='$title' and addedby='$addedby' and activatedate='$activatedate' and deactivatedate='$deactivatedate' and active=$active and permissions='$permissions'");
//		$newid = next_autoindex('sections')-1;
		if ($add) {
			$newid = lastid();
			$sections = decode_array(db_get_value("sites","sections","name='$site'"));
			array_push($sections,$newid);
			$sections = encode_array($sections);
			$query = "update sites set sections='$sections' where name='$site'";
			db_query($query);
			log_entry("add_section","$auser added section id $newid to site $site");
		}
		
		if ($edit) {
			$newid = $edit_section;
			log_entry("edit_section","$auser edited section id $edit_section");
		}
		
		// do the recursive update of active flag and such... .... ugh
		$permissions = decode_array($permissions);
		if ($edit && ($recursiveenable || count($copydownpermissions))) {
			// recursively change the $active or $permissions field for all parts of the site
			$pages = decode_array(db_get_value("sections","pages","id=$edit_section"));
			foreach ($pages as $p) {
				$pa = db_get_line("pages","id=$p");
				$chg = array();
				if ($recursiveenable && permission($auser,SECTION,EDIT,$edit_section)) $chg[] = "active=$active";
				if (count($copydownpermissions) && $auser == $site_owner) {
					$pp = decode_array($pa['permissions']);
					foreach ($copydownpermissions as $e) $pp[$e] = $permissions[$e];
					$pp = encode_array($pp);
					$chg[] = "permissions='$pp'";
				}
				$query = "update pages set " . implode(",",$chg) . " where id=$p";
				print "--> ".$query . "<BR>";
				if (count($chg)) db_query($query);
			}
			
		}
		
		if ($type == 'section') {
			if ($edit) header("Location: index.php?$sid&action=viewsite&site=$site&section=$newid");
			if ($add) header("Location: index.php?$sid&action=viewsite&site=$site&section=$newid");
		}
		if ($edit) header("Location: index.php?$sid&action=viewsite&site=$site");
		
	} else $step = 1;
}

if ($step == 1) { // print out the add form
	include("add_section_form.inc");
}
