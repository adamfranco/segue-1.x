<? // add_page.inc.php -- add a page

//--------------------------------------------------------------------------------------------------------
// Begining of new code

// ---  variables for debugging ---
//foreach ($settings as $n => $v) {
//	$variables .= "$n = $v <br>";	
//}
//add_link(leftnav,'','',"$variables");
//print $variables."<br>site owner = $site_owner <br>typeswitch = $typeswitch <br>";
//print "siteheader = '$siteheader' <br>sitefooter = '$sitefooter' <br>";
//print "site = $site<br>section = $section<br>page=$page<br>";
//------------------------------------

// first check if we are allowed to edit this site at all
if ($auser != $site_owner && $auser != $settings[site_owner] && !is_editor($auser,$site) && !is_editor($auser,$settings[site])) {
	error("You're not even an editor for this site! Bad person!");
	return;
}
if ($edit && !permission($auser,SECTION,EDIT,$section) && !permission($auser,SECTION,EDIT,$settings[section])) {
	error("You don't have permission to edit this page. Nice try.");
	return;
}
if ($add && !permission($auser,SECTION,ADD,$section)  && !permission($auser,SECTION,ADD,$settings[section])) {
	error("You don't have permission to add sections to this site. Nice try.");
	return;
}
if ($edit && !insite($site,$section,$edit_page)) {
	error("Oh, you're good, but not good enough!");
	return;
}

if ($settings) {
	// if we have already started editing...

	// ---- Editor actions ----
	if ($edaction == 'add') {
		if ($settings[editors])
			$edlist = explode(",",$settings[editors]);
		else $edlist = array();
		if (!in_array($edname,$edlist) && $edname != $auser) $edlist[]=$edname;
		$editors = implode(",",$edlist);
		if ($edname == $auser) error("You do not need to add yourself as an editor.");
	}
	
	if ($edaction == 'del') {
		if ($settings[editors])
			$edlist = explode(",",$settings[editors]);
		else $edlist = array();
		$nlist = array();
		foreach ($edlist as $e) {
			if ($e != $edname) $nlist[]=$e;
		}
		$editors = implode(",",$nlist);
	}

	// --- Load any new variables into the array ---
	// Checkboxes need a "if ($settings[step] == 1 && !$link)" tag.
	// True/False radio buttons need a "if ($var != "")" tag to get the "0" values
	if ($type) $settings[type] = $type;
	if ($settings[step] == 1 && $title != "") $settings[title] = $title;
	if ($activateyear != "") $settings[activateyear] = $activateyear;
	if ($activatemonth != "") $settings[activatemonth] = $activatemonth;
	if ($activateday != "") $settings[activateday] = $activateday;
	if ($settings[step] == 2 && !$link) $settings[activatedate] = $activatedate;
	if ($deactivateyear != "") $settings[deactivateyear] = $deactivateyear;
	if ($deactivatemonth != "") $settings[deactivatemonth] = $deactivatemonth;
	if ($deactivateday != "") $settings[deactivateday] = $deactivateday;
	if ($settings[step] == 2 && !$link) $settings[deactivatedate] = $deactivatedate;
	if ($active != "") $settings[active] = $active;
	if ($viewpermissions != "") $settings[viewpermissions] = $viewpermissions;
	if ($settings[step] == 3 && !$link) $settings[editors] = strtolower($editors);
	if ($settings[step] == 3 && !$link) $settings[permissions] = $permissions;
	if ($settings[step] == 3 && !$link) $settings[ediscussion] = $ediscussion;
	if ($settings[step] == 3 && !$link) $settings[locked] = $locked;
//	if ($settings[step] == 1 && !$link) $settings[recursiveenable] = $recursiveenable;
//	if ($copydownpermissions != "") $settings[copydownpermissions] = $copydownpermissions;
	if ($settings[step] == 4 && !$link) $settings[showcreator] = $showcreator;
	if ($settings[step] == 4 && !$link) $settings[showdate] = $showdate;
	if ($archiveby) $settings[archiveby] = $archiveby;
	if ($url) $settings[url] = $url;
	
	//---- If switching type, take values to defaults ----
	if ($typeswitch) {
		$settings[title] = "";
		$settings[url] = "http://";
		$settings[active] = 1;
		$settings[activateyear] = "0000";
		$settings[activatemonth] = "00";
		$settings[activateday] = "00";
		$settings[activatedate] = 0;
		$settings[deactivateyear] = "0000";
		$settings[deactivatemonth] = "00";
		$settings[deactivateday] = "00";
		$settings[deactivatedate] = 0;
		$settings[active] = 1;
		$settings[editors] = "";
		$settings[ediscussion] = 0;
		$settings[locked] = 0;
		$settings[showcreator] = 0;
		$settings[showdate] = 0;
		$settings[archiveby] = "none";
		
		if ($settings[add]) {
			print "<p> deleting settings[permissions]....</p>";
			//$settings[permissions] = "";
			$settings[permissions] = decode_array(db_get_value("sections","permissions","id=$settings[section]"));
		}
	}
}

