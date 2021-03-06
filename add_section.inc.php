<? /* $Id$ */

//--------------------------------------------------------------------------------------------------------
// Begining of new code

/* ----------------------------------------------------------------------- */
/*   redo all below permissions checks with new permissions scheme			*/


if ($_SESSION[settings] && is_object($_SESSION[sectionObj])) {
	// if we have already started editing...

	// --- Load any new variables into the array ---
	// Checkboxes need a "if ($_SESSION[settings][step] == 1 && !$link)" tag.
	// True/False radio buttons need a "if ($var != "")" tag to get the "0" values
	if ($_REQUEST[type]) $_SESSION[sectionObj]->setField("type",$_REQUEST[type]);
	if ($_SESSION[settings][step] == 1) $_SESSION[sectionObj]->setField("title",$_REQUEST[title]);
	
	
	// handle de/activate dates
	$_SESSION[sectionObj]->handleFormDates();	
	if ($_REQUEST[active] != "") $_SESSION[sectionObj]->setField("active",$_REQUEST[active]);
	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[sectionObj]->setField("hide_sidebar",$_REQUEST[hide_sidebar]);
	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[sectionObj]->setField("pageorder",$_REQUEST[pageorder]);
	
/* 	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[sectionObj]->setPermissions($_REQUEST[permissions]); */
/* 	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[sectionObj]->setField("locked",$_REQUEST[locked]); */
/* 	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[settings][copydownpermissions] = $_REQUEST[copydownpermissions]; */
	if ($_REQUEST[url])	$_SESSION[sectionObj]->setField("url",$_REQUEST['url']);
	
	//---- If switching type, take values to defaults ----
	if ($_REQUEST[typeswitch]) {
		$_SESSION[sectionObj]->init(1);				// will reset all vars and fetch from db if necessary - and form dates (hence the 1)
		
		if ($_SESSION[settings][add]) {
			$_SESSION[sectionObj]->setPermissions($thisSite->getPermissions());
		}
	}
}

if (!is_array($_SESSION[settings]) || !is_object($_SESSION[sectionObj])) {
	// create the settings array with default values. $_SESSION[settings] must be passed along with each link.
	// The array will be saved on clicking a save button.
	$_SESSION[settings] = array(
		"site" => $thisSite->name,
		"add" => 0,
		"edit" => 0,
		"step" => 1,
		"comingFrom" => $_REQUEST[comingFrom],
		"source_story" => $_REQUEST[story],
		"source_title" => $_REQUEST[title]
	);
	
	$_SESSION[sectionObj] =& new section($thisSite->name,0,$thisSite);
	
	if ($action == 'add_section') {
		$_SESSION[settings][add]=1;
		$_SESSION[settings][edit]=0;
	}	
	if ($action == 'edit_section') { 
		$_SESSION[settings][add]=0;
		$_SESSION[settings][edit]=1;
	}
	
	if ($_SESSION[settings][add]) {
		$_SESSION[sectionObj]->setPermissions($thisSite->getPermissions());
		$_SESSION[settings][pagetitle] = $thisSite->getField("title") . " > " . "Add Item";
	}
	
	if ($_SESSION[settings][edit]) {
		$_SESSION[sectionObj]->fetchFromDB($_REQUEST[edit_section]);
		$_SESSION[sectionObj]->buildPermissionsArray();
		$_SESSION[settings][pagetitle]= $thisSite->getField("title") . " > " . $_SESSION[sectionObj]->getField("title") . " > Edit Item";
	}
	$_SESSION[sectionObj]->initFormDates(); // initialize form date variables
}

/* $error = 0; */
// error checking
/* if ($_SESSION[settings][step] == 1) { */
/*  */
/* } */

if (!$error) {
	if ($_REQUEST[prevbutton]) $_SESSION[settings][step] -= 1;
	if ($_REQUEST[nextbutton]) $_SESSION[settings][step] += 1; 
}
if ($_REQUEST[step] != "") $_SESSION[settings][step] = $_REQUEST[step];
if ($_SESSION[settings][step] ==3 && $_SESSION[auser] != $site_owner) {
	if ($_REQUEST[prevbutton]) $_SESSION[settings][step] = 2;
	if ($_REQUEST[nextbutton]) $_SESSION[settings][step] = 4;
}

$pagetitle=$_SESSION[settings][pagetitle];

//-----for some reason siteheader and sitefooter keep being defined prior to this point on button click. I'm killing them here until their origin is found ----
$site = "";
$section = "";
$page = "";
$siteheader = "";
$sitefooter = "";

