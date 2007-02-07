<?php

/******************************************************************************
 * An interface for segue updates
 *
 * 
 ******************************************************************************/


class Update170
	extends Update {
	
	var $field01Exists = FALSE;
	var $table01Exists = FALSE;
	var $versionsPopulated = FALSE;
	
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

		// check for version table
		$query = "
		DESCRIBE
			version
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->table01Exists = TRUE;
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
			$this->field01Exists = TRUE;
		} else {
			$hasRun = FALSE;
			print "\n Needs page_show_versions field in page table.<br />";
		}
		
		// check that all existing stories are in the version table as the 
		// starting version.
		if ($hasRun) {
			$query = "
			SELECT 
				COUNT(story_id) AS num
			FROM
				story
				LEFT JOIN version
					ON story_id = FK_parent
			WHERE
				FK_parent IS NULL
			";
			$r = db_query($query);
			$a = db_fetch_assoc($r);
			$num = intval($a['num']);
			
			mysql_free_result($r);
			
			if (!$num) {
				$this->versionsPopulated = TRUE;
			} else {
				$this->versionsPopulated = FALSE;
				$hasRun = FALSE;
				print "\n ".$num." existing stories need to have initial versions created.<br />";
			}
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
				CREATE TABLE version (
				  version_id int(10) unsigned NOT NULL auto_increment,
				  FK_parent int(10) unsigned NOT NULL default '0',
				  FK_createdby int(10) unsigned NOT NULL default '0',
				  version_order INT( 10 ) unsigned NOT NULL  default '0',
				  version_created_tstamp timestamp(14) NOT NULL,
				  version_text_short mediumblob NOT NULL,
				  version_text_long mediumblob NOT NULL,
				  version_comments mediumblob NOT NULL,
				  PRIMARY KEY  (version_id),
				  KEY `FK_parent` (`FK_parent`)
				) TYPE=MyISAM;
			";
			$r = db_query($query);
		}
		
		// modify the page_show_versions field in the page table
		if (!$this->field01Exists) {
			$query = "
			ALTER TABLE 
				page
			ADD 
				page_show_versions enum('0','1') NOT NULL default '0' AFTER page_show_date
			";
			$r = db_query($query);
		}
		
		
		// Existing stories to the version table.
		if (!$this->versionsPopulated) {
			$query = "
			SELECT 
				COUNT(story_id) AS num
			FROM
				story
				LEFT JOIN version
					ON story_id = FK_parent
			WHERE
				FK_parent IS NULL
			";
			$r = db_query($query);
			$a = db_fetch_assoc($r);
			$num = intval($a['num']);
// 			$status = new StatusStars('populating versions with initial state. ('.$num.' versions will be created)');
// 			$status->initializeStatistics($num);
			mysql_free_result($r);
			
			if ($num) {
				$numAffected = true;
				while($numAffected > 0) {
					// If no version exists, add one.
					$query = "
					INSERT INTO
					version
						(FK_parent, 
						FK_createdby,
						version_created_tstamp,
						version_text_short,
						version_text_long,
						version_comments)
						
						SELECT
							story_id,
							FK_updatedby,
							story_updated_tstamp,
							story_text_short,
							story_text_long,
							'Initial version.'
						FROM
							story
							LEFT JOIN version ON story_id = FK_parent
						WHERE
							FK_parent IS NULL
					";
					$r = db_query($query);
					$numAffected = mysql_affected_rows();
					if ($numAffected)
						print "<br/>$numAffected initial versions created.";
// 					
// 					for ($i = 0; $i < 100; $i++)
// 						$status->updateStatistics();
				}
			}
		}
	}
}


?>
