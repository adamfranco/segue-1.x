<? /* $Id$ */


$content = '';

ob_start();
session_start();

// include all necessary files
include("includes.inc.php");

include("$themesdir/common/header.inc.php");
$partialstatus = 1;

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

if (!insite($site,$section,$page,$story)) {
	print "Something screwed up. Seems this story isn't in the site you're viewing.";
	exit;
}

$a=db_get_line("stories","id=$story");

if ($add && candiscuss($a)) {
	if ($authortype == 'anon' && (!$author || $author=='')) error("You must either log in above or enter your name in the field below.");
	if (!$thetext || trim($thetext)=='') error("You must enter some text to post.");
	if (!$error) {
		$thetext = urlencode($thetext);
		$query = "insert into discussions set author='$author', authortype='$authortype', content='$thetext'";
		db_query($query);
		$newid = lastid();
		$discussions = decode_array($a[discussions]);
		$discussions[] = $newid;
		$discussions = $a[discussions] = encode_array($discussions);
		$query = "update stories set discussions='$discussions' where id=$story";
		db_query($query);
		$thetext = '';
	}
}

if ($del && $auser == $site_owner) {
	$discussions = decode_array($a[discussions]);
	$newa = array();
	foreach ($discussions as $d) {
		if ($d != $id) $newa[] = $d;
	}
	$discussions = $a[discussions] = encode_array($newa);
	$query = "update stories set discussions='$discussions' where id=$story";
	db_query($query);
}

$smalltext = urldecode($a[shorttext]);
$fulltext = urldecode($a[longertext]);
//print "$smalltext - $fulltext";
if (!$fulltext || $fulltext=='') $fulltext = $smalltext;
$fulltext = stripslashes($fulltext);
if ($a[texttype] == 'text') $fulltext = htmlbr($fulltext);


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
if ($fulltext) print "<tr><th align=left>".(($a[title])?spchars($a[title]):"Full Text")."</th></tr><tr><td style='padding-bottom: 15px'>$fulltext</td></tr>";

$discussions = decode_array($a[discussions]);

if ($a[discuss]) {
	print "<tr>";
	print "<th align=left>Discussions</th>";
	print "</tr>";
	
	if (count($discussions)) {
		foreach ($discussions as $id) {
			$d = db_get_line("discussions","id=$id");
			$addedby = ($d[authortype]=='user')?ldapfname($d[author]):$d[author];
			print "<tr><td>";
			print "<div style='color: #777' align=right>Posted: ".timestamp2usdate($d[timestamp])." by $addedby";
			if ($auser== $site_owner) {
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
	
	if (candiscuss($a,1)) {
		print "<tr><th align=left>Post to discussion</th></tr><tr><td>";
		print "<form name='postform' action='fullstory.php?$sid&site=$site&section=$section&page=$page&story=$story&action=$action' method=post>";
		print "<input type=hidden name='add' value=1>";
/* 		print "<input type=hidden name='story' value=$story>"; */
/* 		print "<input type=hidden name='site' value=$site>"; */
/* 		print "<input type=hidden name='section' value=$section>"; */
/* 		print "<input type=hidden name='page' value=$page>"; */
/* 		print "<input type=hidden name='action' value='$action'>"; */
		
		print "<table border=0 cellspacing=10 style='border: 0px' width=75%>";
		print "<tr><td align=right>";
		if ($_loggedin) print "Name:</td><td>$afname<input type=hidden name=authortype value='user'><input type=hidden name=author value='$auser'>";
		else print "Name:</td><td><input type=text name=author size=20 class=textfield value='$author'> <input type=hidden name=authortype value='anon'>";
		print "</td></tr>";
		print "<tr><td align=right valign=top>Text:</td><td>";
		print "<textarea name='thetext' rows=6 cols=80 class=textarea>".spchars($thetext)."</textarea></td></tr>";
		print "<tr><td colspan=2 align=right><input type=submit class=button value='Post'></td></tr></table>";
		print "</form>";
		print "</td></tr>";
	} else {
		if (!$_loggedin) {
			if ($a[discusspermissions] == 'midd') {
				print "<tr><td>You must log in above to post to this discussion.</td></tr>";
			} else if ($a[discusspermissions] == 'class') {
				print "<tr><td>You must log in above and be a member of this class to post to this discussion.</td></tr>";
			}
		} else print "<tr><td>Sorry, you are not allowed to post to this discussion.</td></tr>";
	}
}
?>
</table>


<BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>
