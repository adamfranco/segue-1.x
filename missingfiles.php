<? // site.inc.php	-- allows logged in people to view a site

include("includes.inc.php");
db_connect($dbhost, $dbuser, $dbpass, $dbdb); 

$query = "select * from media INNER JOIN slot ON media.FK_site = slot.FK_site INNER JOIN user ON media.FK_createdby = user_id order by media_updated_tstamp";
$r = db_query($query);
$count = $missing = 0;
print "<table>";
while ($a = db_fetch_assoc($r)) {
	/* print "<br />file_exists: ".$uploaddir."/".$a[site_id]."/".$a[name]."<br />"; */
	if (file_exists($uploaddir."/".$a[slot_name]."/".$a[media_tag])) {
	} else {
		print "<tr><td>File missing!</td><td><b>Date:</b> ".$a[media_updated_tstamp]."</td><td><b>addedby:</b> ".$a[user_uname]."</td><td><b>Site:</b> ".$a[slot_name]."</td><td><b>Name:</b> ".$a[media_tag]."</td></tr>";
		$missing++;
	}
	$count++;
}
print "</table><br />";

print "Total Files: $count <br />";
print "Total Files Missing: $missing";
