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

$story->fetchFromDB();
$site_owner=$story->owningSiteObj->getField("addedby");

















?>
<html>
<head>
<title>Full Content/Discussion</title>
<? include("themes/common/logs_css.inc.php"); ?>
</head>

<body>
<table width=100% id="maintable" cellspacing=1>
<tr><td>
	<table cellspacing=1 width=100%>
		<? if ($fulltext) print "<tr><th align=left>".(($story->getField("title"))?spchars($story->getField("title")):"Extended Text")."</th></tr><tr><td style='padding-bottom: 15px'>$fulltext</td></tr>"; ?>
		<?
		
		// output discussions?
		if ($story->getField("discuss")) {
			print "<tr>";
			print "<th align=left>Discussions</th>";
			print "</tr>";
			
			
			$ds = & new discussion($story->id);
			if ($_SESSION["flat_discussion"]) $ds->flat();
			else $ds->threaded();
			
			$ds->opt("showcontent",true);
			
			$ds->outputAll($story->hasPermission("discuss"),$_SESSION[auser]==$_site_owner);
		}
		
		?>
	</table>

</tr></td>
</table>