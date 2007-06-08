<? /* $Id$ */



//printpre($_REQUEST);

/******************************************************************************
 * Adding a node saving values from various steps
 ******************************************************************************/
//	printpre("settings:");
//	printpre($_SESSION[settings]);
//	printpre("request:");
//	printpre($_REQUEST);
	
if (is_array($_SESSION[settings])) {
	if ($_REQUEST[type]) $_SESSION[settings][type] = $_REQUEST[type];	
	if ($_REQUEST[location]) $_SESSION[settings][location] = $_REQUEST[location];
}

/******************************************************************************
 * Adding a Node initiation
 ******************************************************************************/
//$_SESSION[new_node] = array();
if (!is_array($_SESSION[settings]) ) {
//if (!is_array($_SESSION[new_node]) || !isset($_SESSION[new_node])) {
	// create the settings array with default values. $_SESSION[settings] must be passed along with each link.
	// The array will be saved on clicking a save button.
	//printpre("new_node array inactive");
	$_SESSION[settings] = array(
		"site_owner" => $site_owner,
		"add" => 0,
		"edit" => 0,
		"step" => 1,
		"site" => $thisSite->name,
		"section" => $thisSection->id,
		"page" => $_REQUEST[page],
		"story" => $_REQUEST[story],
		"comingFrom" => "viewsite",
		"type" => "page",
		"location" => $_REQUEST[section],
		"title" => $_REQUEST[link_title]
	);

	
	$_SESSION[settings][pagetitle]=$thisSite->getField("title") . " > " . $thisSection->getField("title") . " > ";

	// init to step 1	
	if ($action == 'add_node') {
		$_SESSION[settings][add]=1;
		$_SESSION[settings][edit]=0;
		$_SESSION[settings][pagetitle] .= " Add content for this link";
	}	
}

/******************************************************************************
 * Increment steps based on prev and next buttons
 ******************************************************************************/

if ($_REQUEST[prevbutton]) $_SESSION[settings][step] -= 1;
if ($_REQUEST[nextbutton]) $_SESSION[settings][step] += 1; 

if ($_REQUEST[step] != "") $_SESSION[settings][step] = $_REQUEST[step];
if ($_SESSION[settings][step] == 3 && $_SESSION[auser] != $site_owner) {
	if ($_REQUEST[prevbutton]) $_SESSION[settings][step] = 2;
	if ($_REQUEST[nextbutton]) $_SESSION[settings][step] = 4;
}

$pagetitle=$_SESSION[settings][pagetitle];

/******************************************************************************
 * Cancel options
 ******************************************************************************/

//-----for some reason siteheader and sitefooter keep being define prior to this point on button click. I'm killing them here until their origen is found ----
$site = "";
$section = "";
$page = "";
$siteheader = "";
$sitefooter = "";

if ($_REQUEST[cancel]) {
	$comingFrom = $_SESSION[settings][comingFrom];
	$site = $thisSite->name;
//	session_unregister("settings"); // handled by index.php
	if ($comingFrom) header("Location: index.php?$sid&action=$comingFrom&site=$site");
	else header("Location: index.php?$sid");
	
	exit;
}

if ($_REQUEST[cancel]) {
	$comingFrom = $_SESSION[settings][comingFrom];

	print "cancelling...";
	if ($comingFrom) header("Location: index.php?$sid&action=$comingFrom&site=".$_SESSION[settings][site]."&section=".$_SESSION[settings][section]."&page=".$_SESSION[settings][page]);
	else header("Location: index.php?$sid&action=site&site=".$pageObj->owning_site."&section=".$pageObj->owning_section."&page=".$pageObj->id);
	
	exit;
}

/******************************************************************************
 * Save values from session to object
 ******************************************************************************/

