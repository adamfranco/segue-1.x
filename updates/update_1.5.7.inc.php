<?php

/******************************************************************************
 * An interface for segue updates
 *
 * 
 ******************************************************************************/


class Update157
	extends Update {
	
	var $field01Exists = FALSE;
	
	/**
     * Returns the name of the update
     *
     * @return string Name of the update
	 */
	function getName() {
		return "Segue 1.5.7 Update";
	}
	
    /**
     * Returns the description of the update
     *
     * @return string Description of the update
	 */
	function getDescription() {
		return "This update modifies Segue's tables in order to allow users
		to add metadata to files they upload.
	";
	}
	
    /**
     * Returns the status of the update
     *
     * @return boolean True if update does not need to be run.
	 */
	function hasRun() {
		$hasRun = TRUE;
		
		// check for title_part field in media table
		$query = "
		DESCRIBE
			media title_part
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->field01Exists = TRUE;
			$hasRun = TRUE;
		} else {
			$hasRun = FALSE;
			print "\n Needs metadata fields in media table.<br />";
		}
						
		return $hasRun;	
	}
	
    /**
     * Runs the update
	 */
	function run() {
	
	 	// modify the section_hide_sidebar option
	 	$query = "
		DESCRIBE
			media title_part
		";
		$r = db_query($query);
		if (db_num_rows($r) < 1) {
			$query = "
			ALTER TABLE 
				media
			ADD `is_published` TINYINT( 1 ) DEFAULT '0' NOT NULL AFTER `media_size` ,
			ADD `title_whole` VARCHAR( 255 ) AFTER `is_published` ,
			ADD `title_part` VARCHAR( 255 ) AFTER `title_whole` ,
			ADD `author` VARCHAR( 255 ) AFTER `title_part` ,
			ADD `pagerange` VARCHAR( 255 ) AFTER `author` ,
			ADD `publisher` VARCHAR( 255 ) AFTER `pagerange` ,
			ADD `pubyear` INT( 4 ) AFTER `publisher` ,
			ADD `isbn` VARCHAR( 100 ) AFTER `pubyear` 			
			";
			$r = db_query($query);
		}
			
	}
}


?>
