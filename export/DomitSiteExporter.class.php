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
require_once(dirname(__FILE__).'/../permissions.inc.php');

/**
 * This class exports a Segue Site to a string in a specified format.
 *
 * @author Adam Franco
 */

class DomitSiteExporter {

	/**
	 * The current DOMIT document
	 */
	var $_document;
	
	function DomitSiteExporter() {
	}
	
	/**
	 * Exports a site to XML
	 * 
	 * @param object Site The site to export.
	 *
	 * @return string The resulting text.
	 */
	function export(& $site) {
		$this->_document =& new DOMIT_Document();
		$this->_document->xmlDeclaration = '<?xml version="1.0" encoding="UTF-8" '.'?'.'>';
		$doctype = "<!DOCTYPE site [";
		$doctype .= "\n\t<!ELEMENT site (title,history,(activation?),(permissions?),header,footer,(theme?),(media?),(section|navlink)*)>";
		$doctype .= "\n\t<!ATTLIST site id CDATA #REQUIRED owner CDATA #REQUIRED type (system|class|personal|other) #REQUIRED>";		
 		$doctype .= "\n\t<!ELEMENT section (title,history,(activation?),(permissions?),(page|navlink|heading|divider)*)>";
		$doctype .= "\n\t<!ELEMENT page (title,history,(activation?),(permissions?),(story|link|image|file)*)>";
		$doctype .= "\n\t<!ATTLIST page story_order (custom|addeddesc|addedasc|editeddesc|editedasc|author|editor|category|titleasc|titledesc) #REQUIRED horizontal_rule (TRUE|FALSE) #REQUIRED show_creator (TRUE|FALSE) #REQUIRED show_date (TRUE|FALSE) #REQUIRED archiving CDATA #REQUIRED>";
		$doctype .= "\n\t<!ELEMENT story (title,history,(activation?),(permissions?),shorttext,(longtext?),(category?),(discussion?))>";
		$doctype .= "\n\t<!ELEMENT navlink (title,history,(activation?),(permissions?),url)>";
		$doctype .= "\n\t<!ELEMENT heading (title,history,(activation?),(permissions?))>";
		$doctype .= "\n\t<!ELEMENT divider (history,(activation?),(permissions?))>";
		$doctype .= "\n\t<!ELEMENT link (title,history,(activation?),(permissions?),description,url,(category?),(discussion?))>";
		$doctype .= "\n\t<!ELEMENT image (title,history,(activation?),(permissions?),description,(filename|url),(category?),(discussion?))>";
		$doctype .= "\n\t<!ELEMENT file (title,history,(activation?),(permissions?),description,(filename|url),(category?),(discussion?))>";
		
		$doctype .= "\n\t<!ELEMENT discussion (discussion_node*)>";
		$doctype .= "\n\t<!ATTLIST discussion email_owner (TRUE|FALSE) #REQUIRED show_authors (TRUE|FALSE) #REQUIRED show_others_posts (TRUE|FALSE) #REQUIRED>";
		$doctype .= "\n\t<!ELEMENT discussion_node (creator,created_time,title,text,rating?,filename?,(discussion_node*))>";
		
		$doctype .= "\n\t<!ELEMENT media (media_file*)>";
		$doctype .= "\n\t<!ELEMENT media_file (creator,last_editor,last_edited_time,filename,type)>";
		$doctype .= "\n\t<!ELEMENT type (#PCDATA)>";
		
		$doctype .= "\n\t<!ELEMENT theme (name,color_scheme?,background_color?,border_style?,border_color?,text_color?,link_color?,navigation_arrangement?,navigation_width?,section_nav_size?,page_nav_size?)>";
		$doctype .= "\n\t<!ELEMENT name (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT color_scheme (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT background_color (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT border_style (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT border_color (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT text_color (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT link_color (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT navigation_arrangement (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT navigation_width (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT section_nav_size (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT page_nav_size (#PCDATA)>";
		
		$doctype .= "\n\t<!ELEMENT history (creator,created_time,last_editor,last_edited_time)>";
		$doctype .= "\n\t<!ELEMENT created_time (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT last_edited_time (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT creator (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT last_editor (#PCDATA)>";
		
		$doctype .= "\n\t<!ELEMENT activation ((manual_activiation)?,(activate_date)?,(deactivate_date)?)>";
		$doctype .= "\n\t<!ELEMENT manual_activiation (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT activate_date (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT deactivate_date (#PCDATA)>";
		
		$doctype .= "\n\t<!ELEMENT permissions ((locked?),(view_permission?),(add_permission?),(edit_permission?),(delete_permission?),(discuss_permission?))>";
		$doctype .= "\n\t<!ELEMENT locked EMPTY>";
		$doctype .= "\n\t<!ELEMENT view_permission (agent*)>";
		$doctype .= "\n\t<!ELEMENT add_permission (agent*)>";
		$doctype .= "\n\t<!ELEMENT edit_permission (agent*)>";
		$doctype .= "\n\t<!ELEMENT delete_permission (agent*)>";
		$doctype .= "\n\t<!ELEMENT discuss_permission (agent*)>";
		$doctype .= "\n\t<!ELEMENT agent (#PCDATA)>";
		
		$doctype .= "\n\t<!ELEMENT category (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT title (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT text (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT shorttext (#PCDATA)>";
		$doctype .= "\n\t<!ATTLIST shorttext text_type (text|html) #REQUIRED>";
		$doctype .= "\n\t<!ELEMENT longtext (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT filename (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT rating (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT url (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT description (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT header (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT footer (#PCDATA)>";
		
		$doctype .= "\n]>";
		
		$this->_document->doctype = $doctype;
		$this->addSite($site);
		return $this->_document->toNormalizedString();
//		return $this->_document->toString();
	}
	