if ($_REQUEST[cancel]) {
	$comingFrom = $_SESSION[settings][comingFrom];
	$site = $_SESSION[sectionObj]->owning_site;
	if ($_SESSION[settings][edit] && $_SESSION[sectionObj]->getField("type")=='section') $section = $_SESSION[sectionObj]->id;
/* 	if (ini_get("register_globals")) { session_unregister("settings"); session_unregister("sectionObj"); } */
/* 	unset($_SESSION[sectionObj],$_SESSION[settings]); */
	if ($comingFrom) header("Location: index.php?$sid&action=$comingFrom&site=$site".(($section)?"&section=$section":""));
	else header("Location: index.php?$sid");
	
	exit;
}

if ($_REQUEST[save] && ($cfg['disable_edit_content'] != TRUE || $_SESSION['ltype'] == 'admin')) {
	//printpre ($_SESSION);
	//exit();
	// error checking
	if ($_SESSION[sectionObj]->getField("type")=='section' && (!$_SESSION[sectionObj]->getField("title") || $_SESSION[sectionObj]->getField("title")==''))
		error("You must enter a section title.");
	if ($_SESSION[sectionObj]->getField("type")=='link' && (!$_SESSION[sectionObj]->getField("url") || $_SESSION[sectionObj]->getField("url")=='' || $_SESSION[sectionObj]->getField("url")=='http://'))
		error("You must enter a URL.");
	
	if (!$error) { // save it to the database			
		
		/******************************************************************************
		 * Link section types: replace specific url with general url ($linkpath)
		 ******************************************************************************/

		if ($_SESSION[sectionObj]->getField("type")=='link') {
		
			$_SESSION[sectionObj]->setField("url", convertInteralLinksToTags($_SESSION[settings][site], $_SESSION[sectionObj]->getField("url")));
		
		}

		// add the new section id to the sites table
		if ($_SESSION[settings][add]) {
			$_SESSION[sectionObj]->setPermissions($thisSite->getPermissions());
			$_SESSION[sectionObj]->insertDB();
			log_entry("add_section","$_SESSION[auser] added section id ".$_SESSION[sectionObj]->id." in site ".$_SESSION[sectionObj]->owning_site,$_SESSION[sectionObj]->owning_site,$_SESSION[sectionObj]->id,"section");

//			convertAddNodeLinks($_SESSION[sectionObj]->owning_site, $_SESSION[sectionObj]->id, $_SESSION[settings][source_story], $_SESSION[settings][source_title], $page=0, $story=0);

		}
		if ($_SESSION[settings][edit]) {
			$_SESSION[sectionObj]->updateDB();
			log_entry("edit_section","$_SESSION[auser] edited section id ".$_SESSION[sectionObj]->id." in site ".$_SESSION[sectionObj]->owning_site,$_SESSION[sectionObj]->owning_site,$_SESSION[sectionObj]->id,"section");
		}
		
		
		header("Location: index.php?$sid&action=viewsite&site=".$_SESSION[sectionObj]->owning_site.(($_SESSION[sectionObj]->getField("type")=='section')?"&section=".$_SESSION[sectionObj]->id:""));
		exit;
		
	} else {
		$_SESSION[settings][step] = 1;
	}
}

// ------- print out the add form -------
// --- The Navigation Links for the sidebar ---
$leftlinks = "_________________<br /><table>";
$leftlinks .= "<tr><td>";
if ($_SESSION[settings][step] == 1) $leftlinks .= "&rArr; ";
$leftlinks .= "</td><td>";
if ($_SESSION[settings][step] != 1) $leftlinks .= "<a href='#' onclick=\"submitFormLink(1)\">";
$leftlinks .= "Item";
if ($_SESSION[settings][step] != 1) $leftlinks .= "</a>";
$leftlinks .= "</td></tr>";

if ($_SESSION[sectionObj]->getField("type") == "section" || $_SESSION[sectionObj]->getField("type") == "link") {
	$leftlinks .= "<tr><td>";
	if ($_SESSION[settings][step] == 2) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($_SESSION[settings][step] != 2) $leftlinks .= "<a href='#' onclick=\"submitFormLink(2)\">";
	$leftlinks .= "Activation";
	if ($_SESSION[settings][step] != 2) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

if ($_SESSION[sectionObj]->getField("type") == "section") {
	$leftlinks .= "<tr><td>";
	if ($_SESSION[settings][step] == 3) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($_SESSION[settings][step] != 3) $leftlinks .= "<a href='#' onclick=\"submitFormLink(3)\">";
	$leftlinks .= "Display Options";
	if ($_SESSION[settings][step] != 3) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}



$leftlinks .= "</table>_________________<br /><a href='$PHP_SELF?$sid&amp;action=add_section&amp;cancel=1'>Cancel</a>";

add_link(leftnav,'','',"$leftlinks");

if ($_SESSION[settings][step] == 1) {
	include("add_section_form_1_item.inc");
}
if ($_SESSION[settings][step] == 2) {
	include("add_section_form_2_activation.inc");
}
if ($_SESSION[settings][step] == 3) {
	include("add_section_form_3_show.inc");
}


// End of New Code
//--------------------------------------------------------------------------------------------------------
