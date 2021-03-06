<? /* $Id$ */

class story extends segue {
/* 	var $_allfields = array("page_id","section_id","site_id","title","addedby","addedtimestamp", */
/* 							"editedby","editedtimestamp","shorttext","longertext", */
/* 							"activatedate","deactivatedate","discuss", */
/* 							"locked","category","discussions","texttype","type","url","active"); */

	var $_allfields = array("page_id","section_id","site_id","title","addedby","addedtimestamp", "addedbyfull", "editedbyfull",
							"editedby","editedtimestamp","shorttext","longertext",
							"activatedate","deactivatedate","discuss","discussdisplay","discussauthor","discussemail","discusslabel",
							"locked","category","discussions","texttype","type","url","active");
	
	// fields listed in $_datafields are stored in the database.
	// the first element is the table join syntax required to pull the data.
	// the second element is an array of the database fields we will be selecting
	// the third element is the database field by which we will sort
	
	var $_datafields = array(
		"id" => array(
			"story",
			array("story_id"),
			"story_id"
		),
		"site_id" => array(
			"story
				INNER JOIN
			 page
			 	ON FK_page = page_id
				INNER JOIN
			 section
			 	ON FK_section = section_id
			 site
			 	ON section.FK_site = site.site_id
			 	INNER JOIN
			 slot
				ON site.site_id = slot.FK_site
			",
			array("FK_site"),
			"story_id"
		),
		"section_id" => array(
			"story
				INNER JOIN
			 page
			 	ON FK_page = page_id
			",
			array("FK_section"),
			"story_id"
		),
		"page_id" => array(
			"story",
			array("FK_page"),
			"story_id"
		),
		"type" => array(
			"story",
			array("story_display_type"),
			"story_id"
		),
		"title" => array(
			"story",
			array("story_title"),
			"story_id"
		),
		"activatedate" => array(
			"story",
			array("DATE_FORMAT(story_activate_tstamp, '%Y-%m-%d')"),
			"story_id"
		),
		"deactivatedate" => array(
			"story",
			array("DATE_FORMAT(story_deactivate_tstamp, '%Y-%m-%d')"),
			"story_id"
		),
		"active" => array(
			"story",
			array("story_active"),
			"story_id"
		),
		"url" => array(
			"story
				LEFT JOIN
			media
				ON FK_media = media_id",
			array("media_tag"),
			"story_id"
		),
		"locked" => array(
			"story",
			array("story_locked"),
			"story_id"
		),
		"editedby" => array(
			"story
				INNER JOIN
			user
				ON FK_updatedby = user_id",
			array("user_uname"),
			"story_id"
		),
		"editedbyfull" => array(
			"story
				INNER JOIN
			user
				ON FK_updatedby = user_id",
			array("user_fname"),
			"story_id"
		),
		"editedtimestamp" => array(
			"story",
			array("story_updated_tstamp"),
			"story_id"
		),
		"addedby" => array(
			"story
				INNER JOIN
			user
				ON FK_createdby = user_id",
			array("user_uname"),
			"story_id"
		),
		"addedbyfull" => array(
			"story
				INNER JOIN
			user
				ON FK_createdby = user_id",
			array("user_fname"),
			"story_id"
		),
		"addedtimestamp" => array(
			"story",
			array("story_created_tstamp"),
			"story_id"
		),
		"discuss" => array(
			"story",
			array("story_discussable"),
			"story_id"
		),
		"discussdisplay" => array(
			"story",
			array("story_discussdisplay"),
			"story_id"
		),
		"discussauthor" => array(
			"story",
			array("story_discussauthor"),
			"story_id"
		),
		"discussemail" => array(
			"story",
			array("story_discussemail"),
			"story_id"
		),
		"discusslabel" => array(
			"story",
			array("story_discusslabel"),
			"story_id"
		),
		"category" => array(
			"story",
			array("story_category"),
			"story_id"
		),
		"texttype" => array(
			"story",
			array("story_text_type"),
			"story_id"
		),

		"discussions" => array(
			"story
				INNER JOIN
			 discussion
			 	ON FK_story = story_id
			",
			array("discussion_id"),
			"discussion_order"
		),
		"shorttext" => array(
			"story",
			array("story_text_short"),
			"story_id"
		),
		"longertext" => array(
			"story",
			array("story_text_long"),
			"story_id"
		)
	);

