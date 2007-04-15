<?php

/******************************************************************************
 * An interface for segue updates
 *
 * 
 ******************************************************************************/


class Update175
	extends Update {
	
	var $field01Exists = FALSE;
	var $table01Exists = FALSE;
	
	/**
     * Returns the name of the update
     *
     * @return string Name of the update
	 */
	function getName() {
		return "Segue 1.7.5 Update";
	}
	
    /**
     * Returns the description of the update
     *
     * @return string Description of the update
	 */
	function getDescription() {
		return "This update modifies Segue's tables in order to add support for
		wiki markup and recording internal links.
	";
	}
	
    /**
     * Returns the status of the update
     *
     * @return boolean True if update does not need to be run.
	 */
	function hasRun() {
		$hasRun = TRUE;

		// check for links table
		$query = "
		DESCRIBE
			links
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->table01Exists = TRUE;
		} else {
			$hasRun = FALSE;
			print "\n Needs links table.<br />";
		}
		
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

	 	// add version table 
		if (!$this->table01Exists) {
			$query = "
				CREATE TABLE `links` (
				  `link_id` int(10) unsigned NOT NULL auto_increment,
				  `source_id` int(10) unsigned NOT NULL,
				  `source_type` enum('page','story') NOT NULL,
				  `target_id` int(10) unsigned NOT NULL,
				  `target_type` enum('site','section','page','story') NOT NULL,
				  `link_tstamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
				  `FK_luser` int(10) unsigned NOT NULL default '0',
				  `FK_auser` int(10) unsigned NOT NULL default '0',
				  PRIMARY KEY  (`link_id`),
				  KEY `FK_luser` (`FK_luser`),
				  KEY `FK_auser` (`FK_auser`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
			";
			$r = db_query($query);
		}
		
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