if ($_REQUEST[save]) {
/* 	$error = 0; */
	// error checking
//	if ($_SESSION[pageObj]->getField("type")=='page' && (!$_SESSION[pageObj]->getField("title") || $_SESSION[pageObj]->getField("title")=='')) {
//		error("You must enter a title.");
//	
//	} else if ($_SESSION[pageObj]->getField("type")=='page') {
//		$page_titles = getPageTitles ($pageObj->owning_section);
//		
//		foreach ($page_titles as $page_title => $page_id) {			
//			if (strtolower($_SESSION[pageObj]->getField("title") == strtolower($page_title)) && ($pageObj->id != $page_id)) {
//				error("This section already has a title with this name.  Please choose another title");
//			}
//		}	
//	} else if ($_SESSION[pageObj]->getField("type")=='link' && (!$_SESSION[pageObj]->getField("url") || $_SESSION[pageObj]->getField("url")=='' || $_SESSION[pageObj]->getField("url")=='http://')) {
//		error("You must enter a URL.");
//	} else if ($_SESSION[pageObj]->getField("type")=='rss' && (!$_SESSION[pageObj]->getField("url") || $_SESSION[pageObj]->getField("url")=='' || $_SESSION[pageObj]->getField("url")=='http://')) {
//		error("You must enter the URL of your RSS feed.");
//	}

		
	if (!$error) { // save it to the database
		

		/******************************************************************************
		 * Save: forwords to the appropriate add UI (either poge, story or section
		 ******************************************************************************/
		$site = $thisSite->name;		
		$story = $_SESSION[settings][story];
		$title = $_SESSION[settings][title];
		
		
		if ($_REQUEST[type] == "page") { 
			$section = $_SESSION[settings][location];
			unset($_SESSION[settings],$_SESSION[siteObj],$_SESSION[sectionObj],$_SESSION[pageObj],$_SESSION[storyObj]);
			header("Location: index.php?&action=add_page&site=".$site."&section=".$section."&story=".$story."&comingFrom=viewsite&title=".$title);

			//log_entry("edit_page","$_SESSION[auser] edited page id ".$_SESSION[pageObj]->id." in site ".$_SESSION[pageObj]->owning_site.", section ".$_SESSION[pageObj]->owning_section,$_SESSION[pageObj]->owning_site,$_SESSION[pageObj]->id,"page");
		} else if ($_REQUEST[type] == "section") {
			unset($_SESSION[settings],$_SESSION[siteObj],$_SESSION[sectionObj],$_SESSION[pageObj],$_SESSION[storyObj]);
			header("Location: index.php?&action=add_section&site=".$site."&story=".$story."&comingFrom=viewsite&title=".$title);

			//log_entry("edit_page","$_SESSION[auser] edited page id ".$_SESSION[pageObj]->id." in site ".$_SESSION[pageObj]->owning_site.", section ".$_SESSION[pageObj]->owning_section,$_SESSION[pageObj]->owning_site,$_SESSION[pageObj]->id,"page");
		} else if ($_REQUEST[type] == "content") {
			$page = $_SESSION[settings][location];
			unset($_SESSION[settings],$_SESSION[siteObj],$_SESSION[sectionObj],$_SESSION[pageObj],$_SESSION[storyObj]);				
			header("Location: index.php?$sid&action=add_story&site=".$thisSite->name."&section=".$thisSection->id."&page=".$page."&story=".$_SESSION[settings][story]."&comingFrom=viewsite&title=".$title);
			
			//log_entry("edit_page","$_SESSION[auser] edited page id ".$_SESSION[pageObj]->id." in site ".$_SESSION[pageObj]->owning_site.", section ".$_SESSION[pageObj]->owning_section,$_SESSION[pageObj]->owning_site,$_SESSION[pageObj]->id,"page");		
		}
		
		// do the recursive update of active flag and such... .... ugh
//		$_SESSION[settings][permissions] = decode_array($_SESSION[settings][permissions]);
//		if ($_SESSION[settings][edit] && ($_SESSION[settings][recursiveenable] || count($_SESSION[settings][copydownpermissions]))) {
//			// recursively change the $active or $permissions field for all parts of the site			
//			$stories = decode_array(db_get_value("pages","stories","id='".addslashes($_SESSION[settings][page])."'"));
//			foreach ($stories as $s) {
//				$sa = db_get_line("stories","id='".addslashes($s)."'");
//				$chg = array();
//				if ($recursiveenable && permission($auser,PAGE,EDIT,$p)) $chg[] = "active='".addslashes($_SESSION[settings][active])."'";
//				if (count($_SESSION[settings][copydownpermissions]) && $auser == $_SESSION[settings][site_owner]) {
//					$sp = decode_array($sa['permissions']);
//					foreach ($_SESSION[settings][copydownpermissions] as $e) $sp[$e] = $_SESSION[settings][permissions][$e];
//					$sp = encode_array($sp);
//					$chg[] = "permissions='".addslashes($sp)."'";
//				}
//				$query = "update stories set " . implode(",",$chg) . " where id='".addslashes($s)."'";
//				print "--> ".$query . "<br />";
//				if (count($chg)) db_query($query);
//			}			
//		}
//		
//		header("Location: index.php?$sid&action=viewsite&site=".$thisSite->name."&section=".$thisSection->id.(($_SESSION[pageObj]->getField("type")=='page')?"&page=".$_SESSION[pageObj]->id:""));
		exit;
		
	} else {
		$_SESSION[settings][step] = 1;
	}
}