	function saveXML($fileName) {
		return $this->_document->saveXML($fileName);
	} //saveXML

	/**
	 * Adds a site to the buffer.
	 *
	 * @param object site $site The site to add.
	 * @param integer $indent The indent level of the object
	 */
	function addSite(& $site) {
		// Add flags for when to add permissions rows
		$site->spiderDownLockedFlag();	
		
		$siteElement =& $this->_document->createElement('site');
		$this->_document->appendChild($siteElement);
		
		// site id and type
		$siteElement->setAttribute('owner', slot::getOwner($site->getField('name')));
		$siteElement->setAttribute('type', $site->getField('type'));
		
		$this->addCommonProporties($site, $siteElement);
		
		// use the slot-name for backwards-compatability
		$siteElement->setAttribute('id', $site->getField('name'));
		
		// header
		$header =& $this->_document->createElement('header');
		$siteElement->appendChild($header);
		$header->appendChild($this->_document->createTextNode(htmlspecialchars($site->getField('header'))));
		
		// footer
		$footer =& $this->_document->createElement('footer');
		$siteElement->appendChild($footer);
		$footer->appendChild($this->_document->createTextNode(htmlspecialchars($site->getField('footer'))));
		
		// Theme
		$this->addTheme($site, $siteElement);
		
		// Media
		$this->addMedia($site, $siteElement);
		
		// sections
 		foreach ($site->sections as $key => $val) {
  			if ($site->sections[$key]->getField('type') == 'link')
  				$this->addNavLink($site->sections[$key], $siteElement);
  			else if ($site->sections[$key]->getField('type') == 'heading')
  				$this->addHeading($site->sections[$key], $siteElement);
  			else if ($site->sections[$key]->getField('type') == 'divider')
  				$this->addDivider($site->sections[$key], $siteElement);
  			else
 				$this->addSection($site->sections[$key], $siteElement);
 		}
	}

