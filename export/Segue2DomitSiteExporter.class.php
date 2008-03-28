<?php
/**
 * @since 3/24/08
 * @package segue1.export
 * 
 * @copyright Copyright &copy; 2007, Middlebury College
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License (GPL)
 *
 * @version $Id$
 */ 

require_once(dirname(__FILE__).'/DomitSiteExporter.class.php');

/**
 * Export a site partially cleaned up for Segue 2
 * 
 * @since 3/24/08
 * @package segue1.export
 * 
 * @copyright Copyright &copy; 2007, Middlebury College
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License (GPL)
 *
 * @version $Id$
 */
class Segue2DomitSiteExporter 
	extends DomitSiteExporter
{
		
	/**
	 * Add a story. The existing implementation doesn't use CDATA blocks and instead
	 * runs the text through htmlspecialchars, greatly increasing its size.
	 * 
	 * @param object story $story
	 * @param object DOMITElement $pageElement
	 * @return void
	 * @access public
	 * @since 3/24/08
	 */
	function addStory (&$story, &$pageElement) {
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
			$shorttext->appendChild($this->_document->createCDATASection(stripslashes($story->getField('shorttext'))));
			$shorttext->setAttribute('text_type', $texttype);
 		}
 		
 		if ($story->getField('longertext')) {
 			$longertext =& $this->_document->createElement('longertext');
			$storyElement->appendChild($longertext);
			$longertext->appendChild($this->_document->createCDATASection(stripslashes($story->getField('longertext'))));
			$longertext->setAttribute('text_type', $texttype);
 		}
 		
 		$this->addStoryProporties($story, $storyElement);
	}
	
	/**
	 * Answer an element that represents a version of a story.
	 * 
	 * @param array $version
	 * @param string $storyType One of link, rss, file, image, text
	 * @param optional string $textType text or html
	 * @return DOMITElement
	 * @access protected
	 * @since 3/24/08
	 */
	function getVersion ($version, $storyType, $textType = 'html') {
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
				$value1 = stripslashes(urldecode($version['version_text_short']));
				$value2 = stripslashes(urldecode($version['version_text_long']));
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
	
}

?>