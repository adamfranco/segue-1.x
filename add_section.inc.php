<? // add_section.inc.php -- add a section

//--------------------------------------------------------------------------------------------------------
// Begining of new code

/* ----------------------------------------------------------------------- */
/*   redo all below permissions checks with new permissions scheme			*/

// first check if we are allowed to edit this site at all
if ($_SESSION[auser] != $site_owner && $_SESSION[auser] != $_SESSION[settings][site_owner] && !is_editor($_SESSION[auser],$thisSite->name) && !is_editor($_SESSION[auser],$_SESSION[settings][site])) {
	error("You're not even an editor for this site! Bad person!");
	return;
}
if ($edit && !permission($_SESSION[auser],SECTION,EDIT,$thisSite->name) && !permission($_SESSION[auser],SECTION,EDIT,$_SESSION[settings][site])) {
	error("You don't have permission to edit this page. Nice try.");
	return;
}
if ($add && !permission($_SESSION[auser],SECTION,ADD,$thisSite->name)  && !permission($_SESSION[auser],SECTION,ADD,$_SESSION[settings][site])) {
	error("You don't have permission to add sections to this site. Nice try.");
	return;
}
if ($edit && !insite($thisSite->name,$_REQUEST[edit_section])) {
	error("Oh, you're good, but not good enough!");
	return;
}

if ($_SESSION[settings]) {
	// if we have already started editing...

	// --- Load any new variables into the array ---
	// Checkboxes need a "if ($_SESSION[settings][step] == 1 && !$link)" tag.
	// True/False radio buttons need a "if ($var != "")" tag to get the "0" values
	if ($type) $_SESSION[settings][type] = $type;
	if ($_SESSION[settings][step] == 1 && $title != "") $_SESSION[settings][title] = $title;
	if ($activateyear != "") $_SESSION[settings][activateyear] = $activateyear;
	if ($activatemonth != "") $_SESSION[settings][activatemonth] = $activatemonth;
	if ($activateday != "") $_SESSION[settings][activateday] = $activateday;
	if ($_SESSION[settings][step] == 2 && !$link) $_SESSION[settings][activatedate] = $activatedate;
	if ($deactivateyear != "") $_SESSION[settings][deactivateyear] = $deactivateyear;
	if ($deactivatemonth != "") $_SESSION[settings][deactivatemonth] = $deactivatemonth;
	if ($deactivateday != "") $_SESSION[settings][deactivateday] = $deactivateday;
	if ($_SESSION[settings][step] == 2 && !$link) $_SESSION[settings][deactivatedate] = $deactivatedate;
	if ($active != "") $_SESSION[settings][active] = $active;
	if ($viewpermissions != "") $_SESSION[settings][viewpermissions] = $viewpermissions;
	if ($_SESSION[settings][step] == 3 && !$link) $_SESSION[settings][editors] = strtolower($editors);
	if ($_SESSION[settings][step] == 3 && !$link) $_SESSION[settings][permissions] = $permissions;
	if ($_SESSION[settings][step] == 3 && !$link) $_SESSION[settings][ediscussion] = $ediscussion;
	if ($_SESSION[settings][step] == 3 && !$link) $_SESSION[settings][locked] = $locked;
//	if ($_SESSION[settings][step] == 1 && !$link) $_SESSION[settings][recursiveenable] = $recursiveenable;
	if ($_SESSION[settings][step] == 3 && !$link) $_SESSION[settings][copydownpermissions] = $copydownpermissions;
	if ($_SESSION[settings][step] == 4 && !$link) $_SESSION[settings][showcreator] = $showcreator;
	if ($_SESSION[settings][step] == 4 && !$link) $_SESSION[settings][showdate] = $showdate;
	if ($archiveby) $_SESSION[settings][archiveby] = $archiveby;
	if ($url) $_SESSION[settings][url] = $url;
	
	//---- If switching type, take values to defaults ----
	if ($typeswitch) {
		$_SESSION[settings][title] = "";
		$_SESSION[settings][url] = "http://";
		$_SESSION[settings][active] = 1;
		$_SESSION[settings][activateyear] = "0000";
		$_SESSION[settings][activatemonth] = "00";
		$_SESSION[settings][activateday] = "00";
		$_SESSION[settings][activatedate] = 0;
		$_SESSION[settings][deactivateyear] = "0000";
		$_SESSION[settings][deactivatemonth] = "00";
		$_SESSION[settings][deactivateday] = "00";
		$_SESSION[settings][deactivatedate] = 0;
		$_SESSION[settings][active] = 1;
		$_SESSION[settings][editors] = "";
		$_SESSION[settings][ediscussion] = 0;
		$_SESSION[settings][locked] = 0;
		$_SESSION[settings][showcreator] = 0;
		$_SESSION[settings][showdate] = 0;
		$_SESSION[settings][archiveby] = "none";
		
		if ($_SESSION[settings][add]) {
			//print "<p> deleting settings[permissions]....</p>";
			//$_SESSION[settings][permissions] = "";
			$_SESSION[settings][permissions] = decode_array(db_get_value("sections","permissions","id=$_SESSION[settings][section]"));
		}
	}
}

