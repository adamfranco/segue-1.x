<? /* $Id$ */

//--------------------------------------------------------------------------------------------------------
// Begining of new code

// ---  variables for debugging ---
//foreach ($_SESSION[settings] as $n => $v) {
//	$variables .= "$n = $v <br>";	
//}
//add_link(leftnav,'','',"$variables");
//print $variables."<br>site owner = $site_owner <br>typeswitch = $typeswitch <br>";
//print "siteheader = '$siteheader' <br>sitefooter = '$sitefooter' <br>";
//print "site = $site<br>section = $section<br>page=$page<br>";
//------------------------------------

// first check if we are allowed to edit this site at all
/* if ($auser != $site_owner && $auser != $_SESSION[settings][site_owner] && !is_editor($auser,$site) && !is_editor($auser,$_SESSION[settings][site])) { */
/* 	error("You're not even an editor for this site! Bad person!"); */
/* 	return; */
/* } */
/* if ($edit && !permission($auser,SECTION,EDIT,$section) && !permission($auser,SECTION,EDIT,$_SESSION[settings][section])) { */
/* 	error("You don't have permission to edit this page. Nice try."); */
/* 	return; */
/* } */
/* if ($add && !permission($auser,SECTION,ADD,$section)  && !permission($auser,SECTION,ADD,$_SESSION[settings][section])) { */
/* 	error("You don't have permission to add sections to this site. Nice try."); */
/* 	return; */
/* } */
/* if ($edit && !insite($site,$section,$edit_page)) { */
/* 	error("Oh, you're good, but not good enough!"); */
/* 	return; */
/* } */

