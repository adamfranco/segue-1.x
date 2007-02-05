<?php

/******************************************************************************
 * An interface for segue updates
 *
 * 
 ******************************************************************************/


class Update170
	extends Update {
	
	var $field01Exists = FALSE;
	var $field02Exists = FALSE;
	var $table01Exists = FALSE;
	
	/**
     * Returns the name of the update
     *
     * @return string Name of the update
	 */
	function getName() {
		return "Segue 1.7.0 Update";
	}
	
    /**
     * Returns the description of the update
     *
     * @return string Description of the update
	 */
	function getDescription() {
		return "This update modifies Segue's tables in order to add support for
		versioning content blocks.
	";
	}
	
    /**
     * Returns the status of the update
     *
     * @return boolean True if update does not need to be run.
	 */
	function hasRun() {
		$hasRun = TRUE;
		
		// check for story_versioning field in story table
		$query = "
		DESCRIBE
			story story_versioning
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->field01Exists = TRUE;
			$hasRun = TRUE;
		} else {
			$hasRun = FALSE;
			print "\n Needs story_versioning field in story table.<br />";
		}

		// check for version table
		$query = "
		DESCRIBE
			version
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->table01Exists = TRUE;
			$hasRun = TRUE;
		} else {
			$hasRun = FALSE;
			print "\n Needs version table.<br />";
		}
		
		// check for version_comments field in page table
		$query = "
		DESCRIBE
			page page_show_versions
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->field02Exists = TRUE;
			$hasRun = TRUE;
		} else {
			$hasRun = FALSE;
			print "\n Needs page_show_versions field in page table.<br />";
		}

						
		return $hasRun;	
	}
	
    /**
     * Runs the update
	 */
	function run() {
	
	 	// modify the story_versioning field 
	 	$query = "
		DESCRIBE
			story story_versioning
		";
		$r = db_query($query);
		if (db_num_rows($r) < 1) {
			$query = "
			ALTER TABLE 
				story
			ADD 
				story_versioning enum('0','1') NOT NULL default '0' AFTER story_locked
			";
			$r = db_query($query);
		}

	 	// add version table 
	 	$query = "
		DESCRIBE
			version
		";
		$r = db_query($query);
		if (db_num_rows($r) < 1) {
			$query = "
				CREATE TABLE version (
				  version_id int(10) unsigned NOT NULL auto_increment,
				  FK_parent int(10) unsigned NOT NULL default '0',
				  FK_createdby int(10) unsigned NOT NULL default '0',
				  version_order INT( 10 ) unsigned NOT NULL  default '0',
				  version_created_tstamp timestamp(14) NOT NULL,
				  version_text_short mediumblob NOT NULL,
				  version_text_long mediumblob NOT NULL,
				  version_comment mediumblob NOT NULL,
				  PRIMARY KEY  (version_id)
				) TYPE=MyISAM;
			";
			$r = db_query($query);
		}
		
		// modify the page_show_versions field in the page table
	 	$query = "
		DESCRIBE
			page page_show_versions
		";
		$r = db_query($query);
		if (db_num_rows($r) < 1) {
			$query = "
			ALTER TABLE 
				page
			ADD 
				page_show_versions enum('0','1') NOT NULL default '0' AFTER page_show_date
			";
			$r = db_query($query);
		}

			
	}
}


?>
