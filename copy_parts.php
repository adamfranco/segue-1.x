<? /* $Id$ */
 
$content = ''; 

require("objects/objects.inc.php");
 
ob_start(); 
session_start(); 

//output a meta tag 
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'; 
 
// include all necessary files 
require("includes.inc.php"); 
require("sniffer.inc.php");
 
//if ($ltype != 'admin') exit; 
 
db_connect($dbhost, $dbuser, $dbpass, $dbdb);

/******************************************************************************
 * create an origional site object if starting.
 ******************************************************************************/

if ($_REQUEST[type] && isset($_SESSION[origSiteObj])) {
	unset($_SESSION[origSiteObj],$_SESSION[type],$_SESSION[origSite],$_SESSION[origSection],$_SESSION[origPage],$_SESSION[sites],$_SESSION[onlyCopy]);
//	print "unsetting";
}

if (!is_object($_SESSION[origSiteObj])) {
	$_SESSION[origSiteObj] =& new site($_REQUEST[site]);
	$_SESSION[origSiteObj]->fetchDown();

	$_SESSION[origSite] = $_REQUEST[site];
	$_SESSION[origSection] = $_REQUEST[section];
	$_SESSION[origPage] = $_REQUEST[page];
	$_SESSION[origStory] = $_REQUEST[story];
	
	$_SESSION[type] = $_REQUEST[type];
	
	$_SESSION[sites] = array();
	
/******************************************************************************
 * Make sure that the user try to move, has permission to delete the origional
 ******************************************************************************/
	if ($_SESSION[type] == "section") {
		if (!$_SESSION[origSiteObj]->sections[$_SESSION[origSection]]->hasPermission("delete",$auser)) {
			$_SESSION[onlyCopy] = 1;
			$action = "COPY";
		}
	} else if ($_SESSION[type] == "page") {
		if (!$_SESSION[origSiteObj]->sections[$_SESSION[origSection]]->pages[$_SESSION[origPage]]->hasPermission("delete",$auser)) {
			$_SESSION[onlyCopy] = 1;
			$action = "COPY";
		}
	} else {
		if (!$_SESSION[origSiteObj]->sections[$_SESSION[origSection]]->pages[$_SESSION[origPage]]->stories[$_SESSION[origStory]]->hasPermission("delete",$auser)) {
			$_SESSION[onlyCopy] = 1;
			$action = "COPY";
		}
	}	

/******************************************************************************
 * Get the sites that a person is the owner or editor of.
 ******************************************************************************/
	$sitesArray = segue::getAllSites($auser);
	$sitesArray = array_merge($sitesArray, segue::getAllSitesWhereUserIsEditor($auser));
	foreach ($sitesArray as $s) {
/* 		print $s."<br>"; */
		$temp =& new site($s);
		$temp->fetchDown();
		if ($temp->hasPermissionDown("add",$auser) || $temp->name == $_REQUEST[site] || $temp->site_owner == $auser) {
			$_SESSION[sites][$s] = $temp;
/* 			$title = $sites[$s]->getField("title"); */
/* 			print "title = $title <br>"; */
		}
	}

}
/* $oname = $_SESSION[origSiteObj]->sections['$_SESSION[origSection]']->getField("title"); */
/* print "<pre>"; print_r($_SESSION[origSiteObj]->sections[$_SESSION[origSection]]->getField("title")); print "</pre>"; */

if (!isset($action)) $action = "COPY";

// $oname = $_SESSION[origionalsiteObj]->sections['$_SESSION[origionalsection]']->getField("title");

