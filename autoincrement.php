<?
include("dbwrapper.inc.php");
include("config.inc.php");

$query = "show table status like 'stories'";
print $query . "<br>";
db_connect($dbhost, $dbuser, $dbpass, $dbdb);
$r = db_query($query);
print $r;

$info = db_fetch_assoc($r);
print $info;
print "autoincrement value: ". $info['auto_increment'];
print $info['name'];