<? /* $Id$ */

ob_start();

include("includes.inc.php");

$site = trim($PATH_INFO," /");

if ($allowclasssites != $allowpersonalsites) {
	$type = db_get_value("sites","type","name='$site'");
//	print "$type <br>";
	if ($allowclasssites && !$allowpersonalsites) {
		if ($type != 'personal')
//			print "1";
			header("Location: $_full_uri/index.php?action=site&site=$site");
		else 
//			print "2";
			header("Location: $personalsitesurl/index.php?action=site&site=$site");
	} else if (!$allowclasssites && $allowpersonalsites) {
		if ($type == 'personal' || $type == 'system')
//			print "3";
			header("Location: $_full_uri/index.php?action=site&site=$site");
		else 
//			print "4";
			header("Location: $classsitesurl/index.php?action=site&site=$site");
	} else {
//			print "5";
		header("Location: $_full_uri/index.php?action=site&site=$site");
	}
} else {
//	print "6";
	header("Location: $_full_uri/index.php?action=site&site=$site");
}

?>
