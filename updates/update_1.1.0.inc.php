<?php

/******************************************************************************
 * An interface for segue updates
 *
 * 
 ******************************************************************************/


class Update110
	extends Update {
	
	var $field01Exists = FALSE;
	var $field02Exists = FALSE;
	var $field03Exists = FALSE;
	
	
    /**
     * Returns the name of the update
     *
     * @return string Name of the update
	 */
	function getName() {
		return "Segue 1.1.0 Update";
	}
	
    /**
     * Returns the description of the update
     *
     * @return string Description of the update
	 */
	function getDescription() {
		return "This update adds fields to story table for new discussion settings. These fields are referenced by Segue v1.1.0 and 
		newer.";
	}
	
    /**
     * Returns the status of the update
     *
     * @return boolean True if update does not need to be run.
	 */
	function hasRun() {
		// check for fields
		$query = "
		DESCRIBE
			story story_discussdisplay
		";
		$r = db_query($query);
		if (db_num_rows($r))
			$this->field01Exists = TRUE;
			
		$query = "
		DESCRIBE
			story story_discussauthor
		";
		$r = db_query($query);
		if (db_num_rows($r))
			$this->field02Exists = TRUE;

		$query = "
		DESCRIBE
			story story_discussemail
		";
		$r = db_query($query);
		if (db_num_rows($r))
			$this->field03Exists = TRUE;
			
		if ($this->field01Exists && $this->field02Exists && $this->field03Exists)
			return TRUE;
		else
			return FALSE;
			
	}
	
    /**
     * Runs the update
	 */
	function run() {
	 	// Make sure that the two new fields exist 
	 	$this->hasRun();
	 	
	 	// add fields if they don't exist.
	 	if (!$this->field01Exists) {
			$query = "
			ALTER TABLE 
				story
			ADD 
				story_discussdisplay ENUM( '1', '2' ) DEFAULT '1' NOT NULL AFTER story_discussable
			";
			$r = db_query($query);
		}
		
		if (!$this->field02Exists) {
			$query = "
			ALTER TABLE 
				story
			ADD 
				story_discussauthor ENUM( '1', '2' ) DEFAULT '1' NOT NULL AFTER story_discussdisplay
			";
			$r = db_query($query);
		}
		
				if (!$this->field03Exists) {
			$query = "
			ALTER TABLE 
				story
			ADD 
				story_discussemail ENUM( '0', '1' ) DEFAULT '0' NOT NULL AFTER story_discussable
			";
			$r = db_query($query);
		}		
		
	}
}
?>
