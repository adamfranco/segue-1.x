<?

require_once(dirname(__FILE__).'/SiteExporter.class.php');
require_once(dirname(__FILE__).'/../objects/discussion.inc.php');
require_once(dirname(__FILE__).'/../dbwrapper.inc.php');
require_once(dirname(__FILE__).'/../functions.inc.php');
require_once(dirname(__FILE__).'/../objects/slot.inc.php');
require_once(dirname(__FILE__).'/../objects/segue.inc.php');
require_once(dirname(__FILE__).'/../objects/site.inc.php');
require_once(dirname(__FILE__).'/../objects/section.inc.php');
require_once(dirname(__FILE__).'/../objects/page.inc.php');
require_once(dirname(__FILE__).'/../objects/story.inc.php');
require_once(dirname(__FILE__).'/../objects/ugroup.inc.php');
require_once(dirname(__FILE__).'/../permissions.inc.php');

/**
 * This class exports a Segue Site to a string in a specified format.
 *
 * @author Adam Franco
 */

class DomitSiteImporter {

	/**
	 * The current DOMIT document
	 */
	var $_document;
	
	/**
	 *	The source directory for the media.
	 */
	
	function DomitSiteImporter() {
	}
	
	/**
	 * Imports a site from XML
	 * 
	 * @param string $xml The xml to import.
	 *
	 * @return boolean True on success.
	 */
	function importString($xml, $mediaSource = "") {
		$this->_document =& new DOMIT_Document;
		$loaded = $this->_document->parseXML($xml);
		
		if ($loaded) {
			$this->_mediaSource = $mediaSource;
			return $this->createSite();
		} else
			return FALSE;
	}
	
	/**
	 * Imports a site from an XML file
	 * 
	 * @param string $xmlFile The xml file path to import.
	 *
	 * @return boolean True on success.
	 */
	function importFile($xmlFile, $mediaSource = "") {
		$this->_document =& new DOMIT_Document;
		$loaded = $this->_document->loadXML($xmlFile);
		
		if ($loaded) {
			$this->_mediaSource = $mediaSource;
			return $this->createSite();
		} else
			return FALSE;
	}