	/**
	 * Adds a section to the buffer.
	 *
	 * @param object section $section The section to add.
	 * @param integer $indent The indent level of the object
	 */
	function addSection(& $section, & $siteElement) {
		$sectionElement =& $this->_document->createElement('section');
		$siteElement->appendChild($sectionElement);
		
		$this->addCommonProporties($section, $sectionElement);
			
		foreach ($section->pages as $key => $val) {
 			if ($section->pages[$key]->getField('type') == 'link')
 				$this->addNavLink($section->pages[$key], $sectionElement);
 			else if ($section->pages[$key]->getField('type') == 'heading')
 				$this->addHeading($section->pages[$key], $sectionElement);
 			else if ($section->pages[$key]->getField('type') == 'divider')
 				$this->addDivider($section->pages[$key], $sectionElement);
 			else if ($section->pages[$key]->getField('type') == 'content')
 				$this->addPageContent($section->pages[$key], $sectionElement);
 			else if ($section->pages[$key]->getField('type') == 'rss')
 				$this->addPageRSS($section->pages[$key], $sectionElement);
 			else if ($section->pages[$key]->getField('type') == 'participants')
 				$this->addParticipantList($section->pages[$key], $sectionElement);
 			else if ($section->pages[$key]->getField('type') == 'tags')
 				$this->addCategoryList($section->pages[$key], $sectionElement);
 			else
 				$this->addPage($section->pages[$key], $sectionElement);
		}
	}

	/**
	 * Adds a page to the buffer.
	 *
	 * @param object page $page The page to add.
	 * @param integer $indent The indent level of the object
	 */
	function addPage(& $page, & $sectionElement) {
		$pageElement =& $this->_document->createElement('page');
		$sectionElement->appendChild($pageElement);
		
		$this->addCommonProporties($page, $pageElement);
		
		// Display options
		if (!$order = $page->getField('storyorder'))
			$order = "custom";
		$pageElement->setAttribute('story_order', $order);
		$pageElement->setAttribute('horizontal_rule', (($page->getField('showhr'))?"TRUE":"FALSE"));
		$pageElement->setAttribute('show_creator', (($page->getField('showcreator'))?"TRUE":"FALSE"));
		$pageElement->setAttribute('show_date', (($page->getField('showdate'))?"TRUE":"FALSE"));
		if (!$archiving = $page->getField('archiveby'))
			$archiving = "none";
		$pageElement->setAttribute('archiving', $archiving);
		
		if ($page->getField('location') == 'right')
			$pageElement->setAttribute('location', 'right');
		else
			$pageElement->setAttribute('location', 'left');
			
		$pageElement->setAttribute('type', $page->getField('type'));
		
// 		if (strlen($page->getField('url')))
// 			$pageElement->appendChild($this->_document->createElement('url'))->appendChild(
// 				$this->_document->createCDATASection($page->getField('url')));
		
		foreach ($page->stories as $key => $val) {
  			if ($page->stories[$key]->getField('type') == 'link')
  				$this->addLink($page->stories[$key], $pageElement);
  			else if ($page->stories[$key]->getField('type') == 'file')
  				$this->addFile($page->stories[$key], $pageElement);
  			else if ($page->stories[$key]->getField('type') == 'image')
  				$this->addImage($page->stories[$key], $pageElement);
 			else
				$this->addStory($page->stories[$key], $pageElement);
		}

	}

