<? /* $Id$ */
include("objects/objects.inc.php");
$content = '';

ob_start();
session_start();


// include all necessary files
include("includes.inc.php");

//if ($ltype != 'admin') exit;

db_connect($dbhost, $dbuser, $dbpass, $dbdb);
$siteinfo = db_get_line("site INNER JOIN slot on site_id = FK_site","slot_name='$site'");
$site_type = $siteinfo[type];

if ($_REQUEST[site_type] =="class") {
	//print "<div align='center'>Students in $site</div>";
}
if ($_REQUEST[clear]) {
	$type = "";
	$user = "";
	$site = "";
	$title = "";
} else {
	$type = $_REQUEST[type];
	$user = $_REQUEST[user];
	$site = $_REQUEST[site];
	$title = $_REQUEST[title];
}

if (!isset($order)) $order = "fname asc";
$orderby = " order by $order";

$w = array();
//if ($type) $w[]="type='$type'";
//if ($site) $w[]="site='$name'";
if ($_REQUEST[user]) $w[]="user2.user_uname like '%$user%'";
if ($_REQUEST[site]) {
	$isgroup = ($classlist = group::getClassesFromName($_REQUEST[site]))?1:0;
	if ($isgroup) {
		$class_terms = array();
		foreach ($classlist as $code) {
			$terms[] = "(".generateTermsFromCode($code).")";
		}
		$arg = "(";
		$arg .= implode(" OR ",$classlist);
		$arg .= ")";
		$w[]=$arg;
	} else {
//		$w[]="class_code like '%$site%'";
		$w[] = generateTermsFromCode($site);
	}
}
//if ($title) $w[]="title like '%$title%'";
if (count($w)) $where = " where ".implode(" and ",$w);

$query = "
	SELECT 
		COUNT(*) AS log_count
	FROM 
		class
			INNER JOIN
		user AS user1
			ON
		class.FK_owner = user1.user_id
			INNER JOIN
		ugroup
			ON
		class.FK_ugroup = ugroup_id
			INNER JOIN
		ugroup_user
			ON
		ugroup_id = ugroup_user.FK_ugroup
			INNER JOIN
		user AS user2
			ON
		ugroup_user.FK_user = user2.user_id
	$where";
$r=db_query($query); 
$a = db_fetch_assoc($r);
$numlogs = $a[log_count];
if (!isset($lowerlimit)) $lowerlimit = 0;
if ($lowerlimit < 0) $lowerlimit = 0;


$limit = " limit $lowerlimit,30";

$query = "
	SELECT
		user2.user_fname AS fname,
		user2.user_uname AS uname,
		class.class_id AS id,
		user2.user_type AS type
	FROM
		class
			INNER JOIN
		user AS user1
			ON
		class.FK_owner = user1.user_id
			INNER JOIN
		ugroup
			ON
		class.FK_ugroup = ugroup_id
			INNER JOIN
		ugroup_user
			ON
		ugroup_id = ugroup_user.FK_ugroup
			INNER JOIN
		user AS user2
			ON
		ugroup_user.FK_user = user2.user_id
		
	$where$orderby$limit";

//$query = "select * from classes$where$limit";
//print $query;

$r = db_query($query);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>View Logs</title>
<? include("themes/common/logs_css.inc.php"); ?>
</head>

<script lang="JavaScript">

function selectClass(name) {
    f = document.searchform;
    f.site.value=name;
    f.submit();
}

function selectUser(user) {
    f = document.searchform;
    f.user.value=user;
    f.submit();
}

function changeOrder(order) {
    f = document.searchform;
    f.order.value=order;
//    f.test.value="Working";
    f.submit();
}

</script>
<?// print "test = $test"; ?>

<table width='100%' class='bg'>
<td align='right' class='bg'>
    <a href=viewlogs.php?$sid&site=<? echo $site ?>>Logs</a>
    | <a href=viewsites.php?$sid&site=<? echo $site ?>>Sites</a>
    | Users
