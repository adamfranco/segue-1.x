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
	$_SESSION[origSiteObj] = new site($_REQUEST[site]);
	$_SESSION[origSiteObj]->fetchDown();

	$_SESSION[origSite] = $_REQUEST[site];
	$_SESSION[origSection] = $_REQUEST[section];
	$_SESSION[origPage] = $_REQUEST[page];
	$_SESSION[origStory] = $_REQUEST[story];
	
	$_SESSION[type] = $_REQUEST[type];
	
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
		$temp = new site($s);
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

if (!isset($action)) $action = "MOVE";

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
$siteObj = new site($_REQUEST[site]);
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

if (($type == "story" && !isset($page)) || ($selecttype == "site" || $selecttype == "section")) {
	$pagesArray = $siteObj->sections[$section]->getField("pages");
	$page = $pagesArray[0];
}
/* print "<pre>"; print_r($siteObj->sections); print "</pre>"; */
/* return 1; */
/* if ($type == "story") { */
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
	if ($type == "section") {
		$partObj = $_SESSION[origSiteObj]->sections[$_SESSION[origSection]];
		$parentObj = $siteObj;
		if ($action == "COPY" && $parentObj->id == $_SESSION[origSite]) $removeOrigional = 0;
		else $removeOrigional = 1;
	}
	else if ($type == "page") {
		$partObj = $_SESSION[origSiteObj]->sections[$_SESSION[origSection]]->pages[$_SESSION[origPage]];
		$parentObj = $siteObj->sections[$section];
		if ($action == "COPY" && $parentObj->id == $_SESSION[origSection]) $removeOrigional = 0;
		else $removeOrigional = 1;
	}
	else if ($type == "story") {
		$partObj = $_SESSION[origSiteObj]->sections[$_SESSION[origSection]]->pages[$_SESSION[origPage]]->stories[$_SESSION[origStory]];
		$parentObj = $siteObj->sections[$section]->pages[$page];
		if ($action == "COPY" && $parentObj->id == $_SESSION[origPage]) $removeOrigional = 0;
		else $removeOrigional = 1;
	} else 
		print "Major Error!!!!!!!!!!!!!!!!!!!!!!  AHHHHHhhhhhhhh!!!!!!!!!!!!!!!!!!!!";
	
	// make a copy of the origional to delete later.
	$origPartObj = $partObj;
	
	if ($action == "MOVE" && $site == $origionalsite) $keepaddedby = 1;
	else $keepaddedby = 0;
	
	
	// move the object.
	$partObj->copyObj($parentObj,$removeOrigional,$keepaddedby);
	
	// delete the origional
	if ($action == "MOVE") {
		/* print "<pre>"; print_r($origPartObj); print "</pre>"; */
		$origPartObj->delete(1);
	}
	
}

?> 
<html> 
<head> 
<title>Move/Copy</title> 
 
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
		opener.window.location = "index.php?$sid&action=viewsite&site=<? echo $origionalsite; ?>";
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
if ($type == "story")
	print "<input type=hidden name='story' value='$story'>";
if ($type == "page")
	print "<input type=hidden name='page' value='$page'>";
if ($type == "section")
	print "<input type=hidden name='section' value='$section'>";

print "<table cellspacing=1 width='100%'>";

if (!$domove) {
	print "<tr> ";
		print "<td colspan=2>";
			if (!$_SESSION[onlyCopy])
				print "<input type=radio value='MOVE' name='action'".(($action=="MOVE")?" checked":"")." onClick=\"updateForm('move')\"> Move &nbsp; &nbsp; ";
			print "<input type=radio value='COPY' name='action'".(($action=="COPY")?" checked":"")." onClick=\"updateForm('move')\"> Copy";
		print "</td>";
	print "</tr> ";
}

print "<tr>";
	print "<th style='text-align: left;' colspan=1>";
	if (!$domove) {
		print ucwords($actionlc)." ".ucwords($type);
		print " to:";
	} else {
		print ucwords($actionlc)." ".ucwords($type)." Successfull";
	}
	print "</th>";
	print "<th style='text-align: right'>";
		print helplink("copy_parts");
	print "</th>";
print "</tr>";

