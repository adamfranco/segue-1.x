<?

include("config.inc.php");
include("objects/objects.inc.php");
include("dbwrapper.inc.php");
include("permissions.inc.php");

global $dbuser, $dbpass, $dbdb, $dbhost;
db_connect($dbhost,$dbuser,$dbpass, $dbdb);


// site test

$site = new site("template0");
$site->fetchDown(1);
$site->buildPermissionsArray(1,1);
$site->addEditor("dradichk");
$site->setUserPermissions("dradichk","1","1","1","0","0");
$site->updatePermissionsDB(1);

echo "<pre>";

print_r ($site);

echo "</pre>";

print "<p>Total Queries: ".$_totalQueries."</p>";


?>