	var $_table = "story";
	
	
	function story($insite,$insection,$inpage,$id=0,&$pageObj) {
		$this->owning_site = $insite;
		$this->owning_section = $insection;
		$this->owning_page = $inpage;
		$this->owningPageObj = &$pageObj;
		$this->owningSectionObj = &$this->owningPageObj->owningSectionObj;
		$this->owningSiteObj = &$this->owningPageObj->owningSectionObj->owningSiteObj;

		$this->fetchedup = 1;
		
		$this->id = $id;
		
		// initialize the data array
		$this->data[site_id] = $insite;
		$this->data[section_id] = $insection;
		$this->data[page_id] = $inpage;
		$this->init();
		$this->data[type] = "story";
	}
	
	function init($formdates=0) {
		if (!is_array($this->data)) $this->data = array();
		$this->data[title] = "";
		$this->data[activatedate] = $this->data[deactivatedate] = "0000-00-00";
		$this->data[shorttext] = $this->data[longertext] = "";
/* 		$this->data[discuss] = 0; */
		$this->data[texttype] = "text";
		$this->data[category] = "";
		$this->data[url] = "http://";
		$this->data[locked] = 0;
		if ($this->id) $this->fetchFromDB();
		if ($formdates) $this->initFormDates();
	}
	
	function getFirst($length) {
		if ($this->getField("type") == "image" && $this->getField("shorttext") == "")
			return "Image";
		else {
			$text = $this->getField("shorttext");
			$text = strip_tags($text);
			if (strlen($text) <= $length) return $text;
		
			$text = substr($text,0,$length)."...";
			return $text;
		}
	}
	
/******************************************************************************
 * these functions are old & suck.
 ******************************************************************************/

	function addDiscussion($id) {
		if (!$this->getField("discussions")) $this->data[discussions] = array();
		array_push($this->data[discussions],$id);
		$this->changed[discussions] = 1;
	}
	

	function delDiscussion($id) {
		if (!$this->getField("discussions")) return;
		$d = array();
		foreach ($this->data[discussions] as $i) {
			if ($i != $id) $d[] = $i;
		}
		$this->data[discussions] = $d;
		$this->changed[discussiosn] = 1;
	}
	
/******************************************************************************
 * end
 ******************************************************************************/


	function delete($deleteFromParent=0) {	// delete from db
		if (!$this->id) return false;
		if ($deleteFromParent) {
			$parentObj =& new page($this->owning_site,$this->owning_section,$this->owning_page,$this->owningPageObj->owningSectionObj);
			$parentObj->fetchDown();
			/* print "<br />delStory - ".$this->id."<br />"; */
			$parentObj->delStory($this->id);
			$parentObj->updateDB();
		} else {
			$query = "DELETE FROM story WHERE story_id='".addslashes($this->id)."'";
			db_query($query);

			$query = "DELETE FROM permission WHERE FK_scope_id='".addslashes($this->id)."' AND permission_scope_type='story';";
			db_query($query);
			
			$query = "DELETE FROM discussion WHERE FK_story='".addslashes($this->id)."'";
			db_query($query);
			
			delete_record_tags($this->owning_site,$this->id,"story",$user_id="");
			
			$this->clearPermissions();
			$this->updatePermissionsDB();
		}
	}
	
	function fetchUp($full = 0) {
		if (!$this->fetchedup || $full) {
			$this->owningSiteObj =& new site($this->owning_site);
			$this->owningSiteObj->fetchFromDB();
//			$this->owningSiteObj->buildPermissionsArray(1);
			$this->owningSectionObj =& new section($this->owning_site,$this->owning_section,$this->owningSiteObj);
			$this->owningSectionObj->fetchFromDB();
//			$this->owningSectionObj->buildPermissionsArray(1);
			$this->owningPageObj =& new page($this->owning_site,$this->owning_section,$this->owning_page,$this->owningSectionObj);
			$this->owningPageObj->fetchFromDB();
//			$this->owningPageObj->buildPermissionsArray(1);
			$this->fetchedup = 1;
		}
	}
	
