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
	$_auser = "";
	$_luser = "";
}

if (!isset($order)) $order = "log_tstamp ASC";
$orderby = " order by $order";

$w = array();
if ($type) $w[]="log_type='$type'";
if ($user) $w[]="log_desc like '%$user%'";
if ($_luser) $w[]="FK_luser='$_luser'";
if ($_auser) $w[]="FK_auser='$_auser'";
if ($ltype != 'admin') {
	$w[]="slot_name LIKE '%$site%'";
} else {
	if ($site) $w[]="slot_name LIKE '%$site%'";
}
if ($hideadmin) $w[]="log_type NOT LIKE 'change_auser'";
	

if (count($w)) $where = " WHERE ".implode(" AND ",$w);

$query = "
	SELECT 
		COUNT(*) AS log_count
	FROM 
		log
			LEFT JOIN
		slot
			ON
		log.FK_siteunit = slot.FK_site
			INNER JOIN
		user AS user1
			ON
		log.FK_luser = user1.user_id
			INNER JOIN
		user AS user2
			ON
		log.FK_auser = user2.user_id		
	$where";
$r=db_query($query); 
$a = db_fetch_assoc($r);
$numlogs = $a[log_count];

if (!isset($lowerlimit) && $order == 'log_tstamp ASC') $lowerlimit = $numlogs-30;
if (!isset($lowerlimit) && $order != 'log_tstamp ASC') $lowerlimit = 0;
if ($lowerlimit < 0) $lowerlimit = 0;

$limit = " LIMIT $lowerlimit,30";

$query = "
SELECT 
		log_type,
		log_tstamp,
		log_desc,
		user1.user_uname AS luser,
		user2.user_uname AS auser,
		slot_name
	FROM 
		log
			LEFT JOIN
		slot
			ON
		log.FK_site = slot.FK_site
			INNER JOIN
		user AS user1
			ON
		log.FK_luser = user1.user_id
			INNER JOIN
		user AS user2
			ON
		log.FK_auser = user2.user_id
	$where
	$orderby
	$limit";

$r = db_query($query);

?>
<html>
<head>
<title>View Logs</title>
<? include("themes/common/logs_css.inc.php"); ?>
<script lang="JavaScript">

function selectAUser(user) {
	f = document.searchform;
	f._auser.value=user;
	f._luser.value="";
	f.submit();
}

function selectLUser(user) {
	f = document.searchform;
	f._luser.value=user;
	f._auser.value="";
	f.submit();
}

function changeOrder(order) {
	f = document.searchform;
	f.order.value=order;
	f.submit();
}

</script>

<table width='100%' class='bg'>
<td align=right class='bg'>
	Logs
	| <a href=viewsites.php?<? echo $sid ?>&site=<? echo $site ?>>Sites</a>
	| <a href=viewstudents.php?<? echo $sid ?>&site=<? echo $site ?>>Users</a>

</td></tr>
<tr><td class='bg'>
	<? print $content; ?>
	<? print $numlogs . " | " . $query; ?>
</td></tr>
</table>

<table cellspacing=1 width='100%' id='maintable'>
<tr>
	<td colspan=6>
		<table width='100%'>
		<tr><td>
		<form action=<?echo "$PHP_SELF?$sid"?> method=post name='searchform'>
		<?
		if ($ltype != 'admin') {
			print "<input type=hidden name=site value='$site'>";
			print "Logs of $site <br>";
		}
		
		$r1 = db_query("SELECT DISTINCT log_type FROM log ORDER BY log_type asc");
		?>
		type: <select name=type>
		<option value=''>all
		<?
		while ($a=db_fetch_assoc($r1))
			print "<option".(($type==$a[log_type])?" selected":"").">$a[log_type]\n";
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
		<input type=submit name='clear' value='clear'>
		<input type=hidden name='order' value='<? echo $order ?>'>
		<input type=hidden name='_auser' value='<? echo $_auser ?>'>
		<input type=hidden name='_luser' value='<? echo $_luser ?>'>
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
			print "<input type=button value='&lt;&lt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$prev&type=$type&user=$user&hideadmin=$hideadmin&site=$site&order=$order&_auser=$_auser&_luser=$_luser\"'>\n";
		if ($next != $lowerlimit && $next > $lowerlimit)
			print "<input type=button value='&gt;&gt' onClick='window.location=\"$PHP_SELF?$sid&lowerlimit=$next&type=$type&user=$user&hideadmin=$hideadmin&site=$site&order=$order&_auser=$_auser&_luser=$_luser\"'>\n";
		?>
		</td>
		</tr>
		</table>
	</td>
