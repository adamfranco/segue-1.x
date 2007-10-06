<? /* $Id$ */
 
$content = ''; 

require("objects/objects.inc.php");
 
ob_start(); 
session_start(); 


// include all necessary files 
require("includes.inc.php"); 
require("sniffer.inc.php");
 
//if ($_SESSION['ltype'] != 'admin') exit; 
 
db_connect($dbhost, $dbuser, $dbpass, $dbdb);

/******************************************************************************
 * create an origional site object if starting.
 ******************************************************************************/

if (isset($_REQUEST['type']) && $_REQUEST['type'] && isset($_SESSION['origSiteObj'])) {
	unset($_SESSION['origSiteObj'],$_SESSION['type'],$_SESSION['origSite'],$_SESSION['origSection'],$_SESSION['origPage'],$_SESSION['sites'],$_SESSION['onlyCopy']);
//	print "unsetting";
}

if (!is_object($_SESSION['origSiteObj'])) {
	$_SESSION['origSiteObj'] =& new site($_REQUEST['site']);
	$_SESSION['origSiteObj']->fetchDown();

	$_SESSION['origSite'] = $_REQUEST['site'];
	$_SESSION['origSection'] = $_REQUEST['section'];
	$_SESSION['origPage'] = $_REQUEST['page'];
	$_SESSION['origStory'] = $_REQUEST['story'];
	
	$_SESSION['type'] = $_REQUEST['type'];
	
	$_SESSION['sites'] = array();
	
/******************************************************************************
 * Make sure that the user try to move, has permission to delete the origional
 ******************************************************************************/
	if ($_SESSION['type'] == "section") {
		if (!$_SESSION['origSiteObj']->sections[$_SESSION['origSection']]->hasPermission("delete",$_SESSION['auser'])) {
			$_SESSION['onlyCopy'] = 1;
			$_REQUEST['action'] = "COPY";
		}
	} else if ($_SESSION['type'] == "page") {
		if (!$_SESSION['origSiteObj']->sections[$_SESSION['origSection']]->pages[$_SESSION['origPage']]->hasPermission("delete",$_SESSION['auser'])) {
			$_SESSION['onlyCopy'] = 1;
			$_REQUEST['action'] = "COPY";
		}
	} else {
		if (!$_SESSION['origSiteObj']->sections[$_SESSION['origSection']]->pages[$_SESSION['origPage']]->stories[$_SESSION['origStory']]->hasPermission("delete",$_SESSION['auser'])) {
			$_SESSION['onlyCopy'] = 1;
			$_REQUEST['action'] = "COPY";
		}
	}	

/******************************************************************************
 * Get the sites that a person is the owner or editor of.
 ******************************************************************************/
	$sitesArray = segue::getAllSites($_SESSION['auser']);
	$sitesArray = array_merge($sitesArray, segue::getAllSitesWhereUserIsEditor($_SESSION['auser']));
	foreach ($sitesArray as $s) {
		$temp =& new site($s);
		$temp->fetchDown();
		if ($temp->hasPermissionDown("add",$_SESSION['auser']) || $temp->name == $_REQUEST['site'] || $temp->site_owner == $_SESSION['auser']) {
			$_SESSION['sites'][$s] = $temp;
		}
	}

}

if (!isset($_REQUEST['action'])) $_REQUEST['action'] = "COPY";



/******************************************************************************
 * Initialize the current site.
 ******************************************************************************/
$siteObj =& new site($_REQUEST['site']);
$siteObj->fetchDown();

if (($_SESSION['type'] != "section" && !isset($_REQUEST['section'])) || $_REQUEST['selecttype'] == "site") {
	$sectionsArray = $siteObj->getField("sections");
	$_REQUEST['section'] = $sectionsArray[0];
}


if (($_SESSION['type'] == "story" && !isset($_REQUEST['page'])) || ($_REQUEST['selecttype'] == "site" || $_REQUEST['selecttype'] == "section")) {
	$pagesArray = $siteObj->sections[$_REQUEST['section']]->getField("pages");
	$_REQUEST['page'] = $pagesArray[0];
}

