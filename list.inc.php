<? /* $Id$ */
	// this is a test default script

$pagetitle = "Segue";

$color = 0;
$sitesprinted=array();

$urls = array(
	"Educational Technology"=>"et.middlebury.edu",
	"Academic Programs"=>"www.middlebury.edu/academics/",
	"Libraries"=>"www.middlebury.edu/~lib/",
	"Middlebury College"=>"www.middlebury.edu"
);

// -------------------------------------------------------------------
add_link(leftnav,"Home","index.php?$sid","","");
add_link(leftnav,"Personal Site List<br />","index.php?$sid&action=list","","");
add_link(leftnav,"Links");
foreach ($urls as $t=>$u)
	add_link(leftnav,$t,"http://".$u,'','',"_blank");


// -------------------- query setup -------------------- 	
$orderby = " order by editedtimestamp desc";
$w = array();
$w[]="listed=1";
$w[]="type='personal'";
$w[]="active=1";
if ($_loggedin) $w[]="(viewpermissions='anyone' OR viewpermissions='midd')";
else $w[]="viewpermissions='anyone'";
if ($user) $w[]="addedby like '$user%'";
if ($title) $w[]="title like '$title%'";
if (count($w)) $where = " where ".implode(" and ",$w);

$numlogs=db_num_rows(db_query("select * from sites$where"));

if (!isset($lowerlimit)) $lowerlimit = 0;
if ($lowerlimit < 0) $lowerlimit = 0;


$limit = " limit $lowerlimit,30";

$query = "select * from sites$where$orderby$limit";

$r = db_query($query);
	
	
// -------------------- list printout -------------------- 
printc("<table cellspacing=1 width='100%'>");
printc("<tr>");
	printc("<td colspan=3>");
		printc("<table width='100%'>");
		printc("<tr><td>");
		printc("<form action=$PHP_SELF?$sid method=get>");
		
//		printc("site: <input type='text' name=name size=15 value='$name'>");
		printc("title: <input type='text' name=title size=15 value='$title'>");
		printc("owner: <input type='text' name=user size=15 value='$user'>");
		printc("<input type=submit value='go'>");
		printc("</form>");
		printc("</td>");
		printc("<td align='right'>");
		
		$tpages = ceil($numlogs/30);
		$curr = ceil(($lowerlimit+30)/30);
		$prev = $lowerlimit-30;
		if ($prev < 0) $prev = 0;
		$next = $lowerlimit+30;
		if ($next >= $numlogs) $next = $numlogs-30;
		if ($next < 0) $next = 0;
		printc("($curr of $tpages)");
//		print "$prev $lowerlimit $next ";
		if ($prev != $lowerlimit)
			printc("<input type=button value='&lt;&lt' onClick='window.location=\"$PHP_SELF?$sid&action=list&lowerlimit=$prev&type=$type&user=$user\"'>\n");
		if ($next != $lowerlimit && $next > $lowerlimit)
			printc("<input type=button value='&gt;&gt' onClick='window.location=\"$PHP_SELF?$sid&action=list&lowerlimit=$next&type=$type&user=$user\"'>\n");
		
		printc("</td>");
		printc("</tr>");
		printc("</table>");
	printc("</td>");
printc("</tr>");
printc("<tr>");
	printc("<th>time last modified</th>");
//	printc("<th>site</th>");
	printc("<th>title</th>");
	printc("<th>owner</th>");
printc("</tr>");

$color = 0;

if (db_num_rows($r)) {
	while ($a=db_fetch_assoc($r)) {
		printc("<tr>");
		printc("<td class=td$color><nobr>");
		$time = timestamp2usdate($a[editedtimestamp],1);
		printc("$time");
		printc("</nobr></td>");
//		printc("<td class=td$color>$a[name]</td>");
		printc("<td class=td$color>");
		printc("<a href=\"index.php?$sid&action=site&site=$a[name]\"'>");
		printc("$a[title]");
		printc("</a>");
		printc("</td>");
		printc("<td class=td$color>");
		printc("$a[addedby]");
		printc("</td>");
		printc("</tr>");
		$color = 1-$color;
	}
} else {
	printc("<tr><td colspan=3>No sites listed</td></tr>");
}

printc("</table><br />");



$sitefooter .= "<div align='right' style='color: #999; font-size: 10px;'>by <a style='font-weight: normal; text-decoration: underline' href='mailto: gabe@schine.net'>Gabriel Schine</a>, <a href='mailto:achapin@middlebury.edu' style='font-weight: normal; text-decoration: underline'>Alex Chapin</a>,  and <a href='mailto:afranco@middlebury.edu' style='font-weight: normal; text-decoration: underline'>Adam Franco</a></div>";
