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
if ($edit && !insite($site,$section,$page,$edit_story)) {
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
	if ($settings[step] == 1) $settings[title] = $title;
	if ($activateyear != "") $settings[activateyear] = $activateyear;
	if ($activatemonth != "") $settings[activatemonth] = $activatemonth;
	if ($activateday != "") $settings[activateday] = $activateday;
	if ($settings[step] == 3 && !$link) $settings[activatedate] = $activatedate;
	if ($deactivateyear != "") $settings[deactivateyear] = $deactivateyear;
	if ($deactivatemonth != "") $settings[deactivatemonth] = $deactivatemonth;
	if ($deactivateday != "") $settings[deactivateday] = $deactivateday;
	if ($settings[step] == 3 && !$link) $settings[deactivatedate] = $deactivatedate;
	if ($active != "") $settings[active] = $active;
	if ($viewpermissions != "") $settings[viewpermissions] = $viewpermissions;
	if ($settings[step] == 4 && !$link) $settings[editors] = strtolower($editors);
	if ($settings[step] == 4 && !$link) $settings[permissions] = $permissions;
//	if ($settings[step] == 4 && !$link) $settings[ediscussion] = $ediscussion;
	if ($settings[step] == 4 && !$link) $settings[locked] = $locked;
//	if ($settings[step] == 1 && !$link) $settings[recursiveenable] = $recursiveenable;
//	if ($copydownpermissions != "") $settings[copydownpermissions] = $copydownpermissions;
//	if ($settings[step] == 4 && !$link) $settings[showcreator] = $showcreator;
//	if ($settings[step] == 4 && !$link) $settings[showdate] = $showdate;
//	if ($archiveby) $settings[archiveby] = $archiveby;
	if ($url) $settings[url] = $url;
	if ($texttype) $settings[texttype] = $texttype;
	if ($settings[step] == 5 && !$link) $settings[discuss] = $discuss;
	if ($settings[step] == 5 && !$link) $settings[discusspermissions] = $discusspermissions;
	if ($settings[step] == 5 && !$link) $settings[category] = $category;
	if ($newcategory) {
		$settings[category] = $newcategory;
		$settings[categories][] = $newcategory;
		sort($settings[categories]);
	}
	if ($settings[step] == 1 && !$link) $settings[shorttext] = $shorttext;
	if ($settings[step] == 2 && !$link) $settings[longertext] = $longertext;
	
	//---- If switching type, take values to defaults ----
	if ($typeswitch) {
		$editors = db_get_value("sites","editors","name='$settings[site]'");
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
		$settings[editors] = $editors;
		$settings[ediscussion] = 0;
		$settings[locked] = 0;
		$settings[showcreator] = 0;
		$settings[showdate] = 0;
//		$settings[archiveby] = "none";
		$settings[discuss] = 0;
		$settings[discusspermissions] = "";
		$settings[texttype] = "text";
		$settings[category] = "";
		$settings[shorttext] = "";
		$settings[longertext] = "";
		
		if ($settings[add]) {
			//print "<p> deleting settings[permissions]....</p>";
			//$settings[permissions] = "";
			$settings[permissions] = decode_array(db_get_value("pages","permissions","id=$settings[page]"));
		}
	}
}

