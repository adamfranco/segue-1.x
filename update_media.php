<? 
//adds classes currently in LDAP to class table in Segue
//this allows for listing of students in a class, adding sites for students that are related to a class
//maintaining a history of classes in Segue

include("functions.inc.php");
include("dbwrapper.inc.php");
include("config.inc.php");
 
// connect to the database
db_connect($dbhost, $dbuser, $dbpass, $dbdb);



$query = "SELECT * FROM stories where (type='image' OR type='file')";
$r = db_query($query);
//print $query."<br>";

while ($a = db_fetch_assoc($r)) {

		//$id = $a['id'];
		$medianame = $a['longertext'];
		$addedtimestamp = $a['addedtimestamp'];
		$addedby = 	$a['addedby'];	
		$section_id = $a['section_id'];
		$type = $a[type];
		$site_id = $a['site_id'];
		
		//$siteinfo = db_get_line("sections","id=$section_id");		
		//$site_id = $sectioninfo['site_id'];
		//$site_id = "site_id";	
			
		$tmp = urldecode($medianame);
		$url= $uploaddir."/".$site_id."/".$tmp;
		print $url."<br>";
		$size = filesize($url);
		
		$query = "INSERT into media set site_id = '$site_id', name = '$medianame', addedtimestamp = '$addedtimestamp', addedby = '$addedby', type = '$type', size='$size'";
	
		//$query = "UPDATE stories set url = '$url' where id='$id'";
		print $query."<br>";
		db_query($query);

		$new_id = lastid();
		$query = "UPDATE stories set longertext='$new_id' where id='$a[id]'";
		print $query."<br>";
		db_query($query);

}



?>
