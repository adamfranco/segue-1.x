<? 
//deletes personal or class sites.
/* To split appart personal and class sites:
	1. Back up your tables and scripts!
	2. Make two copies of your scripts, userfiles, and database. ie:
		sitesdb1
		sitesdb1_userfiles
		sitesdb2
		sitesdb2_userfiles
	3. Set up your configs.
	4. Make sure that both sites are working properly.
	5. chmod -R 777 sitesdb1_userfiles	Repeat for #2
	6. Run this script from both script directories.

*/

include("functions.inc.php");
include("dbwrapper.inc.php");
include("config.inc.php");
 
// connect to the database
db_connect($dbhost, $dbuser, $dbpass, $dbdb);

if (!$allowclasssites) $where = "(type='class' OR type='other')";
if (!$allowpersonalsites) $where = "type='personal'";

$query = "SELECT * FROM sites where $where";
$r = db_query($query);
//print $query."<br>";

while ($a = db_fetch_assoc($r)) {
		$site_id = $a[name];
		$query = "DELETE FROM stories WHERE site_id='$site_id'";		
		print $query."<br>";		
		db_query($query);
		$query = "DELETE FROM pages WHERE site_id='$site_id'";		
		print $query."<br>";		
		db_query($query);
		$query = "DELETE FROM sections WHERE site_id='$site_id'";		
		print $query."<br>";		
		db_query($query);
		$query = "DELETE FROM sites WHERE name='$site_id'";		
		print $query."<br>";		
		db_query($query);
		$query = "DELETE FROM logs WHERE site='$site_id'";		
		print $query."<br>";		
		db_query($query);
		$query = "DELETE FROM media WHERE site_id='$site_id'";		
		print $query."<br>";		
		db_query($query);
		
		if ($site_id && $site_id != "" && $site_id != "." && $site_id != ".." && $site_id != " ") {
			$dir = $uploaddir."/".$site_id;
			print "$dir<br>";
			delete_complete($dir);
		}

		print "<br>";
}
//Clean out old logs.
$query = "DELETE FROM logs WHERE (site='NULL' OR site='')";		
print $query."<br>";
db_query($query);



function delete_complete($file) {
//	chmod($file,0777);
	if (is_dir($file)) {
		 $handle = opendir($file);
		 while($filename = readdir($handle)) {
			  if ($filename != "." && $filename != "..") {
				  delete_complete($file."/".$filename);
			  }
		 }
	closedir($handle);
	 rmdir($file);
	} else {
	unlink($file);
	}
} 

?>
