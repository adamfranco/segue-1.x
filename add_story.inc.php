<? /* $Id$ */

// first check if we are allowed to edit this site at all
/* if ($auser != $site_owner && $auser != $settings[site_owner] && !is_editor($auser,$site) && !is_editor($auser,$settings[site])) { */
/* 	error("You're not even an editor for this site! Bad person!"); */
/* 	return; */
/* } */
/* if ($edit && !permission($auser,SECTION,EDIT,$section) && !permission($auser,SECTION,EDIT,$settings[section])) { */
/* 	error("You don't have permission to edit this page. Nice try."); */
/* 	return; */
/* } */
/* if ($add && !permission($auser,SECTION,ADD,$section)  && !permission($auser,SECTION,ADD,$settings[section])) { */
/* 	error("You don't have permission to add sections to this site. Nice try."); */
/* 	return; */
/* } */
/* if ($edit && !insite($site,$section,$page,$edit_story)) { */
/* 	error("Oh, you're good, but not good enough!"); */
/* 	return; */
/* } */

if ($_SESSION[settings] && is_object($_SESSION[storyObj])) {
	// if we have already started editing...

	// --- Load any new variables into the array ---
	// Checkboxes need a "if ($_SESSION[settings][step] == 1 && !$link)" tag.
	// True/False radio buttons need a "if ($var != "")" tag to get the "0" values
	if ($_REQUEST[type]) $_SESSION[storyObj]->setField("type",$_REQUEST[type]);
	if ($_SESSION[settings][step] == 1 && !$_REQUEST[link]) $_SESSION[storyObj]->setField("title",$_REQUEST[title]);
	$_SESSION[storyObj]->handleFormDates();
	if ($_REQUEST[active] != "") $_SESSION[storyObj]->setField("active",$_REQUEST[active]);
	if ($_SESSION[settings][step] == 4 && !$_REQUEST[link]) $_SESSION[storyObj]->setPermissions($_REQUEST[permissions]);
	if ($_SESSION[settings][step] == 4 && !$_REQUEST[link]) $_SESSION[storyObj]->setField("locked",$_REQUEST[locked]);
	if ($_REQUEST[url]) $_SESSION[storyObj]->setField("url",$_REQUEST[url]);
	if ($_REQUEST[texttype]) $_SESSION[storyObj]->setField("texttype",$_REQUEST[texttype]);
	if ($_SESSION[settings][step] == 4 && !$_REQUEST[link]) $_SESSION[storyObj]->setField("discuss",$_REQUEST[discuss]);
	if ($_SESSION[settings][step] == 4 && !$_REQUEST[link]) $_SESSION[storyObj]->setField("discussemail",$_REQUEST[discussemail]);
	if ($_SESSION[settings][step] == 4 && !$_REQUEST[link]) $_SESSION[storyObj]->setField("discussdisplay",$_REQUEST[discussdisplay]);
	if ($_SESSION[settings][step] == 4 && !$_REQUEST[link]) $_SESSION[storyObj]->setField("discussauthor",$_REQUEST[discussauthor]);
	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[storyObj]->setField("category",$_REQUEST[category]);
	if ($_REQUEST[newcategory]) {
		$_SESSION[storyObj]->setField("category",$_REQUEST[newcategory]);
		$_SESSION[settings][categories][] = $_REQUEST[newcategory];
		sort($_SESSION[settings][categories]);
	}
	if ($_SESSION[settings][step] == 1 && !$_REQUEST[link]) $_SESSION[storyObj]->setField("shorttext",$_REQUEST[shorttext]);
	if ($_SESSION[settings][step] == 2 && !$_REQUEST[link]) $_SESSION[storyObj]->setField("longertext",$_REQUEST[longertext]);
	if ($_SESSION[settings][step] == 1 && !$_REQUEST[link]) $_SESSION[settings][libraryfilename] = $_REQUEST[libraryfilename];
	if ($_SESSION[settings][step] == 1 && !$_REQUEST[link]) $_SESSION[settings][libraryfileid] = $_REQUEST[libraryfileid];

	//---- If switching type, take values to defaults ----
	if ($_REQUEST[typeswitch]) {
		$_SESSION[settings][ediscussion] = $thisPage->getField("ediscussion");
		$_SESSION[settings][libraryfilename] = "";
		$_SESSION[settings][libraryfileid] = "";
		$_SESSION[storyObj]->init(1);
		
		if ($_SESSION[settings][add]) {
			$_SESSION[storyObj]->setPermissions($thisPage->getPermissions());
		}
	}
}

