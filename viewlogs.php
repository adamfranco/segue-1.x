<? // editor_access.php

$content = '';

ob_start();
session_start();

//output a meta tag
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

// include all necessary files
include("includes.inc.php");

//if ($ltype != 'admin') exit;

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

$orderby = " order by timestamp asc";
$w = array();
if ($type) $w[]="type='$type'";
if ($user) $w[]="content like '%$user%'";
if ($ltype != 'admin') {
	$w[]="site like '%$site%'";
} else {
	if ($site) $w[]="site like '%$site%'";
}
if ($hideadmin) $w[]="type not like 'change_auser'";
	

if (count($w)) $where = " where ".implode(" and ",$w);

$numlogs=db_num_rows(db_query("select * from logs$where"));

if (!isset($lowerlimit)) $lowerlimit = $numlogs-30;
if ($lowerlimit < 0) $lowerlimit = 0;

$limit = " limit $lowerlimit,30";

$query = "select * from logs$where$orderby$limit";

$r = db_query($query);

?>
<html>
<head>
<title>View Logs</title>

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

</style>

<table width='100%'>
<tr><td width=50%>
	<? print $content; ?>
	<? print $numlogs . " | " . $query; 
	?>
</td><td align=right>
	Logs
	| <a href=viewsites.php?$sid&site=<? echo $site ?>>Sites</a>
	| <a href=viewstudents.php?$sid&site=<? echo $site ?>>Users</a>

</td></tr>
</table>

<table cellspacing=1 width='100%'>
<tr>
	<td colspan=6>
		<table width='100%'>
		<tr><td>
		<form action=<?echo "$PHP_SELF?$sid"?> method=get>
		<?
		if ($ltype != 'admin') {
			print "<input type=hidden name=site value='$site'>";
			print "Logs of $site <br>";
		}
		
		$r1 = db_query("select distinct type from logs order by type asc");
		?>
		type: <select name=type>
		<option value=''>all
		<?
		while ($a=db_fetch_assoc($r1))
			print "<option".(($type==$a[type])?" selected":"").">$a[type]\n";
		?>
		</select>
		<?
		if ($ltype == 'admin') {
		?>
			user: <input type=text name=user size=15 value='<?echo $user?>'>
			site: <input type=text name=site size=15 value='<?echo $site?>'>
			<? print "hide admin: <input type=checkbox name=hideadmin value=1".(($hideadmin)?" checked":"").">"; ?>
		<? } ?>	
		<input type=submit value='go'>
		</form>
		</td>
		<td align=right>
		
		<?
		$tpages = ceil($numlogs/30);
		$curr = ceil(($lowerlimit+30)/30);
		$prev = $lowerlimit-30;
		if ($prev < 0) $prev = 0;
		$next = $lowerlimit+30;
		if ($next >= $numlogs) $next = $numlogs-30;
		if ($next < 0) $next = 0;
		print "$curr of $tpages ";
//		print "$prev $lowerlimit $next ";
		if ($prev != $lowerlimit)
			print "<input type=button value='&lt;&lt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$prev&type=$type&user=$user&hideadmin=$hideadmin&site=$site\"'>\n";
		if ($next != $lowerlimit && $next > $lowerlimit)
			print "<input type=button value='&gt;&gt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$next&type=$type&user=$user&hideadmin=$hideadmin&site=$site\"'>\n";
		?>
		</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<th>time</th>
	<th>type</th>
	<th>luser</th>
	<th>auser</th>
	<th>site</th>
	<th>text</th>
</tr>
<?
$color = 0;
$today = date(Ymd);
$yesterday = date(Ymd)-1;

if (db_num_rows($r)) {
	while ($a=db_fetch_assoc($r)) {
		print "<tr>";
		print "<td class=td$color><span style='color: #";
			if (strstr("add_site, delete_site, classgroups",$a[type])) 
				print "F90";
			else if (strstr("login, change_auser",$a[type])) 
				print "000";
			else
				print "00C";
		print "'><nobr>";
//		print "<td class=td$color><nobr>";
		if (strncmp($today, $a[timestamp], 8) == 0 || strncmp($yesterday, $a[timestamp], 8) == 0) print "<b>";
		print timestamp2usdate($a[timestamp],1);
		if (strncmp($today, $a[timestamp], 8) == 0 || strncmp($yesterday, $a[timestamp], 8) == 0) print "</b>";
		print "</nobr></span></td>";
//		print "</nobr></td>";
		print "<td class=td$color><span style='color: #";
			if (strstr("add_site, delete_site, classgroups",$a[type])) 
				print "F90";
			else if (strstr("login, change_auser",$a[type])) 
				print "000";
			else
				print "00C";
		print "'>$a[type]</span></td>";
/*		print "<td class=td$color><span style='color: #";
			if (strstr("add_site, delete_site, classgroups",$a[type])) 
				print "F90";
			else if (strstr("login, change_auser",$a[type])) 
				print "000";
			else
				print "00C";
		print "'>$a[luser]</span></td>";
*/		print "<td class=td$color>$a[luser]</td>";
/*		print "<td class=td$color><span style='color: #";
			if (strstr("add_site, delete_site, classgroups",$a[type])) 
				print "F90";
			else if (strstr("login, change_auser",$a[type])) 
				print "000";
			else
				print "00C";
		print "'>$a[auser]</span></td>";
*/		print "<td class=td$color>$a[auser]</td>";
		print "<td class=td$color>";
			if ($a[site]) print "<a href='#' onClick='opener.window.location=\"index.php?$sid&action=site&site=$a[site]\"'>";
			print "$a[site]";
			if ($a[site]) print "</a>";
		print "</td>";
		print "<td class=td$color>";
			if ($a[section]) print "<a href='#' onClick='opener.window.location=\"index.php?$sid&action=site&site=$a[site]&section=$a[section]&page=$a[page]\"'>";
			print "$a[content]";
			if ($a[section]) print "</a>";
		print "</td>";
		print "</tr>";
		$color = 1-$color;
	}
} else {
	print "<tr><td colspan=3>No log entries.</td></tr>";
}
?>
</table><BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>
