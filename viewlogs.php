<? // editor_access.php


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

$orderby = " order by timestamp asc";
$w = array();
if ($type) $w[]="type='$type'";
if ($user) $w[]="content like '$user%'";
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

<? print $content; ?>
<? print $numlogs . " | " . $query; ?>
<table cellspacing=1 width='100%'>
<tr>
	<td colspan=3>
		<table width='100%'>
		<tr><td>
		<form action=<?echo "$PHP_SELF?$sid"?> method=get>
		<?
		$r1 = db_query("select distinct type from logs order by type asc");
		?>
		type: <select name=type>
		<option value=''>all
		<?
		while ($a=db_fetch_assoc($r1))
			print "<option".(($type==$a[type])?" selected":"").">$a[type]\n";
		?>
		</select>
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
	<th>type</th>
	<th>text</th>
</tr>
<?
if (db_num_rows($r)) {
	while ($a=db_fetch_assoc($r)) {
		print "<tr>";
		print "<td><nobr>";
		print timestamp2usdate($a[timestamp],1);
		print "</nobr></td>";
		print "<td>$a[type]</td>";
		print "<td>";
		print "$a[content]";
		print "</td>";
		print "</tr>";
	}
} else {
	print "<tr><td colspan=3>No log entries.</td></tr>";
}
?>
</table><BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>