print "<tr>";
	print "<td style='text-align: left;'>Site: </td>";
	print "<td>";
	if (!$domove) {
		print "<select name='site' onChange=\"updateForm('site')\">";
		foreach ($_SESSION[sites] as $s=>$v) {
			$name = $_SESSION[sites][$s]->getField("title");
			print "<option value='$s'".(($_SESSION[sites][$s]->name == $site)?" selected":"").">$name\n";
		}
		print "</select>";
//	} else {
		$oname = $_SESSION[origSiteObj]->getField("title");
		$name = $siteObj->getField("title");
		print "$oname => $name";
	}	
	print "</td>";
print "</tr>";
	
if ($type != "section") {
	print "<tr>";
		print "<td style='text-align: left'>Section: </td>";
		print "<td>";
		if (!$domove) {
			if (count($siteObj->sections)) {
				print "<select name='section' onChange=\"updateForm('section')\"";
				if (!$siteObj->sections[$section]->movePermission($action,$auser,$_SESSION[origSite],$_SESSION[type])) {
					print " style='background-color: #F00'";
					$cantmovehere=1;
				}
				print ">";
				foreach ($siteObj->sections as $s=>$v) {
					$name = $siteObj->sections[$s]->getField("title");
					print "<option value='$s'";
					print (($siteObj->sections[$s]->id == $section)?" selected":"");
					print ((!$siteObj->sections[$s]->movePermission($action,$auser,$_SESSION[origSite],$_SESSION[type]))?" style='background-color: #F00'":" style='background-color: #FFF'");
					print ">$name\n";
				}
				print "</select>";
			} else {
				print "No Sections.";
				$cantmovehere=1;
			}
//		} else {
			$oname = $_SESSION[origSiteObj]->sections[$_SESSION[origSection]]->getField("title");
			$name = $siteObj->sections[$section]->getField("title");
			print "$oname => $name";
		}
		print "</td>";
	print "</tr>";
}

if ($type == "story") {
	print "<tr>";
		print "<td style='text-align: left'>Page: </td>";
		print "<td>";
		if (!$domove) {
			if (count($siteObj->sections[$section]->pages)) {
				print "<select name='page' onChange=\"updateForm('page')\"";
				if (!$siteObj->sections[$section]->pages[$page]->movePermission($action,$auser,$_SESSION[origSite],$_SESSION[type])) {
					print " style='background-color: #F00'";
					$cantmovehere=1;
				}
				print ">";
				foreach ($siteObj->sections[$section]->pages as $p=>$v) {
					$name = $siteObj->sections[$section]->pages[$p]->getField("title");
					print "<option value='$p'";
					print (($siteObj->sections[$section]->pages[$p]->id == $page)?" selected":"");
					print ((!$siteObj->sections[$section]->pages[$p]->movePermission($action,$auser,$_SESSION[origSite],$_SESSION[type]))?" style='background-color: #F00'":" style='background-color: #FFF'");
					print ">$name\n";
				}
				print "</select>";
			} else {
				print "No Pages.";
				$cantmovehere=1;
			}
//		} else {
			$oname = $_SESSION[origSiteObj]->sections[$_SESSION[origSection]]->pages[$_SESSION[origPage]]->getField("title");
			if (is_object($siteObj->sections[$section]->pages[$page])) 
				$name = $siteObj->sections[$section]->pages[$page]->getField("title");
			print "$oname => $name";
		}
		print "</td>";
	print "</tr>";
}

	print "<tr>";
		print "<td colspan=2>";
		if (!$domove) {
			if (!$cantmovehere)
				print "<input type=submit name='domove' value='$action'>";
			else 
				print "<input type=button value='$action' style='background-color: #F00;' onClick=\"showError('$_SESSION[type]','$actionlc')\">";				
		} else {
			print "<input type=button value='Go To ".(($action=="MOVE")?"Moved":"Copied")." $type' onClick=\"followLink('";
			if ($type == "story")
				print "index.php?$sid&action=viewsite&site=$site&section=$section&page=$page";
			if ($type == "page")
				print "index.php?$sid&action=viewsite&site=$site&section=$section&page=$newid";
			if ($type == "section")
				print "index.php?$sid&action=viewsite&site=$site&section=$newid";
			print "')\">";
		}
		print "</td>";
	print "</tr>";
?>
</table>
</form>
 
<input type=button value='Cancel' onClick='window.close()' align=right><BR> 
------------------------------------
<?
// debug output -- handy :)
print "<pre>";
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n"; */
print "request:\n";
print_r($_REQUEST);
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
print "</pre>";
?>
</body>
</html>