	function fetchDown($full=0) {
		if (!$this->fetcheddown || $full) {
			if (!$this->tobefetched || $full) $this->fetchFromDB(0,$full);
			$this->fetcheddown = 1;
		}
	}
	
	function fetchFromDB($id=0,$force=0) {
		if ($id) $this->id = $id;
		global $dbuser, $dbpass, $dbdb, $dbhost;
		global $cfg;
		// take this out when appropriate & replace occurences;
		global $uploaddir;
		
		$this->tobefetched=1;
		
		//$this->id = $this->getField("id"); // why need to do this?
		
		if ($force) {
			// the code below is inefficient! why fetch each field separately when we can fetch all fields at same time
			// thus we can cut the number of queries significantly
/*			foreach ($this->_allfields as $f) {
				$this->getField($f);
			}
*/

			// connect to db and initialize data array
 			db_connect($dbhost,$dbuser,$dbpass, $dbdb);
			$this->data = array();

			// first fetch all fields that are not part of a 1-to-many relationship
 			$query = "
				SELECT  
					story_display_type AS type, 
					story_title AS title, 
					DATE_FORMAT(story_activate_tstamp, '%Y-%m-%d') AS activatedate, 
					DATE_FORMAT(story_deactivate_tstamp, '%Y-%m-%d') AS deactivatedate,
					story_active AS active, 
					story_locked AS locked, 
					story_updated_tstamp AS editedtimestamp, 
					story_created_tstamp AS addedtimestamp,
					story_discussable AS discuss, 
					story_discussdisplay AS discussdisplay, 
					story_discussauthor AS discussauthor, 
					story_discussemail AS discussemail,
					story_discusslabel AS discusslabel,
					story_category AS category, 
					story_text_type AS texttype, 
					story_text_short AS shorttext,
					story_text_long AS longertext,
					media_tag AS url,
					user_createdby.user_uname AS addedby, 
					user_updatedby.user_uname AS editedby, 
					slot_name as site_id,
					FK_section AS section_id, 
					FK_page as page_id
				FROM 
					story
						INNER JOIN
					page
						ON FK_page = page_id
						INNER JOIN
					 section
						ON FK_section = section_id
						INNER JOIN
					user AS user_createdby
						ON story.FK_createdby = user_createdby.user_id
						INNER JOIN
					user AS user_updatedby
						ON story.FK_updatedby = user_updatedby.user_id
						INNER JOIN
					 site
						ON section.FK_site = site.site_id
						INNER JOIN
					 slot
						ON site.site_id = slot.FK_site
						LEFT JOIN
					 media
						ON story.FK_media = media_id
				WHERE 
					story_id = '".addslashes($this->id)."'
			";

			$r = db_query($query);
			$a = db_fetch_assoc($r);
			array_change_key_case($a); // make all keys lower case
			// for each field returned by the query
			foreach ($a as $field => $value)
				// make sure we have defined this field in the _allfields array
				if (in_array($field,$this->_allfields)) {
					// decode if necessary
					if (in_array($field,$this->_encode)) 
						$value = stripslashes(urldecode($value));
					$this->data[$field] = $value;
//					print "$field] = $value<br />";
					$this->fetched[$field] = 1;
				}
				else
					echo "ERROR: field $field not in _allfields!!!<br />";
			

			// now fetch the discussion entries for this story
			$query = "
				SELECT
					discussion_id
				FROM
					story
						INNER JOIN
					discussion
						ON FK_story = story_id
				WHERE 
					story_id = '".addslashes($this->id)."'
				ORDER BY
					discussion_order
				";

			$r = db_query($query);
			$this->data[discussions] = array();
			while ($a = db_fetch_assoc($r))
				$this->data[discussions][] = $a[discussion_id];

			$this->fetched[discussions] = 1;
		}
		
		return $this->id;
	}
	
