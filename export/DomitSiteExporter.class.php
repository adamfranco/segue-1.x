<?

require_once('SiteExporter.class.php');
require_once('../objects/discussion.inc.php');

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
		$doctype .= "\n\t<!ELEMENT site (title,history,(activation?),(permissions?),(section|navlink)*)>";
		$doctype .= "\n\t<!ATTLIST site id CDATA #REQUIRED owner CDATA #REQUIRED type (system|class|personal|other) #REQUIRED>";
 		
 		$doctype .= "\n\t<!ELEMENT section (title,history,(activation?),(permissions?),(page|navlink|heading|divider)*)>";
		$doctype .= "\n\t<!ELEMENT page (title,history,(activation?),(permissions?),(story|link|image|file)*)>";
		$doctype .= "\n\t<!ELEMENT story (title,history,(activation?),(permissions?),shorttext,(longtext?),(category?),(discussion?))>";
		$doctype .= "\n\t<!ELEMENT navlink (title,history,(activation?),(permissions?),url)>";
		$doctype .= "\n\t<!ELEMENT heading (title,history,(activation?),(permissions?))>";
		$doctype .= "\n\t<!ELEMENT divider (history,(activation?),(permissions?))>";
		$doctype .= "\n\t<!ELEMENT link (title,history,(activation?),(permissions?),description,url,(category?),(discussion?))>";
		$doctype .= "\n\t<!ELEMENT image (title,history,(activation?),(permissions?),description,(filename|url),(category?),(discussion?))>";
		$doctype .= "\n\t<!ELEMENT file (title,history,(activation?),(permissions?),description,(filename|url),(category?),(discussion?))>";
		
		$doctype .= "\n\t<!ELEMENT discussion (discussion_node*)>";
		$doctype .= "\n\t<!ATTLIST discussion email_owner (TRUE|FALSE) #REQUIRED show_authors (TRUE|FALSE) #REQUIRED show_others_posts (TRUE|FALSE) #REQUIRED>";
		$doctype .= "\n\t<!ELEMENT discussion_node (creator,created_time,title,text,(discussion_node*))>";
		
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
		$doctype .= "\n\t<!ELEMENT locked (#PCDATA)>";
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
		$doctype .= "\n\t<!ELEMENT url (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT description (#PCDATA)>";
		
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
		$siteElement->setAttribute('id', $site->getField('name'));
		$siteElement->setAttribute('owner', slot::getOwner($site->getField('name')));
		$siteElement->setAttribute('type', $site->getField('type'));
		
		$this->addCommonProporties($site, $siteElement);
		
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
 		
 		if ($story->getField('longtext')) {
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
				
		$this->addStoryProporties($story, $storyElement);
		
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
		$filename = addslashes(urldecode(db_get_value("media","media_tag","media_id=".$story->getField("longertext"))));
		$longertext->appendChild($this->_document->createTextNode(htmlspecialchars($filename)));
		
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
	}
	
	function getPermissions(& $obj, &$permissionsElement) {
		
		// get all the editors
		$obj->buildPermissionsArray();
		$permissions = $obj->getPermissions();
// 		print "\npermissions:";
// 		print_r($permissions);
		
		if ($isLocked = $obj->getField('locked')) {
			$locked =& $this->_document->createElement('locked');
			$permissionsElement->appendChild($locked);
			$locked->appendChild($this->_document->createTextNode("TRUE"));
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
			
			$discussionObj = & new discussion(&$story);
			$discussionObj->_fetchchildren();
			
			while ($node =& $discussionObj->getNext()) {
				$this->addDiscussionNode($node, $discussion);
			}
		}
	}
	
	function addCommonProporties (& $partObj, & $element) {
		// title
		$title =& $this->_document->createElement('title');
		$element->appendChild($title);
		$title->appendChild($this->_document->createTextNode(htmlspecialchars($partObj->getField('title'))));
		
		// history
		$history =& $this->_document->createElement('history');
		$element->appendChild($history);
		$this->gethistory($partObj, $history);
		
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$hasPerms = $this->getPermissions($partObj, $permissions);
		if ($hasPerms)
			$element->appendChild($permissions);
			
		// permissions
		$activation =& $this->_document->createElement('activation');
		$hasActivation = $this->getActivation($partObj, $activation);
		if ($hasActivation)
			$element->appendChild($activation);
	
	}
	
	function addDiscussionNode(& $node, & $parentElement) {
		$discussionNode =& $this->_document->createElement('discussion_node');
		$parentElement->appendChild($discussionNode);
		
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
		
		// If this node has children, add them.
		$node->_fetchchildren();
		foreach ($node->children as $key => $val) {
			$this->addDiscussionNode($node->children[$key], $discussionNode);
		}
	}
}