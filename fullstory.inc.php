<? /* $Id$ */

//require("objects/objects.inc.php");
$content = '';

//ob_start();
/******************************************************************************
 * This script is an adaptation of fullstory.php
 * this script is included in site.inc.php when detail variable is set
 ******************************************************************************/

//session_start();

// include all necessary files
//include("includes.inc.php");

//include("$themesdir/common/header.inc.php");

if (($tmp = $_REQUEST['flat_discussion'])) {
	$_SESSION['flat_discussion'] = ($tmp=='true')?true:false;
}


$partialstatus = 1;
$siteObj =& new site($_REQUEST[site]);
$sectionObj =& new section($_REQUEST[site],$_REQUEST[section], &$siteObj);
$pageObj =& new page($_REQUEST[site],$_REQUEST[section],$_REQUEST[page], &$sectionObj);
$storyObj =& new story($_REQUEST[site],$_REQUEST[section],$_REQUEST[page],$_REQUEST[story], &$pageObj);
$getinfo = "site=".$siteObj->name."&section=".$sectionObj->id."&page=".$pageObj->id."&story=".$storyObj->id."&detail=".$storyObj->id;


$storyObj->fetchFromDB();
$storyObj->owningSiteObj->fetchFromDB();
//$site_owner=slot::getOwner($story->owningSiteObj->name);
$site_owner=$storyObj->owningSiteObj->owner;
//print_r($story->owningSiteObj);
//print $site_owner;

// get the correct shorttext
if ($storyObj->getField("type") == 'story') {
	$smalltext = $storyObj->getField("shorttext");
	$fulltext = $storyObj->getField("longertext");
	$smalltext = stripslashes($smalltext);
	$fulltext = stripslashes($fulltext);
	if ($storyObj->getField("texttype") == 'text') $fulltext = htmlbr($fulltext);	
	if ($storyObj->getField("texttype") == 'text') $smalltext = htmlbr($smalltext);
}
if ($storyObj->getField("type") == 'image') {
	$filename = urldecode(db_get_value("media","media_tag","media_id=".$storyObj->getField("longertext")));
	$dir = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","slot_name","media_id=".$storyObj->getField("longertext"));
	$imagepath = "$uploadurl/$dir/$filename";
	$fulltext = "<div style='text-align: center'><br><img src='$imagepath' border=0></div>";
/* 	if ($story->getField("title")) $fulltext .= "<tr><td align=center><b>".spchars($story->getField("title"))."</b></td></tr>"; */
	if ($storyObj->getField("shorttext")) $fulltext .= "<br>".stripslashes($storyObj->getField("shorttext"));
	$fulltext .= "";
}
if ($storyObj->getField("type") == 'file') {
	$fulltext = "<br>";
	$fulltext .= makedownloadbar($storyObj);
}


?>
<!--<html>
<head>
<title>Full Content/Discussion</title>-->
<? //include("themes/common/logs_css.inc.php"); ?>
<style type="text/css">

.subject { 
	font-weight: bolder; 
}

.dtext {
	padding-top: 0px;
	padding-bottom: 20px;
	padding-left: 0px;
	padding-right: 0px;
}

.dheader {
	font-size: 14px;
	border-bottom: 1px solid #000;
	background: #b5b5b5;
	padding-left: 5px;
	padding-top: 5px;
}

.dheader2 {
	border-bottom: 1px solid #000;
	background: #b5b5b5;
	padding-right: 5px;
	padding-top: 5px;
}

.dheader3 {
	border-top: 0px solid #000;
	background: #b5b5b5;
	padding-left: 5px;
	padding-right: 5px;
}

</style>
<!--</head>

<body>-->
<?
/******************************************************************************
 * print out shory and discussion (if any)
 ******************************************************************************/
if ($storyObj->getField("discuss")) $titleExtra = " Discussion";

printc("<table width=100% id='maintable' cellspacing=1>");
printc("<tr><td>");
printc("<table cellspacing=1 width=100%>");
printc("<tr><td align=left class=title>".(($pageObj->getField('title'))?spchars($pageObj->getField('title')):'&nbsp;')."</td></tr>");		 
printc("<tr><td align=left><b>".(($storyObj->getField('title'))?spchars($storyObj->getField('title')):'&nbsp;')."</b></td></tr>");
printc("<tr><td style='padding-bottom: 15px; font-size: 12px'>$smalltext</td></tr>");
printc("<tr><td style='padding-bottom: 15px; font-size: 12px'>$fulltext</td></tr>");

		
// output discussions?
if ($storyObj->getField("discuss")) {			
	printc("<td align=left><table width=100% border=0 cellspacing=0 cellpadding=0><tr><td align=left class=dheader>Discussion</td>");
	printc("<td align=right class=dheader2>");
	$f = $_SESSION['flat_discussion'];
	if (!$f) {
		//need to change href to index??
		printc("<a class=info href='index.php?$sid&action=site&$getinfo&flat_discussion=true'>flat</a>");
	} else {
		printc("flat");
	}
	printc(" | ");
	if ($f) {
		//need to change href to index
		printc("<a class=info href='index.php?$sid&action=site&$getinfo&flat_discussion=false'>threaded</a>");
	} else {
		printc("threaded");
	}
	printc("</th></tr></table>");
	printc("</th>");
	printc("</tr>");
	
	
	$ds = & new discussion(&$storyObj);
	if ($f) $ds->flat(); // must be called before _fetchchildren();
	$ds->_fetchchildren();
	
	$ds->opt("showcontent",true);
	$ds->opt("showauthor",false);
	$ds->opt("showtstamp",false);
	$ds->opt("useoptforchildren",true);
	$ds->getinfo = $getinfo;
	
	// outputAll is a function in objects/discussion.inc.php object
	$ds->outputAll($storyObj->hasPermission("discuss"),($_SESSION[auser]==$site_owner),true);
	if (!$ds->count()) printc("<tr><td>There have been no posts to this discussion.</td></tr>");
}
		

printc("</table>");

printc("</tr></td>");
printc("</table>");
printc("<BR><BR>");

?>
<!--<div align=right><input type=button value="Close Window" onClick="window.close()"></div>-->