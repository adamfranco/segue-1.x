<? /* $Id$ */
include("objects/objects.inc.php");
$content = '';

ob_start();
session_start();

//output a meta tag
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

// include all necessary files
include("includes.inc.php");

//if ($ltype != 'admin') exit;

db_connect($dbhost, $dbuser, $dbpass, $dbdb);
$siteinfo = db_get_line("sites","name='$site'");
$site_type = $siteinfo[type];

if ($site_type =="class") {
	//print "<div align=center>Students in $site</div>";
}
if ($clear) {
	$type = "";
	$user = "";
	$site = "";
	$title = "";
}

if (!isset($order)) $order = "fname asc";
$orderby = " order by $order";

$w = array();
//if ($type) $w[]="type='$type'";
//if ($site) $w[]="site='$name'";
if ($user) $w[]="uname like '%$user%'";
if ($site) {
	$isgroup = ($classlist = group::getClassesFromName($site))?1:0;
	if ($isgroup) {
		$arg = "(name='";
		$arg .= implode("' or name='",$classlist);
		$arg .= "')";
		$w[]=$arg;
	} else {
		$w[]="name like '%$site%'";
	}
}
//if ($title) $w[]="title like '%$title%'";
if (count($w)) $where = " where ".implode(" and ",$w);

$numlogs=db_num_rows(db_query("select * from classes$where"));

if (!isset($lowerlimit)) $lowerlimit = 0;
if ($lowerlimit < 0) $lowerlimit = 0;


$limit = " limit $lowerlimit,30";

$query = "select * from classes$where$orderby$limit";

//$query = "select * from classes$where$limit";
//print $query;

$r = db_query($query);

?>
<html>
<head>
<title>View Logs</title>
<? include("themes/common/logs_css.inc.php"); ?>