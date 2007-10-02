<? /* $Id$ */

//--------------------------------------------------------------------------------------------------------
// Begining of new code

// ---  variables for debugging ---
//foreach ($_SESSION[settings] as $n => $v) {
//	$variables .= "$n = $v <br />";	
//}
//add_link(leftnav,'','',"$variables");
//print $variables."br>site owner = $site_owner <br />typeswitch = $typeswitch <br />";
//print "siteheader = '$siteheader' <br />sitefooter = '$sitefooter' <br />";
//print "site = $site<br />section = $section<br />page=$page<br />";
//------------------------------------

// first check if we are allowed to edit this site at all
/* if ($_SESSION['auser'] != $site_owner && $_SESSION['auser'] != $_SESSION[settings][site_owner] && !is_editor($_SESSION['auser'],$site) && !is_editor($_SESSION['auser'],$_SESSION[settings][site])) { */
/* 	error("You're not even an editor for this site! Bad person!"); */
/* 	return; */
/* } */
/* if ($edit && !permission($_SESSION['auser'],SECTION,EDIT,$section) && !permission($_SESSION['auser'],SECTION,EDIT,$_SESSION[settings][section])) { */
/* 	error("You don't have permission to edit this page. Nice try."); */
/* 	return; */
/* } */
/* if ($add && !permission($_SESSION['auser'],SECTION,ADD,$section)  && !permission($_SESSION['auser'],SECTION,ADD,$_SESSION[settings][section])) { */
/* 	error("You don't have permission to add sections to this site. Nice try."); */
/* 	return; */
/* } */
/* if ($edit && !insite($site,$section,$edit_page)) { */
/* 	error("Oh, you're good, but not good enough!"); */
/* 	return; */
/* } */
//printpre($_SESSION[settings]);
//printpre($_REQUEST);


if (is_array($_SESSION[settings]) && is_object($_SESSION[pageObj])) {
	// if we have already started editing...

	// --- Load any new variables into the array ---
	// Checkboxes need a "if ($_SESSION[settings][step] == 1 && !$link)" tag.
	// True/False radio buttons need a "if ($var != "")" tag to get the "0" values
	
	
	if ($_REQUEST[type]) $_SESSION[pageObj]->setField("type",$_REQUEST[type]);	
	$_SESSION[pageObj]->handleFormDates();	// handle de/activate dates
	if ($_REQUEST[active] != "") $_SESSION[pageObj]->setField("active",$_REQUEST[active]);
	if ($_SESSION[settings][step] == 1 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("title",$_REQUEST[title]);
	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("ediscussion",$_REQUEST[ediscussion]);
/* 	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("locked",$_REQUEST[locked]); */
	if ($_REQUEST[copydownpermissions] != "") $_SESSION[settings][copydownpermissions] = $_REQUEST[copydownpermissions];
	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("showcreator",$_REQUEST[showcreator]);
	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("showeditor",$_REQUEST[showeditor]);
	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("showdate",$_REQUEST[showdate]);
	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("showversions",$_REQUEST[showversions]);
	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("storyorder",$_REQUEST[storyorder]);
	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[pageObj]->setField("showhr",$_REQUEST[showhr]);
//	if ($_SESSION[settings][step] == 3 && !$_REQUEST[link]) $_SESSION[pageObj]->setPermissions($_REQUEST[permissions]);

	if ($_REQUEST[archiveby]) $_SESSION[pageObj]->setField("archiveby",$_REQUEST[archiveby]);
	if ($_REQUEST[url] != "http://") $_SESSION[pageObj]->setField("url",$_REQUEST[url]);
	if ($_REQUEST[text]) $_SESSION[pageObj]->setField("text",$_REQUEST[text]);
	if ($_REQUEST[location]) $_SESSION[pageObj]->setField("location",$_REQUEST[location]);
	if ($_REQUEST[title]) $_SESSION[pageObj]->setField("title",$_REQUEST[title]);

	
	//---- If switching type, take values to defaults ----
	if ($_REQUEST[typeswitch]) {
		$_SESSION[pageObj]->init(1);		// init values... force form date variables
		if ($thisSite->getField("type") == 'publication') $_SESSION[pageObj]->setField("url","");	
		if ($_SESSION[settings][add]) {
			//print "<p> deleting settings[permissions]....</p>";
			//$_SESSION[settings][permissions] = "";
			$_SESSION[pageObj]->setPermissions($thisSection->getPermissions());
		}
	}
		if ($_REQUEST[editor]) {
		$_SESSION['html_editor'] = $_REQUEST[editor];
	}
}

if ((!is_array($_SESSION[settings]) || !is_object($_SESSION[pageObj]))/*  && !$error */) {
	// create the settings array with default values. $_SESSION[settings] must be passed along with each link.
	// The array will be saved on clicking a save button.
	if ($_SESSION[settings][location]) {
		$this_section = $_SESSION[settings][location];
	} else {
		$this_section = $thisSection->id;
	}
	
	$_SESSION[settings] = array(
		"site_owner" => $site_owner,
		"add" => 0,
		"edit" => 0,
		"step" => 1,
		"site" => $_REQUEST[site],
		"section" => $_REQUEST[section],
		"comingFrom" => $_REQUEST[comingFrom],
		"source_story" => $_REQUEST[story],
		"source_title" => $_REQUEST[title]
	);

//	printpre($_SESSION[settings]);
	
	$_SESSION[pageObj] =& new page($thisSite->name,$thisSection->id,0,$thisSection);
	
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
		$_SESSION[pageObj]->setPermissions($thisSection->getPermissions());
	}
	
	if ($_SESSION[settings][edit]) {
		$_SESSION[pageObj]->fetchFromDB($_REQUEST[edit_page]);
		$_SESSION[pageObj]->buildPermissionsArray();
	}
	if ($thisSite->getField("type") == 'publication') $_SESSION[pageObj]->setField("url","");
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
	$comingFrom = $_SESSION[settings][comingFrom];
	print "cancelling...";
	if ($comingFrom) {	
		$headerText = "Location: index.php?$sid&action=$comingFrom&site=".$pageObj->owning_site."&section=".$pageObj->owning_section."&page=".$_SESSION[pageObj]->id;
	}
	
	unset($_SESSION[pageObj], $_SESSION[settings]);
	header($headerText);
	exit;
}