if (!$settings && !$error) {
	// create the settings array with default values. $settings must be passed along with each link.
	// The array will be saved on clicking a save button.
	session_register("settings");
	$settings = array(
		"site_owner" => $site_owner,
		"add" => 0,
		"edit" => 0,
		"step" => 1,
		"site" => $site,
		"section" => $section,
		"page" => $edit_page,
		"title" => "",
		"activateyear" => "0000",
		"activatemonth" => "00",
		"activateday" => "00",
		"activatedate" => 0,
		"deactivateyear" => "0000",
		"deactivatemonth" => "00",
		"deactivateday" => "00",
		"deactivatedate" => 0,
		"active"  => 1,
		"editors" => "",
		"permissions" => "",
		"ediscussion" => 1,
		"type" => "page",
		"url" => "http://",
		"commingFrom" => $commingFrom
	);
	
	$settings[pagetitle]=db_get_value("sites","title","name='$site'") . " > " . db_get_value("sections","title","id=$section") . " > ";
	
	if ($action == 'add_page') {
		$settings[add]=1;
		$settings[edit]=0;
		$settings[pagetitle] .= " Add Item";
	}	
	if ($action == 'edit_page') { 
		$settings[add]=0;
		$settings[edit]=1;
		$settings[pagetitle] .= " Edit Item";
	}
	
	if ($settings[add]) {
		$settings[permissions] = decode_array(db_get_value("sections","permissions","id=$section"));
	}
	
	if ($settings[edit]) {	
		$a = db_get_line("pages","id=$settings[page]");
		foreach ($a as $n=>$v) $settings[$n]=$v;
		list($settings[activateyear],$settings[activatemonth],$settings[activateday]) = explode("-",$settings[activatedate]);
		list($settings[deactivateyear],$settings[deactivatemonth],$settings[deactivateday]) = explode("-",$settings[deactivatedate]);
		$settings[activatemonth]-=1;
		$settings[deactivatemonth]-=1;
		$settings[activatedate]=($settings[activatedate]=='0000-00-00')?0:1;
		$settings[deactivatedate]=($settings[deactivatedate]=='0000-00-00')?0:1;
		$settings[permissions] = decode_array($settings[permissions]);
	}
}

if ($prevbutton) $settings[step] = $settings[step] - 1;
if ($nextbutton) $settings[step] = $settings[step] + 1; 
if ($step != "") $settings[step] = $step;
if ($settings[step] ==3 && $auser != $settings[site_owner]) {
	if ($prevbutton) $settings[step] = 2;
	if ($nextbutton) $settings[step] = 4;
}

$pagetitle=$settings[pagetitle];

//-----for some reason siteheader and sitefooter keep being define prior to this point on button click. I'm killing them here until their origen is found ----
$site = "";
$section = "";
$page = "";
$siteheader = "";
$sitefooter = "";

// ---  variables for debugging ---
//foreach ($settings as $n => $v) {
//	$variables .= "$n = $v <br>";	
//}
//add_link(leftnav,'','',"$variables");
//print $variables;
//------------------------------------

if ($cancel) {
	$commingFrom = $settings[commingFrom];
	$site = $settings[site];
	session_unregister("settings");
	if ($commingFrom) header("Location: index.php?$sid&action=$commingFrom&site=$site");
	else header("Location: index.php?$sid");
}

