<? // viewsites.php


$content = '';

ob_start();
session_start();

include("functions.inc.php");
include("dbwrapper.inc.php");
include("config.inc.php");
include("error.inc.php");
include("themes/themeslist.inc.php");
include("dates.inc.php");

include("authentication.inc.php");

include("permissions.inc.php");

if ($ltype != 'admin') exit;

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

$orderby = " order by editedtimestamp desc";
$w = array();
if ($type) $w[]="type='$type'";
if ($user) $w[]="addedby like '$user%'";
if ($name) $w[]="name like '$name%'";
if ($title) $w[]="title like '$title%'";
if (count($w)) $where = " where ".implode(" and ",$w);

$numlogs=db_num_rows(db_query("select * from sites$where"));

if (!isset($lowerlimit)) $lowerlimit = 0;
if ($lowerlimit < 0) $lowerlimit = 0;


$limit = " limit $lowerlimit,30";

$query = "select * from sites$where$orderby$limit";

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

th { 
	background-color: #bbb; 
	font-variant: small-caps;
}

.td1 { 
	background-color: #ccc; 
}

.td0 { 
	background-color: #ddd; 
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

<? print $content; ?>
<? print $numlogs . " | " . $query; ?>
<table cellspacing=1 width='100%'>
<tr>
	<td colspan=6>
		<table width='100%'>
		<tr><td>
		<form action=<?echo "$PHP_SELF?$sid"?> method=get>
		<?
		// $r1 = db_query("select distinct type from sites order by type asc");
		?>
		<!-- type: <select name=type>
		<option value=''>all -->
		<?
		//while ($a=db_fetch_assoc($r1))
		//	print "<option".(($type==$a[type])?" selected":"").">$a[type]\n";
		?>
		<!-- </select> -->
		site: <input type=text name=name size=15 value='<?echo $name?>'>
		title: <input type=text name=title size=15 value='<?echo $title?>'>
		user: <input type=text name=user size=15 value='<?echo $user?>'>
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
			print "<input type=button value='&lt;&lt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$prev&type=$type&user=$user\"'>\n";
		if ($next != $lowerlimit && $next > $lowerlimit)
			print "<input type=button value='&gt;&gt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$next&type=$type&user=$user\"'>\n";
		?>
		</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<th>time</th>
	<th>site</th>
	<th>active</th>
	<th>type</th>
	<th>title</th>
	<th>owner</th>
</tr>
<?
$color = 0;

if (db_num_rows($r)) {
	while ($a=db_fetch_assoc($r)) {
		print "<tr>";
		print "<td class=td$color><nobr>";
		print timestamp2usdate($a[editedtimestamp],1);
		print "</nobr></td>";
		print "<td class=td$color>$a[name]</td>";
		print "<td class=td$color><span style='color: #".(($a[active])?"090'>active":"900'>inactive")."</span></td>";
		print "<td class=td$color>$a[type]</td>";
		print "<td class=td$color>";
		print "<a href='#' onClick='opener.window.location=\"index.php?$sid&action=site&site=$a[name]\"'>";
		print "$a[title]";
		print "</a>";
		print "</td>";
		print "<td class=td$color>";
		print "$a[addedby]";
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
