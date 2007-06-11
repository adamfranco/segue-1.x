<?php

/******************************************************************************
 * An interface for segue updates
 *
 * 
 ******************************************************************************/


class Update180
	extends Update {
	
	var $field01Exists = FALSE;
	
	/**
     * Returns the name of the update
     *
     * @return string Name of the update
	 */
	function getName() {
		return "Segue 1.8.0 Update";
	}
	
    /**
     * Returns the description of the update
     *
     * @return string Description of the update
	 */
	function getDescription() {
		return "This update modifies Segue's tables in order to add support for
		ordering pages.
	";
	}
	
    /**
     * Returns the status of the update
     *
     * @return boolean True if update does not need to be run.
	 */
	function hasRun() {
		$hasRun = TRUE;
		
		// check for section_page_order field in section table
		$query = "
		DESCRIBE
			section section_page_order
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->field01Exists = TRUE;
		} else {
			$hasRun = FALSE;
			print "\n Needs section_page_order field in section table.<br />";
		}
							
		return $hasRun;	
	}
	
    /**
     * Runs the update
	 */
	function run() {
		// Make sure that the two new fields exist 
	 	$this->hasRun();
		
		// modify the page_show_versions field in the page table
		if (!$this->field01Exists) {
			$query = "
			ALTER TABLE 
				section
			ADD 
				section_page_order enum('custom','addeddesc','addedasc','editeddesc','editedasc','titleasc') NOT NULL default 'custom' after FK_media
			";
			$r = db_query($query);
		}
		
	}
}


?>
