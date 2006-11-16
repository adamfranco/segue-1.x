<?php

/******************************************************************************
 * An interface for segue updates
 *
 * 
 ******************************************************************************/


class Update153
	extends Update {
	
	var $field01Exists = FALSE;
	
	/**
     * Returns the name of the update
     *
     * @return string Name of the update
	 */
	function getName() {
		return "Segue 1.5.3 Update";
	}
	
    /**
     * Returns the description of the update
     *
     * @return string Description of the update
	 */
	function getDescription() {
		return "This update modifies Segue's tables in order to add new functionality
		for hiding the left sidebar.
	";
	}
	
    /**
     * Returns the status of the update
     *
     * @return boolean True if update does not need to be run.
	 */
	function hasRun() {
		$hasRun = TRUE;
		
		// check for section_hide_sidebar field in section table
		$query = "
		DESCRIBE
			section section_hide_sidebar
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->field01Exists = TRUE;
			$hasRun = TRUE;
		} else {
			$hasRun = FALSE;
			print "\n Needs section_hide_sidebar field in section table.<br />";
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
			section section_hide_sidebar
		";
		$r = db_query($query);
		if (db_num_rows($r) < 1) {
			$query = "
			ALTER TABLE 
				section
			ADD 
				section_hide_sidebar ENUM('0','1') AFTER section_active
			";
			$r = db_query($query);
		}
			
	}
}


?>