	/**
	 * Creates a site object in Segue and saves it.
	 * 
	 * @return boolean True on success.
	 */
	function createSite() {
		$siteElement =& $this->_document->documentElement;
		global $dbhost, $dbuser, $dbpass, $dbdb, $debug;
		$debug=0;
		db_connect($dbhost, $dbuser, $dbpass, $dbdb);
		
		// Make sure that we have a valid XML file
		if ($siteElement->nodeName == "site" && $siteElement->hasAttribute('id') 
			&& $siteElement->hasAttribute('owner') && $siteElement->hasAttribute('type')) {
			
			// Make sure the site doesn't exist
			$query = "
					SELECT 
						site_id
					FROM 
						slot
							INNER JOIN
						site
							ON
								fk_site = site_id
					WHERE
						slot_name = '".$siteElement->getAttribute('id')."'";
			$r = db_query($query);
			if (db_num_rows($r)) {
				print "\nSite '".$siteElement->getAttribute('id')."' exists.";
				return FALSE;
			}
			
			// Create the site object
			$site =& new site ( $siteElement->getAttribute('id') );
			
		/*********************************************************
		 * Set the owner for the Site
		 *********************************************************/
			// The site is going to be setting its owner field to $_SESSION[aid], so
			// lets just set that manually.
			$query = "
				SELECT 
					user_id
				FROM 
					user
				WHERE
					user_uname = '".$siteElement->getAttribute('owner')."'";
			$r = db_query($query);
			if (!db_num_rows($r)) {
				print "\nUser '".$siteElement->getAttribute('owner')."' doesn't exist.";
				return FALSE;
			}
			while ($a = db_fetch_assoc($r)) {
				$_SESSION[aid] = $a['user_id'];
			}
		
		/*********************************************************
		 * Add all the editors
		 *********************************************************/
		 	$editors = array();
		 
		 	// Editors
			$agentList =& $this->_document->getElementsByTagName('agent');
			$numAgents = $agentList->getLength();
			for ($i=0; $i<$numAgents; $i++) {
				$agentElement =& $agentList->item($i);
				$editors[] = trim($agentElement->getText());
			}
// 			
// 			// Creators
// 			$agentList =& $this->_document->getElementsByTagName('creator');
// 			$numAgents = $agentList->getLength();
// 			for ($i=0; $i<$numAgents; $i++) {
// 				$agentElement =& $agentList->item($i);
// 				$editors[] = trim($agentElement->getText());
// 			}
// 			
// 			// Last Editors
// 			$agentList =& $this->_document->getElementsByTagName('last_editor');
// 			$numAgents = $agentList->getLength();
// 			for ($i=0; $i<$numAgents; $i++) {
// 				$agentElement =& $agentList->item($i);
// 				$editors[] = trim($agentElement->getText());
// 			}
			
			
			$editors = array_unique($editors);
			
			foreach ($editors as $key => $editor) {
				// add the editors
				if ($editor != $siteElement->getAttribute('owner'))
					$site->addEditor($editor);
			}
		 
			
		/*********************************************************
		 * Set the rest of the fields.
		 *********************************************************/
			$site->setField( 'type', $siteElement->getAttribute('type') );
			
			if (!$siteElement->hasChildNodes()) {
				print "\nNo child elements of the site!";
				return FALSE;
			}

			// Common Fields - title, history, activation, permissions
			if (!$this->setCommonFields($site, $siteElement))
				return FALSE;
			
			// Header and footer
			if (!$this->setHeaderFooter($site, $siteElement))
				return FALSE;
			
			// Theme
			if (!$this->setTheme($site, $siteElement))
				return FALSE;

		
		/*********************************************************
		 * Save;	media and discussions must be added to existing sites
		 *********************************************************/
			$site->insertDB(0,0,1);
			
			
		/*********************************************************
		 * Add the media
		 *********************************************************/
		 	if (!$this->addMedia($site, $siteElement))
				return FALSE;
		

		/*********************************************************
		 * Add the children
		 *********************************************************/
		 	$types = array("section","navlink");
		 	if ($siteElement->hasChildNodes()) {
				$childElement =& $siteElement->firstChild;
				while ($childElement != NULL) {
					if (in_array($childElement->nodeName, $types)) {
						$childGood = $this->addSection($site, $childElement);
						if (!$childGood)
							return FALSE;
					}
					$childElement =& $childElement->nextSibling;
				}
			}
			
		/*********************************************************
		 * Save (again, update) and finish.
		 *********************************************************/	
			
			//print_r ($site);
			
			// If we've made it this far, we're good.
			return TRUE;
		} else {
			print "\nXML Not a site!";
			return FALSE;
		}
	}
	
	function addSection(& $site, & $sectionElement) {
	
		// Create the section
		$section =& new section($site->name, 0, $site);
		$site->sections[] =& $section;
		
	/*********************************************************
	 * Set the fields.
	 *********************************************************/
	 	if ($sectionElement->nodeName == "section")
			$section->setField( 'type', "section" );
		else if ($sectionElement->nodeName == "navlink")
			$section->setField( 'type', "link" );
		
		if (!$sectionElement->hasChildNodes()) {
			print "\nNo child elements of the section!";
			return FALSE;
		}
		
		// Common Fields - title, history, activation, permissions
		if (!$this->setCommonFields($section, $sectionElement))
			return FALSE;
		
		
	/*********************************************************
	 * Save
	 *********************************************************/
	 	$section->insertDB(1,null,0,1);
	 	
		
	/*********************************************************
	 * Add the children
	 *********************************************************/
		$types = array("page","navlink", "heading", "divider");
		if ($sectionElement->hasChildNodes()) {
			$childElement =& $sectionElement->firstChild;
			while ($childElement != NULL) {
				if (in_array($childElement->nodeName, $types)) {
					$childGood = $this->addPage($section, $childElement);
					if (!$childGood)
						return FALSE;
				}
				$childElement =& $childElement->nextSibling;
			}
		}
		
		return TRUE;
	}
	
