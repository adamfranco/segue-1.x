<? /* $Id$ */

ob_start();

include("includes.inc.php");

$site = trim($PATH_INFO," /");
$site = ereg_replace("[^a-zA-Z0-9]+$","",$site);

if ($allowclasssites != $allowpersonalsites) {
	$type = db_get_value("slot","slot_type","slot_name='".addslashes($site)."'");
	if ($allowclasssites && !$allowpersonalsites) {
		if ($type != 'personal')
			header("Location: $_full_uri/index.php?action=site&site=$site");
		else 
			header("Location: $personalsitesurl/index.php?action=site&site=$site");
	} else if (!$allowclasssites && $allowpersonalsites) {
		if ($type == 'personal' || $type == 'system')
			header("Location: $_full_uri/index.php?action=site&site=$site");
		else 
			header("Location: $classsitesurl/index.php?action=site&site=$site");
	} else {
		header("Location: $_full_uri/index.php?action=site&site=$site");
	}
} else {
	header("Location: $_full_uri/index.php?action=site&site=$site");
}

?>
