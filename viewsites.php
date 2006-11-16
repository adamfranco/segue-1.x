<? /* $Id$ */

// we need to include object files before session_start() or registered
// objects will be broken.
include("objects/objects.inc.php");

$content = '';

ob_start();
session_start();


// include all necessary files
include("includes.inc.php");

//if ($ltype != 'admin') exit;

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

if ($clear) {
	$type = "";
	$user = "";
	$site = "";
	$title = "";
	//$type = "%";
	//$active = "%";
}

if ($_REQUEST[order]) 
	$order = $_REQUEST[order];
	
if (!isset($order)
	|| !preg_match('/^[a-z0-9_.]+( (ASC|DESC))?$/i', $order))
	$order = "editedtimestamp DESC";

$orderby = " ORDER BY $order";

$w = array();
//if ($_REQUEST[type]) $w[]="slot_type like '%$type%'";
if ($_REQUEST[user]) $w[]="user_uname like '%".addslashes($user)."%'";
//if ($site) $w[]="site like '%$name%'";
if ($_REQUEST[site]) $w[]="slot_name like '%".addslashes($site)."%'";
if ($_REQUEST[title]) $w[]="site_title like '%".addslashes($title)."%'";
//if ($_REQUEST[active]) $w[]="site_active like '%$active%'";
if (count($w)) $where = " where ".implode(" and ",$w);

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
	$where
";

$r=db_query($query); 
$a = db_fetch_assoc($r);
$numlogs = $a[log_count];

if (isset($_REQUEST['lowerlimit']))
	$lowerlimit = intval($_REQUEST['lowerlimit']);
else
	$lowerlimit = 0;

if ($lowerlimit < 0) 
	$lowerlimit = 0;

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
	$where $orderby $limit";


$r = db_query($query);