	function addPage(& $section, & $pageElement) {
		// Create the page
		$page =& new page($section->owning_site, $section->id, 0, $section);
		$section->pages[] =& $page;
		
	/*********************************************************
	 * Set the fields.
	 *********************************************************/
	 	if ($pageElement->nodeName == "page")
			$page->setField( 'type', "page" );
		else if ($pageElement->nodeName == "navlink")
			$page->setField( 'type', "link" );
		else if ($pageElement->nodeName == "heading")
			$page->setField( 'type', "heading" );
		else if ($pageElement->nodeName == "divider")
			$page->setField( 'type', "divider" );
		
		if (!$pageElement->hasChildNodes()) {
			print "\nNo child elements of the page!";
			return FALSE;
		}
		
		// Common Fields - title, history, activation, permissions
		if (!$this->setCommonFields($page, $pageElement))
			return FALSE;
		
		// Page display options
		//@todo
		
	
	/*********************************************************
	 * Save
	 *********************************************************/
	 	$page->insertDB(1,NULL,0,0,1);
		
		
	/*********************************************************
	 * Add the children
	 *********************************************************/
		$types = array("story","link", "file", "image");
		if ($pageElement->hasChildNodes()) {
			$childElement =& $pageElement->firstChild;
			while ($childElement != NULL) {
				if (in_array($childElement->nodeName, $types)) {
					$childGood = $this->addStory($page, $childElement);
					if (!$childGood)
						return FALSE;
				}
				$childElement =& $childElement->nextSibling;
			}
		}
		
		return TRUE;
	}
	
