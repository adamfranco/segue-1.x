<? // site.inc.php	-- allows logged in people to view a site

include("includes.inc.php");
db_connect($dbhost, $dbuser, $dbpass, $dbdb); 

$query = "select * from media order by addedtimestamp";
$r = db_query($query);
$count = $missing = 0;
print "<table>";
while ($a = db_fetch_assoc($r)) {
	/* print "<br>file_exists: ".$uploaddir."/".$a[site_id]."/".$a[name]."<br>"; */
	if (file_exists($uploaddir."/".$a[site_id]."/".$a[name])) {
	} else {
		print "<tr><td>File missing!</td><td><b>Date:</b> ".$a[addedtimestamp]."</td><td><b>addedby:</b> ".$a[addedby]."</td><td><b>Site:</b> ".$a[site_id]."</td><td><b>Name:</b> ".$a[name]."</td></tr>";
		$missing++;
	}
	$count++;
}
print "</table><br>";

print "Total Files: $count <br>";
print "Total Files Missing: $missing";