if (!$_SESSION[settings] || !is_object($_SESSION[storyObj])/*  && !$error */) {
	//print "Making a new settings array<br>";
	// create the settings array with default values. $_SESSION[settings] must be passed along with each link.
	// The array will be saved on clicking a save button.
	$_SESSION[settings] = array(
		"site_owner" => $site_owner,
		"add" => 0,
		"edit" => 0,
		"step" => 1,
		"site" => $_REQUEST[site],
		"section" => $_REQUEST[section],
		"page" => $_REQUEST[page],
		"comingFrom" => $_REQUEST[comingFrom]
	);
	
	$_SESSION[storyObj] =& new story($thisSite->name,$thisSection->id,$thisPage->id, 0,&$thisPage);
	
	$_SESSION[settings][pagetitle]=$thisSite->getField("title") . " > " . $thisSection->getField("title") . " > " . $thisPage->getField("title") . " > ";
	
	if ($action == 'add_story') {
		$_SESSION[settings][add]=1;
		$_SESSION[settings][edit]=0;
		$_SESSION[settings][pagetitle] .= " Add Item";
	}	
	if ($action == 'edit_story') { 
		$_SESSION[settings][add]=0;
		$_SESSION[settings][edit]=1;
		$_SESSION[settings][pagetitle] .= " Edit Item";
	}
	
	if ($_SESSION[settings][add]) {
		//print "ooga";
		$_SESSION[storyObj]->setPermissions($thisPage->getPermissions());
	}
	
	if ($_SESSION[settings][edit]) {
		$_SESSION[storyObj]->fetchFromDB($_REQUEST[edit_story]);
//		$_SESSION[storyObj]->getPermissions();
/* 		$_SESSION[storyObj]->fetchDown(1); */
		$_SESSION[storyObj]->buildPermissionsArray();
		
		if ($_SESSION[storyObj]->getField("type") == "image" || $_SESSION[storyObj]->getField("type") == "file") {
			$_SESSION[settings][libraryfileid] = $_SESSION[storyObj]->getField("longertext");
			$_SESSION[settings][libraryfilename] = db_get_value("media","media_tag","media_id=".$_SESSION[settings][libraryfileid]);
		}
	}
	
	$_SESSION[settings][categories]=array_unique($thisSite->getAllValues("story","category"));
	sort($_SESSION[settings][categories]);

	
	$_SESSION[settings][ediscussion] = $thisPage->getField("ediscussion");
}

if ($_REQUEST[prevbutton]) $_SESSION[settings][step] = $_SESSION[settings][step] - 1;
if ($_REQUEST[nextbutton]) $_SESSION[settings][step] = $_SESSION[settings][step] + 1; 
if ($_REQUEST[step] != "") $_SESSION[settings][step] = $_REQUEST[step];
if ($_SESSION[settings][step] ==2 && $_SESSION[storyObj]->getField("type") != 'story') {
	if ($_REQUEST[prevbutton]) $_SESSION[settings][step] = 1;
	if ($_REQUEST[nextbutton]) $_SESSION[settings][step] = 3;
}
/* if ($_SESSION[settings][step] ==4 && $_SESSION[auser] != $site_owner) { */
/* 	if ($_REQUEST[prevbutton]) $_SESSION[settings][step] = 3; */
/* 	if ($_REQUEST[nextbutton]) $_SESSION[settings][step] = 5; */
/* } */

$pagetitle=$_SESSION[settings][pagetitle];

//-----for some reason siteheader and sitefooter keep being define prior to this point on button click. I'm killing them here until their origen is found ----
$site = "";
$section = "";
$page = "";
$siteheader = "";
$sitefooter = "";

if ($_REQUEST[cancel]) {
	$comingFrom = $_SESSION[settings][comingFrom];
	print "cancelling...";
	if ($comingFrom) header("Location: index.php?$sid&action=$comingFrom&site=".$storyObj->owning_site."&section=".$storyObj->owning_section."&page=".$storyObj->owning_page);
	else header("Location: index.php?$sid&action=viewsite&site=".$storyObj->owning_site."&section=".$storyObj->owning_section."&page=".$storyObj->owning_page);
}