	function addStory(& $page, & $storyElement) {
		// Create the story
		$story =& new story($page->owning_site, $page->owning_section, $page->id, 0, $section);
		$page->stories[] =& $story;
		
	/*********************************************************
	 * Set the fields.
	 *********************************************************/
	 	if (!$storyElement->hasChildNodes()) {
			print "\nNo child elements of the story!";
			return FALSE;
		}
		
		$story->setField( 'type', $storyElement->nodeName );
		
		// Story
		if ($storyElement->nodeName == "story") {
			// shorttext
			$shorttextList =& $storyElement->getElementsByPath("shorttext");
			if ($shorttextList->getLength() != 1) {
				print "\nRequired 'shorttext' element is missing!";
				return FALSE;
			}
			$shorttextElement =& $shorttextList->item(0);
			$story->setField('shorttext', html_entity_decode(trim($shorttextElement->getText())));
			
			// longtext
			$longtextList =& $storyElement->getElementsByPath("longtext");
			if ($longtextList->getLength() > 1) {
				print "\nInvalid number of 'longtext' elements!";
				return FALSE;
			}
			if ($longtextList->getLength() == 1) {
				$longtextElement =& $longtextList->item(0);
				$story->setField('longertext', html_entity_decode(trim($longtextElement->getText())));
			}
			
		// Link
		} else if ($storyElement->nodeName == "link") {
			// description
			$descriptionList =& $storyElement->getElementsByPath("description");
			if ($descriptionList->getLength() != 1) {
				print "\nRequired 'description' element is missing!";
				return FALSE;
			}
			$descriptionElement =& $descriptionList->item(0);
			$story->setField('description', html_entity_decode(trim($descriptionElement->getText())));
			
			// url
			$urlList =& $storyElement->getElementsByPath("url");
			if ($urlList->getLength() != 1) {
				print "\nRequired 'url' element is missing!";
				return FALSE;
			}
			$urlElement =& $urlList->item(0);
			$story->setField('url', html_entity_decode(trim($urlElement->getText())));
			
		// File
		} else if ($storyElement->nodeName == "file" || $storyElement->nodeName == "image") {
			// description
			$descriptionList =& $storyElement->getElementsByPath("description");
			if ($descriptionList->getLength() != 1) {
				print "\nRequired 'description' element is missing!";
				return FALSE;
			}
			$descriptionElement =& $descriptionList->item(0);
			$story->setField('description', html_entity_decode(trim($descriptionElement->getText())));
 			
 			// Assume that the files have been added to the media table.
 			$filenameList =& $storyElement->getElementsByPath("filename");
			$filenameElement =& $filenameList->item(0);
			$filename = trim($filenameElement->getText());
			
 			$mediaID = db_get_value("media","media_id", "media_tag='".$filename."'");
			$story->setField('longertext', $mediaID);
			
		}
		
		// Common Fields - title, history, activation, permissions
		if (!$this->setCommonFields($story, $storyElement))
			return FALSE;
		
		
		// category
		$categoryList =& $storyElement->getElementsByPath("category");
		if ($categoryList->getLength() > 1) {
			print "\nInvalid number of 'category' elements!";
			return FALSE;
		}
		if ($categoryList->getLength() == 1) {
			$categoryElement =& $categoryList->item(0);
			$story->setField('category', html_entity_decode(trim($categoryElement->getText())));
		}
		
		
	/*********************************************************
	 * Add the discussion properties (we need to do this before inserting).
	 *********************************************************/
	 	// discussion
		$discussionList =& $storyElement->getElementsByPath("discussion");
		if ($discussionList->getLength() > 1) {
			print "\nInvalid number of 'discussion' elements!";
			return FALSE;
		}
		if ($discussionList->getLength() == 1) {
			$discussionElement =& $discussionList->item(0);
			
			$story->setField("discuss", 1);
		
			$email = (($discussionElement->getAttribute("email_owner") == "TRUE")?1:0);
			$story->setField("discussemail", $email);
			
			$authors = (($discussionElement->getAttribute("show_authors") == "TRUE")?1:2);
			$story->setField("discussauthor", $authors);
			
			$display = (($discussionElement->getAttribute("show_others_posts") == "TRUE")?1:2);
			$story->setField("discussdisplay", $display);
		}
	
		
	/*********************************************************
	 * Save
	 *********************************************************/
	 	$story->insertDB(1,NULL,0,0,0,1);
	 	
	 
	 /*********************************************************
	 * Add the discussion posts
	 *********************************************************/		
		if ($discussionList->getLength() == 1) {
			if (!$this->addDiscussion($story, $discussionElement))
				return FALSE;
		}
		

		return TRUE;
	}
	
	function addDiscussion(& $story, & $discussionElement) {
		
	/*********************************************************
	 * Add the children
	 *********************************************************/
		$types = array("discussion_node");
		if ($discussionElement->hasChildNodes()) {
			$childElement =& $discussionElement->firstChild;
			while ($childElement != NULL) {
				if (in_array($childElement->nodeName, $types)) {
					$childGood = $this->addPost($story, 0, $childElement);
					if (!$childGood)
						return FALSE;
				}
				$childElement =& $childElement->nextSibling;
			}
		}
		
		return TRUE;
	}

