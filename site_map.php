<? /* $Id$ */


$content = '';

ob_start();
session_start();

// include all necessary files
include("includes.inc.php");

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

$sa = db_get_line("sites","name='$site'");
?>
<html>
<head>
<title>Site Map - <? echo $sa[title] ?></title>

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

.td1 { 
	background-color: #ccc; 
}

.td0 { 
	background-color: #ddd; 
}

th { 
	background-color: #bbb; 
	font-variant: small-caps;
}

body { 
	background-color: white; 
}

body, table, td, th, input {
	font-size: 12px;
	font-family: "Verdana", "sans-serif";
}

input {
	border: 1px solid black;
	background-color: white;
	font-size: 10px;
}

</style>

<? print $content; ?>
<? 
$sections = decode_array($sa['sections']);
	
print "<table cellspacing=1 width='100%'>";	
print "<tr>";
	print "<th>Site Map - $sa[title]</th>";
print "</tr>";

$color = 0;

print "<tr>";
print "<td class=td$color style='font-variant: small-caps'><a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site\"'>$sa[title]</a></td>";
print "</tr>";
$color = 1-$color;

if (count($sections)) {
	foreach ($sections as $sec) {
		print "<tr>";
		$seca = db_get_line("sections","id=$sec");
		$secp = decode_array($seca[permissions]);
		print "<td class=td$color style='padding-left: 10px'>";
		if ($seca[type]=='section') print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$sec\"'>";
		print "$seca[title]";
		if ($seca[type]=='section') print "</a>";
//		print "<br><pre>";print_r($secp);print "</pre>";
		print "</td>";
		print "</tr>";
		$color = 1-$color;
		$pages = decode_array($seca['pages']);
		foreach ($pages as $p) {
			$pa = db_get_line("pages","id=$p");
			$pp = decode_array($pa[permissions]);
			if ($pa[type]=='divider' || $pa[type]=='heading') next;
			print "<tr>";
			print "<td class=td$color style='padding-left: 20px'>";
			print "-&gt; ";
			if ($pa[type]=='page') print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$s&page=$p\"'>";
			print "$pa[title]";
			if ($pa[type]=='page') print "</a>";
			print "</td>";
			print "</tr>";
			$color = 1-$color;
	
			$stories = decode_array($pa['stories']);
			$j=1;
			foreach ($stories as $s) {
				print "<tr>";
				$sa = db_get_line("stories","id=$s");
				$sp = decode_array($sa[permissions]);
				print "<td class=td$color style='padding-left: 40px'>";
				/*if ($sa[type]=='story')*/ print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$sec&page=$p\"'>";
				print "$j. &nbsp; $sa[title]";
				/*if ($sa[type]=='story')*/ print "</a>";
//				print "<br><pre>";print_r($sp);print "</pre>";
				print "</td>";					
				print "</tr>";
				$color = 1-$color;
				$j++;
			}
		}
	}
} else {
	print "<tr><td class=td$color colspan=4>No sections in this site.</td></tr>";
}

print "</table><BR>";

?>

<div align=right><input type=button value='Close Window' onClick='window.close()'></div>