/******************************************************************************
 * Get site id for links to participation section
 ******************************************************************************/
	
	$siteObj =&new site($name);
	$siteid = $siteObj->id;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>View Logs</title>
	
	<? include("themes/common/logs_css.inc.php"); ?>
	
	<script type="text/JavaScript">
	// <![CDATA[
	
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
		Logs: sites
		 | <a href='viewlogs.php?$sid&amp;site=$site'>users</a>
		</td><td align='right' class='bg'>
		<a href='users.php?$sid&amp;site=$site'>add/edit users</a> | 
		<a href='classes.php?$sid&amp;site=$site'>add/edit classes</a> | 
		<a href='add_slot.php?$sid&amp;site=$site'>add/edit slots</a> |
		<a href='update.php?$sid&amp;site=$site'>segue updates</a>
		</td></tr></table>";
	}
	if ($site) {
		if (isclass($site)) print "<a href='add_students.php?$sid&amp;name=$site'>Roster</a> |";
		print " <a href='email.php?$sid&amp;siteid=$siteid&amp;site=$site&amp;action=list&amp;scope=site'>Participation</a>";
		print " | <a href='viewlogs.php?$sid&amp;site=$site'>Logs</a>";
	}
	
	
	?>
	
	</div>
	<div class='bg'>
		<? print $content; ?>
	
	</div>
	
	<table cellspacing='1' width='100%' id='maintable' style='margin-top: 5px;'>
		<tr>
			<td colspan='8'>
				<table width='100%'>
					<tr>
						<td>
							<form action='<?echo "$PHP_SELF?$sid"?>' method='post' name='searchform'>
								<?								
								if ($ltype != 'admin') {
									print "Activity on $site";
								} else {
								?>
								
									site: <input type='text' name='site' size='10' value='<?echo $site?>' />
									title: <input type='text' name='title' size='10' value='<?echo $title?>' />
									user: <input type='text' name='user' size='10' value='<?echo $user?>' />
									<input type='submit' value='go' />
									<input type='submit' name='clear' value='clear' />
									<input type='hidden' name='order' value='<? echo $order ?>'/>
								<? } 
								print "\n\t\t\t\t\t\t\t\t\t<br />Total sites found:".$numlogs;
								?>
								
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
							print "$curr of $tpages ";
					//		print "$prev $lowerlimit $next ";
							if ($prev != $lowerlimit)
								print "\n\t\t\t\t\t\t\t\t\t<input type='button' value='&lt;&lt;' onclick='window.location=\"$PHP_SELF?$sid&amp;lowerlimit=$prev&amp;type=$type&amp;user=$user&amp;title=$title&amp;site=$site&amp;order=$order\"' />";
							if ($next != $lowerlimit && $next > $lowerlimit)
								print "\n\t\t\t\t\t\t\t\t\t<input type='button' value='&gt;&gt;' onclick='window.location=\"$PHP_SELF?$sid&amp;lowerlimit=$next&amp;type=$type&amp;user=$user&amp;title=$title&amp;site=$site&amp;order=$order\"' />";
							?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
		<?
			print "\n\t\t\t<th><a href='#' onclick=\"changeOrder('";
			if ($order =='editedtimestamp desc') print "editedtimestamp asc";
			else print "editedtimestamp desc";
			print "')\" style='color: #000'>Time";
			if ($order =='editedtimestamp asc') print " &or;";
			if ($order =='editedtimestamp desc') print " &and;";	
			print "</a></th>";
			
			print "\n\t\t\t<th><a href='#' onclick=\"changeOrder('";
			if ($order =='name asc') print "name desc";
			else print "name asc";
			print "')\" style='color: #000'>Site";
			if ($order =='name asc') print " &or;";
			if ($order =='name desc') print " &and;";	
			print "</a></th>";
			
			print "\n\t\t\t<th><a href='#' onclick=\"changeOrder('";
			if ($order =='active asc') print "active desc";
			else print "active asc";
			print "')\" style='color: #000'>Active";
			if ($order =='active asc') print " &or;";
			if ($order =='active desc') print " &and;";	
			print "</a></th>";
			
			print "\n\t\t\t<th><a href='#' onclick=\"changeOrder('";
			if ($order =='type asc') print "type desc";
			else print "type asc";
			print "')\" style='color: #000'>Type";
			if ($order =='type asc') print " &or;";
			if ($order =='type desc') print " &and;";	
			print "</a></th>";
		
			print "\n\t\t\t<th><a href='#' onclick=\"changeOrder('";
			if ($order =='theme asc') print "theme desc";
			else print "theme asc";
			print "')\" style='color: #000'>Theme";
			if ($order =='theme asc') print " &or;";
			if ($order =='theme desc') print " &and;";	
			print "</a></th>";
			
			print "\n\t\t\t<th><a href='#' onclick=\"changeOrder('";
			if ($order =='title asc') print "title desc";
			else print "title asc";
			print "')\" style='color: #000'>Title";
			if ($order =='title asc') print " &or;";
			if ($order =='title desc') print " &and;";	
			print "</a></th>";
			
			print "\n\t\t\t<th><a href='#' onclick=\"changeOrder('";
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
				print "\n\t\t<tr>";
				print "\n\t\t\t<td class='td$color' style='white-space: nowrap;'>";
					print "\n\t\t\t\t<a href='viewlogs.php?$sid&amp;site=$a[name]' style='color: #000;'>";
					//print "$yesterday";
					if (strncmp($today, $a[editedtimestamp], 8) == 0 || strncmp($yesterday, $a[editedtimestamp], 8) == 0) print "<b>";
					print timestamp2usdate($a[editedtimestamp],1);
					if (strncmp($today, $a[editedtimestamp], 8) == 0 || strncmp($yesterday, $a[editedtimestamp], 8) == 0) print "</b>";
					print "</a>";
				print "\n\t\t\t</td>";
				print "\n\t\t\t<td class='td$color'>$a[name]</td>";
				print "\n\t\t\t<td class='td$color' style='color: #".(($a[active])?"090'>active":"900'>inactive")."</td>";
				print "\n\t\t\t<td class='td$color'>".((group::getClassesFromName($a[name]))?"group - ":"")."$a[type]</td>";
				print "\n\t\t\t<td class='td$color'>$a[theme]</td>";
				print "\n\t\t\t<td class='td$color'>";
				print "\n\t\t\t\t<a href='#' onclick='opener.window.location=\"index.php?$sid&amp;action=site&amp;site=$a[name]\"'>";
				print stripslashes($a[title]);
				print "</a>";
				print "\n\t\t\t</td>";
				print "\n\t\t\t<td class='td$color'>";
				print "$a[addedbyfull] ($a[addedby])";
				print "</td>";
				print "\n\t\t</tr>";
				$color = 1-$color;
			}
		} else {
			print "\n\t\t<tr>\n\t\t\t<td colspan='7'>No sites found based on above criteria.</td>\n\t\t</tr>";
		}
		?>
	</table>
	<br />
	<div align='right'>
		<input type='button' value='Close Window' onclick='window.close()' />
	</div>
</body>
</html>