	function addPost(& $story, $parentId, & $discussion_nodeElement) {
	
	/*********************************************************
	 * Add the discussion_node
	 *********************************************************/
		$discussion =& new discussion($story, NULL, $parentId);
		
	/*********************************************************
	 * Add the fields
	 *********************************************************/
		// creator
		$creatorList =& $discussion_nodeElement->getElementsByPath("creator");
		if ($creatorList->getLength() != 1) {
			print "\nRequired 'creator' element is missing from the discussion!";
			return FALSE;
		}
		$creatorElement =& $creatorList->item(0);
		$discussion->authorid = db_get_value("user", "user_id", "user_uname='".trim($creatorElement->getText())."'");
		
		// created_time
		$created_timeList =& $discussion_nodeElement->getElementsByPath("created_time");
		if ($created_timeList->getLength() != 1) {
			print "\nRequired 'created_time' element is missing from the discussion!";
			return FALSE;
		}
		$created_timeElement =& $created_timeList->item(0);
		$discussion->tstamp = trim($created_timeElement->getText());
		
		// title
		$titleList =& $discussion_nodeElement->getElementsByPath("title");
		if ($titleList->getLength() != 1) {
			print "\nRequired 'title' element is missing from the discussion!";
			return FALSE;
		}
		$titleElement =& $titleList->item(0);
		$discussion->subject = html_entity_decode(trim($titleElement->getText()));
		
		// text
		$textList =& $discussion_nodeElement->getElementsByPath("text");
		if ($textList->getLength() != 1) {
			print "\nRequired 'text' element is missing from the discussion!";
			return FALSE;
		}
		$textElement =& $textList->item(0);
		$discussion->content = html_entity_decode(trim($textElement->getText()));
	
	/*********************************************************
	 * Save
	 *********************************************************/
	 	$discussion->insert();
		
	/*********************************************************
	 * Add the children
	 *********************************************************/
		$types = array("discussion_node");
		if ($discussion_nodeElement->hasChildNodes()) {
			$childElement =& $discussion_nodeElement->firstChild;
			while ($childElement != NULL) {
				if (in_array($childElement->nodeName, $types)) {
					$childGood = $this->addPost($story, $discussion->id, $childElement);
					if (!$childGood)
						return FALSE;
				}
				$childElement =& $childElement->nextSibling;
			}
		}
		
		return TRUE;
	}
	
	function setCommonFields(& $partObj, & $partElement) {
		// Title
		$titleList =& $partElement->getElementsByPath("title");
		$titleElement =& $titleList->item(0);
		if ($titleList->getLength() != 1 && $partElement->nodeName != 'divider') {
			print "\nRequired 'title' element is missing from a ".$partElement->nodeName."!";
			return FALSE;
		} else if ($partElement->nodeName != 'divider') 
			$partObj->setField('title', html_entity_decode(trim($titleElement->getText())));
		
		// History
		$historyList =& $partElement->getElementsByPath("history");
		if ($historyList->getLength() != 1) {
			print "\nRequired 'history' element is missing!";
			return FALSE;
		}
		$historyElement =& $historyList->item(0);
		$historyGood = $this->setHistory($partObj, $historyElement);
		
		// Activation
		$activationList =& $partElement->getElementsByPath("activation");
		if ($activationList->getLength() > 1) {
			print "\nInvaid number of  'activation' elements!";
			return FALSE;
		}
		if ($activationList->getLength()) {
			$activationElement =& $activationList->item(0);
			$activationGood = $this->setActivation($partObj, $activationElement);
		} else {
			$activationGood = TRUE;
		}
		
		// Permissions
		$permissionsList =& $partElement->getElementsByPath("permissions");
		if ($permissionsList->getLength() > 1) {
			print "\nInvaid number of  'permissions' elements!";
			return FALSE;
		}
		if ($permissionsList->getLength()) {
			$permissionsElement =& $permissionsList->item(0);
			$permissionsGood = $this->setPermissions($partObj, $permissionsElement);
		} else {
			$permissionsGood = TRUE;
		}
		
		if ($historyGood && $activationGood && $permissionsGood)
			return TRUE;
		else
			return FALSE;
	}
	
