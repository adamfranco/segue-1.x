<? /* $Id$ */

require("objects/objects.inc.php");
$content = '';

ob_start();
session_start();

// include all necessary files
include("includes.inc.php");

include("$themesdir/common/header.inc.php");




$partialstatus = 1;$site =& new site($_REQUEST[site]);
$section =& new section($_REQUEST[site],$_REQUEST[section], &$site);
$page =& new page($_REQUEST[site],$_REQUEST[section],$_REQUEST[page], &$section);
$story =& new story($_REQUEST[site],$_REQUEST[section],$_REQUEST[page],$_REQUEST[story], &$page);
$getinfo = "site=".$site->name."&section=".$second->id."&page=".$page->id."&story=".$story->id;

$story->fetchFromDB();
$site_owner=$story->owningSiteObj->getField("addedby");

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
<html>
<head>
<title>Full Content/Discussion</title>
<? include("themes/common/logs_css.inc.php"); ?>
<style type="text/css">

th { font-size: 12px; }

.subject { font-weight: bolder; }

.info { color: #888; }
a.info { color: #a77; }

</style>
</head>

<body>
<table width=100% id="maintable" cellspacing=1>
<tr><td>
	<table cellspacing=1 width=100%>
		<? if ($fulltext) print "<tr><th align=left>".(($story->getField("title"))?spchars($story->getField("title")):"Content")."</th></tr><tr><td style='padding-bottom: 15px'>$fulltext</td></tr>"; ?>
		<?
		
		// output discussions?
		if ($story->getField("discuss")) {
			print "<tr>";
			print "<th align=left>Discussion</th>";
			print "</tr>";
			
			
			$ds = & new discussion($story->id);
			$ds->_fetchchildren();
			if ($ds->count()) {
				$ds->opt("showcontent",true);
				$ds->getinfo = $getinfo;
				
				$ds->outputAll($story->hasPermission("discuss"),($_SESSION[auser]==$site_owner),true);
			} else print "<tr><td>There have been no posts to this discussion.</td></tr>";
		}
		
		?>
	</table>

</tr></td>
</table>
<? print "<pre>"; print_r($ds) ?>