$actionlc = strtolower($_REQUEST['action']);

/******************************************************************************
 * save move to DB
 ******************************************************************************/

if ($_REQUEST['domove']) {
	// set the origional objects to move/copy	
	if ($_SESSION['type'] == "section") {
		$partObj = $_SESSION['origSiteObj']->sections[$_SESSION['origSection']];
		$parentObj = $siteObj;
		if ($_REQUEST['action'] == "COPY" && $parentObj->name == $_SESSION['origSite']) $removeOrigional = 0;
		else $removeOrigional = 1;
		log_entry($actionlc."_section",$_SESSION['auser']." ".$actionlc."d section ".$partObj->id." from site ".$_SESSION['origSiteObj']->name." to ".$parentObj->name,$parentObj->name,$parentObj->id,"site");
	}
	else if ($_SESSION['type'] == "page") {
		$partObj = $_SESSION['origSiteObj']->sections[$_SESSION['origSection']]->pages[$_SESSION['origPage']];
		$parentObj = $siteObj->sections[$_REQUEST['section']];
		if ($_REQUEST['action'] == "COPY" && $parentObj->id == $_SESSION['origSection']) 
			$removeOrigional = 0;
		else 
			$removeOrigional = 1;
		log_entry($actionlc."_page",$_SESSION['auser']." ".$actionlc."d page ".$partObj->id." from site ".$_SESSION['origSite'].", section ".$_SESSION['origSection']." to site ".$parentObj->owning_site.", section ".$parentObj->id,$parentObj->owning_site,$parentObj->id,"section");
	}
	else if ($_SESSION['type'] == "story") {	
		$partObj = $_SESSION['origSiteObj']->sections[$_SESSION['origSection']]->pages[$_SESSION['origPage']]->stories[$_SESSION['origStory']];
		$parentObj = $siteObj->sections[$_REQUEST['section']]->pages[$_REQUEST['page']];
		if ($_REQUEST['action'] == "COPY" && $parentObj->id == $_SESSION['origPage']) {
			$removeOrigional = 0;
		} else {
			$removeOrigional = 1;
		}
		
		log_entry($actionlc."_story",$_SESSION['auser']." ".$actionlc."d story ".$partObj->id." from site ".$_SESSION['origSite'].", section ".$_SESSION['origSection'].", page ".$_SESSION['origPage']." to site ".$parentObj->owning_site.", section ".$parentObj->owning_section.", page ".$parentObj->id,$parentObj->owning_site,$parentObj->id,"story");
	} else 
		print "Major Error!!!!!!!!!!!!!!!!!!!!!!  AHHHHHhhhhhhhh!!!!!!!!!!!!!!!!!!!!";
	
	// make a copy of the origional to delete later.
	if (version_compare(phpversion(), '5.0') < 0) {
		eval('
function clone($object) {
	return $object;
}
');
	}
	
	$origPartObj = clone($partObj);
	
	if ($_REQUEST['site'] == $_SESSION['origSite']) $keepaddedby = 1;
	else $keepaddedby = 0;
	
	// build a site hash
	makeSiteHash($_SESSION['origSiteObj']);
	
	// Make sure that we have our "NEXT" value set for the object we are copying.
	if ($_SESSION['type'] == "section") {
		$GLOBALS['__site_hash']['sections'][$partObj->id] = 'NEXT';
	} else if ($_SESSION['type'] == "page") {
		$GLOBALS['__site_hash']['pages'][$partObj->id] = 'NEXT';
	} else if ($_SESSION['type'] == "story") {
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
	if ($_SESSION['type'] == "section") {
		$newPartObj =& $newSiteObj->sections[$partObj->id];
	} else if ($_SESSION['type'] == "page") {
		$newPartObj =& $newSiteObj->sections[$_REQUEST['section']]->pages[$partObj->id];
	} else if ($_SESSION['type'] == "story") {
		$newPartObj =& $newSiteObj->sections[$_REQUEST['section']]->pages[$_REQUEST['page']]->stories[$partObj->id];
	}
	updateSiteLinksFromHash($newSiteObj, $newPartObj);
	$newSiteObj->updateDB(1,1);
	
	
	// delete the origional
	if ($_REQUEST['action'] == "MOVE") {
		$origPartObj->delete(1);
		delete_record_tags($_SESSION['origSiteObj']->name,$_SESSION['origStory'],"story");
	}
	
}

?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

<script type="text/javascript">
// <![CDATA[ 
 
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
		opener.window.location = "index.php?$sid&action=viewsite&site=<? print $_SESSION['origSite']; if ($_SESSION['type'] != "section") print "&section=".$_SESSION['origSection']; if ($_SESSION['type'] == "story") print "&page=".$_SESSION['origPage']; ?>";
	}
}

