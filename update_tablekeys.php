<? 
//adds classes currently in LDAP to class table in Segue
//this allows for listing of students in a class, adding sites for students that are related to a class
//maintaining a history of classes in Segue

print "<a href='update_filepath.php'>Next Step: update_filepath.php</a><br><br>";

include("functions.inc.php");
include("dbwrapper.inc.php");
include("config.inc.php");
 
// connect to the database
db_connect($dbhost, $dbuser, $dbpass, $dbdb);


// adds page_id to each record of stories table

$query = "SELECT * FROM sites";
$r = db_query($query);
while ($a = db_fetch_assoc($r)) {
	$sections = decode_array(db_get_value("sites","sections","name='$a[name]'"));
	foreach ($sections as $sn) {
		$sna = db_get_line("sections","id=$sn");
		$chg = array();
		$chg[] = "site_id='$a[name]'";
		$query = "update sections set " . implode(",",$chg) . " where id=$sn";
		print $query . "<BR>";
		if (count($chg)) db_query($query);
		
		$pages = decode_array($sna['pages']);
		foreach ($pages as $p) {
			$pa = db_get_line("pages","id=$p");
			$chg = array();
			$chg[] = "site_id='$a[name]'";
			$chg[] = "section_id='$sn'";
			$query = "update pages set " . implode(",",$chg) . " where id=$p";
			print "--> ".$query . "<BR>";
			if (count($chg)) db_query($query);
	
			$stories = decode_array(db_get_value("pages","stories","id=$p"));
			foreach ($stories as $s) {
				$sa = db_get_line("stories","id=$s");
				$chg = array();
				$chg[] = "site_id='$a[name]'";
				$chg[] = "section_id='$sn'";
				$chg[] = "page_id='$p'";
				$query = "update stories set " . implode(",",$chg) . " where id=$s";
				print "--> ".$query . "<BR>";
				if (count($chg)) db_query($query);
			}
		}
	}
}


?>