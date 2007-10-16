<? /* $Id$ */

require("objects/objects.inc.php");
$content = '';

ob_start();
session_start();


// include all necessary files
include("includes.inc.php");


//if ($_SESSION['ltype'] != 'admin') exit;

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

// Clean the old entries from the logs if we have not done so yet
if ($cfg[logexpiration] && !$_SESSION['__logs_cleaned']) {
	$removalTStamp = strtotime($cfg[logexpiration].' days ago');
	$date = date('Ymd000000', $removalTStamp);
	
	if ($removalTStamp && $date) {
//		print "Removing logs with timestamp less than $date";
		$query = 
			"DELETE FROM
				log
			WHERE
				log_tstamp < '".addslashes($date)."'
		";
		
		db_query($query);	
		$_SESSION['__logs_cleaned'] = TRUE;
	}
} 

//if ($_REQUEST[order]) $order = $_REQUEST[order];
$order = $_REQUEST[order];
$enddate = $_REQUEST[enddate];
$startdate = $_REQUEST[startdate];
$type = $_REQUEST[type];
$site = $_REQUEST[site];
$user = $_REQUEST[user];
$_auser = $_REQUEST[_auser];
$_luser = $_REQUEST[_luser];


if ($_REQUEST[clear]) {
	$type = "";
	$user = "";
	$site = "";
	$_auser = "";
	$_luser = "";
	$enddate = "";
	$startdate = "";
	$order = "";
}

$w = array();
if ($_REQUEST[type]) $w[]="log_type='".addslashes($type)."'";
if ($_REQUEST[user]) $w[]="log_desc like '%".addslashes($user)."%'";
if ($_REQUEST[_luser]) $w[]="FK_luser='".addslashes($_luser)."'";
if ($_REQUEST[_auser]) $w[]="FK_auser='".addslashes($_auser)."'";

if ($_SESSION[ltype] != 'admin') {
	$w[]="slot_name LIKE '%".addslashes($site)."%'";
	
} else {
	if ($_REQUEST[site] != "") $w[]="slot_name LIKE '%$site%'";
	if ($startdate) {
		$w[]="log_tstamp > $startdate";
		$order = "log_tstamp ASC";
	}
	if ($enddate) {
		$w[]="log_tstamp < $enddate";
		if (!$_REQUEST[startdate]) $order = "log_tstamp DESC";
	}
}

//if (!$order) $order = "log_tstamp DESC";

if (!isset($order)
	|| !preg_match('/^[a-z0-9_.]+( (ASC|DESC))?$/i', $order))
	$order = "log_tstamp DESC";

$orderby = " ORDER BY $order";


if ($_REQUEST[hideadmin]) $w[]="log_type NOT LIKE 'change_auser'";
	
if (count($w)) $where = " WHERE ".implode(" AND ",$w);

$query = "
	SELECT 
		COUNT(*) AS log_count
	FROM 
		log
			LEFT JOIN
		slot
			ON
		log.FK_slot = slot.slot_id
			LEFT JOIN
		user AS user1
			ON
		log.FK_luser = user1.user_id
			LEFT JOIN
		user AS user2
			ON
		log.FK_auser = user2.user_id		
	$where";
//	print "<pre>".print_r($query)."</pre>";
$r=db_query($query); 
$a = db_fetch_assoc($r);
$numlogs = $a[log_count];


if (isset($_REQUEST['lowerlimit']))
	$lowerlimit = intval($_REQUEST['lowerlimit']);
else
	$lowerlimit = 0;

if ($lowerlimit < 0) 
	$lowerlimit = 0;


$limit = " LIMIT $lowerlimit,30";

$query = "
SELECT 
		log_type,
		log_tstamp,
		log_desc,
		FK_siteunit AS siteunit,
		log_siteunit_type AS siteunit_type,
		user1.user_uname AS luser,
		log.FK_luser AS luser_id,
		user2.user_uname AS auser,
		log.FK_auser AS auser_id,
		slot_name,
		FK_site AS site_id
	FROM 
		log
			LEFT JOIN
		slot
			ON
		log.FK_slot = slot.slot_id
			LEFT JOIN
		user AS user1
			ON
		log.FK_luser = user1.user_id
			LEFT JOIN
		user AS user2
			ON
		log.FK_auser = user2.user_id
		
	$where   	 
        $orderby 	 
        $limit";

//printpre($query);
$r = db_query($query);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>View Logs</title>
	<? include("themes/common/logs_css.inc.php"); ?>
	<script type="text/JavaScript">
	// <![CDATA[
	
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
	
	// ]]>
	</script>
