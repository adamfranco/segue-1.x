<? ob_start();

$site = trim($PATH_INFO," /");
$script = $SCRIPT_URI;
$a = explode("/",$script);
print_r($a);
array_pop($a);
array_pop($a);
print_r($a);
$script = implode("/",$a);


header("Location: $script/index.php?action=site&site=$site");
?>