<?

require_once('SiteExporter.class.php');

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
		$doctype .= "\n\t<!ELEMENT site (title,permissions,(section|navlink)*)>";
		$doctype .= "\n\t<!ATTLIST site id CDATA #REQUIRED owner CDATA #REQUIRED type (system|class|personal|other) #REQUIRED>";
 		
 		$doctype .= "\n\t<!ELEMENT section (title,permissions,(page|navlink|heading|divider)*)>";
		$doctype .= "\n\t<!ELEMENT page (title,permissions,(story|link|image|file)*)>";
		$doctype .= "\n\t<!ELEMENT story (title,permissions,shorttext,longtext?,discussion?)>";
		$doctype .= "\n\t<!ELEMENT navlink (title,permissions,url)>";
		$doctype .= "\n\t<!ELEMENT heading (title,permissions)>";
		$doctype .= "\n\t<!ELEMENT divider (permissions)>";
		$doctype .= "\n\t<!ELEMENT link (title,permissions,description,url)>";
		$doctype .= "\n\t<!ELEMENT image (title,permissions,description,(filename|url))>";
		$doctype .= "\n\t<!ELEMENT file (title,permissions,description,(filename|url))>";
		
		$doctype .= "\n\t<!ELEMENT discussion (discussion_node*)>";
		$doctype .= "\n\t<!ELEMENT discussion_node (creator,created_time,title,text,(discussion_node*))>";
		
		$doctype .= "\n\t<!ELEMENT permissions EMPTY>";
//		$doctype .= "\n\t<!ELEMENT permissions (creator,created_time,last_editor,last_edited_time,view_permission,add_permission,edit_permission,delete_permission,discuss_permission)>";
		$doctype .= "\n\t<!ELEMENT view_permission (agent*)>";
		$doctype .= "\n\t<!ELEMENT add_permission (agent*)>";
		$doctype .= "\n\t<!ELEMENT edit_permission (agent*)>";
		$doctype .= "\n\t<!ELEMENT delete_permission (agent*)>";
		$doctype .= "\n\t<!ELEMENT discuss_permission (agent*)>";
		
		$doctype .= "\n\t<!ELEMENT title (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT text (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT shorttext (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT longtext (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT filename (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT url (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT description (#PCDATA)>";
		
		$doctype .= "\n\t<!ELEMENT created_time (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT last_edited_time (#PCDATA)>";
		
		$doctype .= "\n\t<!ELEMENT agent (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT creator (#PCDATA)>";
		$doctype .= "\n\t<!ELEMENT last_editor (#PCDATA)>";
		
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
		$siteElement =& $this->_document->createElement('site');
		$this->_document->appendChild($siteElement);
		
		// site id and type
		$siteElement->setAttribute('id', $site->getField('name'));
		$siteElement->setAttribute('owner', slot::getOwner($site->getField('name')));
		$siteElement->setAttribute('type', $site->getField('type'));
		
		// title
		$title =& $this->_document->createElement('title');
		$siteElement->appendChild($title);
		$title->appendChild($this->_document->createTextNode(htmlspecialchars($site->getField('title'))));
		
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$siteElement->appendChild($permissions);
		$this->getPermissions($site, $permissions);
		
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
		
		$title =& $this->_document->createElement('title');
		$sectionElement->appendChild($title);
		$title->appendChild($this->_document->createTextNode(htmlspecialchars($section->getField('title'))));
		
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$sectionElement->appendChild($permissions);
		$this->getPermissions($section, $permissions);

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
		
		$title =& $this->_document->createElement('title');
		$pageElement->appendChild($title);
		$title->appendChild($this->_document->createTextNode(htmlspecialchars($page->getField('title'))));
		
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$pageElement->appendChild($permissions);
		$this->getPermissions($page, $permissions);
		
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
		
		$title =& $this->_document->createElement('title');
		$storyElement->appendChild($title);
		$title->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('title'))));
		
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$storyElement->appendChild($permissions);
		$this->getPermissions($story, $permissions);
		
 		if ($story->getField('shorttext')) {
 			$shorttext =& $this->_document->createElement('shorttext');
			$storyElement->appendChild($shorttext);
			$shorttext->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('shorttext'))));
 		}
 		
 		if ($story->getField('longtext')) {
 			$longertext =& $this->_document->createElement('longertext');
			$storyElement->appendChild($longertext);
			$longertext->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('longertext'))));
 		}
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
		
		$title =& $this->_document->createElement('title');
		$linkElement->appendChild($title);
		$title->appendChild($this->_document->createTextNode(htmlspecialchars($link->getField('title'))));
		
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$linkElement->appendChild($permissions);
		$this->getPermissions($link, $permissions);
		
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
		
		$title =& $this->_document->createElement('title');
		$headingElement->appendChild($title);
		$title->appendChild($this->_document->createTextNode(htmlspecialchars($heading->getField('title'))));
		
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$headingElement->appendChild($permissions);
		$this->getPermissions($heading, $permissions);
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
	
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$dividerElement->appendChild($permissions);
		$this->getPermissions($divider, $permissions);
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
		
		$title =& $this->_document->createElement('title');
		$storyElement->appendChild($title);
		$title->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('title'))));
		
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$storyElement->appendChild($permissions);
		$this->getPermissions($story, $permissions);
		
 		// description
		$shorttext =& $this->_document->createElement('description');
		$storyElement->appendChild($shorttext);
		$shorttext->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('shorttext'))));
 		
 		
 		// file
		$longertext =& $this->_document->createElement('filename');
		$storyElement->appendChild($longertext);
		$filename = addslashes(urldecode(db_get_value("media","media_tag","media_id=".$story->getField("longertext"))));
		$longertext->appendChild($this->_document->createTextNode(htmlspecialchars($filename)));
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
		
		$title =& $this->_document->createElement('title');
		$storyElement->appendChild($title);
		$title->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('title'))));
		
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$storyElement->appendChild($permissions);
		$this->getPermissions($story, $permissions);
		
 		// description
		$shorttext =& $this->_document->createElement('description');
		$storyElement->appendChild($shorttext);
		$shorttext->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('shorttext'))));
 		
 		
 		// file
		$longertext =& $this->_document->createElement('filename');
		$storyElement->appendChild($longertext);
		$filename = addslashes(urldecode(db_get_value("media","media_tag","media_id=".$story->getField("longertext"))));
		$longertext->appendChild($this->_document->createTextNode(htmlspecialchars($filename)));
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
		
		$title =& $this->_document->createElement('title');
		$storyElement->appendChild($title);
		$title->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('title'))));
		
		// permissions
		$permissions =& $this->_document->createElement('permissions');
		$storyElement->appendChild($permissions);
		$this->getPermissions($story, $permissions);
		
 		// description
		$shorttext =& $this->_document->createElement('description');
		$storyElement->appendChild($shorttext);
		$shorttext->appendChild($this->_document->createTextNode(htmlspecialchars($story->getField('shorttext'))));
 		
 		
 		// file
		$longertext =& $this->_document->createElement('url');
		$storyElement->appendChild($longertext);
		$filename = addslashes(urldecode(db_get_value("media","media_tag","media_id=".$story->getField("longertext"))));
		$longertext->appendChild($this->_document->createTextNode(htmlspecialchars($filename)));
	}
	
	function getPermissions(& $obj, &$permissionsElement) {
		
	}
	
}