	/**
	 * Adds a story to the buffer.
	 *
	 * @param object story $story The story to add.
	 * @param integer $indent The indent level of the object
	 */
	function addStory(& $story, & $pageElement) {
		$storyElement =& $this->_document->createElement('story');
		$pageElement->appendChild($storyElement);
		
		$this->addCommonProporties($story, $storyElement);
		
		if ($story->getField('texttype') == "text")
			$texttype = "text";
		else
			$texttype = "html";
				
 		if ($story->getField('shorttext')) {
 			$shorttext =& $this->_document->createElement('shorttext');
			$storyElement->appendChild($shorttext);
			$shorttext->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('shorttext'))));
			$shorttext->setAttribute('text_type', $texttype);
 		}
 		
 		if ($story->getField('longertext')) {
 			$longertext =& $this->_document->createElement('longertext');
			$storyElement->appendChild($longertext);
			$longertext->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('longertext'))));
			$longertext->setAttribute('text_type', $texttype);
 		}
 		
 		$this->addStoryProporties($story, $storyElement);
	}
	
	/**
	 * Adds a link to the buffer.
	 *
	 * @param object link $link The link to add.
	 * @param integer $indent The indent level of the object
	 */
	function addNavLink(& $link, & $parentElement) {
		$linkElement =& $this->_document->createElement('navlink');
		$parentElement->appendChild($linkElement);
		
		$this->addCommonProporties($link, $linkElement);
		
		// url
		$url =& $this->_document->createElement('url');
		$linkElement->appendChild($url);
		$url->appendChild($this->_document->createTextNode(htmlspecialchars($link->getField('url'))));
		
		if ($link->getField('location') == 'right')
			$linkElement->setAttribute('location', 'right');
		else
			$linkElement->setAttribute('location', 'left');
	}
	
	/**
	 * Adds an RSS page block to the buffer.
	 *
	 * @param object link $link The link to add.
	 * @param integer $indent The indent level of the object
	 */
	function addPageRSS(& $link, & $parentElement) {
		$linkElement =& $this->_document->createElement('pageRSS');
		$parentElement->appendChild($linkElement);
		
		$this->addCommonProporties($link, $linkElement);
		
		// url
		$url =& $this->_document->createElement('url');
		$linkElement->appendChild($url);
		$url->appendChild($this->_document->createTextNode(htmlspecialchars($link->getField('url'))));
		
		if ($link->getField('location') == 'right')
			$linkElement->setAttribute('location', 'right');
		else
			$linkElement->setAttribute('location', 'left');
	}

	/**
	 * Adds a heading to the buffer.
	 *
	 * @param object heading $heading The heading to add.
	 * @param integer $indent The indent level of the object
	 */
	function addHeading(& $heading, & $parentElement) {
		$headingElement =& $this->_document->createElement('heading');
		$parentElement->appendChild($headingElement);
		
		$this->addCommonProporties($heading, $headingElement);
		
		if ($heading->getField('location') == 'right')
			$headingElement->setAttribute('location', 'right');
		else
			$headingElement->setAttribute('location', 'left');
	}
	
	/**
	 * Adds a page content to the buffer.
	 *
	 * @param object page $page The pageContent to add.
	 * @param integer $indent The indent level of the object
	 */
	function addPageContent(& $page, & $parentElement) {
		$element = $parentElement->appendChild(
			$this->_document->createElement('pageContent'));
		
		$this->addCommonProporties($page, $element);
		
		$element->appendChild($this->_document->createElement('text'))->appendChild(
			$this->_document->createCDATASection(urldecode($page->getField('text'))));
		
		if ($page->getField('location') == 'right')
			$element->setAttribute('location', 'right');
		else
			$element->setAttribute('location', 'left');
	}
	
	/**
	 * Adds a divider to the buffer.
	 *
	 * @param object divider $divider The divider to add.
	 * @param integer $indent The indent level of the object
	 */
	function addDivider(& $divider, & $parentElement) {
		$dividerElement =& $this->_document->createElement('divider');
		$parentElement->appendChild($dividerElement);
		
		// history
		$history =& $this->_document->createElement('history');
		$dividerElement->appendChild($history);
		$this->gethistory($divider, $history);
	
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$hasPerms = $this->getPermissions($divider, $permissions);
		if ($hasPerms)
			$dividerElement->appendChild($permissions);
		
		if ($divider->getField('location') == 'right')
			$dividerElement->setAttribute('location', 'right');
		else
			$dividerElement->setAttribute('location', 'left');
	}
	
	/**
	 * Adds a participant list to the buffer.
	 *
	 * @param object $participantList
	 * @param integer $indent The indent level of the object
	 */
	function addParticipantList(& $participantList, & $parentElement) {
		$element =& $this->_document->createElement('participantList');
		$parentElement->appendChild($element);
		
		$this->addCommonProporties($participantList, $element);
	
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$hasPerms = $this->getPermissions($participantList, $permissions);
		if ($hasPerms)
			$element->appendChild($permissions);
		
		if ($participantList->getField('location') == 'right')
			$element->setAttribute('location', 'right');
		else
			$element->setAttribute('location', 'left');
	}
	
	/**
	 * Adds a category list to the buffer.
	 *
	 * @param object $categoryList
	 * @param integer $indent The indent level of the object
	 */
	function addCategoryList(& $categoryList, & $parentElement) {
		$element =& $this->_document->createElement('categoryList');
		$parentElement->appendChild($element);
		
		$this->addCommonProporties($categoryList, $element);
	
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$hasPerms = $this->getPermissions($categoryList, $permissions);
		if ($hasPerms)
			$element->appendChild($permissions);
		
		if ($categoryList->getField('location') == 'right')
			$element->setAttribute('location', 'right');
		else
			$element->setAttribute('location', 'left');
	}
	
	/**
	 * Adds a file to the buffer.
	 *
	 * @param object story $story The file to add.
	 * @param integer $indent The indent level of the object
	 */
	function addFile(& $story, & $pageElement) {
		$storyElement =& $this->_document->createElement('file');
		$pageElement->appendChild($storyElement);
				
		$this->addCommonProporties($story, $storyElement);
		
 		// description
		$shorttext =& $this->_document->createElement('description');
		$storyElement->appendChild($shorttext);
		$shorttext->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('shorttext'))));
 		
 		// file
		$longertext =& $this->_document->createElement('filename');
		$storyElement->appendChild($longertext);
		$filename = addslashes(urldecode(db_get_value("media","media_tag","media_id=".$story->getField("longertext"))));
		$longertext->appendChild($this->_document->createTextNode(htmlspecialchars($filename)));
		
		$this->addStoryProporties($story, $storyElement);
	}
	
	/**
	 * Adds a image to the buffer.
	 *
	 * @param object story $story The image to add.
	 * @param integer $indent The indent level of the object
	 */
	function addImage(& $story, & $pageElement) {
		$storyElement =& $this->_document->createElement('image');
		$pageElement->appendChild($storyElement);
		
		$this->addCommonProporties($story, $storyElement);
		
 		// description
		$shorttext =& $this->_document->createElement('description');
		$storyElement->appendChild($shorttext);
		$shorttext->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('shorttext'))));
 		
 		// file
		$longertext =& $this->_document->createElement('filename');
		$storyElement->appendChild($longertext);
		$filename = addslashes(urldecode(db_get_value("media","media_tag","media_id=".$story->getField("longertext"))));
		$longertext->appendChild($this->_document->createTextNode(htmlspecialchars($filename)));
		
		$this->addStoryProporties($story, $storyElement);
	}

	/**
	 * Adds a link to the buffer.
	 *
	 * @param object story $story The link to add.
	 * @param integer $indent The indent level of the object
	 */
	function addLink(& $story, & $pageElement) {		
		$storyElement =& $this->_document->createElement('link');
		$pageElement->appendChild($storyElement);
		
		$this->addCommonProporties($story, $storyElement);
		
 		// description
		$shorttext =& $this->_document->createElement('description');
		$storyElement->appendChild($shorttext);
		$shorttext->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('shorttext'))));
 		
 		// file
		$longertext =& $this->_document->createElement('url');
		$storyElement->appendChild($longertext);
		$longertext->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField("url"))));
		
		$this->addStoryProporties($story, $storyElement);
	}
	
	function getHistory(& $obj, &$historyElement) {
		// Creator
		$creator =& $this->_document->createElement('creator');
		$historyElement->appendChild($creator);
		$creator->appendChild($this->_document->createTextNode($obj->getField('addedby')));
		
		// Created timestamp
		$created_time =& $this->_document->createElement('created_time');
		$historyElement->appendChild($created_time);
		$created_time->appendChild($this->_document->createTextNode($obj->getField('addedtimestamp')));
		
		// Last Editor
		$last_editor =& $this->_document->createElement('last_editor');
		$historyElement->appendChild($last_editor);
		$last_editor->appendChild($this->_document->createTextNode($obj->getField('editedby')));
		
		// Last Edited timestamp
		$edited_time =& $this->_document->createElement('last_edited_time');
		$historyElement->appendChild($edited_time);
		$edited_time->appendChild($this->_document->createTextNode($obj->getField('editedtimestamp')));
		
		$versions = $this->getVersions($obj);
		if ($versions)
			$historyElement->appendChild($versions);
	}
	
	/**
	 * Answer a list of version or null if not supported
	 * 
	 * @param object $obj
	 * @return mixed DOMITElement or null
	 * @access protected
	 * @since 2/13/08
	 */
	protected function getVersions ($obj) {
		if (strtolower(get_class($obj)) != 'story')
			return null;
		
		$element = $this->_document->createElement('versions');
		$versions = get_versions($obj->id);
		foreach ($versions as $version) {
			$element->appendChild($this->getVersion($version, $obj->getField('type'), $obj->getField('texttype')));
		}
		return $element;
	}
	
	/**
	 * Answer an element that represents a version of a story.
	 * 
	 * @param array $version
	 * @param string $storyType One of link, rss, file, image, text
	 * @param optional string $textType text or html
	 * @return DOMITElement
	 * @access protected
	 * @since 2/13/08
	 */
	protected function getVersion (array $version, $storyType, $textType = 'html') {
		$element = $this->_document->createElement('version');
		$element->setAttribute('id', $version['version_id']);
		$element->setAttribute('number', $version['version_order']);
		$element->setAttribute('time_stamp', $version['create_time_stamp']);
		$element->setAttribute('agent_id', $version['author_uname']);
		
		switch ($storyType) {
			case 'link':
			case 'rss':
				$field1 = 'description';
				$field2 = 'url';
				$value1 = urldecode($version['version_text_short']);
				$value2 = urldecode($version['version_text_long']);
				break;
			case 'file':
			case 'image':
				$field1 = 'description';
				$field2 = 'filename';
				$value1 = urldecode($version['version_text_short']);
				$filename = addslashes(urldecode(db_get_value("media","media_tag","media_id='".addslashes(urldecode($version['version_text_long']))."'")));
				$value2 = htmlspecialchars($filename);
				break;
			default:
				$field1 = 'shorttext';
				$field2 = 'longertext';
				$value1 = urldecode($version['version_text_short']);
				$value2 = urldecode($version['version_text_long']);
		}
		
		$commentElement = $element->appendChild($this->_document->createElement('comment'));
		$commentElement->appendChild($this->_document->createCDATASection($version['version_comments']));
		
		$shortText = $element->appendChild($this->_document->createElement($field1));
		$shortText->appendChild($this->_document->createCDATASection($value1));
		$shortText->setAttribute('text_type', $textType);
		
		$shortText = $element->appendChild($this->_document->createElement($field2));
		$shortText->appendChild($this->_document->createCDATASection($value2));
		$shortText->setAttribute('text_type', $textType);
		
		return $element;
	}
	
	function getPermissions(& $obj, &$permissionsElement) {
		
		// get all the editors
		$obj->buildPermissionsArray();
		$permissions = $obj->getPermissions();
// 		print "\npermissions:";
// 		print_r($permissions);
		
		if (get_class($obj) != 'site' && $isLocked = $obj->getField('locked')) {
			$locked =& $this->_document->createElement('locked');
			$permissionsElement->appendChild($locked);
		}
		
		$hasView = $this->addPermissions($obj, $permissionsElement, 'view', $permissions);
		$hasAdd = $this->addPermissions($obj, $permissionsElement, 'add', $permissions);
		$hasEdit = $this->addPermissions($obj, $permissionsElement, 'edit', $permissions);
		$hasDelete = $this->addPermissions($obj, $permissionsElement, 'delete', $permissions);
		$hasDiscuss = $this->addPermissions($obj, $permissionsElement, 'discuss', $permissions);
		
		if ($isLocked | $hasView | $hasAdd | $hasEdit | $hasDelete | $hasDiscuss)
			$hasAny = TRUE;
		else
			$hasAny = FALSE;
		
		return $hasAny;
	}
	
	function hasPermission($permissionArray, $agent, $type) {
		$permTypes = array('add'=>0, 'edit'=>1, 'delete'=>2, 'view'=>3, 'discuss'=>4);
		return $permissionArray[$agent][$permTypes[$type]];
	
	}
	
	function addPermissions(& $obj, & $permissionsElement, $type, $permissionsArray) {
		//add agents/groups of type
		$hasPerms = FALSE;
		$element =& $this->_document->createElement($type.'_permission');
		
		foreach ($permissionsArray as $editorName => $array) {
			
			// if they have permission here, create an entry for them.
			if ($this->hasPermission($permissionsArray, $editorName, $type) && !$obj->getField("l%$editorName%".$type)) {
				$agent =& $this->_document->createElement('agent');
				$agent->appendChild($this->_document->createTextNode($editorName));
				$element->appendChild($agent);
				$hasPerms = TRUE;
			}
		}
		
		if ($hasPerms)
			$permissionsElement->appendChild($element);
		
		return $hasPerms;
	}
	
	function getActivation(& $obj, & $activationElement) {
		$hasActivation = FALSE;
		
		if (get_class($obj) != 'story' && !$obj->getField('active')) {
			$active =& $this->_document->createElement('manual_activiation');
			$active->appendChild($this->_document->createTextNode('INACTIVE'));
			$activationElement->appendChild($active);
			$hasActivation = TRUE;
		}
		
		if ($obj->getField('activatedate') != '0000-00-00') {
			$activate_date =& $this->_document->createElement('activate_date');
			$activate_date->appendChild($this->_document->createTextNode($obj->getField('activatedate')));
			$activationElement->appendChild($activate_date);
			$hasActivation = TRUE;
		}
		
		if ($obj->getField('deactivatedate') != '0000-00-00') {
			$deactivate_date =& $this->_document->createElement('deactivate_date');
			$deactivate_date->appendChild($this->_document->createTextNode($obj->getField('deactivatedate')));
			$activationElement->appendChild($deactivate_date);
			$hasActivation = TRUE;
		}
		
		return $hasActivation;
	}
	
	function addStoryProporties (& $story, & $storyElement) {		
		// category
		if ($story->getField('category')) {
			$category =& $this->_document->createElement('category');
			$storyElement->appendChild($category);
			$category->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('category'))));
		}
		
		// discussions
		if ($story->getField('discuss')) {
			$discussion =& $this->_document->createElement('discussion');
			$storyElement->appendChild($discussion);
			
			$emailOwner = (($story->getField('discussemail'))?"TRUE":"FALSE");
			$discussion->setAttribute('email_owner', $emailOwner);
			
			$show_authors = (($story->getField('discussauthor') == 1)?"TRUE":"FALSE");
			$discussion->setAttribute('show_authors', $show_authors);
			
			$show_others_posts = (($story->getField('discussdisplay') == 1)?"TRUE":"FALSE");
			$discussion->setAttribute('show_others_posts', $show_others_posts);
			
			$discussionObj = & new discussion( $story);
			$discussionObj->_fetchchildren();
			
			while ($node =& $discussionObj->getNext()) {
				$this->addDiscussionNode($node, $discussion);
			}
		}
	}
	
	function addCommonProporties (& $partObj, & $element) {
		if (isset($partObj->id))
			$element->setAttribute('id', $partObj->id);
		
		// title
		$title =& $this->_document->createElement('title');
		$element->appendChild($title);
		$title->appendChild($this->_document->createTextNode(htmlspecialchars($partObj->getField('title'))));
		
		// history
		$history =& $this->_document->createElement('history');
		$element->appendChild($history);
		$this->gethistory($partObj, $history);
			
		// activation
		$activation =& $this->_document->createElement('activation');
		$hasActivation = $this->getActivation($partObj, $activation);
		if ($hasActivation)
			$element->appendChild($activation);
		
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$hasPerms =& $this->getPermissions($partObj, $permissions);
		if ($hasPerms)
			$element->appendChild($permissions);
	}
	
	function addDiscussionNode(& $node, & $parentElement) {
		$discussionNode =& $this->_document->createElement('discussion_node');
		$parentElement->appendChild($discussionNode);
		
		if (isset($node->id))
			$discussionNode->setAttribute('id', $node->id);
		
		// Add the creator, title, timestamp, and text.
		// creator
		$creator =& $this->_document->createElement('creator');
		$discussionNode->appendChild($creator);
		$creator->appendChild($this->_document->createTextNode(htmlspecialchars($node->authoruname)));
		
		// created_time
		$created_time =& $this->_document->createElement('created_time');
		$discussionNode->appendChild($created_time);
		$created_time->appendChild($this->_document->createTextNode(htmlspecialchars($node->tstamp)));
		
		// title
		$title =& $this->_document->createElement('title');
		$discussionNode->appendChild($title);
		$title->appendChild($this->_document->createTextNode(htmlspecialchars($node->subject)));
		
		// text
		$text =& $this->_document->createElement('text');
		$discussionNode->appendChild($text);
		$text->appendChild($this->_document->createTextNode(htmlspecialchars($node->content)));
		
		// rating
		$rating =& $this->_document->createElement('rating');
		$discussionNode->appendChild($rating);
		$rating->appendChild($this->_document->createTextNode(htmlspecialchars($node->rating)));
		
		// filename
		$filename =& $this->_document->createElement('filename');
		$discussionNode->appendChild($filename);
		$filename->appendChild($this->_document->createTextNode($node->media_tag));
		
		// If this node has children, add them.
		$node->_fetchchildren();
		foreach ($node->children as $key => $val) {
			$this->addDiscussionNode($node->children[$key], $discussionNode);
		}
	}
	
	function addMedia(& $site, & $siteElement) {
		$query = "
			SELECT 
				media_tag AS filename,
				media_updated_tstamp AS last_edited_time,
				media_type AS type,
				creator.user_uname AS creator,
				editor.user_uname AS last_editor
			FROM 
				media
					INNER JOIN
				slot
					ON media.FK_site = slot.FK_site
					INNER JOIN
				user AS creator
					ON media.FK_createdby = creator.user_id
					INNER JOIN
				user AS editor
					ON media.FK_updatedby = editor.user_id
			
			WHERE
				slot_name = '".$site->name."'
				AND media_location = 'local'
		"; 
		$r = db_query($query); 
		
		if (db_num_rows($r)) {
			$mediaElement =& $this->_document->createElement('media');
			$siteElement->appendChild($mediaElement);
			
			while ($a = db_fetch_assoc($r)) {
				$fileElement =& $this->_document->createElement('media_file');
				$mediaElement->appendChild($fileElement);
				
				// Creator
				$creatorElement =& $this->_document->createElement('creator');
				$fileElement->appendChild($creatorElement);
				$creatorElement->appendChild($this->_document->createTextNode(htmlspecialchars($a['creator'])));
				
				// last_editor
				$last_editorElement =& $this->_document->createElement('last_editor');
				$fileElement->appendChild($last_editorElement);
				$last_editorElement->appendChild($this->_document->createTextNode(htmlspecialchars($a['last_editor'])));
				
				// last_edited_time
				$last_edited_timeElement =& $this->_document->createElement('last_edited_time');
				$fileElement->appendChild($last_edited_timeElement);
				$last_edited_timeElement->appendChild($this->_document->createTextNode(htmlspecialchars($a['last_edited_time'])));
				
				// filename
				$filenameElement =& $this->_document->createElement('filename');
				$fileElement->appendChild($filenameElement);
				$filenameElement->appendChild($this->_document->createTextNode(htmlspecialchars($a['filename'])));
				
				// type
				$typeElement =& $this->_document->createElement('type');
				$fileElement->appendChild($typeElement);
				$typeElement->appendChild($this->_document->createTextNode(htmlspecialchars($a['type'])));
			}
		}
	}
	
	function addTheme(& $site, & $siteElement) {
		
		// If we have a theme add it
		if ($site->getField('theme')) {
			$themeElement =& $this->_document->createElement('theme');
			$siteElement->appendChild($themeElement);

			$element =& $this->_document->createElement('name');
			$themeElement->appendChild($element);
			$element->appendChild($this->_document->createTextNode($site->getField('theme')));
			
			// if we have themesettings, add them
			if ($themesettings = $site->getField('themesettings')) {
				$settingsArray = unserialize(urldecode($themesettings));
				
				$settingsParts = array(
						
						'color_scheme' => 'colorscheme',
						'background_color' => 'bgcolor',
						'border_style' => 'borderstyle',
						'border_color' => 'bordercolor',
						'text_color' => 'textcolor',
						'link_color' => 'linkcolor',
						'navigation_arrangement' => 'nav_arrange',
						'site_width' => 'site_width',
						'navigation_width' => 'nav_width',
						'section_nav_size' => 'sectionnav_size',
						'page_nav_size' => 'nav_size'
				);
				
				foreach ($settingsParts as $tag => $key) {
					
					if ($settingsArray[$key]) {
						$element =& $this->_document->createElement($tag);
						$themeElement->appendChild($element);
						$element->appendChild($this->_document->createTextNode($settingsArray[$key]));
					}
				}
			}
		}
	}
}