<?php

/******************************************************************************
 * An interface for segue updates
 *
 * 
 ******************************************************************************/


class Update130
	extends Update {
	
	var $field01Exists = FALSE;
	
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
		return "This update modifies Segue to allow the use of RSS Feeds as content-types.  As well it adds visitor and guest usertypes to the user table, adds a discussion label to story table, and changes the column-type of the class_semester column in the class table to allow for user-specified semesters.

<br /><br />This update will also convert all links internal to a site and all links to media files in the site to links containing placeholder tags that will be parsed out on display. This conversion may take a long time if you have many sites.";
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
		
		// check for usertype visitor and guest enum option
		$query = "
		DESCRIBE
			user user_type
		";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		
		if (!eregi("(enum\()(.*'visitor'.*)(\))", $a['Type'], $parts)) {
			$hasRun = FALSE;
			print "\nNeeds type, 'visitor' and 'guest' in ".$a['Type']."<br>";
		}
		
		// check for discusslabel field in story table
		$query = "
		DESCRIBE
			story story_discusslabel
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->field01Exists = TRUE;
			$hasRun = TRUE;
		} else {
			$hasRun = FALSE;
			print "\nNeeds discuss link label field in story table.<br>";
		}
		
		// check that the class_semester field in the class table is of type varchar
		$query = "
		DESCRIBE
			class class_semester
		";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		if ($a['Type'] == "varchar(50)") {
			$hasRun = TRUE;
		} else {
			$hasRun = FALSE;
			print "\nThe class_semester field in the class table needs to be of type varchar.<br>";
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
		
		//modify the usertype in user table	
		$query = "
		DESCRIBE
			user user_type
		";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		
	 	if (!eregi("(enum\()(.*'visitor'.*)(\))", $a['Type']) && 
	 		eregi("(enum\()(.*)(\))", $a['Type'], $parts)) {

			$query = "
			ALTER TABLE 
				user
			CHANGE 
				user_type user_type  
					ENUM(".$parts[2].",'visitor','guest') 
					DEFAULT '".$a['Default']."' 
					".(($a['Null'])?"":"NOT")." NULL
			";
			
			$r = db_query($query);
		}

		//add the story_discusslabel field to the story table	
		$query = "
		DESCRIBE
			story story_discusslabel
		";
		$r = db_query($query);
		if (db_num_rows($r) < 1) {
			$query = "
			ALTER TABLE 
				story
			ADD 
				story_discusslabel VARCHAR( 128 ) NULL AFTER story_discussauthor
			";
			$r = db_query($query);
		}
		
		
		// check that the class_semester field in the class table is of type varchar
		$query = "
		DESCRIBE
			class class_semester
		";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		if ($a['Type'] != "varchar(50)") {
			$query = "ALTER TABLE 
							class
						CHANGE 
							class_semester
							class_semester 
							VARCHAR(50) 
							DEFAULT 'w' 
							NOT NULL";
			$r = db_query($query);
		}
		
		// Go through all the sites and make sure that all of their parts have
		// the media links updated
		if (ini_get('max_execution_time') < 300)
			ini_set('max_execution_time', 300);
		
		$allSlots = slot::getAllSlots();
		print "\n<br>Starting Link->Tag conversion.";
		foreach($allSlots as $id => $sitename) {
			print "\n<br>Converting links to tags in site '".$sitename."'...";
			convertAllInteralLinksToTags($sitename);
		}
		print "\n<br>Link->Tag conversion done.";
		
	}
}


?>