</tr>
<tr>

<?
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='log_tstamp asc') print "log_tstamp desc";
	else print "log_tstamp asc";
	print "')\" style='color: #000'>Time";
	if ($order =='log_tstamp asc') print " &or;";
	if ($order =='log_tstamp desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='log_type asc') print "log_type desc";
	else print "log_type asc";
	print "')\" style='color: #000'>Type";
	if ($order =='log_type asc') print " &or;";
	if ($order =='log_type desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='luser asc') print "luser desc";
	else print "luser asc";
	print "')\" style='color: #000'>luser";
	if ($order =='luser asc') print " &or;";
	if ($order =='luser desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='auser asc') print "auser desc";
	else print "auser asc";
	print "')\" style='color: #000'>auser";
	if ($order =='auser asc') print " &or;";
	if ($order =='auser desc') print " &and;";	
	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='slot_name asc') print "slot_name desc";
	else print "slot_name asc";
	print "')\" style='color: #000'>Site";
	if ($order =='slot_name asc') print " &or;";
	if ($order =='slot_name desc') print " &and;";	
	print "</a></th>";

	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='log_desc asc') print "log_desc desc";
	else print "log_desc asc";
	print "')\" style='color: #000'>Text";
	if ($order =='log_desc asc') print " &or;";
	if ($order =='log_desc desc') print " &and;";	
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
		print "<td class=td$color><span style='color: #";
			if (strstr("add_site, delete_site, classgroups",$a[log_type])) 
				print "F90";
			else if (strstr("login, change_auser",$a[log_type])) 
				print "000";
			else
				print "00C";
		print "'><nobr>";
//		print "<td class=td$color><nobr>";
		if (strncmp($today, $a[log_tstamp], 8) == 0 || strncmp($yesterday, $a[log_tstamp], 8) == 0) print "<b>";
		print timestamp2usdate($a[log_tstamp],1);
		if (strncmp($today, $a[log_tstamp], 8) == 0 || strncmp($yesterday, $a[log_tstamp], 8) == 0) print "</b>";
		print "</nobr></span></td>";
//		print "</nobr></td>";
		print "<td class=td$color><span style='color: #";
			if (strstr("add_site, delete_site, classgroups",$a[log_type])) 
				print "F90";
			else if (strstr("login, change_auser",$a[log_type])) 
				print "000";
			else
				print "00C";
		print "'>$a[log_type]</span></td>";
/*		print "<td class=td$color><span style='color: #";
			if (strstr("add_site, delete_site, classgroups",$a[log_type])) 
				print "F90";
			else if (strstr("login, change_auser",$a[log_type])) 
				print "000";
			else
				print "00C";
		print "'>$a[log_luser]</span></td>";
*/		print "<td class=td$color><a href=# onClick=\"selectLUser('".$a[luser]."')\"  style='color: #000;'>$a[luser]</a></td>";
/*		print "<td class=td$color><span style='color: #";
			if (strstr("add_site, delete_site, classgroups",$a[type])) 
				print "F90";
			else if (strstr("login, change_auser",$a[type])) 
				print "000";
			else
				print "00C";
		print "'>$a[auser]</span></td>";
*/		print "<td class=td$color><a href=# onClick=\"selectAUser('".$a[auser]."')\"  style='color: #000;'>$a[auser]</a></td>";
		print "<td class=td$color>";
			if ($a[slot_name]) print "<a href='#' onClick='opener.window.location=\"index.php?$sid&action=site&site=$a[slot_name]\"'>";
			print "$a[slot_name]";
			if ($a[slot_name]) print "</a>";
		print "</td>";
		print "<td class=td$color>";
			if ($a[section_id]) print "<a href='#' onClick='opener.window.location=\"index.php?$sid&action=site&site=$a[slot_name]&section=$a[section_id]&page=$a[page_id]\"'>";
			print "$a[log_desc]";
			if ($a[section_id]) print "</a>";
		print "</td>";
		print "</tr>";
		$color = 1-$color;
	}
} else {
	print "<tr><td colspan=6>No log entries.</td></tr>";
}
?>
</table><BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>