</td></tr>
<tr><td class='bg'>
    <? print $content; ?>
    <? //print $numlogs . " | " . $query;
    print "Current Site Users: ".$numlogs
    ?>
</td></tr>
</table>

<table cellspacing=1 width='100%' id='maintable'>
<tr>
    <td colspan=8>
        <table width='100%'>
        <tr><td>
        <form action=<?echo "$PHP_SELF?$sid"?> name='searchform' method=post>
        <?
        // $r1 = db_query("select distinct type from sites order by type asc");
        ?>
        <!-- type: <select name=type>
        <option value=''>all -->
        <?
        //while ($a=db_fetch_assoc($r1))
        //    print "<option".(($type==$a[type])?" selected":"").">$a[type]\n";
        if ($ltype != 'admin') {
            print "Users for $site";
        } else {
        ?>
            <!-- </select> -->
            site: <input type=text name='site' size=15 value='<?echo $site?>'>
            <!--title: <input type=text name=title size=15 value='<?echo $title?>'>-->
            user: <input type=text name=user size=15 value='<?echo $user?>'>
            <input type=submit value='go'>
            <input type=submit name='clear' value='clear'>
            <input type=hidden name='order' value='<? echo $order ?>'>
        <? } ?>
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
//        print "$prev $lowerlimit $next ";
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
    if ($order =='fname asc') print "fname desc";
    else print "fname asc";
    print "')\" style='color: #000'>Name";
    if ($order =='fname asc') print " &or;";
    if ($order =='fname desc') print " &and;";    
    print "</a></th>";
    
    print "<th><a href=# onClick=\"changeOrder('";
    if ($order =='uname asc') print "uname desc";
    else print "uname asc";
    print "')\" style='color: #000'>User Name";
    if ($order =='uname asc') print " &or;";
    if ($order =='uname desc') print " &and;";    
    print "</a></th>";
    
    print "<th><a href=# onClick=\"changeOrder('";
    if ($order =='name asc') print "name desc";
    else print "name asc";
    print "')\" style='color: #000'>Site";
    if ($order =='name asc') print " &or;";
    if ($order =='name desc') print " &and;";    
    print "</a></th>";
    
    print "<th><a href=# onClick=\"changeOrder('";
    if ($order =='type asc') print "type desc";
    else print "type asc";
    print "')\" style='color: #000'>Type";
    if ($order =='type asc') print " &or;";
    if ($order =='type desc') print " &and;";    
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
        //print "<td class=td$color><nobr>";
            //print "<a href='viewlogs.php?$sid&site=$a[name]' style='color: #000;'>";
            //print "$yesterday";
            //if (strncmp($today, $a[editedtimestamp], 8) == 0 || strncmp($yesterday, $a[editedtimestamp], 8) == 0) print "<b>";
            //print timestamp2usdate($a[editedtimestamp],1);
            //if (strncmp($today, $a[editedtimestamp], 8) == 0 || strncmp($yesterday, $a[editedtimestamp], 8) == 0) print "</b>";
            //print "</nobr>";
            //print "</a>";
        //print "</td>";
        print "<td class=td$color><a href=# onClick=\"selectUser('".$a[uname]."')\"  style='color: #000;'>$a[fname]</a></td>";
        print "<td class=td$color>$a[uname]</td>";
        print "<td class=td$color><a href=# onClick=\"selectClass('".generateCourseCode($a[id])."')\"  style='color: #000;'>".generateCourseCode($a[id])."</a></td>";
        print "<td class=td$color>$a[type]</td>";
        
        /*print "<td class=td$color><span style='color: #".(($a[active])?"090'>active":"900'>inactive")."</span></td>";
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
        print "</td>"; */
        print "</tr>";
        $color = 1-$color;
    }
} else {
    print "<tr><td colspan=4>No log entries.</td></tr>";
}
?>
</table><br />
<div align='right'><input type=button value='Close Window' onClick='window.close()'></div>