if (!$_SESSION[settings] && !$error) {
	// create the settings array with default values. $_SESSION[settings] must be passed along with each link.
	// The array will be saved on clicking a save button.
//	$editors = db_get_value("sites","editors","name='$site'");
//	session_register("settings");
	$_SESSION[settings] = array(
		"add" => 0,
		"edit" => 0,
		"step" => 1,
		"activateyear" => "0000",
		"activatemonth" => "00",
		"activateday" => "00",
		"activatedate" => 0,
		"deactivateyear" => "0000",
		"deactivatemonth" => "00",
		"deactivateday" => "00",
		"deactivatedate" => 0,
		"commingFrom" => $commingFrom
	);
	
	$_SESSION[sectionObj] = new section($thisSite->name);
	
	if ($action == 'add_section') {
		$_SESSION[settings][add]=1;
		$_SESSION[settings][edit]=0;
	}	
	if ($action == 'edit_section') { 
		$_SESSION[settings][add]=0;
		$_SESSION[settings][edit]=1;
	}
	
	if ($_SESSION[settings][add]) {
		$_SESSION[sectionObj]->setPermissionsArray($thisSite->getPermissionsArray());
		$_SESSION[settings][pagetitle] = $thisSite->getField("title") . " > " . "Add Item";
	}
	
	if ($_SESSION[settings][edit]) {
		$_SESSION[sectionObj]->fetchFromDB($_REQUEST[edit_section]);
		$a = $_SESSION[sectionObj]->getData();
//		foreach ($a as $n=>$v) $_SESSION[settings][$n]=$v;
		list($_SESSION[settings][activateyear],$_SESSION[settings][activatemonth],$_SESSION[settings][activateday]) = explode("-",$_SESSION[sectionObj]->getField("activatedate"));
		list($_SESSION[settings][deactivateyear],$_SESSION[settings][deactivatemonth],$_SESSION[settings][deactivateday]) = explode("-",$_SESSION[sectionObj]->getField("deactivatedate"));
		$_SESSION[settings][activatemonth]-=1;
		$_SESSION[settings][deactivatemonth]-=1;
		$_SESSION[settings][activatedate]=($_SESSION[sectionObj]->getField("activatedate")=='0000-00-00')?0:1;
		$_SESSION[settings][deactivatedate]=($_SESSION[sectionObj]->getField("deactivatedate")=='0000-00-00')?0:1;
/* 		$_SESSION[settings][permissions] = decode_array($_SESSION[settings][permissions]); */
		$_SESSION[settings][pagetitle]= $thisSite->getField("title") . " > " . $_SESSION[sectionObj]->getField("title") . " > Edit Item";
	}
	
}

