<? /* $Id$ */

require("objects/objects.inc.php");
$content = '';

ob_start();
session_start();

// include all necessary files
include("includes.inc.php");

include("$themesdir/common/header.inc.php");
$partialstatus = 1;

if ($_REQUEST[site])$site=$_REQUEST[site];
if ($_REQUEST[section])$section=$_REQUEST[section];
if ($_REQUEST[page])$page=$_REQUEST[page];

$site =& new site($_REQUEST[site]);
$section =& new section($_REQUEST[site],$_REQUEST[section], &$site);
$page =& new page($_REQUEST[site],$_REQUEST[section],$_REQUEST[page], &$section);
$story =& new story($_REQUEST[site],$_REQUEST[section],$_REQUEST[page],$_REQUEST[story], &$page);

$story->fetchFromDB();
$site_owner=$story->owningSiteObj->getField("addedby");

/* if (!insite($site,$section,$page,$story)) { */
/* 	print "Something screwed up. Seems this story isn't in the site you're viewing."; */
/* 	exit; */
/* } */

if ($_REQUEST[add] && $story->hasPermission("discuss")) {
	if ($_REQUEST[authortype] == 'anon' && (!$_REQUEST[author] || $_REQUEST[author]=='')) error("You must either log in above or enter your name in the field below.");
	if (!$_REQUEST[thetext] || trim($_REQUEST[thetext])=='') error("You must enter some text to post.");
	if (!$error) {
		$thetext = urlencode($_REQUEST[thetext]);
		$query = "insert into discussions set author='$_REQUEST[author]', authortype='$_REQUEST[authortype]', content='$thetext'";
		db_query($query);
		$newid = mysql_insert_id();
		$story->addDiscussion($newid);
		$story->updateDB();
/* 		$discussions = decode_array($a[discussions]); */
/* 		$discussions[] = $newid; */
/* 		$discussions = $a[discussions] = encode_array($discussions); */
/* 		$query = "update stories set discussions='$discussions' where id=$story"; */
/* 		db_query($query); */
		$thetext = '';
	}
}

if ($_REQUEST[del] && $_SESSION[auser] == $site_owner) {
	$story->delDiscussion($_REQUEST[id]);
	$story->updateDB();
/* 	$discussions = decode_array($a[discussions]); */
/* 	$newa = array(); */
/* 	foreach ($discussions as $d) { */
/* 		if ($d != $id) $newa[] = $d; */
/* 	} */
/* 	$discussions = $a[discussions] = encode_array($newa); */
/* 	$query = "update stories set discussions='$discussions' where id=$story"; */
/* 	db_query($query); */
}

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

<style type='text/css'>
a {
	color: #a33;
	text-decoration: none;
}

a:hover {text-decoration: underline;}

.downloadbar {
	color: #000;
	background-color: #ddd;
	border: 1px solid #555;
	padding: 5px;
	padding-left: 15px;
}

body, table, td, th, input {
	font-family: "Verdana";
}

table {
	border: 1px solid #555;
}

th, td {
	font-size: 12px;
	border: 0px;
	background-color: #ddd;
	padding-left: 10px;
}

th { 
	color: #555;
	font-size: 18px;
	font-weight: bold;
	padding-left: 25px;
	background-color: #ccc;
	font-variant: small-caps;
}

body { 
	background-color: white; 
}


/* td { font-size: 10px; } */

input,select {
	border: 1px solid black;
	background-color: white;
	font-size: 10px;
}

.pad { padding-left: 15px;}

.pad2 {padding-left: 30px; }

.pad, .pad2 { background-color: #eee; }

.headerbox { border: 1px solid #888; padding: 6px; background-color: #eee }

.td1 { background-color: #eee; }
.td2 { background-color: #ddd; }

textarea { font-size: 11px; font-family: "Verdana"; }

</style>
</head>
<? include("$themesdir/common/status.inc.php"); ?>
<? printerr(); print $content; ?>
<table cellspacing=1 width='100%'>
<?
if ($fulltext) print "<tr><th align=left>".(($story->getField("title"))?spchars($story->getField("title")):"Full Text")."</th></tr><tr><td style='padding-bottom: 15px'>$fulltext</td></tr>";

$discussions = $story->getField("discussions");

if ($story->getField("discuss")) {
	print "<tr>";
	print "<th align=left>Discussions</th>";
	print "</tr>";
	
	if (count($discussions)) {
		foreach ($discussions as $id) {
			$d = db_get_line("discussions","id=$id");
			$addedby = ($d[authortype]=='user')?ldapfname($d[author]):$d[author];
			print "<tr><td>";
			print "<div style='color: #777' align=right>Posted: ".timestamp2usdate($d[timestamp])." by $addedby";
			if ($_SESSION[auser]== $site_owner) {
				print " <a href='fullstory.php?$sid&site=$site&section=$section&page=$page&story=$story&del=1&id=$id'>[delete]</a>";
			}
			print "</div>";
			print htmlbr(spchars(urldecode($d[content])));
			print "</td></tr>";
		}
	} else {
		print "<tr><td>No posts yet.</td></tr>";
	}
	
//	print "</td></tr>";
	
	if ($story->hasPermission("discuss")) {
		print "<tr><th align=left>Post to discussion</th></tr><tr><td>";
		print "<form name='postform' action='fullstory.php?$sid&site=$site&section=$section&page=$page&story=$story&action=$action' method=post>";
		print "<input type=hidden name='add' value=1>";
		print "<input type=hidden name='story' value=".$story->id.">";
/* 		print "<input type=hidden name='site' value=$site>"; */
/* 		print "<input type=hidden name='section' value=$section>"; */
/* 		print "<input type=hidden name='page' value=$page>"; */
/* 		print "<input type=hidden name='action' value='$action'>"; */
		
		print "<table border=0 cellspacing=10 style='border: 0px' width=75%>";
		print "<tr><td align=right>";
		if ($_loggedin) print "Name:</td><td>$_SESSION[afname]<input type=hidden name=authortype value='user'><input type=hidden name=author value='$_SESSION[auser]'>";
		else print "Name:</td><td><input type=text name=author size=20 class=textfield value='$author'> <input type=hidden name=authortype value='anon'>";
		print "</td></tr>";
		print "<tr><td align=right valign=top>Text:</td><td>";
		print "<textarea name='thetext' rows=6 cols=80 class=textarea>".spchars($thetext)."</textarea></td></tr>";
		print "<tr><td colspan=2 align=right><input type=submit class=button value='Post'></td></tr></table>";
		print "</form>";
		print "</td></tr>";
	} else {
		if (!$_loggedin) {
/* 			if ($a[discusspermissions] == 'midd') { */
/* 				print "<tr><td>You must log in above to post to this discussion.</td></tr>"; */
/* 			} else if ($a[discusspermissions] == 'class') { */
/* 				print "<tr><td>You must log in above and be a member of this class to post to this discussion.</td></tr>"; */
/* 			} */
			print "<tr><td>Sorry, you are not allowed to post to this discussion.</td></tr>";
		} else print "<tr><td>Sorry, you are not allowed to post to this discussion. Try logging in above.</td></tr>";
	}
}
?>
</table>


<BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>