if ($save) {
	$error = 0;
	// error checking
	if ($settings[type]!='divider' && (!$settings[title] || $settings[title]==''))
		error("You must enter a header title.");
	if ($settings[type]=='url' && (!$settings[url] || $settings[url]=='' || $settings[url]=='http://'))
		error("You must enter a URL.");
		
	if (!$error) { // save it to the database
		$addedby=$auser;
		if ($settings[activatedate]) $settings[activatedate] = $settings[activateyear] . "-" . ($settings[activatemonth]+1) . "-" . $settings[activateday];
		else $settings[activatedate] = "0000-00-00";
		if ($settings[deactivatedate]) $settings[deactivatedate] = $settings[deactivateyear] . "-" . ($settings[deactivatemonth]+1) . "-" . $settings[deactivateday];
		else $settings[deactivatedate] = "0000-00-00";
		$settings[active] = ($settings[active])?1:0;
		$settings[locked] = ($settings[locked])?1:0;
		$settings[showcreator] = ($settings[showcreator])?1:0;
		$settings[showdate] = ($settings[showdate])?1:0;
		$settings[ediscussion] = ($settings[ediscussion])?1:0;
		
		// check make sure the owner is the current user if they are changing permissions
		if ($settings[site_owner] != $auser)
			$settings[permissions] = decode_array(db_get_value("sections","permissions","id=$settings[section]"));
		
		// make sure that the permissions array represents all of the editors (giving them either permission (1) or not (0))
		$settings[editors] = db_get_value("sites","editors","name='$settings[site]'");
		if ($settings[editors]) {
			$edlist = explode(",",$settings[editors]);
			foreach ($edlist as $e) {
				for ($i=0;$i<3;$i++) {
					$settings[permissions][$e][$i] = ($settings[permissions][$e][$i])?1:0;
				}
			}
		}
		
		$settings[permissions] = encode_array($settings[permissions]);
		if ($settings[add]) $query = "insert into pages set addedby='$auser',addedtimestamp=NOW(),";
		$where = '';
		if ($settings[edit]) { 
			$query = "update pages set editedby='$auser',"; $where = " where id=$settings[page]"; 
		}
		$query .= "ediscussion=$settings[ediscussion],archiveby='$settings[archiveby]',url='$settings[url]',type='$settings[type]',title='$settings[title]', showcreator=$settings[showcreator], showdate=$settings[showdate], locked=$settings[locked], activatedate='$settings[activatedate]', deactivatedate='$settings[deactivatedate]', active=$settings[active], permissions='$settings[permissions]'";
		db_query($query.$where);
		print "$query$where<BR>";
		//print mysql_error();
		
		// add the new section id to the sites table
		if ($settings[add]) {
			$newid = lastid();
			$pages = decode_array(db_get_value("sections","pages","id=$settings[section]"));
			array_push($pages,$newid);
			$pages = encode_array($pages);
			$query = "update sections set pages='$pages' where id=$settings[section]";
			db_query($query);
			log_entry("add_page","$auser added page id $newid to $settings[site]");
		}
		if ($settings[edit]) {
			log_entry("edit_page","$auser edited page id $settings[page] in $settings[site]");
			$newid=$settings[page];
		}
		
		header("Location: index.php?$sid&action=viewsite&site=$settings[site]&section=$settings[section]".(($type=='page')?"&page=$newid":""));
		
	} else {
		$settings[step] = 1;
	}
}

// ------- print out the add form -------
// --- The Navigation Links for the sidebar ---
$leftlinks = "________________<br><table>";
$leftlinks .= "<tr><td>";
if ($settings[step] == 1) $leftlinks .= "&rArr; ";
$leftlinks .= "</td><td>";
if ($settings[step] != 1) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($setting[add])?"edit":"add")."_page&step=1&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Item";
if ($settings[step] != 1) $leftlinks .= "</a>";
$leftlinks .= "</td></tr>";

if ($settings[type] == "page" || $settings[type] == "url") {
	$leftlinks .= "<tr><td>";
	if ($settings[step] == 2) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($settings[step] != 2) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($setting[add])?"edit":"add")."_page&step=2&link=1 onClick=\"submitForm()\">";
	$leftlinks .= "Activation";
	if ($settings[step] != 2) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

if ($settings[type] == "page" && $auser == $settings[site_owner]) {
	$leftlinks .= "<tr><td>";
	if ($settings[step] == 3) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($settings[step] != 3) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($setting[add])?"edit":"add")."_page&step=3&link=1 onClick=\"submitForm()\">";
	$leftlinks .= "Editing Permissions";
	if ($settings[step] != 3) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

if ($settings[type] == "page") {
	$leftlinks .= "<tr><td>";
	if ($settings[step] == 4) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($settings[step] != 4) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($setting[add])?"edit":"add")."_page&step=4&link=1 onClick=\"submitForm()\">";
	$leftlinks .= "Show & Archive";
	if ($settings[step] != 4) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

$leftlinks .= "</table>________________<br><a href=$PHP_SELF?$sid&action=add_page&cancel=1>Cancel</a>";

add_link(leftnav,'','',"$leftlinks");

if ($settings[step] == 1) {
	include("add_page_form_1_item.inc");
}
if ($settings[step] == 2) {
	include("add_page_form_2_activation.inc");
}
if ($settings[step] == 3) {
	include("add_page_form_3_permissions.inc");
}
if ($settings[step] == 4) {
	include("add_page_form_4_show.inc");
}


// ---  variables for debugging ---
//foreach ($settings as $n => $v) {
//	$variables .= "$n = $v <br>";	
//}
//add_link(leftnav,'','',"$variables");
//print $variables;
//------------------------------------

// End of New Code
//--------------------------------------------------------------------------------------------------------