/* $error = 0; */
// error checking
if ($_SESSION[settings][step] == 1) {
	if ($_SESSION[sectionObj]->getField("type")!='divider' && (!$_SESSION[sectionObj]->getField("title") || $_SESSION[sectionObj]->getField("title")==''))
		error("You must enter a header title.");
	if ($_SESSION[sectionObj]->getField("type")=='url' && (!$_SESSION[sectionObj]->getField("url") || $_SESSION[sectionObj]->getField("url")=='' || $_SESSION[sectionObj]->getField("url")=='http://'))
		error("You must enter a URL.");
}

if (!$error) {
	if ($_REQEUST[prevbutton]) $_SESSION[settings][step] = $_SESSION[settings][step] - 1;
	if ($_REQEUST[nextbutton]) $_SESSION[settings][step] = $_SESSION[settings][step] + 1; 
}
if ($_REQUEST[step] != "") $_SESSION[settings][step] = $_REQUEST[step];
if ($_SESSION[settings][step] ==3 && $_SESSION[auser] != $site_owner) {
	if ($_REQEUST[prevbutton]) $_SESSION[settings][step] = 2;
	if ($_REQEUST[nextbutton]) $_SESSION[settings][step] = 4;
}

$pagetitle=$_SESSION[settings][pagetitle];

//-----for some reason siteheader and sitefooter keep being defined prior to this point on button click. I'm killing them here until their origin is found ----
$site = "";
$section = "";
$page = "";
$siteheader = "";
$sitefooter = "";

if ($_REQEUST[cancel]) {
	$commingFrom = $_SESSION[settings][commingFrom];
	$site = $thisSite->name;	
	if ($_SESSION[settings][edit] && $_SESSION[sectionObj]->getField("type")=='section') $section = $_SESSION[sectionObj]->id;
	if (ini_get("register_globals")) { session_unregister("settings"); session_unregister("sectionObj"); }
	unset($_SESSION[sectionObj],$_SESSION[settings]);
	if ($commingFrom) header("Location: index.php?$sid&action=$commingFrom&site=$site".(($section)?"&section=$section":""));
	else header("Location: index.php?$sid");
}

