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

if ($tmp = $_REQUEST['flat_discussion']) {
	$_SESSION['flat_discussion'] = ($tmp=='true')?true:false;
}

if ($tmp2 = $_REQUEST['recent']) {
	$_SESSION['recent'] = ($tmp2=='true')?true:false;
}

//printpre($_SESSION);
//printpre($_REQUEST);

$partialstatus = 1;
$siteObj =& new site($_REQUEST[site]);
$sectionObj =& new section($_REQUEST[site],$_REQUEST[section], &$siteObj);
$pageObj =& new page($_REQUEST[site],$_REQUEST[section],$_REQUEST[page], &$sectionObj);
$storyObj =& new story($_REQUEST[site],$_REQUEST[section],$_REQUEST[page],$_REQUEST[story], &$pageObj);
$getinfo = "site=".$siteObj->name."&section=".$sectionObj->id."&page=".$pageObj->id."&story=".$storyObj->id."&detail=".$storyObj->id;
$getinfo2 = "site=".$siteObj->name."&section=".$sectionObj->id."&page=".$pageObj->id;


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
printc("<tr><td align=left class=title><a href=index.php?action=site&".$getinfo2.">".spchars($pageObj->getField('title'))."</a> > in depth</td></tr>");
//printc("<tr><td align=left class=title>".(($pageObj->getField('title'))?spchars($pageObj->getField('title')):'&nbsp;')."</td></tr>");		 
printc("<tr><td align=left><b>".(($storyObj->getField('title'))?spchars($storyObj->getField('title')):'&nbsp;')."</b></td></tr>");
printc("<tr><td style='padding-bottom: 15px; font-size: 12px'>$smalltext</td></tr>");
printc("<tr><td style='padding-bottom: 15px; font-size: 12px'>$fulltext</td></tr>");

		
// output discussions?
if ($storyObj->getField("discuss")) {
	$mailposts = $storyObj->getField("discussemail");	
	$showposts = $storyObj->getField("discussdisplay");
	$showallauthors = $storyObj->getField("discussauthor");	
	$siteowner = $siteObj->getField("addedbyfull");	
	
	if ($showposts == 1) {
		printc("<td align=left><table width=100% border=0 cellspacing=0 cellpadding=0><tr><td align=left class=dheader>Discussion</td>");
	} else {
		printc("<td align=left><table width=100% border=0 cellspacing=0 cellpadding=0><tr><td align=left class=dheader>Assessment</td>");	
	}
	printc("<td align=right class=dheader2>");
	
	printc("<table>");
	printc("<tr><td>");
	$f = $_SESSION['flat_discussion'];
	printc("<form action='index.php?$sid&action=site&".$getinfo."' method=post name=viewform>");
	printc("<select name='flat_discussion'>");
/* 	printc("<option value='true'".(($f)?" selected":"")." onClick='javascript:document.viewform.submit()'>Flat"); */
/* 	printc("<option value='false'".((!$f)?" selected":"")." onClick='javascript:document.viewform.submit()'>Threaded"); */
	printc("<option value='true'".(($f)?" selected":"").">Flat");
	printc("<option value='false'".((!$f)?" selected":"").">Threaded");
	printc("</select>");
	printc("</td><td>");

	$r = $_SESSION['order'];
	printc("<select name='order'>");
/* 	printc("<option value='true'".(($r)?" selected":"")." onClick='javascript:document.viewform.submit()'>Recent First"); */
/* 	printc("<option value='false'".((!$r)?" selected":"")." onClick='javascript:document.viewform.submit()'>Recent Last"); */
	printc("<option value='1'".(($order == 1)?" selected":"").">Recent First");
	printc("<option value='2'".(($order == 2)?" selected":"").">Recent Last");
	if ($_SESSION[auser]==$site_owner) {
		printc("<option value='3'".(($order == 3)?" selected":"").">Rating");
		printc("<option value='4'".(($order == 4)?" selected":"").">Author");
	}
	printc("</select>");
	printc("<input type=submit class='button' value='Change'>");
	printc("</td></tr></table>");
	printc("</form>");	
	printc("</th></tr>");
	printc("</table>");
	
	//printc("showposts=$showposts<br>");
	//printc("showallauthors=$showallauthors<br><br>");
	//printpre ($siteObj);
	if ($showposts == 2 && $showallauthors == 1) {
		printc("Posts to this assessment are currently viewable only be the site owner, <i>$siteowner</i>.  Shown here are only your posts and any replies to your post by <i>$siteowner</i>.");
		if ($_SESSION[auser]==$site_owner) {
			printc("<br><div style='font-size: 9px'> To make posts to this assessment available for discussion by all participants, edit the display options for this content block and select Show Posts.</div>");
		}
	} else if ($showposts == 1 && $showallauthors == 2) {
		printc("Author of posts to this discussion or assessment are known only to the site owner, <i>$siteowner</i>.  Other participants will not see your name associated with your posts.");
		if ($_SESSION[auser]==$site_owner) {
			printc("<br><div style='font-size: 9px'> To make authors known to all participants, edit the display options for this content block and select Show Authors.</div>");
		}
	} else if ($showposts == 2 && $showallauthors == 2) {
		printc("Posts to this assessment are currently viewable only be the site owner, <i>$siteowner</i>.  Shown here are only your posts and any replies to your post by <i>$siteowner</i>.");
		if ($_SESSION[auser]==$site_owner) {
			printc("<br><div style='font-size: 9px'> To make posts and their authors viewable by all participants, edit the display options for this content block and select both Show Authors and Show Posts.</div>");
		}
	}

	
	printc("</th>");
	printc("</tr>");
	
	

	$ds = & new discussion(&$storyObj);
	if ($f) $ds->flat(); // must be called before _fetchchildren();
	
	if ($order == 1) {
		$ds->recentfirst();
	} else if ($order == 2) {
		$ds->recentlast();
	} else if ($order == 3) {
		$ds->rating();
	} else if ($order == 4) {
		$ds->author();
	}
	
	$ds->_fetchchildren();
	
	$ds->opt("showcontent",true);
	$ds->opt("showauthor",false);
	$ds->opt("showtstamp",false);
	$ds->opt("useoptforchildren",true);
	$ds->getinfo = $getinfo;
	
			
	// outputAll is a function in objects/discussion.inc.php object
	$ds->outputAll($storyObj->hasPermission("discuss"),($_SESSION[auser]==$site_owner),true,$showposts,$showallauthors,$mailposts);
	if (!$ds->count()) printc("<tr><td>There have been no posts to this discussion.</td></tr>");
}
		
printc("<tr><td align=left><br><a href=index.php?action=site&".$getinfo2.">".spchars($pageObj->getField('title'))."</a> > in depth</td></tr>");
printc("</table>");

printc("</tr></td>");
printc("</table>");
printc("<BR><BR>");

?>
<!--<div align=right><input type=button value="Close Window" onClick="window.close()"></div>-->