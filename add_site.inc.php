<? // add_site.inc.php -- add a site (passed $name)

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
	if ($sitename != "") $settings[sitename] = $sitename;
	if ($settings[step] == 1 && $title != "") $settings[title] = $title;
	if ($activateyear != "") $settings[activateyear] = $activateyear;
	if ($activatemonth != "") $settings[activatemonth] = $activatemonth;
	if ($activateday != "") $settings[activateday] = $activateday;
	if ($settings[step] == 1 && !$link) $settings[activatedate] = $activatedate;
	if ($deactivateyear != "") $settings[deactivateyear] = $deactivateyear;
	if ($deactivatemonth != "") $settings[deactivatemonth] = $deactivatemonth;
	if ($deactivateday != "") $settings[deactivateday] = $deactivateday;
	if ($settings[step] == 1 && !$link) $settings[deactivatedate] = $deactivatedate;
	if ($active != "") $settings[active] = $active;
	if ($viewpermissions != "") $settings[viewpermissions] = $viewpermissions;
	if ($theme != "") $settings[theme] = $theme;
	if ($theme != "") $settings[themesettings] = $themesettings;
	if ($settings[step] == 3) $settings[template] = $template;
	if ($settings[step] == 4 && !$link) $settings[editors] = strtolower($editors);
	if ($settings[step] == 4 && !$link) $settings[permissions] = $permissions;
	if ($settings[step] == 1 && !$link) $settings[recursiveenable] = $recursiveenable;
	if ($copydownpermissions != "") $settings[copydownpermissions] = $copydownpermissions;
	if ($header != "") $settings[header] = $header;
	if ($footer != "") $settings[footer] = $footer;
	if ($copyfooter) $settings[header] = $settings[footer];
	if ($copyheader) $settings[footer] = $settings[header];
}

if (!$settings) {
	// create the settings array with default values. $settings must be passed along with each link.
	// The array will be saved on clicking a save button.
	session_register("settings");
	$settings = array(
		"add" => 0,
		"edit" => 0,
		"step" => 1,
		"sitename" => $sitename,
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
		"viewpermissions" => "anyone",
		"theme" => "minimal",
		"themesettings" => "",
		"template" => "template0",
		"editors" => "",
		"permissions" => "",
		"recursiveenable" => "",
		"copydownpermissions" => "",
		"header" => "",
		"footer" => "",
		"commingFrom" => $commingFrom
	);
	
	if ($action == 'add_site') {
		$settings[add]=1;
		$settings[edit]=0;
	}	
	if ($action == 'edit_site') { 
		$settings[add]=0;
		$settings[edit]=1;		
	}
	
	if ($settings[edit]) {
		if (!$settings[sitename]) $settings[sitename] = $edit_site;
		$a = db_get_line("sites","name='$settings[sitename]'");
		foreach ($a as $n=>$v) $settings[$n]=$v;
		list($settings[activateyear],$settings[activatemonth],$settings[activateday]) = explode("-",$settings[activatedate]);
		list($settings[deactivateyear],$settings[deactivatemonth],$settings[deactivateday]) = explode("-",$settings[deactivatedate]);
		$settings[activatemonth]-=1;
		$settings[deactivatemonth]-=1;
		$settings[activatedate]=($settings[activatedate]=='0000-00-00')?0:1;
		$settings[deactivatedate]=($settings[deactivatedate]=='0000-00-00')?0:1;
		$settings[permissions] = decode_array($settings[permissions]);
		$settings[copydownpermissions] = decode_array($settings[copydownpermissions]);
		$settings[header] = urldecode($settings[header]);
		$settings[footer] = urldecode($settings[footer]);
	}
}


if ($prevbutton) $settings[step] = $settings[step] - 1;
if ($nextbutton) $settings[step] = $settings[step] + 1; 
if ($step != "") $settings[step] = $step;

// error checking
if (($settings[step] != 1) && (!$settings[title] || $settings[title] == '')) {
	error("You must enter a site title.");
	$settings[step] = 1;
}

if ($settings[add]) $pagetitle="Add Site";
if ($settings[edit]) $pagetitle="Edit Site";

/*
// ---  variables for debugging ---
$variables = "<br><br>active = $active<br>";
$variables .= "action = $action <br> auser = $auser <br> settings = $settings";
foreach ($settings as $n => $v) {
	$variables .= "$n = $v <br>";	
}
add_link(leftnav,'','',"$variables");
//------------------------------------
*/

if (!sitenamevalid($settings[sitename])) {// check if the site name is valid
	error("You are not allowed to edit this site. Nice try.");
	return;
}
if ($cancel) {
	$commingFrom = $settings[commingFrom];
	$sitename = $settings[sitename];
	session_unregister("settings");
	if ($commingFrom) header("Location: index.php?$sid&action=$commingFrom&site=$sitename");
	else header("Location: index.php?$sid");
}


