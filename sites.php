<? ob_start();

include("config.inc.php");

$site = trim($PATH_INFO," /");

header("Location: $_full_uri/index.php?action=site&site=$site");
?>
