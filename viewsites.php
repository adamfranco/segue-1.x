<? /* $Id$ */

$content = '';

ob_start();
session_start();

//output a meta tag
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

// include all necessary files
include("includes.inc.php");

//if ($ltype != 'admin') exit;

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

if ($clear) {
	$type = "";
	$user = "";
	$site = "";
	$title = "";
}

if (!isset($order)) $order = "editedtimestamp desc";
$orderby = " order by $order";

$w = array();
if ($type) $w[]="type='$type'";
if ($user) $w[]="addedby like '%$user%'";
//if ($site) $w[]="site like '%$name%'";
if ($site) $w[]="name like '%$site%'";
if ($title) $w[]="title like '%$title%'";
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

<? include("themes/common/logs_css.inc.php"); ?>

<script lang="JavaScript">

function changeOrder(order) {
	f = document.searchform;
	f.order.value=order;
	f.submit();
}

</script>

<table width='100%' class='bg'>
<tr><td  align=right class='bg'>
	<a href=viewlogs.php?$sid&site=<? echo $site ?>>Logs</a>
	| Sites
	| <a href=viewstudents.php?$sid&site=<? echo $site ?>>Users</a>

</td></tr>
<tr><td class='bg'>
	<? print $content; ?>
	<? print $numlogs . " | " . $query; ?>


</td></tr>
</table>

<table cellspacing=1 width='100%' style='border: 1px solid #555'>
<tr>
	<td colspan=8>
		<table width='100%'>
		<tr><td>
		<form action=<?echo "$PHP_SELF?$sid"?> method=post name='searchform'>
		<?
		// $r1 = db_query("select distinct type from sites order by type asc");
		?>
		<!-- type: <select name=type>
		<option value=''>all -->
		<?
		//while ($a=db_fetch_assoc($r1))
		//	print "<option".(($type==$a[type])?" selected":"").">$a[type]\n";
		
		if ($ltype != 'admin') {
			print "Activity on $site";
		} else {
		?>
			<!-- </select> -->
			site: <input type=text name=site size=15 value='<?echo $site?>'>
			title: <input type=text name=title size=15 value='<?echo $title?>'>
			user: <input type=text name=user size=15 value='<?echo $user?>'>
			<input type=submit value='go'>
			<input type=submit name='clear' value='clear'>
			<input type=hidden name='order' value='<? echo $order ?>'>
		<? } ?>
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
			print "<input type=button value='&lt;&lt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$prev&type=$type&user=$user&title=$title&site=$site&order=$order\"'>\n";
		if ($next != $lowerlimit && $next > $lowerlimit)
			print "<input type=button value='&gt;&gt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$next&type=$type&user=$user&title=$title&site=$site&order=$order\"'>\n";
		?>
		</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
<?
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='editedtimestamp desc') print "editedtimestamp asc";
	else print "editedtimestamp desc";
	print "')\" style='color: #000'>Time";
	if ($order =='editedtimestamp asc') print " &or;";
	if ($order =='editedtimestamp desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='name asc') print "name desc";
	else print "name asc";
	print "')\" style='color: #000'>Site";
	if ($order =='name asc') print " &or;";
	if ($order =='name desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='active asc') print "active desc";
	else print "active asc";
	print "')\" style='color: #000'>Active";
	if ($order =='active asc') print " &or;";
	if ($order =='active desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='type asc') print "type desc";
	else print "type asc";
	print "')\" style='color: #000'>Type";
	if ($order =='type asc') print " &or;";
	if ($order =='type desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='viewpermissions asc') print "viewpermissions desc";
	else print "viewpermissions asc";
	print "')\" style='color: #000'>View";
	if ($order =='viewpermissions asc') print " &or;";
	if ($order =='viewpermissions desc') print " &and;";	
	print "</a></th>";

	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='theme asc') print "theme desc";
	else print "theme asc";
	print "')\" style='color: #000'>Theme";
	if ($order =='theme asc') print " &or;";
	if ($order =='theme desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='title asc') print "title desc";
	else print "title asc";
	print "')\" style='color: #000'>Title";
	if ($order =='title asc') print " &or;";
	if ($order =='title desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='addedby asc') print "addedby desc";
	else print "addedby asc";
	print "')\" style='color: #000'>Owner";
	if ($order =='addedby asc') print " &or;";
	if ($order =='addedby desc') print " &and;";	
	print "</a></th>";
?>
</tr>
<?
$color = 0;
$today = date(Ymd);
$yesterday = date(Ymd)-1;

if (db_num_rows($r)) {
	while ($a=db_fetch_assoc($r)) {
		print "<tr>";
		print "<td class=td$color><nobr>";
			print "<a href='viewlogs.php?$sid&site=$a[name]' style='color: #000;'>";
			//print "$yesterday";
			if (strncmp($today, $a[editedtimestamp], 8) == 0 || strncmp($yesterday, $a[editedtimestamp], 8) == 0) print "<b>";
			print timestamp2usdate($a[editedtimestamp],1);
			if (strncmp($today, $a[editedtimestamp], 8) == 0 || strncmp($yesterday, $a[editedtimestamp], 8) == 0) print "</b>";
			print "</nobr>";
			print "</a>";
		print "</td>";
		print "<td class=td$color>$a[name]</td>";
		print "<td class=td$color><span style='color: #".(($a[active])?"090'>active":"900'>inactive")."</span></td>";
		print "<td class=td$color>$a[type]</td>";
		print "<td class=td$color><span style='color: #";
			if ($a[viewpermissions] == 'anyone') print "000";
			if ($a[viewpermissions] == 'midd') print "00c";
			if ($a[viewpermissions] == 'class') print "900";
		print "'>$a[viewpermissions]</span></td>";
		print "<td class=td$color>$a[theme]</td>";
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
