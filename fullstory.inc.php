<? /* $Id$ */

//require("objects/objects.inc.php");
$content = '';

//ob_start();
//session_start();

// include all necessary files
//include("includes.inc.php");

//include("$themesdir/common/header.inc.php");

if (($tmp = $_REQUEST['flat_discussion'])) {
	$_SESSION['flat_discussion'] = ($tmp=='true')?true:false;
}


$partialstatus = 1;$site =& new site($_REQUEST[site]);
$section =& new section($_REQUEST[site],$_REQUEST[section], &$site);
$page =& new page($_REQUEST[site],$_REQUEST[section],$_REQUEST[page], &$section);
$story =& new story($_REQUEST[site],$_REQUEST[section],$_REQUEST[page],$_REQUEST[story], &$page);
$getinfo = "site=".$site->name."&section=".$section->id."&page=".$page->id."&story=".$story->id."&detail=".$story->id;


$story->fetchFromDB();
$story->owningSiteObj->fetchFromDB();
//$site_owner=slot::getOwner($story->owningSiteObj->name);
$site_owner=$story->owningSiteObj->owner;
//print_r($story->owningSiteObj);
//print $site_owner;

// get the correct shorttext
if ($story->getField("type") == 'story') {
	$smalltext = $story->getField("shorttext");
	$fulltext = $story->getField("longertext");
	//print "$smalltext - $fulltext";
	if (!$fulltext || $fulltext=='') $fulltext = $smalltext;
	$fulltext = stripslashes($fulltext);
	if ($story->getField("texttype") == 'text') $fulltext = htmlbr($fulltext);
}
if ($story->getField("type") == 'image') {
	$filename = urldecode(db_get_value("media","media_tag","media_id=".$story->getField("longertext")));
	$dir = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","slot_name","media_id=".$story->getField("longertext"));
	$imagepath = "$uploadurl/$dir/$filename";
	$fulltext = "<div style='text-align: center'><br><img src='$imagepath' border=0></div>";
/* 	if ($story->getField("title")) $fulltext .= "<tr><td align=center><b>".spchars($story->getField("title"))."</b></td></tr>"; */
	if ($story->getField("shorttext")) $fulltext .= "<br>".stripslashes($story->getField("shorttext"));
	$fulltext .= "";
}
if ($story->getField("type") == 'file') {
	$fulltext = "<br>";
	$fulltext .= makedownloadbar($story);
}

?>
<!--<html>
<head>
<title>Full Content/Discussion</title>-->
<? //include("themes/common/logs_css.inc.php"); ?>
<style type="text/css">

.subject { font-weight: bolder; }

.info { color: #888; }
a.info { color: #a77; }
th.info { color: #FFFFFF; }

.content {
	border-bottom: 1px solid #000;
}

.header {
	font-size: 14px;
	border-bottom: 1px solid #000;
}

</style>
<!--</head>

<body>-->
<?
/******************************************************************************
 * print out shory and discussion (if any)
 ******************************************************************************/

printc("<table width=100% id='maintable' cellspacing=1>");
printc("<tr><td>");
	printc("<table cellspacing=1 width=100%>");
		 if ($fulltext) {
		 	printc("<tr><td align=left><b>".(($story->getField('title'))?spchars($story->getField('title')):'&nbsp;')."</b></td></tr>");
		 	printc("<tr><td style='padding-bottom: 15px; font-size: 12px'>$smalltext</td></tr>");
		 	printc("<tr><td style='padding-bottom: 15px; font-size: 12px'>$fulltext</td></tr>");
		}
		
		// output discussions?
		if ($story->getField("discuss")) {			
 			printc("<td align=left><table width=100% border=0 cellspacing=0 cellpadding=0><tr><td align=left class=header>Discussion</td>");
			printc("<td align=right class=content>");
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
			
			
			$ds = & new discussion(&$story);
			if ($f) $ds->flat(); // must be called before _fetchchildren();
			$ds->_fetchchildren();
			
			$ds->opt("showcontent",true);
			$ds->opt("showauthor",false);
			$ds->opt("showtstamp",false);
			$ds->opt("useoptforchildren",true);
			$ds->getinfo = $getinfo;
			
			// outputAll is a function in discussion.inc.php object
			$ds->outputAll($story->hasPermission("discuss"),($_SESSION[auser]==$site_owner),true);
			if (!$ds->count()) printc("<tr><td>There have been no posts to this discussion.</td></tr>");
		}
		

	printc("</table>");

printc("</tr></td>");
printc("</table>");
printc("<BR><BR>");

?>
<!--<div align=right><input type=button value="Close Window" onClick="window.close()"></div>-->