// ------- print out the add form -------
// --- The Navigation Links for the sidebar ---
$leftlinks = "<table>";
$leftlinks .= "<tr><td>";
//if ($_SESSION[settings][step] == 1) $leftlinks .= "&rArr; ";
//$leftlinks .= "</td><td>";
//if ($_SESSION[settings][step] != 1) $leftlinks .= "<a href='#' onclick=\"submitFormLink(1)\">";
//$leftlinks .= "Item";
//if ($_SESSION[settings][step] != 1) $leftlinks .= "</a>";
//$leftlinks .= "</td></tr>";
//
//if ($_SESSION[pageObj]->getField("type") == "page" || $_SESSION[pageObj]->getField("type") == "link") {
//	$leftlinks .= "<tr><td>";
//	if ($_SESSION[settings][step] == 2) $leftlinks .= "&rArr; ";
//	$leftlinks .= "</td><td>";
//	if ($_SESSION[settings][step] != 2) $leftlinks .= "<a href='#' onclick=\"submitFormLink(2)\">";
//	$leftlinks .= "Activation";
//	if ($_SESSION[settings][step] != 2) $leftlinks .= "</a>";
//	$leftlinks .= "</td></tr>";
//}
//
//if ($_SESSION[pageObj]->getField("type") == "page") {
//	$leftlinks .= "<tr><td>";
//	if ($_SESSION[settings][step] == 3) $leftlinks .= "&rArr; ";
//	$leftlinks .= "</td><td>";
//	if ($_SESSION[settings][step] != 3) $leftlinks .= "<a href='#' onclick=\"submitFormLink(3)\">";
//	$leftlinks .= "Display Options";
//	if ($_SESSION[settings][step] != 3) $leftlinks .= "</a>";
//	$leftlinks .= "</td></tr>";
//}
$leftlinks .= "</td></tr>";
$leftlinks .= "</table>";
//$leftlinks .= "<a href='$PHP_SELF?$sid&amp;action=add_page&amp;cancel=1'>Cancel</a>";

add_link(leftnav,'','',"$leftlinks");

// go to step specified

if ($_SESSION[settings][step] == 1) {
	include("add_node_form_1_item.inc");
}
if ($_SESSION[settings][step] == 2) {
	include("add_page_form_2_activation.inc");
}
if ($_SESSION[settings][step] == 3) {
	include("add_page_form_4_show.inc");
}

// End of New Code
//--------------------------------------------------------------------------------------------------------