if ($save) {
	if (!$error) { // save it to the database
		if ($settings[add]) $addedby=$auser;
		if ($settings[edit]) $editedby = $auser;
		
		$where = '';
		if ($settings[add]) $query = "insert into sites set name='$settings[sitename]', addedby='$addedby', addedtimestamp=NOW()";
		else {
			$query = "update sites set editedby='$editedby'";
			$where = " where name='$settings[sitename]'";
		}
		
		if ($settings[activatedate]) $settings[activatedate] = $settings[activateyear] . "-" . ($settings[activatemonth]+1) . "-" . $settings[activateday];
		else $settings[activatedate] = "0000-00-00";
	
		if ($settings[deactivatedate]) $settings[deactivatedate] = $settings[deactivateyear] . "-" . ($settings[deactivatemonth]+1) . "-" . $settings[deactivateday];
		else $settings[deactivatedate] = "0000-00-00";
	
//		$active = ($active)?1:0;
			
//		$viewpermissions = 'anyone';
			
		$query .= ", title='$settings[title]', viewpermissions='$settings[viewpermissions]', activatedate='$settings[activatedate]', deactivatedate='$settings[deactivatedate]', active=$settings[active]";
		
		$query .= ", theme='$settings[theme]', themesettings='$settings[themesettings]'";

		$query .=", editors='$settings[editors]'";
		
		if ($settings[editors]) {
			$edlist = explode(",",$settings[editors]);
			foreach ($edlist as $e) {
				for ($i=0;$i<3;$i++) {
					$settings[permissions][$e][$i] = ($settings[permissions][$e][$i])?1:0;
				}
			}
		} else $settings[permissions] = '';
		print_r($settings[permissions]);
		$settings[permissions]=encode_array($settings[permissions]);
	
		$query .= ", permissions='$settings[permissions]'";

		$settings[header] = urlencode($settings[header]);
		$query .= ", header='$settings[header]'";

		$settings[footer] = urlencode($settings[footer]);
		$query .= ", footer='$settings[footer]'";
		
		db_query($query.$where);
		log_entry($action,"$auser ".(($settings[edit])?"edited":"added")." $settings[sitename]");
		printc("<br>query = $query$where");
		
		// --- Copy the Template on add ---
		if ($settings[add] && $settings[template] != "") {
			copySite($settings[template],$settings[sitename]);
		} else if ($settings[add]) {
			copySite("template0",$settings[sitename]);
		}
	
		// --- do the copy down and recursive changes for sections & pages --- 
		print "count for copy down: " . count($settings[copydownpermissions]) . "<BR>";
		
		$settings[permissions] = decode_array($settings[permissions]);
		$site_owner = $auser;
		if (/*$settings[edit] && */($settings[recursiveenable] || count($settings[copydownpermissions]))) {
			// recursively change the $active or $permissions field for all parts of the site
			$sections = decode_array(db_get_value("sites","sections","name='$settings[sitename]'"));
			foreach ($sections as $s) {
				$sa = db_get_line("sections","id=$s");
				$chg = array();
				if ($settings[recursiveenable] && permission($auser,SITE,EDIT,$settings[sitename])) $chg[] = "active=$settings[active]";
				if (count($settings[copydownpermissions])) {
					$sp = decode_array($sa['permissions']);
					foreach ($settings[copydownpermissions] as $e) $sp[$e] = $settings[permissions][$e];
					print_r($sp);
					$sp = encode_array($sp);
					$chg[] = "permissions='$sp'";
				}
				$query = "update sections set " . implode(",",$chg) . " where id=$s";
				print $query . "<BR>";
				if (count($chg)) db_query($query);
				
				$pages = decode_array($sa['pages']);
				foreach ($pages as $p) {
					$pa = db_get_line("pages","id=$p");
					$chg = array();
					if ($settings[recursiveenable] && permission($auser,SECTION,EDIT,$s)) $chg[] = "active=$settings[active]";
					if (count($settings[copydownpermissions])) {
						$pp = decode_array($pa['permissions']);
						foreach ($settings[copydownpermissions] as $e) $pp[$e] = $settings[permissions][$e];
						$pp = encode_array($pp);
						$chg[] = "permissions='$pp'";
					}
					$query = "update pages set " . implode(",",$chg) . " where id=$p";
					print "--> ".$query . "<BR>";
					if (count($chg)) db_query($query);
				}
			}
		}
		
		if ($settings[add]) {
			header("Location: index.php?$sid&action=viewsite&site=$settings[sitename]");
			session_unregister("settings");
		} else {
			$commingFrom = $settings[commingFrom];
			$sitename = $settings[sitename];
			session_unregister("settings");
			if ($commingFrom) header("Location: index.php?$sid&action=$commingFrom&site=$sitename");
			else header("Location: index.php?$sid");
		}

//		if ($edit) header("Location: index.php?$sid");
//		if ($add) {
//			if ($template) header("Location: index.php?$sid&action=viewsite&site=$sitename");
//			if (!$sitename) $sitename = $edit_site;
//		}
		
	} else {
//		$step = 1;
		printc ("<br>There was an error");
	}
	
}

