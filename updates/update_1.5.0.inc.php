<?php

/******************************************************************************
 * An interface for segue updates
 *
 * 
 ******************************************************************************/


class Update150
	extends Update {
	
	var $field01Exists = FALSE;
	var $field02Exists = FALSE;
	var $field03Exists = FALSE;
	var $table01Exists = FALSE;
	var $data01Exists = FALSE;
	
	/**
     * Returns the name of the update
     *
     * @return string Name of the update
	 */
	function getName() {
		return "Segue 1.5.0 Update";
	}
	
    /**
     * Returns the description of the update
     *
     * @return string Description of the update
	 */
	function getDescription() {
		return "This update modifies Segue's tables in order to add new functionality
		including: page level content blocks, page level RSS feeds, right navigation, 
		and page show editor
	";
	}
	
    /**
     * Returns the status of the update
     *
     * @return boolean True if update does not need to be run.
	 */
	function hasRun() {
		$hasRun = TRUE;
		
		// check for page_text field in page table
		$query = "
		DESCRIBE
			page page_text
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->field01Exists = TRUE;
			$hasRun = TRUE;
		} else {
			$hasRun = FALSE;
			print "\nNeeds page_text  field in page table.<br />";
		}
		
		// check for page_location field in page table
		$query = "
		DESCRIBE
			page page_location
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->field02Exists = TRUE;
			$hasRun = TRUE;
		} else {
			$hasRun = FALSE;
			print "\nNeeds page_location field in page table.<br />";
		}
		
		// check for page_show_editor field in page table
		$query = "
		DESCRIBE
			page page_show_editor
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->field03Exists = TRUE;
			$hasRun = TRUE;
		} else {
			$hasRun = FALSE;
			print "\nNeeds page_show_editor field in page table.<br />";
		}
		
		// check for "rss" enum option in page table
		$query = "
		DESCRIBE
			page page_display_type
		";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		
		if (!eregi("(enum\()(.*'rss'.*)(\))", $a['Type'], $parts)) {
			$hasRun = FALSE;
			print "\nNeeds type, 'rss' in ".$a['Type']."<br />";
		}

		// check for "content" enum option in page table
		$query = "
		DESCRIBE
			page page_display_type
		";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		
		if (!eregi("(enum\()(.*'content'.*)(\))", $a['Type'], $parts)) {
			$hasRun = FALSE;
			print "\nNeeds type, 'content' in ".$a['Type']."<br />";
		}

		// check for "tags" enum option in page table
		$query = "
		DESCRIBE
			page page_display_type
		";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		
		if (!eregi("(enum\()(.*'tags'.*)(\))", $a['Type'], $parts)) {
			$hasRun = FALSE;
			print "\nNeeds type, 'tags' in ".$a['Type']."<br />";
		}

		// check for tags table
		$query = "
		DESCRIBE
			tags
		";
		$r = db_query($query);
		if (db_num_rows($r)) {
			$this->table01Exists = TRUE;
			$hasRun = TRUE;
		} else {
			$hasRun = FALSE;
			print "\nNeeds tags table.<br />";
		}
		
		// check if category info was moved to tags table
		$query = "
		SELECT 
			DISTINCT story_category  
		FROM
			story			
		";
		
		$r = db_query($query);
		if (db_num_rows($r) > 1) {		
			$query = "
			SELECT * FROM
				tags			
			";
			$r = db_query($query);
			if (db_num_rows($r)) {
				$this->data01Exists = TRUE;
				$hasRun = TRUE;
			} else {
				$hasRun = FALSE;
				print "\ntags have not been moved to tags table.<br />";
			}
		}
		
		// check for "participants" enum option in page table
		$query = "
		DESCRIBE
			page page_display_type
		";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		
		if (!eregi("(enum\()(.*'participants'.*)(\))", $a['Type'], $parts)) {
			$hasRun = FALSE;
			print "\nNeeds type, 'participants' in ".$a['Type']."<br />";
		}

				
		return $hasRun;	
	}
	
    /**
     * Runs the update
	 */
	function run() {
	
	 	// modify the page_text option
	 	$query = "
		DESCRIBE
			page page_text
		";
		$r = db_query($query);
		if (db_num_rows($r) < 1) {
			$query = "
			ALTER TABLE 
				page
			ADD 
				page_text MEDIUMBLOB AFTER page_created_tstamp
			";
			$r = db_query($query);
		}
		
	 	// modify the page_location option
	 	$query = "
		DESCRIBE
			page page_location
		";
		$r = db_query($query);
		if (db_num_rows($r) < 1) {
			$query = "
			ALTER TABLE 
				page
			ADD 
				page_location ENUM('left','right') AFTER page_order
			";
			$r = db_query($query);
		}
		
		// modify the page_show_editor option
	 	$query = "
		DESCRIBE
			page page_show_editor
		";
		$r = db_query($query);
		if (db_num_rows($r) < 1) {
			$query = "
			ALTER TABLE 
				page
			ADD 
				page_show_editor enum('0', '1') AFTER page_show_creator
			";
			$r = db_query($query);
		}

		
		// modify the page_display_type in page table	
		$query = "
		DESCRIBE
			page page_display_type
		";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		
	 	if (!eregi("(enum\()(.*'content'.*)(\))", $a['Type']) && 
	 		eregi("enum\((.*)\)", $a['Type'], $parts)) {
	 		printpre($a['Null']);

			$query = "
			ALTER TABLE 
				page
			CHANGE 
				page_display_type page_display_type  
					ENUM(".$parts[1].",'content','tags','rss','participants') 
					DEFAULT '".$a['Default']."' 
					".(($a['Null'])?"":"NOT")." NULL
			";
			
			$r = db_query($query);
		}
		
		// add tags table and move all category info into tags table
	 	$query = "
		DESCRIBE
			tags
		";
		$r = db_query($query);
		if (db_num_rows($r) < 1) {
			$query = "
			CREATE TABLE `tags` (
			  `record_type` varchar(128) NOT NULL default '',
			  `FK_record_id` int(11) NOT NULL default '0',
			  `FK_user_id` int(11) NOT NULL default '0',
			  `record_tag` varchar(255) NOT NULL default '',
			  `record_tag_added` timestamp(14) NOT NULL,
			  KEY `FK_record_id` (`FK_record_id`),
			  KEY `FK_user_id` (`FK_user_id`),
			  KEY `record_type` (`record_type`(7)),
			  KEY `record_tag` (`record_tag`(10))
			) TYPE=MyISAM;
			";
			$r = db_query($query);
		}
		
		// find all stories with category info
		$query = "
		SELECT * 
		FROM  story 
		WHERE  story_category LIKE  '%_'
		";
		$r = db_query($query);
		
		// move category info to tags table
		while ($a = db_fetch_assoc($r)) {
			$category = $a['story_category'];
			$record_tag = urlencode(ereg_replace(" ", "_", $category));
			$FK_record_id = $a['story_id'];
			$FK_user_id = $a['FK_createdby'];
			
			$query02 = "
			INSERT INTO 
				tags
				(`record_type`, `FK_record_id`, `FK_user_id`, `record_tag`, `record_tag_added`) 
				VALUES 
				('story',  '$FK_record_id',  '$FK_user_id',  '$record_tag', NOW())
			";
			$r02 = db_query($query02);
		}		
	}
}


?>