if ($_REQUEST[save]) {
		
	if (!$error) { // save it to the database
/* 		$addedby=$auser; */
/* 		if ($_SESSION[settings][activatedate]) $_SESSION[settings][activatedate] = $_SESSION[settings][activateyear] . "-" . ($_SESSION[settings][activatemonth]+1) . "-" . $_SESSION[settings][activateday]; */
/* 		else $_SESSION[settings][activatedate] = "0000-00-00"; */
/* 		if ($_SESSION[settings][deactivatedate]) $_SESSION[settings][deactivatedate] = $_SESSION[settings][deactivateyear] . "-" . ($_SESSION[settings][deactivatemonth]+1) . "-" . $_SESSION[settings][deactivateday]; */
/* 		else $_SESSION[settings][deactivatedate] = "0000-00-00"; */
/* 		$_SESSION[settings][active] = ($_SESSION[settings][active])?1:0; */
/* 		$_SESSION[settings][locked] = ($_SESSION[settings][locked])?1:0; */
//		$_SESSION[settings][showcreator] = ($_SESSION[settings][showcreator])?1:0;
//		$_SESSION[settings][showdate] = ($_SESSION[settings][showdate])?1:0;
//		$_SESSION[settings][ediscussion] = ($_SESSION[settings][ediscussion])?1:0;
		
		// check make sure the owner is the current user if they are changing permissions
		if ($site_owner != $_SESSION[auser])
			$_SESSION[sectionObj]->setPermissionsArray($thisSite->getPermissionsArray());
		
		// make sure that the permissions array represents all of the editors (giving them either permission (1) or not (0))
		// $_SESSION[settings][editors] = db_get_value("sites","editors","name='$_SESSION[settings][site]'"); // taken care of durring initialization
/* 		if ($_SESSION[settings][editors]) { */
/* 			$edlist = explode(",",$_SESSION[settings][editors]); */
/* 			foreach ($edlist as $e) { */
/* 				for ($i=0;$i<3;$i++) { */
/* 					$_SESSION[settings][permissions][$e][$i] = ($_SESSION[settings][permissions][$e][$i])?1:0; */
/* 				} */
/* 			} */
/* 		} */
		
/* 		$_SESSION[settings][permissions] = encode_array($_SESSION[settings][permissions]); */
/* 		if ($_SESSION[settings][add]) $query = "insert into sections set addedby='$auser',addedtimestamp=NOW(),"; */
/* 		$where = ''; */
/* 		if ($_SESSION[settings][edit]) {  */
/* 			$query = "update sections set editedby='$auser',"; $where = " where id=$_SESSION[settings][section]";  */
/* 		} */
		
/* 		$chg = array(); */
/* 		$chg[] = "site_id='$_SESSION[settings][site]'"; */
/* 		$chg[] = "url='$_SESSION[settings][url]'"; */
/* 		$chg[] = "type='$_SESSION[settings][type]'"; */
/* 		$chg[] = "title='$_SESSION[settings][title]'"; */
/* 		$chg[] = "locked=$_SESSION[settings][locked]"; */
/* 		$chg[] = "activatedate='$_SESSION[settings][activatedate]'"; */
/* 		$chg[] = "deactivatedate='$_SESSION[settings][deactivatedate]'"; */
/* 		$chg[] = "active=$_SESSION[settings][active]"; */
/* 		$chg[] = "permissions='$_SESSION[settings][permissions]'"; */
/* 		 */
/* 		$query .= implode(",",$chg); */
/* 		print $query.$where."<BR>"; */
/* 		if (count($chg)) db_query($query.$where); */
/* 		print mysql_error(); */
		
		// add the new section id to the sites table
		if ($_SESSION[settings][add]) {
			$_SESSION[sectionObj]->insertDB();
			$newid = $_SESSION[sectionObj]->id;
/* 			$newid = lastid(); */
/* 			print "newid = $newid <br>"; */
/* 			$sections = decode_array(db_get_value("sites","sections","name='$_SESSION[settings][site]'")); */
/* 			array_push($sections,$newid); */
/* 			$sections = encode_array($sections); */
/* 			$query = "update sites set sections='$sections' where name='$_SESSION[settings][site]'"; */
/* 			db_query($query); */
/* 			print "$query <br>"; */
			log_entry("add_section",$thisSite->name,$newid,"","$auser added section id $newid to site ".$thisSite->name);
		}
		if ($_SESSION[settings][edit]) {
/*  			$newid=$_SESSION[settings][section]; */
			$newid = $_SESSION[sectionObj]->id;
			log_entry("edit_section",$thisSite->name,$newid,"","$auser edited section id $newid in site ".$thisSite->name);
		}

		// add or remove any changes to the site editor list.
/* 		$query = "update sites set ".(($_SESSION[settings][type]=="section")?"editors='$_SESSION[settings][editors]',":"")."editedtimestamp=NOW() where  name='$_SESSION[settings][site]'"; */
/* 		db_query($query); */
		
		// do the recursive update of active flag and such... .... ugh
		$_SESSION[settings][permissions] = decode_array($_SESSION[settings][permissions]);
		if ($_SESSION[settings][edit] && ($_SESSION[settings][recursiveenable] || count($_SESSION[settings][copydownpermissions]))) {
			// recursively change the $active or $permissions field for all parts of the site
			$pages = decode_array(db_get_value("sections","pages","id=$_SESSION[settings][section]"));
			foreach ($pages as $p) {
				$pa = db_get_line("pages","id=$p");
				$chg = array();
				if ($recursiveenable && permission($auser,SECTION,EDIT,$_SESSION[settings][section])) $chg[] = "active=$_SESSION[settings][active]";
				if (count($_SESSION[settings][copydownpermissions]) && $auser == $_SESSION[settings][site_owner]) {
					$pp = decode_array($pa['permissions']);
					foreach ($_SESSION[settings][copydownpermissions] as $e) $pp[$e] = $_SESSION[settings][permissions][$e];
					$pp = encode_array($pp);
					$chg[] = "permissions='$pp'";
				}
				$query = "update pages set " . implode(",",$chg) . " where id=$p";
				print "--> ".$query . "<BR>";
				if (count($chg)) db_query($query);
				
				$stories = decode_array(db_get_value("pages","stories","id=$p"));
				foreach ($stories as $s) {
					$sa = db_get_line("stories","id=$s");
					$chg = array();
					if ($recursiveenable && permission($auser,PAGE,EDIT,$p)) $chg[] = "active=$_SESSION[settings][active]";
					if (count($_SESSION[settings][copydownpermissions]) && $auser == $_SESSION[settings][site_owner]) {
						$sp = decode_array($sa['permissions']);
						foreach ($_SESSION[settings][copydownpermissions] as $e) $sp[$e] = $_SESSION[settings][permissions][$e];
						$sp = encode_array($sp);
						$chg[] = "permissions='$sp'";
					}
					$query = "update stories set " . implode(",",$chg) . " where id=$s";
					print "--> ".$query . "<BR>";
					if (count($chg)) db_query($query);
				}
			}
			
		}
		
		header("Location: index.php?$sid&action=viewsite&site=$_SESSION[settings][site]".(($_SESSION[settings][type]=='section')?"&section=$newid":""));
		
	} else {
		$_SESSION[settings][step] = 1;
	}
}

