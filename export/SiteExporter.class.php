<?

/**
 * This class exports a Segue Site to a string in a specified format.
 *
 * @author Adam Franco
 */

class SiteExporter {
	
	/**
	 * The output buffer that will eventually be returned.
	 */
	var $_buffer;
	
	/**
	 * The indent string to use.
	 */
	var $_indenter;
	
	
	function SiteExporter($indenter = '	') {
		$this->_buffer = "";
		$this->_indenter = $indenter;
	}
	
	/**
	 * Exports a site to XML
	 * 
	 * @param object Site The site to export.
	 *
	 * @return string The resulting text.
	 */
	function export(& $site) {
		$this->startTags();
		
		$this->addSite($site, 0);
		
		$this->endTags();
		
		return $this->_buffer;
	}
	
	/**
	 * Adds the XML definitions to the buffer.
	 */
	function startTags() {
		$this->_buffer .= '<?xml version="1.0" encoding="ISO-8859-1"\?>';
	}

	/**
	 * Adds any end tags opened in the startTags.
	 */
	function endTags() {
		$this->_buffer .= '';
	}

	/**
	 * Adds a site to the buffer.
	 *
	 * @param object site $site The site to add.
	 * @param integer $indent The indent level of the object
	 */
	function addSite(& $site, $indent) {
		$tabs = $this->_getTabs($indent);
		
		$this->_buffer .= '
'.$tabs.'<site';
		$this->_buffer .= ' name="'.($site->getField('name')).'"';
		$this->_buffer .= ' owner="'.(slot::getOwner($site->getField('name'))).'"';
		$this->_buffer .= ' title="'.addslashes($site->getField('title')).'"';
		$this->_buffer .= ' type="'.($site->getField('type')).'"';
		$this->_buffer .= '>';
		
		foreach ($site->sections as $key => $val) {
			if ($site->sections[$key]->getField('type') == 'link')
				$this->addNavLink($site->sections[$key], $indent+1);
			if ($site->sections[$key]->getField('type') == 'heading')
				$this->addHeading($site->sections[$key], $indent+1);
			if ($site->sections[$key]->getField('type') == 'divider')
				$this->addDivider($site->sections[$key], $indent+1);
			else
				$this->addSection($site->sections[$key], $indent+1);
		}
		
		$this->_buffer .='
'.$tabs.'</site>
';
	}

	/**
	 * Adds a section to the buffer.
	 *
	 * @param object section $section The section to add.
	 * @param integer $indent The indent level of the object
	 */
	function addSection(& $section, $indent) {
		$tabs = $this->_getTabs($indent);
		
		$this->_buffer .= '
'.$tabs.'<section';
//		$this->_buffer .= ' id="'.$section->getField('id').'"';
		$this->_buffer .= ' title="'.addslashes($section->getField('title')).'"';
		$this->_buffer .= ' creator="'.$section->getField('addedby').'"';
		$this->_buffer .= ' creation_date="'.$section->getField('addedtimestamp').'"';
		$this->_buffer .= ' last_editor="'.$section->getField('editedby').'"';
		$this->_buffer .= ' last_edit_date="'.$section->getField('editedtimestamp').'"';
		$this->_buffer .= '>';
		
		foreach ($section->pages as $key => $val) {
			if ($section->pages[$key]->getField('type') == 'link')
				$this->addNavLink($section->pages[$key], $indent+1);
			if ($section->pages[$key]->getField('type') == 'heading')
				$this->addHeading($section->pages[$key], $indent+1);
			if ($section->pages[$key]->getField('type') == 'divider')
				$this->addDivider($section->pages[$key], $indent+1);
			else
				$this->addPage($section->pages[$key], $indent+1);
			}
		
		$this->_buffer .='
'.$tabs.'</section>';
	}

	/**
	 * Adds a page to the buffer.
	 *
	 * @param object page $page The page to add.
	 * @param integer $indent The indent level of the object
	 */
	function addPage(& $page, $indent) {
		$tabs = $this->_getTabs($indent);
		
		$this->_buffer .= '
'.$tabs.'<page';
//		$this->_buffer .= ' id="'.$page->getField('id').'"';
//		$this->_buffer .= ' addedby="'.$page->getField('addedby').'"';
		$this->_buffer .= ' title="'.addslashes($page->getField('title')).'"';
		$this->_buffer .= ' creator="'.$page->getField('addedby').'"';
		$this->_buffer .= ' creation_date="'.$page->getField('addedtimestamp').'"';
		$this->_buffer .= ' last_editor="'.$page->getField('editedby').'"';
		$this->_buffer .= ' last_edit_date="'.$page->getField('editedtimestamp').'"';
		$this->_buffer .= '>';
		
		foreach ($page->stories as $key => $val) {
 			if ($page->stories[$key]->getField('type') == 'link')
 				$this->addLink($page->stories[$key], $indent+1);
 			if ($page->stories[$key]->getField('type') == 'file')
 				$this->addFile($page->stories[$key], $indent+1);
 			if ($page->stories[$key]->getField('type') == 'image')
 				$this->addImage($page->stories[$key], $indent+1);
			else
				$this->addStory($page->stories[$key], $indent+1);
		}
		
		$this->_buffer .='
'.$tabs.'</page>';
	}