if (!$settings && !$error) {
	// create the settings array with default values. $settings must be passed along with each link.
	// The array will be saved on clicking a save button.
	$editors = db_get_value("sites","editors","name='$site'");
	session_register("settings");
	$settings = array(
		"site_owner" => $site_owner,
		"add" => 0,
		"edit" => 0,
		"step" => 1,
		"site" => $site,
		"section" => $section,
		"page" => $page,
		"story" => $edit_story,
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
		"editors" => $editors,
		"permissions" => "",
//		"ediscussion" => 1,
		"type" => "story",
		"url" => "http://",
		"discuss" => 0,
		"discusspermissions" => "",
		"texttype" => "text",
		"category" => "",
		"shorttext" => "",
		"longertext" => "",
		"commingFrom" => $commingFrom
	);
	
	$settings[pagetitle]=db_get_value("sites","title","name='$site'") . " > " . db_get_value("sections","title","id=$section") . " > ";
	
	if ($action == 'add_story') {
		$settings[add]=1;
		$settings[edit]=0;
		$settings[pagetitle] .= " Add Item";
	}	
	if ($action == 'edit_story') { 
		$settings[add]=0;
		$settings[edit]=1;
		$settings[pagetitle] .= " Edit Item";
	}
	
	if ($settings[add]) {
		$settings[permissions] = decode_array(db_get_value("pages","permissions","id=$page"));
	}
	
	if ($settings[edit]) {	
		$a = db_get_line("stories","id=$settings[story]");
		foreach ($a as $n=>$v) $settings[$n]=$v;
		list($settings[activateyear],$settings[activatemonth],$settings[activateday]) = explode("-",$settings[activatedate]);
		list($settings[deactivateyear],$settings[deactivatemonth],$settings[deactivateday]) = explode("-",$settings[deactivatedate]);
		$settings[activatemonth]-=1;
		$settings[deactivatemonth]-=1;
		$settings[activatedate]=($settings[activatedate]=='0000-00-00')?0:1;
		$settings[deactivatedate]=($settings[deactivatedate]=='0000-00-00')?0:1;
		$settings[permissions] = decode_array($settings[permissions]);
		$settings[shorttext] = urldecode($settings[shorttext]);
		$settings[longertext] = urldecode($settings[longertext]);
	}
	
	$settings[categories]=array();
	if (db_num_rows(($r=db_query("select distinct category,id from stories")))) {
		while ($a=db_fetch_assoc($r)) {
			if ($a[category]!='' && insite($settings[site],$settings[section],$settings[page],$a[id])) {
				$settings[categories][] = $a[category];
	/* 			$strs = decode_array(db_get_value("pages","stories","id=$settings[page]")); */
	/* 			print_r($strs); */
	/* 			print "$a[id] => ".in_array($a[id],$strs)."<BR>"; */
				
			}
		}
		sort($settings[categories]);
	}
}

if ($prevbutton) $settings[step] = $settings[step] - 1;
if ($nextbutton) $settings[step] = $settings[step] + 1; 
if ($step != "") $settings[step] = $step;
if ($settings[step] ==2 && $settings[type] != 'story') {
	if ($prevbutton) $settings[step] = 1;
	if ($nextbutton) $settings[step] = 3;
}
if ($settings[step] ==4 && $auser != $settings[site_owner]) {
	if ($prevbutton) $settings[step] = 3;
	if ($nextbutton) $settings[step] = 5;
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
	if ($settings[type]=='story' && (!$settings[shorttext] || trim($settings[shorttext])==''))
			error ("You must enter some story content.");
	if ($settings[type]=='link' && (!$settings[url] || $settings[url]=='' || $settings[url]=='http://'))
		error("You must enter a URL.");
	if ($settings[type]=='file' && (!$_FILES['file']['name'] || $_FILES['file']['name'] == '') && $settings[add])
		error("You must select a file to upload.");
	if ($settings[type]=='image' && (!$_FILES['file']['name'] || $_FILES['file']['name'] == '') && $settings[add])
		error("You must select an image to upload.");
		
		
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
		

		if ($settings[type] == 'image' || $settings[type] == 'file') {
			$settings[longertext] = $_FILES['file']['name'];
			if ($settings[edit]) {
				$oldfilename = db_get_value("stories","longertext","id=$settings[story]");
				if (!$settings[longertext]) {
					$settings[longertext] = $oldfilename;
				} else if ($oldfilename != $settings[longertext]) {
					// delete the old file
					deleteuserfile($settings[story],$oldfilename);
				}
			}
		}

		$settings[shorttext]=urlencode($settings[shorttext]);
		$settings[longertext]=urlencode($settings[longertext]);
		
		
		// check make sure the owner is the current user if they are changing permissions
		if ($settings[site_owner] != $auser)
			$settings[permissions] = decode_array(db_get_value("sections","permissions","id=$settings[section]"));
		
		// make sure that the permissions array represents all of the editors (giving them either permission (1) or not (0))
		//$settings[editors] = db_get_value("sites","editors","name='$settings[site]'");
		if ($settings[editors]) {
			$edlist = explode(",",$settings[editors]);
			foreach ($edlist as $e) {
				for ($i=0;$i<3;$i++) {
					$settings[permissions][$e][$i] = ($settings[permissions][$e][$i])?1:0;
				}
			}
		}
		
		$settings[permissions] = encode_array($settings[permissions]);
		if ($settings[add]) $query = "insert into stories set addedby='$auser',addedtimestamp=NOW(),";
		$where = '';
		if ($settings[edit]) { 
			$query = "update stories set editedby='$auser',"; $where = " where id=$settings[story]"; 
		}
		$query .= "discuss='$settings[discuss]',discusspermissions='$settings[discusspermissions]',texttype='$settings[texttype]',category='$settings[category]',shorttext='$settings[shorttext]',longertext='$settings[longertext]',url='$settings[url]',type='$settings[type]',title='$settings[title]', locked=$settings[locked], activatedate='$settings[activatedate]', deactivatedate='$settings[deactivatedate]', permissions='$settings[permissions]'";
		
		db_query($query.$where);
		print "$query$where<BR>";
		print mysql_error();
		
		// add the new story id to the pages table
		if ($settings[add]) {
			$newid = lastid();
			$stories = decode_array(db_get_value("pages","stories","id=$settings[page]"));
			array_push($stories,$newid);
			$stories = encode_array($stories);
			$query = "update pages set stories='$stories' where id=$settings[page]";
			db_query($query);
			log_entry("add_story",$settings[site],$settings[section],$page,"$auser added content id $newid to page $settings[page] in section $settings[section] of site $settings[site]");
		}
		if ($settings[edit]) {
			log_entry("edit_page",$settings[site],$settings[section],$settings[page],"$auser edited content id $settings[story] in page $settings[page] of section $settings[section] of site $settings[site]");
			$newid=$settings[page];
			if ($settings[type] == 'image' || $settings[type] == 'file') {
				copyuserfile($settings[story],$_FILES['file']);
			}
		}
		
		// add or remove any changes to the site editor list.
		$query = "update sites set editors='$settings[editors]',editedtimestamp=NOW() where  name='$settings[site]'";
		db_query($query);
		print "$query <br>";
		
//		$query = "update sites set editedtimestamp=NOW() where  name='$settings[site]'";
//		db_query($query);
//		print "$query <br>";
		
		header("Location: index.php?$sid&action=viewsite&site=$settings[site]&section=$settings[section]&page=$settings[page]");
		
	} else {
		$settings[step] = 1;
	}
}