	function updateDB($down=0, $force=0, $keepEditHistory=0) {
		if ($this->changed) {
		
			/******************************************************************************
			 * get story text, convert wiki links to internal links
			 ******************************************************************************/			 
//			$text = $this->getField("shorttext");					 
//			$text = convertWikiMarkupToLinks($this->owning_site, $this->owning_section, $this->owning_page, $this->id, "page", $text);				
//		//	$text = recordInternalLinks ($_SESSION[settings][site],$_SESSION[settings][section],$_SESSION[settings][page], $page_title, $text);	
//			$text = convertInteralLinksToTags($this->owning_site, $text);			
//			$text = $this->setField("shorttext", $text);
//			
//			$text = $this->getField("longertext");					 
//			$text = convertWikiMarkupToLinks($this->owning_site, $this->owning_section, $this->owning_page, $this->id, "page", $text);				
//		//	$text = recordInternalLinks ($_SESSION[settings][site],$_SESSION[settings][section],$_SESSION[settings][page], $page_title, $text);	
//			$text = convertInteralLinksToTags($this->owning_site, $text);			
//			$text = $this->setField("longertext", $text);
		
			$this->parseMediaTextForDB("shorttext");
			$this->parseMediaTextForDB("longertext");
			$a = $this->createSQLArray();
			
			
			if ($keepEditHistory) {
				$a[] = $this->_datafields[editedtimestamp][1][0]."='".$this->getField("editedtimestamp")."'";
			} else
				$a[] = "FK_updatedby=".$_SESSION[aid];

			$query = "
				UPDATE 
					story 
				SET 
					".implode(",",$a)." 
				WHERE 
					story_id='".addslashes($this->id)."'
			";
			
/* 			print "<pre>Story->UpdateDB: $query<br />"; */
			db_query($query);
/* 			print mysql_error()."<br />"; */
/* 			print_r($this->data['stories']); */
/* 			print "</pre>"; */

			/******************************************************************************
			 * update the page updated timestamp
			 ******************************************************************************/

			$query = "
				UPDATE 
					page 
				SET 
					page_updated_tstamp = NOW()
				WHERE 
					page_id='".addslashes($this->getField("page_id"))."'
			";
			
			db_query($query);



			
			// the hard step: update the fields in the JOIN tables
			
			/******************************************************************************
			 * Update version table
			 ******************************************************************************/
			 
			save_version($this->getField("shorttext"), $this->getField("longertext"), $this->id, $this->version_comments);
			 

			
			// Urls are now stored in the media table
			if ($this->changed[url] && ($this->getField("type") == 'link' || $this->getField("type") == 'rss')) {
				// Urls are now stored in the media table
				// get id of media item
				$query = "
					SELECT
						FK_media
					FROM
						story
					WHERE
						story_id = '".addslashes($this->id)."'
				";

				$a = db_fetch_assoc(db_query($query));
				$media_id = $a[FK_media];
							
				$query = "
					UPDATE
						media
					SET
						media_tag = '".$this->data[url]."',
						FK_updatedby = ".$_SESSION[aid]."
					WHERE
						media_id = '".addslashes($media_id)."'
				";

				db_query($query);

				 
			}
		}
		
		// update permissions
		$this->updatePermissionsDB($force);
		
		// add log entry, now handled elsewhere
/* 		log_entry("edit_story",$this->owning_site,$this->owning_section,$this->owning_page,"$_SESSION[auser] edited content id ".$this->id." in site ".$this->owning_site); */

		return true;
	}
	