	/**
	 * Adds a story to the buffer.
	 *
	 * @param object story $story The story to add.
	 * @param integer $indent The indent level of the object
	 */
	function addStory(& $story, $indent) {
		$tabs = $this->_getTabs($indent);
		
		$this->_buffer .= '
'.$tabs.'<story';
//		$this->_buffer .= ' id="'.$story->getField('id').'"';
//		$this->_buffer .= ' addedby="'.$story->getField('addedby').'"';
		$this->_buffer .= ' title="'.addslashes($story->getField('title')).'"';
		$this->_buffer .= ' creator="'.$story->getField('addedby').'"';
		$this->_buffer .= ' creation_date="'.$story->getField('addedtimestamp').'"';
		$this->_buffer .= ' last_editor="'.$story->getField('editedby').'"';
		$this->_buffer .= ' last_edit_date="'.$story->getField('editedtimestamp').'"';
		$this->_buffer .= '>';
		
		$this->addContent($story, 'short', $indent+1);
		$this->addContent($story, 'longer', $indent+1);
//		$this->addCategories($story, $indent+1);
		
//		foreach ($story->storys as $key => $val) {
//			$this->addStory($story->storys[$key], $indent+1);
//		}
		
		$this->_buffer .='
'.$tabs.'</story>';
	}
	
	/**
	 * Adds categories on a story to the buffer.
	 *
	 * @param object story $story The story to add.
	 * @param integer $indent The indent level of the object
	 */
// 	function addCategories(& $story, $indent) {
// 		$tabs = $this->_getTabs($indent);		
// 		$tags = get_record_tags_info($story->getField('id'));
// 
// 		$this->_buffer .= '
// '.$tabs.'<tags>';
// 		if (count($tags) > 0) {
// 			$this->addCategory($tags, $indent);	
// 		}
// 		$this->_buffer .='
// '.$tabs.'</tags>';
// 
// 	}

	/**
	 * Adds categories on a story to the buffer.
	 *
	 * @param object story $story The story to add.
	 * @param integer $indent The indent level of the object
	 */
// 	function addCategory($tags, $indent) {
// 		$tabs = $this->_getTabs($indent);		
// 		foreach($tags as $tag) {
// 			$this->_buffer .= '
// '.$tabs.'<tag';
// 			$this->_buffer .= ' agent_id="'.addslashes($tag['agent_id']).'"';	
// 			$this->_buffer .= ' create_date="'.addslashes($tag['time_stamp']).'">';
// 			$this->_buffer .= $tag['value'];
// 			$this->_buffer .='
// '.$tabs.'</tag>';
// 		}
// 
// 	}
	
	/**
	 * Adds a link to the buffer.
	 *
	 * @param object link $link The link to add.
	 * @param integer $indent The indent level of the object
	 */
	function addNavLink(& $link, $indent) {
		$tabs = $this->_getTabs($indent);
		
		$this->_buffer .= '
'.$tabs.'<link';
		$this->_buffer .= ' title="'.addslashes($link->getField('title')).'"';
		$this->_buffer .= ' url="'.($link->getField('url')).'"';
		$this->_buffer .= ' creator="'.$link->getField('addedby').'"';
		$this->_buffer .= ' creation_date="'.$link->getField('addedtimestamp').'"';
		$this->_buffer .= ' last_editor="'.$link->getField('editedby').'"';
		$this->_buffer .= ' last_edit_date="'.$link->getField('editedtimestamp').'"';
		$this->_buffer .= '>';
		$this->_buffer .= '
'.$this->_getTabs($indent+1).($link->getField('shorttext'));
		$this->_buffer .='
'.$tabs.'</link>';
	}

	/**
	 * Adds a heading to the buffer.
	 *
	 * @param object heading $heading The heading to add.
	 * @param integer $indent The indent level of the object
	 */
	function addHeading(& $heading, $indent) {
		$tabs = $this->_getTabs($indent);
		
		$this->_buffer .= '
'.$tabs.'<heading';
		$this->_buffer .= ' title="'.addslashes($heading->getField('title')).'"';
		$this->_buffer .= ' creator="'.$heading->getField('addedby').'"';
		$this->_buffer .= ' creation_date="'.$heading->getField('addedtimestamp').'"';
		$this->_buffer .= ' last_editor="'.$heading->getField('editedby').'"';
		$this->_buffer .= ' last_edit_date="'.$heading->getField('editedtimestamp').'"';
		$this->_buffer .= '>';
//		$this->_buffer .= $this->_getTabs($indent+1).$story->getField('url');
		$this->_buffer .='
'.$tabs.'</heading>';
	}

