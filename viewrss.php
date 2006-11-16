<? /* $Id$ */

require("objects/objects.inc.php");
// include all necessary files
//include("includes.inc.php");
$content = '';

ob_start();
session_start();


// include all necessary files
include("includes.inc.php");


//if ($ltype != 'admin') exit;

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

//if ($_REQUEST[order]) $order = $_REQUEST[order];
$site = $_REQUEST[site];

$recent_site_edits = recent_site_edits($site);
$recent_discussion = recent_discussion($site);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>RSS Feeds</title>

<? include("themes/common/logs_css.inc.php"); ?>

<table width='100%' cellpadding="5" cellspacing="5">
<tr>
<td align='left'>  
All the content blocks on Segue pages that are public are available in RSS format.  The RSS feeds available here are aggregations of content across all public sections and pages of a given Segue site.
</td>
</tr><tr>
<td align='left'>
<?
print "<img border='0' src='".$cfg[themesdir]."/common/images/rss_icon02.png' alt='rss' /> ";
print "<a href='".$_full_uri."/index.php?site=$site&amp;action=rss&amp;scope=allcontent' target='new_window'>";
print "Recently Edited Content RSS</a><br />";
print "This is an RSS feed of recently edited content on this site that is publicly viewable.";
print "</td></tr>";
print "<tr><td align='left'>";
print "<img border='0' src='".$cfg[themesdir]."/common/images/rss_icon02.png' alt='rss' /> ";
print "<a href='".$_full_uri."/index.php?site=$site&amp;action=rss&amp;scope=alldiscuss' target='new_window'>";
print "Recent Discussion Posts RSS</a><br />";
print "This is an RSS feed of recent discussions on this site that are publicly viewable.";


?>
</td>
</tr></table>
<br />
<div align='right'><input type='button' class='button' value='Close Window' onclick='window.close()' /></div>