if (is_array($_SESSION[settings]) && is_object($_SESSION[pageObj])) {
	// if we have already started editing...

	// --- Load any new variables into the array ---
	// Checkboxes need a "if ($_SESSION[settings][step] == 1 && !$link)" tag.
	// True/False radio buttons need a "if ($var != "")" tag to get the "0" values
	if ($_REQUEST[type]) $_SESSION[pageObj]->setField("type",$_REQUEST[type]);
	if ($_REQUEST[title] != "") $_SESSION[pageObj]->setField("title",$_REQUEST[title]);
	$_SESSION[pageObj]->handleFormDates();	// handle de/activate dates
	if ($_REQUEST[active] != "") $_SESSION[pageObj]->setField("active",$_REQUEST[active]);
	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("ediscussion",$_REQUEST[ediscussion]);
	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("locked",$_REQUEST[locked]);
	if ($_REQUEST[copydownpermissions] != "") $_SESSION[settings][copydownpermissions] = $_REQUEST[copydownpermissions];
	if ($_SESSION[settings][step] == 4 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("showcreator",$_REQUEST[showcreator]);
	if ($_SESSION[settings][step] == 4 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("showdate",$_REQUEST[showdate]);
	if ($_SESSION[settings][step] == 4 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("storyorder",$_REQUEST[storyorder]);
	if ($_SESSION[settings][step] == 4 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("showhr",$_REQUEST[showhr]);
	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[pageObj]->setPermissions($_REQUEST[permissions]);
	if ($_REQUEST[archiveby]) $_SESSION[pageObj]->setField("archiveby",$_REQUEST[archiveby]);
	if ($_REQUEST[url]) $_SESSION[pageObj]->setField("url",$_REQUEST[url]);
	
	//---- If switching type, take values to defaults ----
	if ($_REQUEST[typeswitch]) {
		$_SESSION[pageObj]->init(1);		// init values... force form date variables
		
		if ($_SESSION[settings][add]) {
			//print "<p> deleting settings[permissions]....</p>";
			//$_SESSION[settings][permissions] = "";
			$_SESSION[pageObj]->setPermissions($thisSection->getPermissions());
		}
	}
}

if ((!is_array($_SESSION[settings]) || !is_object($_SESSION[pageObj]))/*  && !$error */) {
	// create the settings array with default values. $_SESSION[settings] must be passed along with each link.
	// The array will be saved on clicking a save button.
	$_SESSION[settings] = array(
		"site_owner" => $site_owner,
		"add" => 0,
		"edit" => 0,
		"step" => 1,
		"site" => $thisSite->name,
		"section" => $thisSection->id,
		"commingFrom" => $commingFrom
	);

	$_SESSION[pageObj] = new page($thisSite->name,$thisSection->id);
	
	$_SESSION[settings][pagetitle]=$thisSite->getField("title") . " > " . $thisSection->getField("title") . " > ";
	
	if ($action == 'add_page') {
		$_SESSION[settings][add]=1;
		$_SESSION[settings][edit]=0;
		$_SESSION[settings][pagetitle] .= " Add Item";
	}	
	if ($action == 'edit_page') { 
		$_SESSION[settings][add]=0;
		$_SESSION[settings][edit]=1;
		$_SESSION[settings][pagetitle] .= " Edit Item";
	}
	
	if ($_SESSION[settings][add]) {
		$_SESSION[pageObj]->setPermissions($thisSection->getPermissions);
	}
	
	if ($_SESSION[settings][edit]) {
		$_SESSION[pageObj]->fetchFromDB($_REQUEST[edit_page]);
/* 		$a = db_get_line("pages","id=$_SESSION[settings][page]"); */
/* 		foreach ($a as $n=>$v) $_SESSION[settings][$n]=$v; */
/* 		list($_SESSION[settings][activateyear],$_SESSION[settings][activatemonth],$_SESSION[settings][activateday]) = explode("-",$_SESSION[settings][activatedate]); */
/* 		list($_SESSION[settings][deactivateyear],$_SESSION[settings][deactivatemonth],$_SESSION[settings][deactivateday]) = explode("-",$_SESSION[settings][deactivatedate]); */
/* 		$_SESSION[settings][activatemonth]-=1; */
/* 		$_SESSION[settings][deactivatemonth]-=1; */
/* 		$_SESSION[settings][activatedate]=($_SESSION[settings][activatedate]=='0000-00-00')?0:1; */
/* 		$_SESSION[settings][deactivatedate]=($_SESSION[settings][deactivatedate]=='0000-00-00')?0:1; */
/* 		$_SESSION[settings][permissions] = decode_array($_SESSION[settings][permissions]); */
	}
	$_SESSION[pageObj]->initFormDates();
}

if ($_REQUEST[prevbutton]) $_SESSION[settings][step] -= 1;
if ($_REQUEST[nextbutton]) $_SESSION[settings][step] += 1; 
if ($_REQUEST[step] != "") $_SESSION[settings][step] = $_REQUEST[step];
if ($_SESSION[settings][step] == 3 && $_SESSION[auser] != $site_owner) {
	if ($_REQUEST[prevbutton]) $_SESSION[settings][step] = 2;
	if ($_REQUEST[nextbutton]) $_SESSION[settings][step] = 4;
}

$pagetitle=$_SESSION[settings][pagetitle];

//-----for some reason siteheader and sitefooter keep being define prior to this point on button click. I'm killing them here until their origen is found ----
$site = "";
$section = "";
$page = "";
$siteheader = "";
$sitefooter = "";

if ($_REQUEST[cancel]) {
	$commingFrom = $_SESSION[settings][commingFrom];
	$site = $thisSite->name;
//	session_unregister("settings"); // handled by index.php
	if ($commingFrom) header("Location: index.php?$sid&action=$commingFrom&site=$site");
	else header("Location: index.php?$sid");
}

if ($_REQUEST[save]) {
/* 	$error = 0; */
	// error checking
	if ($_SESSION[pageObj]->getField("type")!='divider' && (!$_SESSION[pageObj]->getField("title") || $_SESSION[pageObj]->getField("title")==''))
		error("You must enter a title.");
	if ($_SESSION[pageObj]->getField("type")=='url' && (!$_SESSION[pageObj]->getField("url") || $_SESSION[settings][url]=='' || $_SESSION[pageObj]->getField("url")=='http://'))
		error("You must enter a URL.");
		
	if (!$error) { // save it to the database
		
		// check make sure the owner is the current user if they are changing permissions
		if ($site_owner != $_SESSION[auser])
			$_SESSION[pageObj]->setPermissions($thisSection->getPermissions());
		
		if ($_SESSION[settings][edit]) { 
			$_SESSION[pageObj]->updateDB();
/* 			$query = "update pages set editedby='$auser',"; $where = " where id=$_SESSION[settings][page]";  */
		}
		if ($_SESSION[settings][add]) {
			$_SESSION[pageObj]->insertDB();
		}
		
		// do the recursive update of active flag and such... .... ugh
		$_SESSION[settings][permissions] = decode_array($_SESSION[settings][permissions]);
		if ($_SESSION[settings][edit] && ($_SESSION[settings][recursiveenable] || count($_SESSION[settings][copydownpermissions]))) {
			// recursively change the $active or $permissions field for all parts of the site			
			$stories = decode_array(db_get_value("pages","stories","id=$_SESSION[settings][page]"));
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
		
		header("Location: index.php?$sid&action=viewsite&site=".$thisSite->name."&section=".$thisSection->id.(($_SESSION[pageObj]->getField("type")=='page')?"&page=".$_SESSION[pageObj]->id:""));
		
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
if ($_SESSION[settings][step] != 1) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($_SESSION[settings][edit])?"edit":"add")."_page&step=1&link=1 onClick=\"submitForm()\">";
$leftlinks .= "Item";
if ($_SESSION[settings][step] != 1) $leftlinks .= "</a>";
$leftlinks .= "</td></tr>";

if ($_SESSION[pageObj]->getField("type") == "page" || $_SESSION[pageObj]->getField("type") == "url") {
	$leftlinks .= "<tr><td>";
	if ($_SESSION[settings][step] == 2) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($_SESSION[settings][step] != 2) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($_SESSION[settings][edit])?"edit":"add")."_page&step=2&link=1 onClick=\"submitForm()\">";
	$leftlinks .= "Activation";
	if ($_SESSION[settings][step] != 2) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

if ($_SESSION[pageObj]->getField("type") == "page" && $_SESSION[auser] == $site_owner) {
	$leftlinks .= "<tr><td>";
	if ($_SESSION[settings][step] == 3) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($_SESSION[settings][step] != 3) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($_SESSION[settings][edit])?"edit":"add")."_page&step=3&link=1 onClick=\"submitForm()\">";
	$leftlinks .= "Editing Permissions";
	if ($_SESSION[settings][step] != 3) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

if ($_SESSION[pageObj]->getField("type") == "page") {
	$leftlinks .= "<tr><td>";
	if ($_SESSION[settings][step] == 4) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($_SESSION[settings][step] != 4) $leftlinks .= "<a href=$PHP_SELF?$sid&action=".(($_SESSION[settings][edit])?"edit":"add")."_page&step=4&link=1 onClick=\"submitForm()\">";
	$leftlinks .= "Display Options";
	if ($_SESSION[settings][step] != 4) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

$leftlinks .= "</table>_________________<br><a href=$PHP_SELF?$sid&action=add_page&cancel=1>Cancel</a>";

add_link(leftnav,'','',"$leftlinks");

if ($_SESSION[settings][step] == 1) {
	include("add_page_form_1_item.inc");
}
if ($_SESSION[settings][step] == 2) {
	include("add_page_form_2_activation.inc");
}
if ($_SESSION[settings][step] == 3) {
	include("add_page_form_3_permissions.inc");
}
if ($_SESSION[settings][step] == 4) {
	include("add_page_form_4_show.inc");
}

// End of New Code
//--------------------------------------------------------------------------------------------------------
