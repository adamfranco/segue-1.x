<? /* $Id$ */

// we need to include object files before session_start() or registered
// objects will be broken.
include("objects/objects.inc.php");

$content = '';

ob_start();
session_start();

//output a meta tag
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

// include all necessary files
include("includes.inc.php");

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

if ($clear) {
	$type = "";
	$user = "";
	$site = "";
	$title = "";
	//$type = "%";
	//$active = "%";
}

if ($_REQUEST[order]) $order = $_REQUEST[order];
if (!isset($order)) $order = "editedtimestamp DESC";
$orderby = " ORDER BY $order";

$w = array();$wExtra=array();
//if ($_REQUEST[type]) $w[]="slot_type like '%$type%'";
if ($cfg[allowpersonalsites] && $cfg[allowclasssites])
	$w[]="(slot_type='personal' OR slot_type='class' OR slot_type='other')";
else if ($cfg[allowpersonalsites]) $w[]="(slot_type='personal' OR slot_type='other')";
else if ($cfg[allowclasssites]) $w[]="slot_type='class'";
if ($_REQUEST[user]) $wExtra[]="user_uname like '%$user%'";
if ($_REQUEST[site]) $wExtra[]="slot_name like '%$site%'";
if ($_REQUEST[title]) $wExtra[]="site_title like '%$title%'";
$w[] = "site_active='1'";
$w[] = "site_listed='1'";
//if ($_REQUEST[active]) $w[]="site_active like '%$active%'";
if (count($w)) {
	$where = " where ".implode(" and ",$w);
	$where2 = " where ".implode(" and ",array_merge($w,$wExtra));
}

$query = "
	SELECT 
		COUNT(*) AS log_count
	FROM 
		slot
			INNER JOIN
		site
			ON
		FK_site = site_id
			INNER JOIN
		user
			ON
		FK_owner = user_id	
	$where";
$r=db_query($query); 
$a = db_fetch_assoc($r);
$numSites = $a[log_count];

if (!isset($lowerlimit)) $lowerlimit = 0;
if ($lowerlimit < 0) $lowerlimit = 0;


$limit = " limit $lowerlimit,30";

$query = "
	SELECT 
		slot_type AS type,
		slot_name AS name,
		site_title AS title,
		site_theme AS theme,
		site_updated_tstamp AS editedtimestamp,
		site_active AS active,
		user_uname AS addedby,
		user_fname AS addedbyfull		
	FROM
		slot
			INNER JOIN
		site
			ON
		FK_site = site_id
			INNER JOIN
		user
			ON
		FK_owner = user_id
	$where2$orderby$limit";

$r = db_query($query);
$numlogs = db_num_rows($r);

//print $query;

?>
<html>
<head>
<title>Segue: Public Site Listing</title>

<? include("themes/common/logs_css.inc.php"); ?>

<script lang="JavaScript">

function changeOrder(order) {
	f = document.searchform;
	f.order.value=order;
	f.submit();
}

</script>

<table width='100%' class='bg'>
<tr><td class='bg'>
	<? print $content; ?>
	<? //print $numlogs . " | " . $query; ?>
	<? print "Total Active Segue Sites: ".$numSites ?>


</td></tr>
</table>

<table cellspacing=1 width='100%' id='maintable'>
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
		
		if (true) {
		?>
			<!-- </select> -->
			site: <input type=text name=site size=10 value='<?echo $site?>'>
			title: <input type=text name=title size=10 value='<?echo $title?>'>
			user: <input type=text name=user size=10 value='<?echo $user?>'>
			<!--
			type: <select name=type>
				<option<?=($type=='%')?" selected":""?>>all
				<option<?=($type=='class')?" selected":""?>>class
				<option<?=($type=='other')?" selected":""?>>other
				<option<?=($type=='personal')?" selected":""?>>personal
				<option<?=($type=='system')?" selected":""?>>system
				</select>
			-->
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
	
//	print "<th><a href=# onClick=\"changeOrder('";
//	if ($order =='active asc') print "active desc";
//	else print "active asc";
//	print "')\" style='color: #000'>Active";
//	if ($order =='active asc') print " &or;";
//	if ($order =='active desc') print " &and;";	
//	print "</a></th>";
	
	print "<th><a href=# onClick=\"changeOrder('";
	if ($order =='type asc') print "type desc";
	else print "type asc";
	print "')\" style='color: #000'>Type";
	if ($order =='type asc') print " &or;";
	if ($order =='type desc') print " &and;";	
	print "</a></th>";
	
/* 	print "<th><a href=# onClick=\"changeOrder('"; */
/* 	if ($order =='viewpermissions asc') print "viewpermissions desc"; */
/* 	else print "viewpermissions asc"; */
/* 	print "')\" style='color: #000'>View"; */
/* 	if ($order =='viewpermissions asc') print " &or;"; */
/* 	if ($order =='viewpermissions desc') print " &and;";	 */
/* 	print "</a></th>"; */

//	print "<th><a href=# onClick=\"changeOrder('";
//	if ($order =='theme asc') print "theme desc";
//	else print "theme asc";
//	print "')\" style='color: #000'>Theme";
//	if ($order =='theme asc') print " &or;";
//	if ($order =='theme desc') print " &and;";	
//	print "</a></th>";
	
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
			if (strncmp($today, $a[editedtimestamp], 8) == 0 || strncmp($yesterday, $a[editedtimestamp], 8) == 0) print "<b>";
			print timestamp2usdate($a[editedtimestamp],1);
			if (strncmp($today, $a[editedtimestamp], 8) == 0 || strncmp($yesterday, $a[editedtimestamp], 8) == 0) print "</b>";
			print "</nobr>";
		print "</td>";
		print "<td class=td$color>$a[name]</td>";
//		print "<td class=td$color><span style='color: #".(($a[active])?"090'>active":"900'>inactive")."</span></td>";
		print "<td class=td$color>".((group::getClassesFromName($a[name]))?"group - ":"")."$a[type]</td>";
/* 		print "<td class=td$color><span style='color: #"; */
/* 			if ($a[viewpermissions] == 'anyone') print "000"; */
/* 			if ($a[viewpermissions] == 'midd') print "00c"; */
/* 			if ($a[viewpermissions] == 'class') print "900"; */
/* 		print "'>$a[viewpermissions]</span></td>"; */
//		print "<td class=td$color>$a[theme]</td>";
		print "<td class=td$color>";
		print "<a href='#' onClick='opener.window.location=\"index.php?$sid&action=site&site=$a[name]\"'>";
		print stripslashes($a[title]);
		print "</a>";
		print "</td>";
		print "<td class=td$color>";
		print "$a[addedbyfull] ($a[addedby])";
		print "</td>";
		print "</tr>";
		$color = 1-$color;
	}
} else {
	print "<tr><td colspan=5>No sites found based on above criteria.</td></tr>";
}
?>
</table><BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>

<?
// debug output -- handy :)
/* print "<pre>"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* print "\n\n"; */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n"; */

/*
 if (is_object($thisPage)) { 
 	print "\n\n"; 
 	print "thisPage:\n"; 
 	print_r($thisPage); 
 } /*else if (is_object($thisSection)) { 
	print "\n\n"; 
 	print "thisSection:\n"; 
 	print_r($thisSection); 
 } else if (is_object($thisSite)) { 
 	print "\n\n"; 
 	print "thisSite:\n"; 
 	print_r($thisSite); 
 } */
 
/* print "</pre>"; */