function followLink(url) {
	opener.location=url.replace(/&amp;/, '&');
	window.close();
}

// ]]> 
</script> 

</head>

<?
if ($_REQUEST['domove'])
	print "<body onload=\"finishUp('".$_REQUEST['action']."')\">";
else
	print "<body>";
 
print "<form action='".$_SERVER['PHP_SELF']."' name='moveform' method='post'>";

print "<input type='hidden' name='selecttype' />";
if ($_SESSION['type'] == "story")
	print "<input type='hidden' name='story' value='".$_REQUEST['story']."' />";
if ($_SESSION['type'] == "page")
	print "<input type='hidden' name='page' value='".$_REQUEST['page']."' />";
if ($_SESSION['type'] == "section")
	print "<input type='hidden' name='section' value='".$_REQUEST['section']."' />";

print "<table cellspacing='1' width='100%'>";

if (!$_REQUEST['domove']) {
	print "<tr> ";
		print "<td colspan='2'>";
			print "<input type='radio' value='COPY' name='action'".(($_REQUEST['action']=="COPY")?" checked='checked'":"")." onclick=\"updateForm('move')\" /> Copy &nbsp; &nbsp; ";
			if (!$_SESSION['onlyCopy'])
				print "<input type='radio' value='MOVE' name='action'".(($_REQUEST['action']=="MOVE")?" checked='checked'":"")." onclick=\"updateForm('move')\" /> Move";
		print "</td>";
	print "</tr> ";
}