	function setHistory(& $partObj, & $historyElement) {
		// Creator
		$creatorList =& $historyElement->getElementsByPath("creator");
		$creatorElement =& $creatorList->item(0);
		if ($creatorList->getLength() != 1) {
			print "\nRequired 'creator' element is missing!";
			return FALSE;
		}
		$partObj->setField('addedby', trim($creatorElement->getText()));
		
		// Last Editor
		$last_editorList =& $historyElement->getElementsByPath("last_editor");
		$last_editorElement =& $last_editorList->item(0);
		if ($last_editorList->getLength() != 1) {
			print "\nRequired 'last_editor' element is missing!";
			return FALSE;
		}
		$partObj->setField('editedby', trim($last_editorElement->getText()));
		
		// Created Time
		$created_timeList =& $historyElement->getElementsByPath("created_time");
		$created_timeElement =& $created_timeList->item(0);
		if ($created_timeList->getLength() != 1) {
			print "\nRequired 'created_time' element is missing!";
			return FALSE;
		}
		$partObj->setField('addedtimestamp', trim($created_timeElement->getText()));
		
		// Last Edited Time
		$last_edited_timeList =& $historyElement->getElementsByPath("last_edited_time");
		$last_edited_timeElement =& $last_edited_timeList->item(0);
		if ($last_edited_timeList->getLength() != 1) {
			print "\nRequired 'last_edited_time' element is missing!";
			return FALSE;
		}
		$partObj->setField('editedtimestamp', trim($last_edited_timeElement->getText()));
		
		return TRUE;
	}
	
	function setActivation(& $partObj, & $activationElement) {
		// Activate Date
		$activate_dateList =& $activationElement->getElementsByPath("activate_date");
		$activate_dateElement =& $activate_dateList->item(0);
		if ($activate_dateList->getLength() != 1) {
			print "\nRequired 'activate_date' element is missing!";
			return FALSE;
		}
		$partObj->setField('activatedate', trim($activate_dateElement->getText()));
		
		// Deactivate Date
		$deactivate_dateList =& $activationElement->getElementsByPath("deactivate_date");
		$deactivate_dateElement =& $deactivate_dateList->item(0);
		if ($deactivate_dateList->getLength() != 1) {
			print "\nRequired 'deactivate_date' element is missing!";
			return FALSE;
		}
		$partObj->setField('deactivatedate', trim($deactivate_dateElement->getText()));
		
		return TRUE;
	}
	
	function setPermissions(& $partObj, & $permissionsElement) {
		// Assume that we have added any agents as an editor already.
		
		// editor permissions
		$viewGood = $this->setPermission('view', $partObj, $permissionsElement);
		$addGood = $this->setPermission('add', $partObj, $permissionsElement);
		$editGood = $this->setPermission('edit', $partObj, $permissionsElement);
		$deleteGood = $this->setPermission('delete', $partObj, $permissionsElement);
		$discussGood = $this->setPermission('discuss', $partObj, $permissionsElement);
		
		// Locked flags
		$lockedList =& $permissionsElement->getElementsByPath("locked");
		if($lockedList->getLength() == 1) {
			$partObj->setField("locked", 1);
		}
		
		if ($viewGood && $addGood && $editGood && $deleteGood && $discussGood)
			return TRUE;
		else
			return FALSE;
	}
	
	function setPermission($type, & $partObj, & $permissionsElement) {
		$permissionList =& $permissionsElement->getElementsByPath($type."_permission");
		if ($permissionList->getLength() > 1) {
			print "\nInvaid number of '".$type."_permission' elements!";
			return FALSE;
		}
		
		if ($permissionElement =& $permissionList->item(0)) {
			// Get all the agents
			$agentList =& $permissionElement->getElementsByPath("agent");
			$count = $agentList->getLength();
			for ($i=0; $i<$count; $i++) {
				$agentElement =& $agentList->item($i);
				
				// Set the permissions for for the aget/part
				$partObj->setUserPermissionDown($type, trim($agentElement->getText()));
			}
		}
		return TRUE;
	}
	
	function setHeaderFooter(& $site, & $siteElement) {
		// header
		$headerList =& $siteElement->getElementsByPath("header");
		$headerElement =& $headerList->item(0);
		if ($headerElement) {
			$site->setField('header', html_entity_decode(trim($headerElement->getText())));
		}
		
		// footer
		$footerList =& $siteElement->getElementsByPath("footer");
		$footerElement =& $footerList->item(0);
		if ($footerElement) {
			$site->setField('footer', html_entity_decode(trim($footerElement->getText())));
		}
		
		return TRUE;
	}
	