if ($_REQUEST[save]) {
/* 	$error = 0; */
	// error checking
	if ($_SESSION[pageObj]->getField("type")=='page' && (!$_SESSION[pageObj]->getField("title") || $_SESSION[pageObj]->getField("title")=='')) {
		error("You must enter a title.");
	
	} else if ($_SESSION[pageObj]->getField("type")=='page') {
		$page_titles = getPageTitles ($pageObj->owning_section);
		
		foreach ($page_titles as $page_title => $page_id) {			
			if (strtolower($_SESSION[pageObj]->getField("title") == strtolower($page_title)) && ($pageObj->id != $page_id)) {
				error("This section already has a title with this name.  Please choose another title");
			}
		}	
	} else if ($_SESSION[pageObj]->getField("type")=='link' && (!$_SESSION[pageObj]->getField("url") || $_SESSION[pageObj]->getField("url")=='' || $_SESSION[pageObj]->getField("url")=='http://')) {
		error("You must enter a URL.");
	} else if ($_SESSION[pageObj]->getField("type")=='rss' && (!$_SESSION[pageObj]->getField("url") || $_SESSION[pageObj]->getField("url")=='' || $_SESSION[pageObj]->getField("url")=='http://')) {
		error("You must enter the URL of your RSS feed.");
	}

		
	if (!$error) { // save it to the database
		
/* 		// check make sure the owner is the current user if they are changing permissions */
/* 		if ($site_owner != $_SESSION[auser]) */

		/******************************************************************************
		 * Set the title fields for Tags and Participants
		 ******************************************************************************/

		if ($_SESSION[pageObj]->getField("type")=='tags') {
			$_SESSION[pageObj]->setField("title","Categories");
		}
		if ($_SESSION[pageObj]->getField("type")=='participants') {
			$_SESSION[pageObj]->setField("title","Participants");
		}

		/******************************************************************************
		 * Link and content page types: replace specific url with general url ($linkpath)
		 ******************************************************************************/

		if ($_SESSION[pageObj]->getField("type")=='link') {
		
			$url = convertInteralLinksToTags($_SESSION[settings][site], $url);
			
			// save general internal_linkpath to object	
			$_SESSION[pageObj]->setField("url",$url);
		
		} else if ($_SESSION[pageObj]->getField("type")=='content') {
			$page_title = $_SESSION[pageObj]->getField("title");
			$content = $_SESSION[pageObj]->getField("text");
//			$content = convertWikiMarkupToLinks($_SESSION[settings][site],$_SESSION[settings][section],$_SESSION[pageObj]->id, $page_title, $content);
//			$content = recordInternalLinks ($_SESSION[settings][site],$_SESSION[settings][section],$_SESSION[pageObj]->id, $page_title, $content);	
			
			$content = convertInteralLinksToTags($_SESSION[settings][site], $content);

			// save general internal_linkpath to object	
			$_SESSION[pageObj]->setField("text",$content);
			
		} else if ($_SESSION[pageObj]->getField("type")=='rss') {
			$url = convertInteralLinksToTags($_SESSION[settings][site], $url);

			$_SESSION[pageObj]->setField("url",$url);
		}

		/******************************************************************************
		 * Save: calls insertDB and updateDB functions
		 ******************************************************************************/
		
		if ($_SESSION[settings][edit]) { 
			$_SESSION[pageObj]->updateDB();
			log_entry("edit_page","$_SESSION[auser] edited page id ".$_SESSION[pageObj]->id." in site ".$_SESSION[pageObj]->owning_site.", section ".$_SESSION[pageObj]->owning_section,$_SESSION[pageObj]->owning_site,$_SESSION[pageObj]->id,"page");
 		//	$query = "update pages set editedby='$_SESSION['auser']',"; $where = " where id=$_SESSION[settings][page]";
 	//		printpre($_REQUEST[location]);
 		//	exit();
		}
		if ($_SESSION[settings][add]) {
			// automatically inherit permissions from above;
			$_SESSION[pageObj]->setPermissions($thisSection->getPermissions());
			$_SESSION[pageObj]->insertDB();
			log_entry("add_page","$_SESSION[auser] added page id ".$_SESSION[pageObj]->id." in site ".$_SESSION[pageObj]->owning_site.", section ".$_SESSION[pageObj]->owning_section,$_SESSION[pageObj]->owning_site,$_SESSION[pageObj]->id,"page");
			
//			convertAddNodeLinks($_SESSION[pageObj]->owning_site, $_SESSION[pageObj]->owning_section, $_SESSION[settings][source_story], $_SESSION[settings][source_title], $_SESSION[pageObj]->id, $story=0);


		}
		
		// do the recursive update of active flag and such... .... ugh
		$_SESSION[settings][permissions] = decode_array($_SESSION[settings][permissions]);
		if ($_SESSION[settings][edit] && ($_SESSION[settings][recursiveenable] || count($_SESSION[settings][copydownpermissions]))) {
			// recursively change the $active or $permissions field for all parts of the site			
			$stories = decode_array(db_get_value("pages","stories","id='".addslashes($_SESSION[settings][page])."'"));
			foreach ($stories as $s) {
				$sa = db_get_line("stories","id='".addslashes($s)."'");
				$chg = array();
				if ($recursiveenable && permission($_SESSION['auser'],PAGE,EDIT,$p)) $chg[] = "active='".addslashes($_SESSION[settings][active])."'";
				if (count($_SESSION[settings][copydownpermissions]) && $_SESSION['auser'] == $_SESSION[settings][site_owner]) {
					$sp = decode_array($sa['permissions']);
					foreach ($_SESSION[settings][copydownpermissions] as $e) $sp[$e] = $_SESSION[settings][permissions][$e];
					$sp = encode_array($sp);
					$chg[] = "permissions='".addslashes($sp)."'";
				}
				$query = "update stories set " . implode(",",$chg) . " where id='".addslashes($s)."'";
				print "--> ".$query . "<br />";
				if (count($chg)) db_query($query);
			}			
		}
		
		header("Location: index.php?$sid&action=viewsite&site=".$thisSite->name."&section=".$thisSection->id.(($_SESSION[pageObj]->getField("type")=='page')?"&page=".$_SESSION[pageObj]->id:""));
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

if ($_SESSION[pageObj]->getField("type") == "page" || $_SESSION[pageObj]->getField("type") == "link") {
	$leftlinks .= "<tr><td>";
	if ($_SESSION[settings][step] == 2) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($_SESSION[settings][step] != 2) $leftlinks .= "<a href='#' onclick=\"submitFormLink(2)\">";
	$leftlinks .= "Activation";
	if ($_SESSION[settings][step] != 2) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

if ($_SESSION[pageObj]->getField("type") == "page") {
	$leftlinks .= "<tr><td>";
	if ($_SESSION[settings][step] == 3) $leftlinks .= "&rArr; ";
	$leftlinks .= "</td><td>";
	if ($_SESSION[settings][step] != 3) $leftlinks .= "<a href='#' onclick=\"submitFormLink(3)\">";
	$leftlinks .= "Display Options";
	if ($_SESSION[settings][step] != 3) $leftlinks .= "</a>";
	$leftlinks .= "</td></tr>";
}

$leftlinks .= "</table>_________________<br /><a href='$PHP_SELF?$sid&amp;action=add_page&amp;cancel=1'>Cancel</a>";

add_link(leftnav,'','',"$leftlinks");

if ($_SESSION[settings][step] == 1) {
	include("add_page_form_1_item.inc");
}
if ($_SESSION[settings][step] == 2) {
	include("add_page_form_2_activation.inc");
}
if ($_SESSION[settings][step] == 3) {
	include("add_page_form_4_show.inc");
}

// End of New Code
//--------------------------------------------------------------------------------------------------------
