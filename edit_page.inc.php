<? // add_page.inc.php -- add a page
$add = $edit = 0;
if (!$step) $step = 1;
if (!isset($mode))$mode=0;

if ($action == 'add_page') $add=1;
if ($action == 'edit_page') $edit=1;

// first check if we are allowed to edit this site at all
if ($auser != $site_owner && !is_editor($auser,$site)) {
	error("You're not even an editor for this site! Bad person!");
	return;
}
if ($edit && !permission($auser,SECTION,EDIT,$section)) {
	error("You don't have permission to edit this page. Nice try.");
	return;
}
if ($add && !permission($auser,SECTION,ADD,$section)) {
	error("You don't have permission to add sections to this site. Nice try.");
	return;
}
if ($edit && !insite($site,$section,$edit_page)) {
	error("Oh, you're good, but not good enough!");
	return;
}

if ($typeswitch) $step = 1;

$pagetitle=db_get_value("sites","title","name='$site'") . " > " . db_get_value("sections","title","id=$section") . " > ";

if ($edit) {
	$pagetitle .= "Edit Page";
	if ($step == 1) {
		$a = db_get_line("pages","id=$edit_page");
		foreach ($a as $n=>$v) $$n = $v;
		$permissions = decode_array($permissions);
		list($activateyear,$activatemonth,$activateday) = explode("-",$activatedate);
		list($deactivateyear,$deactivatemonth,$deactivateday) = explode("-",$deactivatedate);
		$activatemonth-=1;$deactivatemonth-=1;
		$activatedate=($activatedate=='0000-00-00')?0:1;
		$deactivatedate=($deactivatedate=='0000-00-00')?0:1;
	}
}
if ($add) {
	$pagetitle .= "Add Page";
	if ($step == 1) {
		if (!isset($permissions)) $permissions = decode_array(db_get_value("sections","permissions","id=$section"));
		if (!isset($active)) $active=1;
		if (!isset($ediscussion)) $ediscussion=1;
		if (!$type) $type='page';
		if (!$url) $url='http://';
	}
}

if ($edit && $mode==0) {
	$a = db_get_line("pages","id=$edit_page");
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


if ($step == 2) {
	$error = 0;
	// error checking
	if ($type!='divider' && (!$title || $title==''))
		error("You must enter a header title.");
	if ($type=='url' && (!$url || $url=='' || $url=='http://'))
		error("You must enter a URL.");
		
	if (!$error) { // save it to the database
		$addedby=$auser;
		if ($activatedate) $activatedate = $activateyear . "-" . ($activatemonth+1) . "-" . $activateday;
		else $activatedate = "0000-00-00";
		if ($deactivatedate) $deactivatedate = $deactivateyear . "-" . ($deactivatemonth+1) . "-" . $deactivateday;
		else $deactivatedate = "0000-00-00";
		$active = ($active)?1:0;
		$locked = ($locked)?1:0;
		$showcreator = ($showcreator)?1:0;
		$showdate = ($showdate)?1:0;
		$ediscussion = ($ediscussion)?1:0;
		
		// check make sure the owner is the current user if they are changing permissions
		if ($site_owner != $auser)
			$permissions = decode_array(db_get_value("sections","permissions","id=$section"));
		
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
		if ($add) $query = "insert into pages set addedby='$auser',addedtimestamp=NOW(),";
		$where = '';
		if ($edit) { $query = "update pages set editedby='$auser',"; $where = " where id=$edit_page"; }
		$query .= "ediscussion=$ediscussion,archiveby='$archiveby',url='$url',type='$type',title='$title', showcreator=$showcreator, showdate=$showdate, locked=$locked, activatedate='$activatedate', deactivatedate='$deactivatedate', active=$active, permissions='$permissions'";
		db_query($query.$where);
		print "$query$where<BR>";
		//print mysql_error();
		
		// add the new section id to the sites table
		if ($add) {
			$newid = lastid();
			$pages = decode_array(db_get_value("sections","pages","id=$section"));
			array_push($pages,$newid);
			$pages = encode_array($pages);
			$query = "update sections set pages='$pages' where id=$section";
			db_query($query);
			log_entry("add_page","$auser added page id $newid to $site");
		}
		if ($edit) {
			log_entry("edit_page","$auser edited page id $edit_page in $site");
			$newid=$edit_page;
		}
		
		header("Location: index.php?$sid&action=viewsite&site=$site&section=$section".(($type=='page')?"&page=$newid":""));
		
	} else $step = 1;
}

if ($step == 1) { // print out the add form
	include("add_page_form.inc");
}