// ------- print out the add form -------
// --- The Navigation Links for the sidebar ---
$leftlinks = "_________________<br><table>";
$leftlinks .= "<tr><td>";
if ($settings[step] == 1) $leftlinks .= "&rArr; ";
$leftlinks .= "</td><td>";
if ($settings[step] != 1) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($setting[add])?"edit":"add")."_story&step=1&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Content";
if ($settings[step] != 1) $leftlinks .= "</a>";
$leftlinks .= "</td></tr>";

if ($settings[type] == "story") {
	$leftlinks .= "<tr><td>";
	if ($settings[step] == 2) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($settings[step] != 2) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($setting[add])?"edit":"add")."_story&step=2&link=1 onClick=\"submitForm()\">";
	$leftlinks .= "Full Content";
	if ($settings[step] != 2) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

if (1) {
	$leftlinks .= "<tr><td>";
	if ($settings[step] == 3) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($settings[step] != 3) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($setting[add])?"edit":"add")."_story&step=3&link=1 onClick=\"submitForm()\">";
	$leftlinks .= "Activation";
	if ($settings[step] != 3) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

if ($auser == $settings[site_owner]) {
	$leftlinks .= "<tr><td>";
	if ($settings[step] == 4) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($settings[step] != 4) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($setting[add])?"edit":"add")."_story&step=4&link=1 onClick=\"submitForm()\">";
	$leftlinks .= "Permissions";
	if ($settings[step] != 4) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

if ($settings[type] == "story") {
	$leftlinks .= "<tr><td>";
	if ($settings[step] == 5) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($settings[step] != 5) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($setting[add])?"edit":"add")."_story&step=5&link=1 onClick=\"submitForm()\">";
	$leftlinks .= "Discussion";
	if ($settings[step] != 5) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

$leftlinks .= "</table>_________________<br><a href=$PHP_SELF?$sid&action=add_page&cancel=1>Cancel</a>";

add_link(leftnav,'','',"$leftlinks");

if ($settings[step] == 1) {
	include("add_story_form_1_item.inc");
}
if ($settings[step] == 2) {
	include("add_story_form_2_fulltext.inc");
}
if ($settings[step] == 3) {
	include("add_story_form_3_activation.inc");
}
if ($settings[step] == 4) {
	include("add_story_form_4_permissions.inc");
}
if ($settings[step] == 5) {
	include("add_story_form_5_discussion.inc");
}

// ---  variables for debugging ---
$vars = $settings;
ksort($vars);
foreach ($vars as $n => $v) {
	$variables .= "$n = $v <br>";	
}
//add_link(leftnav,'','',"$variables");
//printc("$variables");
//------------------------------------

// End of New Code
//--------------------------------------------------------------------------------------------------------
