<? /* $Id$ */
//moves all userfiles from subdirectories to site directory in segue_userfiles
//renames files by adding incremental number to end of file name

print "<a href='update_media.php'>Next Step: update_media.php</a><br><br>";

include("functions.inc.php");
include("dbwrapper.inc.php");
include("config.inc.php");

//uploaddir is defined in config.inc.php

//$mediadir = opendir($uploaddir);
$mediapath = $uploaddir;
$dirName = opendir($mediapath);
print $mediapath."<br>";
print $mediadir."<br>";

$count = 0;

while ($entry = readdir($dirName)) {
	print $entry."<br>";
	$firstChar = substr($entry, 0, 1);
	
	 if ($firstChar != "." && $entry != "." && $entry != ".." && is_dir($mediapath."/".$entry)) {
 		
	 	$dirName2 = opendir($mediapath."/".$entry);
	 	while ($entry2 = readdir($dirName2)) {
	 		$firstChar = substr($entry2, 0, 1);
	 		print " &nbsp; &nbsp; &nbsp; &nbsp; ".$entry2;
	//		if ($entry2 == "." || $entry2 == "..") print "<br>";	
 			 		
	 		if ($firstChar != "." && $entry2 != "." && $entry2 != ".." && is_dir($mediapath."/".$entry."/".$entry2)) {
			 	$dirName3 = opendir($mediapath."/".$entry."/".$entry2);
			 	while ($entry3 = readdir($dirName3)) {
			 		if ($entry3 == ".") print "<br>";	
			 		print " &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; ".$entry3;
		 				 			 		
			 		if ($entry3 != "." && $entry3 != ".." && is_dir($mediapath."/".$entry."/".$entry2."/".$entry3)) {
						print "<b> Here is a fourth level directory! </b><br>";

			 		} else {
			 			$firstChar = substr($entry3, 0, 1);
		 				$src = $mediapath."/".$entry."/".$entry2."/".$entry3;
		 				$dest = $mediapath."/".$entry."/".$entry3;
			 			if ($firstChar != "." && !is_dir($src)) {
			 				if (file_exists($dest)) {
			 					print "File $dest already exists<br>";
				 				if (unlink($src))
				 					print " -- Successfully Deleted $src<br>";
				 				else 
				 					print "  -- <font color=red>Error deleting <b> $src </b></font><br>";
			 				} else {
			 					if (copy($src,$dest)) { 
				 					print "  -- Successfully Moved <b> $src </b> to <b>$dest</b> <br>";
				 					$count++;
					 				if (unlink($src))
					 					print " -- Successfully Deleted $src <br>";
					 				else 
					 					print "  -- <font color=red>Error deleting <b> $src </b></font><br>";
				 				} else
				 					print " -- <font color=red>Error moving <b> $src </b> to <b>$dest</b> </font><br>";		
			 				}
			 			} else print "<br>";
			 		}
			 	}

	 		} else {
	 			$firstChar = substr($entry2, 0, 1);
	 			if ($firstChar != "." && !is_dir($mediapath."/".$entry."/".$entry2)) {
	 				print " -- File is in the correct place <br>";
	 				$count++;
	 			} else {
	 				print "<br>";	 		
	 			}
	 		}
		}
	}
}

print "<br><br>NUMBER OF FILES MOVED: $count";

//$mediapath = "/www/segue_userfiles";
$dirName = opendir($mediapath);

while ($entry = readdir($dirName)) {
	print $entry."<br>";
	$firstChar = substr($entry, 0, 1);
	
	 if ($firstChar != "." && $entry != "." && $entry != ".." && is_dir($mediapath."/".$entry)) {
 		
	 	$dirName2 = opendir($mediapath."/".$entry);
	 	while ($entry2 = readdir($dirName2)) {
 			$firstChar = substr($entry2, 0, 1);
			if (($entry2 != "." && $entry2 != ".." && is_dir($mediapath."/".$entry."/".$entry2)) || ($firstChar == "." && $entry2 != "."&& $entry2 != "..")) {
				delete_complete($mediapath."/".$entry."/".$entry2);
			}
		}
	} else if ($firstChar == "." && $entry != "." && $entry != "..") {
		delete_complete($mediapath."/".$entry);
	}
}
				
				
			
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