if ($_REQUEST[save]) {
//	$error = 0;
	// error checking
	if ($_SESSION[storyObj]->getField("type")=='story' && (!$_SESSION[storyObj]->getField("shorttext") || trim($_SESSION[storyObj]->getField("shorttext"))==''))
		error ("You must enter some story content.");
	if ($_SESSION[storyObj]->getField("type")=='link' && (!$_SESSION[storyObj]->getField("url") || $_SESSION[storyObj]->getField("url")=='' || $_SESSION[storyObj]->getField("url")=='http://'))
		error("You must enter a URL.");
	if ($_SESSION[storyObj]->getField("type")=='file' && (!$_SESSION[settings][libraryfileid] || $_SESSION[settings][libraryfileid] == ''))
		error("You must select a file to upload.");
	if ($_SESSION[storyObj]->getField("type")=='file' && (!$_SESSION[storyObj]->getField("title") || $_SESSION[storyObj]->getField("title") == ''))
		error("You must enter a title.");
	if ($_SESSION[storyObj]->getField("type")=='image' && (!$_SESSION[settings][libraryfileid] || $_SESSION[settings][libraryfileid] == ''))
		error("You must select an image to upload.");
		
	if (!$error) { // save it to the database
		
		// put image id into the longer text field
		if ($_SESSION[storyObj]->getField("type") == "image" || $_SESSION[storyObj]->getField("type") == "file") {
			$_SESSION[storyObj]->setField("longertext",$_SESSION[settings][libraryfileid]);
		}
		
		// check make sure the owner is the current user if they are changing permissions
/* 		if ($site_owner != $_SESSION[auser]) { */
/* 			if ($_SESSION[settings][edit]) $_SESSION[storyObj]->buildPermissionsArray(); */
/* 			else $_SESSION[storyObj]->setPermissions($thisPage->getPermissions()); */
/* 		} */

		if ($_SESSION[settings][add]) {
			$_SESSION[storyObj]->insertDB();
			log_entry("add_story","$_SESSION[auser] added content id ".$_SESSION[storyObj]->id." in site ".$_SESSION[storyObj]->owning_site.", section ".$_SESSION[storyObj]->owning_section.", page ".$_SESSION[storyObj]->owning_page,$_SESSION[storyObj]->owning_site,$_SESSION[storyObj]->id,"story");
		}
		if ($_SESSION[settings][edit]) {
			$_SESSION[storyObj]->updateDB();
			log_entry("edit_story","$_SESSION[auser] edited content id ".$_SESSION[storyObj]->id." in site ".$_SESSION[storyObj]->owning_site.", section ".$_SESSION[storyObj]->owning_section.", page ".$_SESSION[storyObj]->owning_page,$_SESSION[storyObj]->owning_site,$_SESSION[storyObj]->id,"story");
		}
		
/* 			log_entry("add_story",$_SESSION[settings][site],$_SESSION[settings][section],$page,"$auser added content id $newid to page $_SESSION[settings][page] in section $_SESSION[settings][section] of site $_SESSION[settings][site]"); */
/* 		} */
/* 		if ($_SESSION[settings][edit]) { */
/* 			log_entry("edit_page",$_SESSION[settings][site],$_SESSION[settings][section],$_SESSION[settings][page],"$auser edited content id $_SESSION[settings][story] in page $_SESSION[settings][page] of section $_SESSION[settings][section] of site $_SESSION[settings][site]"); */
/* 			$newid=$_SESSION[settings][page]; */
/* 		} */

		header("Location: index.php?$sid&action=viewsite&site=".$thisSite->name."&section=".$thisSection->id."&page=".$thisPage->id);
		
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
if ($_SESSION[settings][step] != 1) $leftlinks .= "<a href='#' onClick=\"submitFormLink(1)\">";
$leftlinks .= "Content";
if ($_SESSION[settings][step] != 1) $leftlinks .= "</a>";
$leftlinks .= "</td></tr>";

if ($_SESSION[storyObj]->getField("type") == "story") {
	$leftlinks .= "<tr><td>";
	if ($_SESSION[settings][step] == 2) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($_SESSION[settings][step] != 2) $leftlinks .= "<a href='#' onClick=\"submitFormLink(2)\">";
	$leftlinks .= "Extended Content";
	if ($_SESSION[settings][step] != 2) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

if (1) {
	$leftlinks .= "<tr><td>";
	if ($_SESSION[settings][step] == 3) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($_SESSION[settings][step] != 3) $leftlinks .= "<a href='#' onClick=\"submitFormLink(3)\">";
	$leftlinks .= "Activation & Category";
	if ($_SESSION[settings][step] != 3) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

if (true) {
	$leftlinks .= "<tr><td>";
	if ($_SESSION[settings][step] == 4) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($_SESSION[settings][step] != 4) $leftlinks .= "<a href='#' onClick=\"submitFormLink(4)\">";
	$leftlinks .= "Discuss/Assess";
	if ($_SESSION[settings][step] != 4) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

$leftlinks .= "</table>_________________<br><a href=$PHP_SELF?$sid&action=add_story&cancel=1>Cancel</a>";

add_link(leftnav,'','',"$leftlinks");

if ($_SESSION[settings][step] == 1) {
	include("add_story_form_1_item.inc");
}
if ($_SESSION[settings][step] == 2) {
	include("add_story_form_2_fulltext.inc");
}
if ($_SESSION[settings][step] == 3) {
	include("add_story_form_3_activation.inc");
}
if ($_SESSION[settings][step] == 4) {
	include("add_story_form_5_discussion.inc");
}

// ---  variables for debugging ---
/* $vars = $_SESSION[settings]; */
/* ksort($vars); */
/* $variables .= "<br>----------------------<br>"; */
/* foreach ($vars as $n => $v) { */
/* 	$variables .= "$n = $v <br>";	 */
/* } */
/* if ($_SESSION[settings][file]) foreach ($_SESSION[settings][file] as $n => $v) $variables .= "<br>$n - $v"; */
//add_link(leftnav,'','',"$variables");
//printc("$variables");
//------------------------------------
/* print "<pre>"; */
/* print_r($_SESSION[storyObj]->data); */
/* print "</pre>"; */
