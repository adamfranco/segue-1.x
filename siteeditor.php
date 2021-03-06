<? /* $Id$ */


$content = '';

ob_start();
session_start();

// include all necessary files
include_once("includes.inc.php");

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");
	
include("themes/common/header.inc.php");

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

$sitea=db_get_line("sites","name='".addslashes($site)."'");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SiteMap</title>

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
}

th { 
	background-color: #ccc; 
	font-variant: small-caps;
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

.pad { padding-left: 15px;}

.pad2 {padding-left: 30px; }

.pad, .pad2 { background-color: #eee; }

</style>

<? print $content; ?>

<table cellspacing='1' width='100%'>
<tr>
	<th colspan='5'>
		SiteMap for <? echo $sitea[title] ?>
	</th>
	<?
	print "<tr>";
	print "<td colspan='5' class='pad'>";
	$addedby = $sitea[addedby];
	$viewpermissions=$sitea[viewpermissions];
	$added = timestamp2usdate($sitea[addedtimestamp]);
	$edited = $sitea[editedtimestamp];
	$editedby = $sitea[editedby];
	print "added by <i>$addedby</i> on $added".(($editedby)?", edited on ".timestamp2usdate($edited):"");
	print "</td>";
	print "</tr>";
	
	if ($_SESSION['auser'] == $site_owner) {
		$edlist = explode(",",$sitea[editors]);
		if (count($edlist)) {
			print "<tr><td colspan='5' class='pad'>";
			print "editors (click on name to see privileges): ";
			$l = array();
			foreach ($edlist as $e) {
				$l[] = "<a href='editor_access.php?$sid&amp;site=$site&amp;user=$e' target='privileges' onclick='doWindow(\"privileges\",400,400)'>$e</a>";
			}
			print implode(", ",$l);
			print "</td></tr>";
		}
	}
	
	?>
</tr>
<tr>
	<th>title</th>
	<th>type</th>
	<th>A</th>
	<th>L</th>
	<th>options</th>
</tr>
<?
$sections = decode_array($sitea[sections]);
if (count($sections)) {
	foreach ($sections as $s) {
		$sa = db_get_line("sections","id='".addslashes($s)."'");
		print "<tr>";
		print "<td>$sa[title]</td>";
		print "<td>$sa[type]</td>";
		print "<td align='center'>".(($sa[active])?"yes":"no")."</td>";
		print "<td align='center'>".(($sa[locked])?"yes":"no")."</td>";
		print "<td align='center'>";
		if ($sa[type]=='section') print "<a href='#' onclick='opener.window.location=\"index.php?$sid&amp;action=viewsite&amp;site=$site&amp;section=$s\"'>[view]</a>";
		print "</td>";
		print "</tr>";
		print "<tr>";
		print "<td colspan='5' class='pad'>";
		$addedby = $sa[addedby];
		$viewpermissions=$sa[viewpermissions];
		$added = timestamp2usdate($sa[addedtimestamp]);
		$edited = $sa[editedtimestamp];
		$editedby = $sa[editedby];
		print "added by <i>$addedby</i> on $added".(($editedby)?", edited by <i>$editedby</i> on ".timestamp2usdate($edited):"");
		print "</td>";
		print "</tr>";
		if ($sa[type]=='url') {
			print "<tr><td colspan='5' class='pad'>";
			print "url: <i>$sa[url]</i>";
			print "</td></tr>";
		}
		
		
		$pages = decode_array($sa[pages]);
		foreach ($pages as $p) {
			$pa = db_get_line("pages","id='".addslashes($p)."'");
			$stories = decode_array($pa[stories]);
			$nums = count($stories);
			$nlocked = 0;
			foreach ($stories as $st) {
				$sta = db_get_line("stories","id='".addslashes($st)."'");
				if ($sta[locked]) $nlocked++;
			}
			
			print "<tr>";
			print "<td class='pad'><li>$pa[title]</td>";
			print "<td>$pa[type]</td>";
			print "<td align='center'>".(($sa[active])?"yes":"no")."</td>";
			print "<td align='center'>".(($pa[locked])?"yes":"no")."</td>";
			print "<td align='center'>";
			if ($pa[type]=='page') print "<a href='#' onclick='opener.window.location=\"index.php?$sid&amp;action=viewsite&amp;site=$site&amp;section=$s&amp;page=$p\"'>[view]</a>";
			print "</td>";
			print "</tr>";
			print "<tr>";
			print "<td colspan='5' class='pad2'>";
			$addedby = $pa[addedby];
			$viewpermissions=$pa[viewpermissions];
			$added = timestamp2usdate($pa[addedtimestamp]);
			$edited = $pa[editedtimestamp];
			$editedby = $pa[editedby];
			print "added by <i>$addedby</i> on $added".(($editedby)?", edited by <i>$editedby</i> on ".timestamp2usdate($edited):"");
			print "</td>";
			print "</tr>";
			print "<tr>";
			print "<td class='pad2' colspan='5'>";
			if ($pa[type]=='page') print "# stories: $nums ($nlocked locked)";
			if ($pa[type]=='url') print "url: <i>$pa[url]</i>";
			print "</td></tr>";
		}
	}
} else {
	print "<tr><td colspan='5'>No sections.</td></tr>";
}
?>
</table><br />
A = active, L = locked
<div align='right'><input type='button' value='Close Window' onclick='window.close()' /></div>
