<? /* $Id$ */

include("config.inc.php");
include("functions.inc.php");
include("dbwrapper.inc.php");

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

$dhost = "badger.middlebury.edu";
$duser = "sitesdb";
$dpass = "sitesdb#%&";
$ddb = "sitesdb";

if ($site) {
	serverCopySite($site,$dest);
}

function serverCopySite($orig,$dest) {
	$sections = decode_array(db_get_value("sites","sections","name='$orig'"));
	$nsections = array();
	foreach ($sections as $s) {
		$sa = db_get_line("sections","id=$s");
		$squery = "insert into sections set addedby='".addslashes($_SESSION['auser'])."', addedtimestamp=NOW()";
		$squery .= ",title='$sa[title]', active=$sa[active], type='$sa[type]', url='$sa[url]'";
		
		
		$pages = decode_array($sa[pages]);
		$npages = array();
		foreach ($pages as $p) {
			$pa = db_get_line("pages","id=$p");
			$pquery = "insert into pages set addedby='".addslashes($_SESSION['auser'])."', addedtimestamp=NOW()";
			$pquery .= ",ediscussion=1,archiveby='$pa[archiveby]',url='$pa[url]',type='$pa[type]',title='$pa[title]', showcreator=$pa[showcreator], showdate=$pa[showdate], locked=$pa[locked], active=$pa[active]";
			
			$stories = decode_array($pa[stories]);
			$nstories = array();
			foreach ($stories as $st) {
				$sta = db_get_line("stories","id=$st");
				$stquery = "insert into stories set addedby='".addslashes($_SESSION['auser'])."', addedtimestamp=NOW()";
				$stquery.=",type='$sta[type]',texttype='$sta[texttype]',category='$sta[category]',title='$sta[title]', discuss=$sta[discuss], discusspermissions='$sta[discusspermissions]', shorttext='$sta[shorttext]', longertext='$sta[longertext]', locked=$sta[locked], url='$sa[url]'";
				db_query($stquery);
//				print "$stquery<br />";
				$nstories[] = lastid();
			}
			
			$stories = encode_array($nstories);
			$pquery.=",stories='$stories'";
			db_query($pquery);
			$npages[]=lastid();
//			print "$pquery<br />";
		}
		
		$pages = encode_array($npages);
		$squery.=",pages='$pages'";
		db_query($squery);
		$nsections[] = lastid();
//		print "$squery<br />";
	}
	$sections = encode_array($nsections);
	$query = "update sites set sections='$sections' where name='$dest'";
	db_query($query);
//	print "$query<br />";
}