/* // debug output -- handy :) */
/* print "<pre>"; */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* if (is_object($thisPage)) { */
/* 	print "\n\n"; */
/* 	print "thisPage:\n"; */
/* 	print_r($thisPage); */
/* } else if (is_object($thisSection)) { */
/* 	print "\n\n"; */
/* 	print "thisSection:\n"; */
/* 	print_r($thisSection); */
/* } else if (is_object($thisSite)) { */
/* 	print "\n\n"; */
/* 	print "thisSite:\n"; */
/* 	print_r($thisSite); */
/* } */
/* print "</pre>"; */
/* return 1; */

/******************************************************************************
 * Initialize the current site.
 ******************************************************************************/
$siteObj =& new site($_REQUEST[site]);
$siteObj->fetchDown();
$site = $_REQUEST[site];
$section = $_REQUEST[section];
$page = $_REQUEST[page];

if (($_SESSION[type] != "section" && !isset($section)) || $selecttype == "site") {
	$sectionsArray = $siteObj->getField("sections");
	$section = $sectionsArray[0];
}

/* print "<pre>"; print_r($siteObj); print "</pre>"; */
/* print "section = ".$section."<br>"; */
/* print "section = ".$site."<br>"; */

if (($_SESSION[type] == "story" && !isset($page)) || ($selecttype == "site" || $selecttype == "section")) {
	$pagesArray = $siteObj->sections[$section]->getField("pages");
	$page = $pagesArray[0];
}
/* print "<pre>"; print_r($siteObj->sections); print "</pre>"; */
/* return 1; */
/* if ($_SESSION[type] == "story") { */
/* 	$pages = $sectionObj->getField("pages"); */
/* 	$newpages = array(); */
/* 	foreach ($pages as $p) { */
/* 		$pagetype = db_get_value("pages","type","id=$p"); */
/* 		if (permission($auser,PAGE,ADD,$p) && $pagetype == "page") */
/* 			array_push($newpages,$p); */
/* 	} */
/* 	$pages = $newpages; */
/* 	if ($selecttype == "site" || $selecttype == "section") */
/* 		$page = $pages[0]; */
/* } */

$actionlc = strtolower($action);

/******************************************************************************
 * save move to DB
 ******************************************************************************/

if ($domove) {
	// set the origional objects to move/copy	
	if ($_SESSION[type] == "section") {
		$partObj = $_SESSION[origSiteObj]->sections[$_SESSION[origSection]];
		$parentObj = $siteObj;
		if ($action == "COPY" && $parentObj->name == $_SESSION[origSite]) $removeOrigional = 0;
		else $removeOrigional = 1;
		log_entry($actionlc."_section","$_SESSION[auser] ".$actionlc."d section ".$partObj->id." from site ".$_SESSION[origSiteObj]->name." to ".$parentObj->name,$parentObj->name,$parentObj->id,"site");
	}
	else if ($_SESSION[type] == "page") {
		$partObj = $_SESSION[origSiteObj]->sections[$_SESSION[origSection]]->pages[$_SESSION[origPage]];
		$parentObj = $siteObj->sections[$section];
		if ($action == "COPY" && $parentObj->id == $_SESSION[origSection]) $removeOrigional = 0;
		else $removeOrigional = 1;
		log_entry($actionlc."_page","$_SESSION[auser] ".$actionlc."d page ".$partObj->id." from site ".$_SESSION[origSite].", section ".$_SESSION[origSection]." to site ".$parentObj->owning_site.", section ".$parentObj->id,$parentObj->owning_site,$parentObj->id,"section");
	}
	else if ($_SESSION[type] == "story") {
		$partObj = $_SESSION[origSiteObj]->sections[$_SESSION[origSection]]->pages[$_SESSION[origPage]]->stories[$_SESSION[origStory]];
		$parentObj = $siteObj->sections[$section]->pages[$page];
		if ($action == "COPY" && $parentObj->id == $_SESSION[origPage]) $removeOrigional = 0;
		else $removeOrigional = 1;
		log_entry($actionlc."_story","$_SESSION[auser] ".$actionlc."d story ".$partObj->id." from site ".$_SESSION[origSite].", section ".$_SESSION[origSection].", page ".$_SESSION[origPage]." to site ".$parentObj->owning_site.", section ".$parentObj->owning_section.", page ".$parentObj->id,$parentObj->owning_site,$parentObj->id,"story");
	} else 
		print "Major Error!!!!!!!!!!!!!!!!!!!!!!  AHHHHHhhhhhhhh!!!!!!!!!!!!!!!!!!!!";
	
	// make a copy of the origional to delete later.
	$origPartObj = $partObj;
	
	if ($action == "MOVE" && $site == $origionalsite) $keepaddedby = 1;
	else $keepaddedby = 0;
	
	// build a site hash
	makeSiteHash($_SESSION[origSiteObj]);
	
	// Make sure that we have our "NEXT" value set for the object we are copying.
	if ($_SESSION[type] == "section") {
		$GLOBALS['__site_hash']['sections'][$partObj->id] = 'NEXT';
	} else if ($_SESSION[type] == "page") {
		$GLOBALS['__site_hash']['pages'][$partObj->id] = 'NEXT';
	} else if ($_SESSION[type] == "story") {
		$GLOBALS['__site_hash']['stories'][$partObj->id] = 'NEXT';
	}
	
	if ($_REQUEST['keep_discussions'] == 'no')
		$keepDiscussion = FALSE;
	else
		$keepDiscussion = TRUE;
		
	// move the object.
	$partObj->copyObj($parentObj,$removeOrigional,$keepaddedby, $keepDiscussion);
	
	// If we have moved to a new site, update the site links from the hash.
	$newSiteObj = new site($parentObj->owningSiteObj->name);
	$newSiteObj->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen();
	if ($_SESSION[type] == "section") {
		$newPartObj =& $newSiteObj->sections[$partObj->id];
	} else if ($_SESSION[type] == "page") {
		$newPartObj =& $newSiteObj->sections[$section]->pages[$partObj->id];
	} else if ($_SESSION[type] == "story") {
		$newPartObj =& $newSiteObj->sections[$section]->pages[$page]->stories[$partObj->id];
	}
	updateSiteLinksFromHash($newSiteObj, $newPartObj);
	$newSiteObj->updateDB(1,1);
	
	
	// delete the origional
	if ($action == "MOVE") {
		/* print "<pre>"; print_r($origPartObj); print "</pre>"; */
		$origPartObj->delete(1);
	}
	
}

?> 
<html> 
<head> 
<title>Copy/Move</title> 
 
<style type='text/css'> 
a { 
	color: #a33; 
	text-decoration: none; 
} 
 
a:hover {text-decoration: underline;} 
 
table { 
	border: 1px solid #555; 
} 
 
th, td { 
	border: 0px; 
	background-color: #ddd; 
	text-align: center; 
} 
 
.td1 {  
	background-color: #ccc;  
} 
 
.td0 {  
	background-color: #ddd;  
} 
 
th {  
	background-color: #ccc;  
	font-variant: small-caps; 
} 

.sizebox1 {
	text-align: left;
	padding-right: 5px;
}

.sizebox2 {
	text-align: right;
}

body {  
	background-color: white;  
} 
 
body, table, td, th, input { 
	font-size: 10px; 
	font-family: "Verdana", "sans-serif"; 
} 
 
/* td { font-size: 10px; } */ 
 
input,select { 
	border: 1px solid black; 
	background-color: white; 
	font-size: 10px; 
} 
 
</style> 

<script lang="JavaScript"> 
 
function updateForm(type) { 
	f = document.moveform; 
	f.selecttype.value=type;
	f.submit();  
} 
 
function showError(type,action) { 
	if (type == "story")
		var error = "You do not have permission to "+action+" this content block to any pages in this section.";
	else
		var error = "You do not have permission to "+action+" this page to any sections in this site.";
	alert(error);
} 

function finishUp(action) {
	if (action == "COPY") {
		opener.history.go(0);
	} else {
		opener.window.location = "index.php?$sid&action=viewsite&site=<? print $_SESSION[origSite]; if ($_SESSION[type] != "section") print "&section=".$_SESSION[origSection]; if ($_SESSION[type] == "story") print "&page=".$_SESSION[origPage]; ?>";
	}
}

function followLink(url) {
	opener.location=url;
	window.close();
}
 
</script> 

</head>

<?
if ($domove)
	print "<body onLoad=\"finishUp('$action')\">";
else
	print "<body>";
 
print "<form action='$PHP_SELF?$sid' name='moveform' method='post'>";
/* print "<input type=hidden name='origionalsite' value='$origionalsite'>"; */
/* print "<input type=hidden name='origionalsection' value='$origionalsection'>"; */
/* print "<input type=hidden name='origionalpage' value='$origionalpage'>"; */
print "<input type=hidden name='selecttype'>";
/* print "<input type=hidden name='sites' value='".encode_array($sites)."'>"; */
if ($_SESSION[type] == "story")
	print "<input type=hidden name='story' value='$story'>";
if ($_SESSION[type] == "page")
	print "<input type=hidden name='page' value='$page'>";
if ($_SESSION[type] == "section")
	print "<input type=hidden name='section' value='$section'>";

print "<table cellspacing=1 width='100%'>";

if (!$domove) {
	print "<tr> ";
		print "<td colspan=2>";
			print "<input type=radio value='COPY' name='action'".(($action=="COPY")?" checked":"")." onClick=\"updateForm('move')\"> Copy &nbsp; &nbsp; ";
			if (!$_SESSION[onlyCopy])
				print "<input type=radio value='MOVE' name='action'".(($action=="MOVE")?" checked":"")." onClick=\"updateForm('move')\"> Move";
		print "</td>";
	print "</tr> ";
}

print "<tr>";
	print "<th style='text-align: left;' colspan=1>";
	if (!$domove) {
		print ucwords($actionlc)." ".ucwords($_SESSION[type]);
		print " to:";
	} else {
		print ucwords($actionlc)." ".ucwords($_SESSION[type])." Successfull";
	}
	print "</th>";
	print "<th style='text-align: right'>";
		print helplink("copy_parts");
	print "</th>";
print "</tr>";

/******************************************************************************
 * print out the Site row
 ******************************************************************************/
print "<tr>";
	print "<td style='text-align: left;'>Site: </td>";
	print "<td style='text-align: left'>";
	if (!$domove) {
		print "<select name='site' onChange=\"updateForm('site')\" style='";
		if ($siteObj->name == $_SESSION[origSite] && $action=="MOVE" && $_SESSION[type] == "section") {
			print "background-color: #F33;";
			$cantmovehere=1;
			$cantmovereason = "You can not move this section to the same place it exists. Try copying or moving to a new location instead.";
/* 		} else if (!$siteObj->movePermission($action,$auser,$_SESSION[origSite],$_SESSION[type])) { */
/* 	//		print "background-color: #F33;"; */
/* 			$cantmovehere=1; */
/* 			$cantmovereason = "You do not have permission to $actionlc this section here."; */
		}
		if ($siteObj->name == $_SESSION[origSite]) print " font-weight: bold;";
		else print " font-weight: normal;";
		print "'>";
		foreach ($_SESSION[sites] as $s=>$v) {
			$title = $_SESSION[sites][$s]->getField("name").": ".$_SESSION[sites][$s]->getField("title");
			$title = segue::cropString($title,25);
			print "<option value='$s'";
			print (($siteObj->name == $s)?" selected":"");
			print " style='";
			print (($s == $_SESSION[origSite] && $action == "MOVE" && $_SESSION[type] == "section")?"background-color: #F33;":"background-color: #FFF;");
			print (($s == $_SESSION[origSite])?" font-weight: bold;":" font-weight: normal;");
			print "'";
			print ">$title\n";
		}
		print "</select>";
	} else {
		$oname = $_SESSION[origSiteObj]->getField("title");
		$name = $siteObj->getField("title");
		print "$oname => $name";
	}	
	print "</td>";
print "</tr>";

/******************************************************************************
 * print out the Section row
 ******************************************************************************/
if ($_SESSION[type] != "section") {
	print "<tr>";
		print "<td style='text-align: left'>Section: </td>";
		print "<td style='text-align: left'>";
		if (!$domove) {
			if (count($siteObj->sections)) {
				print "<select name='section' onChange=\"updateForm('section')\" style='";
				if ($siteObj->sections[$section]->id == $_SESSION[origSection] && $action=="MOVE" && $_SESSION[type] == "page") {
					print "background-color: #F33;";
					$cantmovehere=1;
					$cantmovereason = "You can not move this page to the same place it exists. Try copying or moving to a new location instead.";
				} else if (!$siteObj->sections[$section]->movePermission($action,$auser,$_SESSION[origSite],$_SESSION[type])) {
					print "background-color: #F33;";
					$cantmovehere=1;
					if ($siteObj->sections[$section]->getField("type") != "section")
						$cantmovereason = "This is not a section which you can $actionlc this page to.";
					else
						$cantmovereason = "You do not have permission to $actionlc this page here.";
				}
				if ($siteObj->sections[$section]->id == $_SESSION[origSection]) print " font-weight: bold;";
				else print " font-weight: normal;";
				print "'>";
				foreach ($siteObj->sections as $s=>$v) {
					$title = $siteObj->sections[$s]->getField("title");
					$title = segue::cropString($title,25);
					print "<option value='$s'";
					print (($siteObj->sections[$s]->id == $section)?" selected":"");
					print " style='";
					print ((!$siteObj->sections[$s]->movePermission($action,$auser,$_SESSION[origSite],$_SESSION[type]) || ($siteObj->sections[$s]->id == $_SESSION[origSection] && $action == "MOVE" && $_SESSION[type] == "page"))?"background-color: #F33;":"background-color: #FFF;");
					print (($siteObj->sections[$s]->id == $_SESSION[origSection])?" font-weight: bold;":" font-weight: normal;");
					print "'";
					print ">$title\n";
				}
				print "</select>";
			} else {
				print "No Sections.";
				$cantmovehere=1;
			}
		} else {
			$oname = $_SESSION[origSiteObj]->sections[$_SESSION[origSection]]->getField("title");
			$name = $siteObj->sections[$section]->getField("title");
			print "$oname => $name";
		}
		print "</td>";
	print "</tr>";
}

/******************************************************************************
 * print out the page row
 ******************************************************************************/
if ($_SESSION[type] == "story") {
	print "<tr>";
		print "<td style='text-align: left'>Page: </td>";
		print "<td style='text-align: left'>";
		if (!$domove) {
			if (count($siteObj->sections[$section]->pages)) {
				print "<select name='page' onChange=\"updateForm('page')\" style='";
				if ($siteObj->sections[$section]->pages[$page]->id == $_SESSION[origPage] && $action=="MOVE") {
					print "background-color: #F33;";
					$cantmovehere=1;
					$cantmovereason = "You can not move this story to the same place it exists. Try copying or moving to a new location instead.";
				} else if (!$siteObj->sections[$section]->pages[$page]->movePermission($action,$auser,$_SESSION[origSite],$_SESSION[type])) {
					print "background-color: #F33;";
					$cantmovehere=1;
					if ($siteObj->sections[$section]->pages[$page]->getField("type") != "page")
						$cantmovereason = "This is not a page which you can $actionlc this story to.";
					else
						$cantmovereason = "You do not have permission to $actionlc this story here.";
				}
				if ($siteObj->sections[$section]->pages[$page]->id == $_SESSION[origPage]) print "font-weight: bold;";
				print "'>";
				foreach ($siteObj->sections[$section]->pages as $p=>$v) {
					$title = $siteObj->sections[$section]->pages[$p]->getField("title");
					$title = segue::cropString($title,25);
					print "<option value='$p'";
					print (($siteObj->sections[$section]->pages[$p]->id == $page)?" selected":"");
					print " style='";
					print ((!$siteObj->sections[$section]->pages[$p]->movePermission($action,$auser,$_SESSION[origSite],$_SESSION[type]) || ($siteObj->sections[$section]->pages[$p]->id == $_SESSION[origPage] && $action=="MOVE"))?"background-color: #F33;":"background-color: #FFF;");
					if ($siteObj->sections[$section]->pages[$p]->id == $_SESSION[origPage]) print "font-weight: bold;";
					else print "font-weight: normal;";
					print "'>$title\n";
				}
				print "</select>";
			} else {
				print "No Pages.";
				$cantmovehere=1;
				$cantmovereason = "There are no pages in this section.";
			}
		} else {
			$oname = $_SESSION[origSiteObj]->sections[$_SESSION[origSection]]->pages[$_SESSION[origPage]]->getField("title");
			if (is_object($siteObj->sections[$section]->pages[$page])) 
				$name = $siteObj->sections[$section]->pages[$page]->getField("title");
			print "$oname => $name";
		}
		print "</td>";
	print "</tr>";
}

if (!$domove) {
	print "\n<tr><td colspan=2>";
	print "\n".(($action == "COPY")?"Copy":"Move")." discussions? ";
	print "\nYes: <input type='radio' name='keep_discussions' value='yes' ".(($_REQUEST['keep_discussions'] != 'no')?"checked='checked'":"").">";
	print "\nNo: <input type='radio' name='keep_discussions' value='no' ".(($_REQUEST['keep_discussions'] == 'no')?"checked='checked'":"").">";
	print "\n</td></tr>";
}
/******************************************************************************
 * print buttons
 ******************************************************************************/
print "\n<tr>";
	print "<td colspan=2>";
	if (!$domove) {
		if (!$cantmovehere)
			print "<input type=submit name='domove' value='$action'>";
		else 
			print "<input type=button value='$action' style='background-color: #F33;' onClick=\"alert('$cantmovereason')\">";				
	} else {
		print "<input type=button value='Go To ".(($action=="MOVE")?"Moved":"Copied")." $_SESSION[type]' onClick=\"followLink('";
		if ($_SESSION[type] == "story")
			print "index.php?$sid&action=viewsite&site=$site&section=$section&page=$page";
		if ($_SESSION[type] == "page")
			print "index.php?$sid&action=viewsite&site=$site&section=$section&page=".$partObj->id;
		if ($_SESSION[type] == "section")
			print "index.php?$sid&action=viewsite&site=$site&section=".$partObj->id;
		print "')\">";
	}
	print "</td>";
print "</tr>";


?>
</table>
</form>
 
<div align=right><input type=button value='Cancel' onClick='window.close()' align=right></div><BR> 

<?
// debug output -- handy :)
/* print "<pre>"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n"; */
/* if (is_object($thisPage)) { */
/* 	print "\n\n"; */
/* 	print "thisPage:\n"; */
/* 	print_r($thisPage); */
/* } else if (is_object($thisSection)) { */
/* 	print "\n\n"; */
/* 	print "thisSection:\n"; */
/* 	print_r($thisSection); */
/* } else if (is_object($thisSite)) { */
/* 	print "\n\n"; */
/* 	print "thisSite:\n"; */
/* 	print_r($thisSite); */
/* } */
/* print "</pre>"; */
?>
</body>
</html>