	function insertDB($down=0, $newsite=null, $newsection=0, $newpage=0, $removeOrigional=0, $keepaddedby=0, $keepDiscussions=0, $storyTags = null) {
		$origsite = $this->owning_site;
		$origid = $this->id;
		if ($newsite) {
			$this->owning_site = $newsite;
			unset($this->owningSiteObj);
		}
		if ($newsection) {
			$this->owning_section = $newsection;
			unset($this->owningSectionObj);
		}
		if ($newpage) {
			$this->owning_page = $newpage;
			unset($this->owningPageObj);
		}
		
		$this->fetchUp(1);
		
		
		
		/*********************************************************
		 * Re-Key the ordering of the rest of the stories in the
		 * section to make sure that there are no holes
		 *********************************************************/
		foreach($this->owningPageObj->getField("stories") as $order => $storyId) {
			$query = 
				"UPDATE
					story
				SET
					story_order = '".addslashes($order)."'
				WHERE
					story_id = '".$storyId."'";
			
// 			printpre($query);
			db_query($query);
		}
		
		
				
		// if moving to a new site, copy the media
		if ($origsite != $this->owning_site && $down) {
			$images = array();
			if ($this->getField("type") == "image" || $this->getField("type") == "rss" || $this->getField("type") == "file") {
				$media_id = $this->getField("longertext");
				$this->setField("longertext",copy_media($media_id,$newsite));
			} else if ($this->getField("type") == "story") {
				// These do some moving of files based on a ####id##### syntax
				// for storing inline images. 
				// Adam 2005-06-27: I don't believe that these were ever used, but
				// I'll leave them here so as not to break any old data that uses them.
				$ids = segue::getMediaIDs("shorttext");
				segue::replaceMediaIDs($ids,"shorttext",$newsite);
				$ids = segue::getMediaIDs("longertext");
				segue::replaceMediaIDs($ids,"longertext",$newsite);
				
				// Search for and copy images that use the "[[mediapath]]/filename.ext"
				// syntax.
				preg_match_all("/\[\[mediapath\]\]\/([^'\"]+)/",
					$this->getField("shorttext").$this->getField("longertext"), 
					$matches);
				$fnames = array_unique($matches[1]);
				foreach ($fnames as $fname) {
					copy_media_with_fname($fname, $origsite, $newsite);
				}
			}
		}
		
		$a = $this->createSQLArray(1);
		if (!$keepaddedby) {
			$a[] = "FK_createdby='".addslashes($_SESSION[aid])."'";
			$a[] = $this->_datafields[addedtimestamp][1][0]."=NOW()";
			$a[] = "FK_updatedby='".addslashes($_SESSION[aid])."'";
		} else {
			$a[] = "FK_createdby=".db_get_value("user","user_id","user_uname='".addslashes($this->getField("addedby"))."'");
			$a[] = $this->_datafields[addedtimestamp][1][0]."='".addslashes($this->getField("addedtimestamp"))."'";
			$a[] = "FK_updatedby=".db_get_value("user","user_id","user_uname='".addslashes($this->getField("editedby"))."'");
			$a[] = $this->_datafields[editedtimestamp][1][0]."='".addslashes($this->getField("editedtimestamp"))."'";
		}

		// insert media (url)
		if ($this->data[url] && ($this->data['type'] == 'link' || $this->data['type'] == 'rss')) {
			// first see, if media item already exists in media table
			
			$query = "
				SELECT
					media_id
				FROM
					media
				WHERE
					FK_site = '".addslashes($this->owningSiteObj->id)."' AND
					FK_createdby = '".addslashes($_SESSION[aid])."' AND
					media_tag = '".addslashes($this->data[url])."' AND
					media_location = 'remote'
			";
	
			$r = db_query($query);
			
			// if not in media table insert it
			if (!db_num_rows($r)) {
				$query = "
					INSERT INTO 
						media
					SET
						FK_site = '".addslashes($this->owningSiteObj->id)."',
						FK_createdby = '".addslashes($_SESSION[aid])."',
						media_tag = '".addslashes($this->data[url])."',
						media_location = 'remote',
						FK_updatedby = '".addslashes($_SESSION[aid])."'
				";
				
				db_query($query);
				$a[] = "FK_media=".lastid();
			}
			// if in media table, assign the media id
			else {
				$arr = db_fetch_assoc($r);
				$a[] = "FK_media='".addslashes($arr[media_id])."'";
			}
		}

		$query = "INSERT INTO story SET ".implode(",",$a);
		db_query($query);
		
		$this->id = lastid();
							 
		/******************************************************************************
		 * get story text, convert wiki links to internal links
		 ******************************************************************************/			 
//		$text = $this->getField("shorttext");					 
//		$text = convertWikiMarkupToLinks($this->owning_site, $this->owning_section, $this->owning_page, $this->id, "page", $text);				
	//	$text = recordInternalLinks ($_SESSION[settings][site],$_SESSION[settings][section],$_SESSION[settings][page], $page_title, $text);	
//		$shorttext = convertInteralLinksToTags($this->owning_site, $text);			

		
//		$text = $this->getField("longertext");					 
//		$text = convertWikiMarkupToLinks($this->owning_site, $this->owning_section, $this->owning_page, $this->id, "page", $text);				
	//	$text = recordInternalLinks ($_SESSION[settings][site],$_SESSION[settings][section],$_SESSION[settings][page], $page_title, $text);	
//		$longertext = convertInteralLinksToTags($this->owning_site, $text);			

		// update table with new short and long text
//		$query = "UPDATE
//					story
//				SET 
//					story_text_short ='".addslashes($shorttext)."',
//					story_text_long ='".addslashes($longertext)."' 
//				WHERE
//					story_id ='".addslashes($this->id)."'
//				";
//								
//		db_query($query);
//		 		 

		// See if there is a site hash (meaning that we are being copied).
		// If so, try to match our id with the hash entry for 'NEXT'.
		if ($GLOBALS['__site_hash']['stories'] 
			&& $oldId = array_search('NEXT', $GLOBALS['__site_hash']['stories']))
		{
			$GLOBALS['__site_hash']['stories'][$oldId] = $this->id;
		}
		
		$this->fetchUp();
/* 		$this->owningPageObj->addStory($this->id); */
		if ($removeOrigional) {
			$this->owningPageObj->delStory($origid,0);
			$this->owningPageObj->updateDB();
		}
		
			/******************************************************************************
			 * update the page updated timestamp
			 ******************************************************************************/

			$query = "
				UPDATE 
					page 
				SET 
					page_updated_tstamp = NOW()
				WHERE 
					page_id='".addslashes($this->getField("page_id"))."'
			";
			
			db_query($query);

		
		/******************************************************************************
		 * Update version table
		 ******************************************************************************/
		 
		save_version($this->getField("shorttext"), $this->getField("longertext"), $this->id, $this->version_comments);
		
		// add new permissions entry.. force update
		$this->updatePermissionsDB(1);
		
		if ($keepDiscussions && $this->fetcheddown && $this->data[discussions]) {
			
			$idMapping = array();
			$discussionData = array();
	
			// The discussions objects are way to fucked up to use to copy the
			// posts so we are going to have to do this 'maunally'.
			
			// Fetch all of the discussling data
			foreach($this->data[discussions] as $discussionId) {
				// Get all the posts and dump their properties into an array.
				$query = "
					SELECT
						*
					FROM
						discussion
					WHERE
						discussion_id='".addslashes($discussionId)."'";
						
				$r = db_query($query);
				
				$discussionData[$discussionId] = db_fetch_assoc($r);
			}
			
			// Insert new posts, pointing to the new story	
			foreach (array_keys($discussionData) as $oldId) {
				
				// Insert the post
				$query = 
					"INSERT INTO
						discussion
					SET";
					
				// Set the FK_story to our new story ID
				$discussionData[$oldId]['FK_story'] = $this->id;
				
				// Add the rest of the fields.
				$i=0;
				foreach ($discussionData[$oldId] as $field => $val) {
					if ($field != 'discussion_id' && $val) {
						$query .= "\n\t".(($i==0)?"":", ").$field."='".addslashes($val)."'";
						$i++;
					}
				}
				
				$r = db_query($query);
				
				// store the id mapping
				$idMapping[$oldId] = lastid();
				if ($GLOBALS['__site_hash']['discussions']){
					$GLOBALS['__site_hash']['discussions'][$oldId] = lastid();
				}
			}
			
			// go through and update all of the FK_parents to point to the new Ids.
			// Also, rebuild the discussions array in case we try to access it after
			// the copy.
			$this->data[discussions] = array();
			foreach (array_keys($discussionData) as $oldId) {
				$newId = $idMapping[$oldId];
				
				$this->data[discussions][] = $newId;
				
				// If we were a reply, update our parent key
				if ($discussionData[$oldId]['FK_parent']) {
				
					$query = "
						UPDATE
							discussion
						SET
							FK_parent = '".addslashes($idMapping[$discussionData[$oldId]['FK_parent']])."',
							discussion_tstamp = '".addslashes($discussionData[$oldId]['discussion_tstamp'])."'
							
						WHERE
							discussion_id = '".addslashes($newId)."'
					";
					
//					printpre($query);
					$res = db_query($query);
				}
			}
		}
		
		
		// Update any story tags
		if (is_array($storyTags)) {
			save_record_tags($storyTags, null, $this->id, $_SESSION[aid], "story");
		}
		
		return true;
	}
	