// ------- print out the add form -------
// --- The Navigation Links for the sidebar ---
$leftlinks = "_________________<br><table>";
$leftlinks .= "<tr><td>";
if ($_SESSION[settings][step] == 1) $leftlinks .= "&rArr; ";
$leftlinks .= "</td><td>";
if ($_SESSION[settings][step] != 1) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($setting[add])?"edit":"add")."_section&step=1&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Item";
if ($_SESSION[settings][step] != 1) $leftlinks .= "</a>";
$leftlinks .= "</td></tr>";

if ($_SESSION[settings][type] == "section" || $_SESSION[settings][type] == "url") {
	$leftlinks .= "<tr><td>";
	if ($_SESSION[settings][step] == 2) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($_SESSION[settings][step] != 2) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($setting[add])?"edit":"add")."_section&step=2&link=1 onClick=\"submitForm()\">";
	$leftlinks .= "Activation";
	if ($_SESSION[settings][step] != 2) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

if ($_SESSION[settings][type] == "section" && $auser == $_SESSION[settings][site_owner]) {
	$leftlinks .= "<tr><td>";
	if ($_SESSION[settings][step] == 3) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($_SESSION[settings][step] != 3) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($setting[add])?"edit":"add")."_section&step=3&link=1 onClick=\"submitForm()\">";
	$leftlinks .= "Editing Permissions";
	if ($_SESSION[settings][step] != 3) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

$leftlinks .= "</table>_________________<br><a href=$PHP_SELF?$sid&action=add_page&cancel=1>Cancel</a>";

add_link(leftnav,'','',"$leftlinks");

if ($_SESSION[settings][step] == 1) {
	include("add_section_form_1_item.inc");
}
if ($_SESSION[settings][step] == 2) {
	include("add_section_form_2_activation.inc");
}
if ($_SESSION[settings][step] == 3) {
	include("add_section_form_3_permissions.inc");
}


// ---  variables for debugging ---
//foreach ($_SESSION[settings] as $n => $v) {
//	$variables .= "$n = $v <br>";	
//}
//add_link(leftnav,'','',"$variables");
//print $variables;
//------------------------------------

// End of New Code
//--------------------------------------------------------------------------------------------------------