	/**
	 * Adds a divider to the buffer.
	 *
	 * @param object divider $divider The divider to add.
	 * @param integer $indent The indent level of the object
	 */
	function addDivider(& $divider, $indent) {
		$tabs = $this->_getTabs($indent);
		
		$this->_buffer .= '
'.$tabs.'<divider';
		$this->_buffer .= ' title="'.addslashes($divider->getField('title')).'"';
				$this->_buffer .= ' creator="'.$divider->getField('addedby').'"';
		$this->_buffer .= ' creation_date="'.$divider->getField('addedtimestamp').'"';
		$this->_buffer .= ' last_editor="'.$divider->getField('editedby').'"';
		$this->_buffer .= ' last_edit_date="'.$divider->getField('editedtimestamp').'"';
		$this->_buffer .= '>';
//		$this->_buffer .= $this->_getTabs($indent+1).$story->getField('url');
		$this->_buffer .='
'.$tabs.'</divider>';
	}

	/**
	 * Adds a content to the buffer.
	 *
	 * @param object story $story The content to add.
	 * @param integer $indent The indent level of the object
	 */
	function addContent(& $story, $type, $indent) {
		$tabs = $this->_getTabs($indent);
		if ($story->getField($type.'text')) {
			$this->_buffer .= '
'.$tabs.'<content';
			$this->_buffer .= ' type="'.$type.'"';
			$this->_buffer .= '>
';
			$this->_buffer .= $this->_getTabs($indent+1).($story->getField($type.'text'));
			$this->_buffer .='
'.$tabs.'</content>';
		}
	}

	/**
	 * Adds a file to the buffer.
	 *
	 * @param object story $story The file to add.
	 * @param integer $indent The indent level of the object
	 */
	function addFile(& $story, $indent) {
		$tabs = $this->_getTabs($indent);
		
		$this->_buffer .= '
'.$tabs.'<file';
		$this->_buffer .= ' title="'.addslashes($story->getField('title')).'"';
		$filename = addslashes(urldecode(db_get_value("media","media_tag","media_id=".$story->getField("longertext"))));
		$this->_buffer .= ' url="'.$filename.'"';
		$this->_buffer .= ' creator="'.$story->getField('addedby').'"';
		$this->_buffer .= ' creation_date="'.$story->getField('addedtimestamp').'"';
		$this->_buffer .= ' last_editor="'.$story->getField('editedby').'"';
		$this->_buffer .= ' last_edit_date="'.$story->getField('editedtimestamp').'"';
		$this->_buffer .= '>';
		if ($story->getField('shorttext')) {
			$this->_buffer .= '
'.$this->_getTabs($indent+1).($story->getField('shorttext')).'
';
		}
		$this->_buffer .= $tabs.'</file>';
	}
	
	/**
	 * Adds a image to the buffer.
	 *
	 * @param object story $story The image to add.
	 * @param integer $indent The indent level of the object
	 */
	function addImage(& $story, $indent) {
		$tabs = $this->_getTabs($indent);
		
		$this->_buffer .= '
'.$tabs.'<image';
		$this->_buffer .= ' title="'.addslashes($story->getField('title')).'"';
		$filename = addslashes(urldecode(db_get_value("media","media_tag","media_id=".$story->getField("longertext"))));
		$this->_buffer .= ' url="'.$filename.'"';
		$this->_buffer .= ' creator="'.$story->getField('addedby').'"';
		$this->_buffer .= ' creation_date="'.$story->getField('addedtimestamp').'"';
		$this->_buffer .= ' last_editor="'.$story->getField('editedby').'"';
		$this->_buffer .= ' last_edit_date="'.$story->getField('editedtimestamp').'"';
		$this->_buffer .= '>';
		if ($story->getField('shorttext')) {
			$this->_buffer .= '
'.$this->_getTabs($indent+1).($story->getField('shorttext')).'
';
		}
		$this->_buffer .= $tabs.'</image>';
	}

	/**
	 * Adds a link to the buffer.
	 *
	 * @param object story $story The link to add.
	 * @param integer $indent The indent level of the object
	 */
	function addLink(& $story, $indent) {
		$tabs = $this->_getTabs($indent);
		
		$this->_buffer .= '
'.$tabs.'<link';
		$this->_buffer .= ' title="'.addslashes($story->getField('title')).'"';
		$this->_buffer .= ' url="'.($story->getField('url')).'"';
		$this->_buffer .= '>';
		$this->_buffer .= ' creator="'.$story->getField('addedby').'"';
		$this->_buffer .= ' creation_date="'.$story->getField('addedtimestamp').'"';
		$this->_buffer .= ' last_editor="'.$story->getField('editedby').'"';
		$this->_buffer .= ' last_edit_date="'.$story->getField('editedtimestamp').'"';
		if ($story->getField('shorttext')) {
			$this->_buffer .= '
'.$this->_getTabs($indent+1).($story->getField('shorttext')).'
';
		}
		$this->_buffer .= $tabs.'</link>';
	}

	/**
	 * Returns a string of indents of the number specified
	 *
	 * @param integer $indent The indent level
	 * @return string The tabs string.
	 */
	function _getTabs($indent) {
		$tabs = '';
		for ($i=0; $i<$indent; $i++)
			$tabs .= $this->_indenter;
		return $tabs;
	}
	
}