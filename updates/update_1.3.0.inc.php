<?php

/******************************************************************************
 * An interface for segue updates
 *
 * 
 ******************************************************************************/


class Update130
	extends Update {
	
	/**
     * Returns the name of the update
     *
     * @return string Name of the update
	 */
	function getName() {
		return "Segue 1.3.0 Update";
	}
	
    /**
     * Returns the description of the update
     *
     * @return string Description of the update
	 */
	function getDescription() {
		return "This update modifies Segue to allow the use of RSS Feeds as content-types.";
	}
	
    /**
     * Returns the status of the update
     *
     * @return boolean True if update does not need to be run.
	 */
	function hasRun() {
		$hasRun = TRUE;
		
		// check for rss enum option
		$query = "
		DESCRIBE
			story story_display_type
		";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		if (!eregi("(enum\()(.*'rss'.*)(\))", $a['Type'], $parts)) {
			$hasRun = FALSE;
			print "\nNeeds type, 'rss' in ".$a['Type']."<br>";
		}
		
		// check for rss cache dir
		global $cfg;
		$path = realpath($cfg[uploaddir]);
		if (!file_exists($path."/RSScache/autocache/")) {
			$hasRun = FALSE;
			print "\nRSS cache path, ".$path."/RSScache/autocache/".", doesn't exist.<br>";
		}
		
		return $hasRun;	
	}
	
    /**
     * Runs the update
	 */
	function run() {
	 	// modify the story_display_type option
	 	$query = "
		DESCRIBE
			story story_display_type
		";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		
	 	if (!eregi("(enum\()(.*'rss'.*)(\))", $a['Type']) && 
	 		eregi("(enum\()(.*)(\))", $a['Type'], $parts)) {

			$query = "
			ALTER TABLE 
				story
			CHANGE 
				story_display_type story_display_type 
					ENUM(".$parts[2].",'rss') 
					DEFAULT '".$a['Default']."' 
					".(($a['Null'])?"":"NOT")." NULL
			";
			
			$r = db_query($query);
		}
		
		// create the cache directory if it doesn't exist
		// check for rss cache dir
		global $cfg;
		$path = realpath($cfg[uploaddir]);
		
		if (!file_exists($path."/RSScache/")) {
			mkdir ($path."/RSScache/", 0770);
		}
		
		if (!file_exists($path."/RSScache/autocache/")) {
			mkdir ($path."/RSScache/autocache/", 0770);
		}		

	}
}


?>