print "<tr>";
	print "<th style='text-align: left;' colspan='1'>";
	if (!$_REQUEST['domove']) {
		print ucwords($actionlc)." ".ucwords($_SESSION['type']);
		print " to:";
	} else {
		print ucwords($actionlc)." ".ucwords($_SESSION['type'])." Successfull";
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
	if (!$_REQUEST['domove']) {
		print "<select name='site' onchange=\"updateForm('site')\" style='";
		if ($siteObj->name == $_SESSION['origSite'] && $_REQUEST['action']=="MOVE" && $_SESSION['type'] == "section") {
			print "background-color: #F33;";
			$cantmovehere=1;
			$cantmovereason = "You can not move this section to the same place it exists. Try copying or moving to a new location instead.";
		}
		if ($siteObj->name == $_SESSION['origSite']) print " font-weight: bold;";
		else print " font-weight: normal;";
		print "'>";
		foreach ($_SESSION['sites'] as $s=>$v) {
			$title = $_SESSION['sites'][$s]->getField("name").": ".$_SESSION['sites'][$s]->getField("title");
			$title = segue::cropString($title,25);
			print "<option value='$s'";
			print (($siteObj->name == $s)?" selected":"");
			print " style='";
			print (($s == $_SESSION['origSite'] && $_REQUEST['action'] == "MOVE" && $_SESSION['type'] == "section")?"background-color: #F33;":"background-color: #FFF;");
			print (($s == $_SESSION['origSite'])?" font-weight: bold;":" font-weight: normal;");
			print "'";
			print ">$title\n";
		}
		print "</select>";
	} else {
		$oname = $_SESSION['origSiteObj']->getField("title");
		$name = $siteObj->getField("title");
		print "$oname => $name";
	}	
	print "</td>";
print "</tr>";

/******************************************************************************
 * print out the Section row
 ******************************************************************************/
if ($_SESSION['type'] != "section") {
	print "<tr>";
		print "<td style='text-align: left'>Section: </td>";
		print "<td style='text-align: left'>";
		if (!$_REQUEST['domove']) {
			if (count($siteObj->sections)) {
				print "<select name='section' onchange=\"updateForm('section')\" style='";
				if ($siteObj->sections[$_REQUEST['section']]->id == $_SESSION['origSection'] && $_REQUEST['action']=="MOVE" && $_SESSION['type'] == "page") {
					print "background-color: #F33;";
					$cantmovehere=1;
					$cantmovereason = "You can not move this page to the same place it exists. Try copying or moving to a new location instead.";
				} else if (!$siteObj->sections[$_REQUEST['section']]->movePermission($_REQUEST['action'],$_SESSION['auser'],$_SESSION['origSite'],$_SESSION['type'])) {
					print "background-color: #F33;";
					$cantmovehere=1;
					if ($siteObj->sections[$section]->getField("type") != "section")
						$cantmovereason = "This is not a section which you can $actionlc this page to.";
					else
						$cantmovereason = "You do not have permission to $actionlc this page here.";
				}
				if ($siteObj->sections[$_REQUEST['section']]->id == $_SESSION['origSection']) print " font-weight: bold;";
				else print " font-weight: normal;";
				print "'>";
				foreach ($siteObj->sections as $s=>$v) {
					$title = $siteObj->sections[$s]->getField("title");
					$title = segue::cropString($title,25);
					print "<option value='$s'";
					print (($siteObj->sections[$s]->id == $_REQUEST['section'])?" selected":"");
					print " style='";
					print ((!$siteObj->sections[$s]->movePermission($_REQUEST['action'],$_SESSION['auser'],$_SESSION['origSite'],$_SESSION['type']) || ($siteObj->sections[$s]->id == $_SESSION['origSection'] && $_REQUEST['action'] == "MOVE" && $_SESSION['type'] == "page"))?"background-color: #F33;":"background-color: #FFF;");
					print (($siteObj->sections[$s]->id == $_SESSION['origSection'])?" font-weight: bold;":" font-weight: normal;");
					print "'";
					print ">$title\n";
				}
				print "</select>";
			} else {
				print "No Sections.";
				$cantmovehere=1;
			}
		} else {
			$oname = $_SESSION['origSiteObj']->sections[$_SESSION['origSection']]->getField("title");
			$name = $siteObj->sections[$_REQUEST['section']]->getField("title");
			print "$oname => $name";
		}
		print "</td>";
	print "</tr>";
}

/******************************************************************************
 * print out the page row
 ******************************************************************************/
if ($_SESSION['type'] == "story") {
	print "<tr>";
		print "<td style='text-align: left'>Page: </td>";
		print "<td style='text-align: left'>";
		if (!$_REQUEST['domove']) {
			if (count($siteObj->sections[$_REQUEST['section']]->pages)) {
				print "<select name='page' onchange=\"updateForm('page')\" style='";
				if ($siteObj->sections[$_REQUEST['section']]->pages[$_REQUEST['page']]->id == $_SESSION['origPage'] && $_REQUEST['action']=="MOVE") {
					print "background-color: #F33;";
					$cantmovehere=1;
					$cantmovereason = "You can not move this story to the same place it exists. Try copying or moving to a new location instead.";
				} else if (!$siteObj->sections[$_REQUEST['section']]->pages[$_REQUEST['page']]->movePermission($_REQUEST['action'],$_SESSION['auser'],$_SESSION['origSite'],$_SESSION['type'])) {
					print "background-color: #F33;";
					$cantmovehere=1;
					if ($siteObj->sections[$_REQUEST['section']]->pages[$_REQUEST['page']]->getField("type") != "page")
						$cantmovereason = "This is not a page which you can $actionlc this story to.";
					else
						$cantmovereason = "You do not have permission to $actionlc this story here.";
				}
				if ($siteObj->sections[$_REQUEST['section']]->pages[$_REQUEST['page']]->id == $_SESSION['origPage']) print "font-weight: bold;";
				print "'>";
				foreach ($siteObj->sections[$_REQUEST['section']]->pages as $p=>$v) {
					$title = $siteObj->sections[$_REQUEST['section']]->pages[$p]->getField("title");
					$title = segue::cropString($title,25);
					print "<option value='$p'";
					print (($siteObj->sections[$_REQUEST['section']]->pages[$p]->id == $_REQUEST['page'])?" selected":"");
					print " style='";
					print ((!$siteObj->sections[$_REQUEST['section']]->pages[$p]->movePermission($_REQUEST['action'],$_SESSION['auser'],$_SESSION['origSite'],$_SESSION['type']) || ($siteObj->sections[$_REQUEST['section']]->pages[$p]->id == $_SESSION['origPage'] && $_REQUEST['action']=="MOVE"))?"background-color: #F33;":"background-color: #FFF;");
					if ($siteObj->sections[$_REQUEST['section']]->pages[$p]->id == $_SESSION['origPage']) print "font-weight: bold;";
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
			$oname = $_SESSION['origSiteObj']->sections[$_SESSION['origSection']]->pages[$_SESSION['origPage']]->getField("title");
			if (is_object($siteObj->sections[$_REQUEST['section']]->pages[$_REQUEST['page']])) 
				$name = $siteObj->sections[$_REQUEST['section']]->pages[$_REQUEST['page']]->getField("title");
			print "$oname => $name";
		}
		print "</td>";
	print "</tr>";
}

if (!$_REQUEST['domove']) {
	print "\n<tr><td colspan='2'>";
	print "\n".(($_REQUEST['action'] == "COPY")?"Copy":"Move")." discussions? ";
	print "\nYes: <input type='radio' name='keep_discussions' value='yes' ".(($_REQUEST['keep_discussions'] != 'no')?"checked='checked'":"")." />";
	print "\nNo: <input type='radio' name='keep_discussions' value='no' ".(($_REQUEST['keep_discussions'] == 'no')?"checked='checked'":"")." />";
	print "\n</td></tr>";
}
/******************************************************************************
 * print buttons
 ******************************************************************************/
print "\n<tr>";
	print "<td colspan='2'>";
	if (!$_REQUEST['domove']) {
		if (!$cantmovehere)
			print "<input type='submit' name='domove' value='".$_REQUEST['action']."' />";
		else 
			print "<input type='button' value='".$_REQUEST['action']."' style='background-color: #F33;' onclick=\"alert('$cantmovereason')\" />";				
	} else {
		print "<input type=button value='Go To ".(($_REQUEST['action']=="MOVE")?"Moved":"Copied")." ".$_SESSION['type']."' onclick=\"followLink('";
		if ($_SESSION['type'] == "story")
			print "index.php?$sid&amp;action=viewsite&amp;site=".$_REQUEST['site']."&amp;section=".$_REQUEST['section']."&amp;page=".$_REQUEST['page'];
		if ($_SESSION['type'] == "page")
			print "index.php?$sid&amp;action=viewsite&amp;site=".$_REQUEST['site']."&amp;section=".$_REQUEST['section']."&amp;page=".$partObj->id;
		if ($_SESSION['type'] == "section")
			print "index.php?$sid&amp;action=viewsite&amp;site=".$_REQUEST['site']."&amp;section=".$partObj->id;
		print "')\" />";
	}
	print "</td>";
print "</tr>";


?>
</table>
</form>
 
<div align='right'><input type='button' value='Cancel' onclick='window.close()' align='right' /></div><br /> 

</body>
</html>