	function createSQLArray($all=0) {
		$d = $this->data;
		$a = array();

/* 		if (!isset($this->owningSiteObj)) $this->owningSiteObj =& new site($this->owning_site); */
/* 		if ($all) $a[] = $this->_datafields[site_id][1][0]."='".$this->owningSiteObj->getField("id")."'"; */
/* 		if (!isset($this->owningSectionObj)) $this->owningSectionObj = new section($this->owning_site,$this->owning_section); */
/* 		if ($all) $a[] = $this->_datafields[section_id][1][0]."='".$this->owningSectionObj->getField("id")."'"; */

		$this->fetchUp();

		if ($all) 
			$a[] = $this->_datafields[page_id][1][0]."='".addslashes($this->owningPageObj->getField("id"))."'";
		
//		if ($this->id && ($all || $this->changed[pages])) { //I belive we may always need to fix the order.
		if ($this->id) {
			$orderkeys = array_keys($this->owningPageObj->getField("stories"),$this->id);
//			print "<br />".$this->id."<br />".$orderkeys[0]."<br />";
			$a[] = "story_order='".addslashes($orderkeys[0])."'";
		} else {
			$a[] = "story_order='".addslashes(count($this->owningPageObj->getField("stories")))."'";
		}
		
		if ($all || $this->changed[title]) $a[] = $this->_datafields[title][1][0]."='".addslashes($d[title])."'";
		if ($all || $this->changed[activatedate]) $a[] = "story_activate_tstamp ='".addslashes(ereg_replace("-","",$d[activatedate]))."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[deactivatedate]) $a[] = "story_deactivate_tstamp ='".addslashes(ereg_replace("-","",$d[deactivatedate]))."'"; // remove dashes to make a tstamp
		if ($all || $this->changed[active]) $a[] = $this->_datafields[active][1][0]."='".addslashes((($d[active])?1:0))."'";
		if ($all || $this->changed[type]) $a[] = $this->_datafields[type][1][0]."='".addslashes($d[type])."'";
		if ($all || $this->changed[locked]) $a[] = $this->_datafields[locked][1][0]."='".addslashes((($d[locked])?1:0))."'";
//		if ($all || $this->changed[stories]) $a[] = "stories='".encode_array($d[stories])."'";
//		if (($all && $this->data[url]) || $this->changed[url]) $a[] = $this->_datafields[url][1][0]."='$d[url]'";

		if ($all || $this->changed[discuss]) $a[] = $this->_datafields[discuss][1][0]."='".addslashes((($d[discuss])?1:0))."'";
		if ($all || $this->changed[discussemail]) $a[] = $this->_datafields[discussemail][1][0]."='".addslashes((($d[discussemail])?1:0))."'";
		if ($all || $this->changed[discussdisplay]) $a[] = $this->_datafields[discussdisplay][1][0]."='".addslashes(($d[discussdisplay]))."'";
		if ($all || $this->changed[discussauthor]) $a[] = $this->_datafields[discussauthor][1][0]."='".addslashes(($d[discussauthor]))."'";
		if ($all || $this->changed[discusslabel]) $a[] = $this->_datafields[discusslabel][1][0]."='".addslashes($d[discusslabel])."'";

		
		if ($all || $this->changed[texttype]) $a[] = $this->_datafields[texttype][1][0]."='".addslashes($d[texttype])."'";
		if ($all || $this->changed[category]) $a[] = $this->_datafields[category][1][0]."='".addslashes($d[category])."'";
		if ($all || $this->changed[shorttext]) $a[] = $this->_datafields[shorttext][1][0]."='".addslashes(urlencode($d[shorttext]))."'";
		if ($all || $this->changed[longertext]) $a[] = $this->_datafields[longertext][1][0]."='".addslashes(urlencode($d[longertext]))."'";
//		if ($all || $this->changed[discussions]) $a[] = "discussions='".encode_array($d[discussions])."'";
		
		return $a;
	}
}