</head>
<body>
	
	<div align='right' class='bg'>
	<?
	/******************************************************************************
	 * Get site id for links to participation section
	 ******************************************************************************/
		
	$siteObj =&new site($site);
	$siteid = $siteObj->id;
	
	
	if ($_SESSION['ltype']=='admin') {
		print "<table width='100%'  class='bg'><tr><td class='bg'>
		Logs: <a href='viewsites.php?$sid&amp;site=$site'>sites</a>
		 | users
		</td><td align='right' class='bg'>
		<a href='users.php?$sid&amp;site=$site'>add/edit users</a> | 
		<a href='classes.php?$sid&amp;site=$site'>add/edit classes</a> | 
		<a href='add_slot.php?$sid&amp;site=$site'>add/edit slots</a> |
		<a href='update.php?$sid&amp;site=$site'>segue updates</a>
		</td></tr></table>";
	}
	if ($site) {
		if (isclass($site)) print "<a href='add_students.php?$sid&amp;name=$site&amp;scope=".$_REQUEST['scope']."&amp;storyid=".$_REQUEST['storyid']."'>Roster</a> |";
		print " <a href='email.php?$sid&amp;siteid=$siteid&amp;site=$site&amp;action=list&amp;order=user_fname&amp;scope=".$_REQUEST['scope']."&amp;storyid=".$_REQUEST['storyid']."'>Participation</a>";
		print " | Logs";
	}
	
	?>
	
	</div>
	<div class='bg'>
		<? print $content; ?>
		
	</div>
	
	<table cellspacing='1' width='100%' id='maintable' style='margin-top: 5px;'>
		<tr>
			<td colspan='6'>
				<table width='100%'>
					<tr>
						<td>
							<form action='<?echo "$PHP_SELF?$sid"?>/' method='post' name='searchform'>
								<?
								if ($_SESSION['ltype'] != 'admin') {
									print "\n\t\t\t\t\t\t\t\t<input type='hidden' name='site' value='$site' />";
									print "\n\t\t\t\t\t\t\t\tLogs of $site <br />";
								}
								print "\n\t\t\t\t\t\t\t\t<input type='hidden' name='scope' value='".$_REQUEST['scope']."' />";
								print "\n\t\t\t\t\t\t\t\t<input type='hidden' name='storyid' value='".$_REQUEST['storyid']."' />";
								
								$r1 = db_query("SELECT DISTINCT log_type FROM log ORDER BY log_type asc");
								?>
								
								type: 
								<select name='type'>
									<option value=''>all</option>
								<?
								while ($a=db_fetch_assoc($r1))
									print "\n\t\t\t\t\t\t\t\t\t<option".(($type==$a[log_type])?" selected":"").">$a[log_type]</option>";
								?>
								
								</select>
							
								<?
								if ($_SESSION['ltype'] == 'admin') {
								?>
								
									user: <input type='text' name='user' size='15' value='<?echo $user?>' />
									site: <input type='text' name='site' size='15' value='<?echo $site?>' />
									<? print "\n\t\t\t\t\t\t\t\thide admin: <input type='checkbox' name='hideadmin' value='1'".(($hideadmin)?" checked='checked'":"")." />"; ?>
									
									<br />
									start date (yyyymmdd): <input type='text' name='startdate' size='10' value='<?echo $startdate?>' /> 
									end date (yyyymmdd): <input type='text' name='enddate' size='10' value='<?echo $enddate?>' /> 
						
								<? } ?>	
								<input type='submit' value='go' />
								<input type='submit' name='clear' value='clear' />
								<input type='hidden' name='order' value='<? echo $order ?>' />
								<input type='hidden' name='_auser' value='<? echo $_auser ?>' />
								<input type='hidden' name='_luser' value='<? echo $_luser ?>' />
								<? print "\n\t\t\t\t\t\t\t\t<br />Total log entries:".$numlogs; ?>
							</form>
						</td>
						<td align='right'>
						
							<?
							$tpages = ceil($numlogs/30);
							$curr = ceil(($lowerlimit+30)/30);
							$prev = $lowerlimit-30;
							if ($prev < 0) $prev = 0;
							$next = $lowerlimit+30;
							if ($next >= $numlogs) $next = $numlogs-30;
							if ($next < 0) $next = 0;
							print "\n\t\t\t\t\t\t\t\t$curr of $tpages ";
							if ($prev != $lowerlimit)
								print "\n\t\t\t\t\t\t\t\t<input type='button' value='&lt;&lt;' onclick='window.location=\"$PHP_SELF?$sid&amp;enddate=$enddate&amp;startdate=$startdate&amp;lowerlimit=$prev&amp;type=$type&amp;user=$user&amp;hideadmin=$hideadmin&amp;site=$site&amp;order=$order&amp;_auser=$_auser&amp;_luser=$_luser\"' />";
							if ($next != $lowerlimit && $next > $lowerlimit)
								print "\n\t\t\t\t\t\t\t\t<input type='button' value='&gt;&gt;' onclick='window.location=\"$PHP_SELF?$sid&amp;enddate=$enddate&amp;startdate=$startdate&amp;lowerlimit=$next&amp;type=$type&amp;user=$user&amp;hideadmin=$hideadmin&amp;site=$site&amp;order=$order&amp;_auser=$_auser&amp;_luser=$_luser\"' />";
							?>
							
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
		
		<?
			print "\n\t\t\t<th><a href='#' onclick=\"changeOrder('";
			if ($order =='log_tstamp asc') print "log_tstamp desc";
			else print "log_tstamp asc";
			print "')\" style='color: #000'>Time";
			if ($order =='log_tstamp asc') print " &or;";
			if ($order =='log_tstamp desc') print " &and;";	
			print "</a></th>";
			
			print "\n\t\t\t<th><a href='#' onclick=\"changeOrder('";
			if ($order =='log_type asc') print "log_type desc";
			else print "log_type asc";
			print "')\" style='color: #000'>Type";
			if ($order =='log_type asc') print " &or;";
			if ($order =='log_type desc') print " &and;";	
			print "</a></th>";
			
			print "\n\t\t\t<th><a href='#' onclick=\"changeOrder('";
			if ($order =='luser asc') print "luser desc";
			else print "luser asc";
			print "')\" style='color: #000'>luser";
			if ($order =='luser asc') print " &or;";
			if ($order =='luser desc') print " &and;";	
			print "</a></th>";
			
			print "\n\t\t\t<th><a href='#' onclick=\"changeOrder('";
			if ($order =='auser asc') print "auser desc";
			else print "auser asc";
			print "')\" style='color: #000'>auser";
			if ($order =='auser asc') print " &or;";
			if ($order =='auser desc') print " &and;";	
			print "</a></th>";
			
			print "\n\t\t\t<th><a href='#' onclick=\"changeOrder('";
			if ($order =='slot_name asc') print "slot_name desc";
			else print "slot_name asc";
			print "')\" style='color: #000'>Site";
			if ($order =='slot_name asc') print " &or;";
			if ($order =='slot_name desc') print " &and;";	
			print "</a></th>";
		
			print "\n\t\t\t<th><a href='#' onclick=\"changeOrder('";
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
				print "\n\t\t<tr>";
				print "\n\t\t\t<td class='td$color' style='white-space: nowrap; color: #";
					if (strstr("add_site, delete_site, classgroups",$a[log_type])) 
						print "F90";
					else if (strstr("login, change_auser",$a[log_type])) 
						print "000";
					else
						print "00C";
				print "'>";
				if (strncmp($today, $a[log_tstamp], 8) == 0 || strncmp($yesterday, $a[log_tstamp], 8) == 0) print "<b>";
				print timestamp2usdate($a[log_tstamp],1);
				if (strncmp($today, $a[log_tstamp], 8) == 0 || strncmp($yesterday, $a[log_tstamp], 8) == 0) print "</b>";
				print "</td>";
				print "\n\t\t\t<td class='td$color' style='color: #";
					if (strstr("add_site, delete_site, classgroups",$a[log_type])) 
						print "F90";
					else if (strstr("login, change_auser",$a[log_type])) 
						print "000";
					else
						print "00C";
				print "'>$a[log_type]</td>";
				
				print "\n\t\t\t<td class='td$color'><a href='#' onclick=\"selectLUser('".$a[luser]."')\"  style='color: #000;'>".(($a[luser])?$a[luser]:$a[luser_id])."</a></td>";
				
				print "\n\t\t\t<td class='td$color'><a href='#' onclick=\"selectAUser('".$a[auser]."')\"  style='color: #000;'>".(($a[auser])?$a[auser]:$a[auser_id])."</a></td>";
				print "\n\t\t\t<td class='td$color'>";
					if ($a[site_id]) print "<a href='#' onclick='opener.window.location=\"index.php?$sid&amp;action=site&amp;site=$a[slot_name]\"'>";
					print stripslashes($a[slot_name]);
					if ($a[site_id]) print "</a>";
				print "</td>";
				print "\n\t\t\t<td class='td$color'>";
					if ($a[siteunit_type] == "section") print "<a href='#' onclick='opener.window.location=\"index.php?$sid&amp;action=site&amp;site=$a[slot_name]&amp;section=$a[siteunit]\"'>";
					print "$a[log_desc]";
					if ($a[siteunit_type] == "section") print "</a>";
				print "</td>";
				print "\n\t\t</tr>";
				$color = 1-$color;
			}
		} else {
			print "\n\t\t<tr>\n\t\t\t<td colspan='6'>No log entries.</td>\n\t\t</tr>";
		}
		?>
	
	</table>
	<br />
	<div align='right'>
		<input type='button' value='Close Window' onclick='window.close()' />
	</div>
</body>
</html>