if ($settings[edit] && $settings[step] == 3 && $prevbutton) $settings[step] = 2;
else if ($settings[edit] && $settings[step] == 3) $settings[step] = 4;

// ------- print out the add form -------
// --- The Navigation Links for the sidebar ---
$leftlinks = "________________<br><table>";
$leftlinks .= "<tr><td>";
if ($settings[step] == 1) $leftlinks .= "&rArr; ";
$leftlinks .= "</td><td>";
if ($settings[edit] && $settings[step] != 1) $leftlinks .= "<a href=$PHP_SELF?$sid&action=edit_site&sitename=$settings[sitename]&step=1&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Title & Availability";
if ($settings[step] != 1 && $settings[edit]) $leftlinks .= "</a>";

$leftlinks .= "</td></tr><tr><td>";
if ($settings[step] == 2) $leftlinks .= "&rArr; ";
$leftlinks .= "</td><td>";
if ($settings[edit] && $settings[step] != 2) $leftlinks .= "<a href=$PHP_SELF?$sid&action=edit_site&sitename=$settings[sitename]&step=2&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Appearance";
if ($settings[step] != 2 && $settings[edit]) $leftlinks .= "</a>";

if ($settings[add]) $leftlinks .= "</td></tr><tr><td>";
if ($settings[step] == 3 && $settings[add]) $leftlinks .= "&rArr; ";
$leftlinks .= "</td><td>";
if ($settings[edit] && $settings[step] != 3) $leftlinks .= "<a href=$PHP_SELF?$sid&action=edit_site&sitename=$settings[sitename]&step=3&link=1 onClick=\"submitForm()\">";
if ($settings[add]) $leftlinks .= "Template";
if ($settings[step] != 3 && $settings[edit]) $leftlinks .= "</a>";

$leftlinks .= "</td></tr><tr><td>";
if ($settings[step] == 4) $leftlinks .= "&rArr; ";
$leftlinks .= "</td><td>";
if ($settings[edit] && $settings[step] != 4) $leftlinks .= "<a href=$PHP_SELF?$sid&action=edit_site&sitename=$settings[sitename]&step=4&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Editing Permissions";
if ($settings[step] != 4 && $settings[edit]) $leftlinks .= "</a>";

$leftlinks .= "</td></tr><tr><td>";
if ($settings[step] == 5) $leftlinks .= "&rArr; ";
$leftlinks .= "</td><td>";
if ($settings[edit] && $settings[step] != 5) $leftlinks .= "<a href=$PHP_SELF?$sid&action=edit_site&sitename=$settings[sitename]&step=5&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Custom Header";
if ($settings[step] != 5 && $settings[edit]) $leftlinks .= "</a>";

$leftlinks .= "</td></tr><tr><td>";
if ($settings[step] == 6) $leftlinks .= "&rArr; ";
$leftlinks .= "</td><td>";
if ($settings[edit] && $settings[step] != 6) $leftlinks .= "<a href=$PHP_SELF?$sid&action=edit_site&sitename=$settings[sitename]&step=6&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Custom Footer";
if ($settings[step] != 6 && $settings[edit]) $leftlinks .= "</a>";
$leftlinks .= "</td></tr></table>________________<br><a href=$PHP_SELF?$sid&action=add_site&cancel=1>Cancel</a>";

add_link(leftnav,'','',"$leftlinks");

if ($settings[step] == 1) {
	include("add_site_form_1_title.inc");
}
if ($settings[step] == 2) {
	include("add_site_form_2_theme.inc");
}
if ($settings[step] == 3) {
	if ($settings[add]) include("add_site_form_3_template.inc");
}
if ($settings[step] == 4) {
	include("add_site_form_4_permissions.inc");
}
if ($settings[step] == 5) {
	include("add_site_form_5_header.inc");
}
if ($settings[step] == 6) {
	include("add_site_form_6_footer.inc");	
}



/*
// ---  variables for debugging ---
$variables = "<br>";
$variables .= "action = $action <br> auser = $auser <br> settings = $settings<br>";
foreach ($settings as $n =>$v) {
	$variables .= "$n = $v <br>";
}
printc("$variables");
//------------------------------------
*/