	function setTheme(& $site, & $siteElement) {
		// theme
		$themeList =& $siteElement->getElementsByPath("theme");
		$themeElement =& $themeList->item(0);
		if ($themeElement) {

			$settingsParts = array(
						'name' => 'theme',
						'color_scheme' => 'colorscheme',
						'background_color' => 'bgcolor',
						'border_style' => 'borderstyle',
						'border_color' => 'bordercolor',
						'text_color' => 'textcolor',
						'link_color' => 'linkcolor',
						'navigation_arrangement' => 'nav_arrange',
						'navigation_width' => 'nav_width',
						'section_nav_size' => 'sectionnav_size',
						'page_nav_size' => 'nav_size'
			);
			
			$themeSettings = array();
			
			foreach ($settingsParts as $tag => $key) {
				$list =& $themeElement->getElementsByPath($tag);
				$element =& $list->item(0);
				if ($element) {
					if ($tag == 'name')
						$site->setField($key, trim($element->getText()));
					$themeSettings[$key] = trim($element->getText());
				}
			}
			
			$site->setField('themesettings', urlencode(serialize($themeSettings)));
		}
		
		return TRUE;
	}
	
	function addMedia(& $site, & $siteElement) {
		global $uploaddir;
		$sitedir = $uploaddir.$site->name."/";
		
		// media
		$mediaList =& $siteElement->getElementsByPath("media");
		$mediaElement =& $mediaList->item(0);
		if ($mediaElement) {
			
			// Make sure that we have the directory created.
			if (!is_dir($sitedir))
				mkdir($sitedir,0775);
			
			// Get the file list
			$fileList =& $mediaElement->getElementsByPath("media_file");
			$numFiles =& $fileList->getLength();
			
			// Add each file
			for ($i=0; $i<$numFiles; $i++) {
				$fileElement =& $fileList->item(0);
				
				// get the filename
				$filenameList =& $fileElement->getElementsByPath("filename");
				$filenameElement =& $filenameList->item(0);
				$filename = trim($filenameElement->getText());
				
				// Copy the file to segue
				$result = copy($this->_mediaSource.$filename,$sitedir.$filename);
				if (!$result) {
					print "\nError copying file '".$this->_mediaSource.$filename."' to '".$sitedir.$filename."'. ";
					return FALSE;
				}
				
				// Collect our info about the file
				$size = filesize($sitedir.$filename);
				
				$creatorList =& $fileElement->getElementsByPath("creator");
				$creatorElement =& $creatorList->item(0);
				$creatorId =  db_get_value("user", "user_id", "user_uname='".trim($creatorElement->getText())."'");
				
				$last_editorList =& $fileElement->getElementsByPath("last_editor");
				$last_editorElement =& $last_editorList->item(0);
				$last_editorId =  db_get_value("user", "user_id", "user_uname='".trim($last_editorElement->getText())."'");
				
				$last_edited_timeList =& $fileElement->getElementsByPath("last_edited_time");
				$last_edited_timeElement =& $last_edited_timeList->item(0);
				$last_edited_time = trim($last_edited_timeElement->getText());
				
				$typeList =& $fileElement->getElementsByPath("type");
				$typeElement =& $typeList->item(0);
				$type = trim($typeElement->getText());
				
				
				// Insert into the media directory
				$query = "
					INSERT INTO 
						media 
					SET 
						media_tag='".$filename."',
						FK_site='".$site->id."',
						FK_createdby='".$creatorId."',
						FK_updatedby='".$last_editorId."',
						media_updated_tstamp='".$last_edited_time."',
						media_type='".$type."',
						media_size='".$size."'";
				
				db_query($query);
			}
		}
		
		return TRUE;
	}

}