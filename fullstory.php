<? /* $Id$ */

require("objects/objects.inc.php");
$content = '';

ob_start();
session_start();

// include all necessary files
include("includes.inc.php");

include("$themesdir/common/header.inc.php");

if (($tmp = $_REQUEST['flat_discussion'])) {
	$_SESSION['flat_discussion'] = ($tmp=='true')?true:false;
}


$partialstatus = 1;$site =& new site($_REQUEST[site]);
$section =& new section($_REQUEST[site],$_REQUEST[section], &$site);
$page =& new page($_REQUEST[site],$_REQUEST[section],$_REQUEST[page], &$section);
$story =& new story($_REQUEST[site],$_REQUEST[section],$_REQUEST[page],$_REQUEST[story], &$page);
$getinfo = "site=".$site->name."&section=".$section->id."&page=".$page->id."&story=".$story->id;

$story->fetchFromDB();
$story->owningSiteObj->fetchFromDB();
$site_owner=$story->owningSiteObj->owner);
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
<html>
<head>
<title>Full Content/Discussion</title>
<? include("themes/common/logs_css.inc.php"); ?>