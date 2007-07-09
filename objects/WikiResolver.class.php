<?php
/**
 * @since 4/23/07
 * @package segue
 * 
 * @copyright Copyright &copy; 2005, Middlebury College
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License (GPL)
 *
 * @version $Id$
 */ 
global $cfg;
/**
 * This is a wiki-text parsing and formatting class. All wiki-text operations should
 * go through this class
 * 
 * @since 4/23/07
 * @package segue
 * 
 * @copyright Copyright &copy; 2005, Middlebury College
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License (GPL)
 *
 * @version $Id$
 */
class WikiResolver {
	
	var $_cachedSites;
	var $_currentSlotName = null;
	var $_currentSectionId = null;
	var $_currentPageId = null;

	/**
	 * Get the singleton instance of the WikiResolver
	 * 
	 * @return object WikiResolver
	 * @access public
	 * @since 4/23/07
	 * @static
	 */
	function &instance () {
		if (!defined("WIKIRESOLVER_INSTANTIATED")) {
			$GLOBALS['__WikiResolver'] =& new WikiResolver();
			define("WIKIRESOLVER_INSTANTIATED", true);
		}
		
		return $GLOBALS['__WikiResolver'];
	}
	
	/**
	 * Add an inline field to show the wiki-style link for an item
	 * 
	 * @param string $title
	 * @param optional string $discussionId
	 * @return string The wiki markup
	 * @access public
	 * @since 4/23/07
	 */
	function getMarkupExample ($title, $discussionId = null) {
		global $cfg;
		
		ob_start();
		$title = trim($title);
		if ($title) {
			print "\n<a href='#' onclick=\"";
			print "if (this.nextSibling.style.display == 'none') { ";
			print "this.nextSibling.style.display = 'inline'; ";
			print "} else { ";
			print "this.nextSibling.style.display = 'none'; ";
			print "} ";
			print "return false; ";
			print "\">";
			print "<img src='".$cfg['full_uri']."/themes/common/images/WikiLink.gif' alt='wikilink' border='0'/>";
			print "</a>";
			print "<input type='text' value=\"[[";
			print $title;
			if ($discussionId)
				print "#".$discussionId;
			print "]]\"";
			print "style='display: none;' size='30' />";
		}
		
		return ob_get_clean();
	}
		
	/**
	 * Constructor. This class uses the "Singleton" pattern. 
	 * Do not use this constructor directly. Use "WikiResolver::instance()" instead.
	 * 
	 * @return void
	 * @access protected
	 * @since 4/23/07
	 */
	function WikiResolver () {
		// Verify that there is only one instance of the WikiResolver, 
		// enforcing the singleton pattern.
		$backtrace = debug_backtrace();
		if (false && $GLOBALS['__WikiResolver'] 
			|| !isset($backtrace[1])
			|| !(strtolower($backtrace[1]['class']) == 'wikiresolver'
				&& $backtrace[1]['function'] == 'instance'
// 				&& $backtrace[1]['type'] == '::'	// PHP 5.2.1 seems to get this wrong
			))
		{
			die("\n<dl style='border: 1px solid #F00; padding: 10px;'>"
			."\n\t<dt><strong>Invalid WikiResolver instantiation at...</strong></dt>"
			."\n\t<dd> File: ".$backtrace[0]['file']
			."\n\t\t<br/> Line: ".$backtrace[0]['line']
			."\n\t</dd>"
			."\n\t<dt><strong>Access Wiki Resolver with <em>WikiResolver::instance()</em></strong></dt>"
			."\n\t<dt><strong>Backtrace:</strong></dt>"
			."\n\t<dd>".printpre(debug_backtrace(), true)."</dd>"
			."\n\t<dt><strong>PHP Version:</strong></dt>"
			."\n\t<dd>".phpversion()."</dd>"
			."\n</dl>");
		}
		
		// Initialize our instance variables
		$this->_cachedSites = array();
	}
	
	/**
	 * Parse the wiki-text and replace wiki markup with HTML markup
	 * 
	 * @param string $text Text that may contain wiki markup.
	 * @param string $currentSlotName
	 * @param string $currentSectionId
	 * @param string $currentPageId
	 * @return string The translated text
	 * @access public
	 * @since 4/23/07
	 */
	function parseText ($text, $currentSlotName, $currentSectionId, $currentPageId) 
	{
		// set the current place
		$this->_currentSlotName = $currentSlotName;
		$this->_currentSectionId = $currentSectionId;
		$this->_currentPageId = $currentPageId;
		
		// loop through the text and look for wiki markup.
		preg_match_all('/(\[\[[^\]]+\]\])/', $text, $matches);
		
		// for each wiki link replace it with the HTML link text
		foreach ($matches[1] as $wikiLink) {
			$htmlLink = $this->_makeHtmlLink($wikiLink);
			$text = str_replace($wikiLink, $htmlLink, $text);
		}
		
		// loop through the text and look for wiki external link markup.
		$regexp = "/
\[		# starting bracket

\s*		# optional whitespace

(
	[a-z]{2,7}	# Protocol i.e. http, ftp, rtsp, ...
	:\/\/		# separator
	[^\]\s]+	# the rest of the url
)

(?: [\s|]* ([^\]]+) )?	# optional display text

\s*		# optional whitespace

\]		# closing bracket
/xi";
		preg_match_all($regexp, $text, $matches);
// 		printpre($matches);
		
		// for each wiki link replace it with the HTML link text
		foreach ($matches[0] as $index => $wikiText) {
			ob_start();
			print "<a href='".$matches[1][$index]."'>";
			if ($matches[2][$index])
				print $matches[2][$index];
			else
				print $matches[1][$index];
			print "</a>";
			
			$text = str_replace($wikiText, ob_get_clean(), $text);
		}
		
		// Unset the current place
		$this->_currentSlotName = null;
		$this->_currentSectionId = null;
		$this->_currentPageId = null;
		
		return $text;
	}
	
	/**
	 * Convert a single wiki link [[SomeTitle]] to an html link.
	 *
	 * Forms:
	 *		[[Some Title]]
	 *		[[Some Title|alternate text to display]]
	 *		[[site:my_other_slot_name Some Title]]
	 *		[[site:my_other_slot_name Some Title|alternate text to display]]
	 * 
	 * @param string $wikiText
	 * @return string An HTML version of the link
	 * @access private
	 * @since 4/23/07
	 */
	function _makeHtmlLink ($wikiText) {
		global $cfg;
		
		$regexp = "/

^		# Anchor for the beginning of the line
\[\[	# The opening link tags

	\s*		# optional whitespace

	(?: site:([a-z0-9_\-]+) \s+ )?	# An optional designator for linking to another site
	
	([^\]#\|]+)	# The Title of the linked section, page, story
	
	(?: \s*\#\s* ([0-9]+) )?	# The optional discussion post id
	
	(?: \s*\|\s* ([^\]]+) )?	# The optional link-text to display instead of the title

	\s*		# optional whitespace

\]\]	# The closing link tags
$		# Anchor for the end of the line

/xi";

		$siteOnlyRegexp = "/

^		# Anchor for the beginning of the line
\[\[	# The opening link tags

	\s*		# optional whitespace

	(?: site:([a-z0-9_\-]+) )?	# A designator for linking to another site
	
	(?: \s*\|\s* ([^\]]+) )?	# The optional link-text to display instead of the title

	\s*		# optional whitespace

\]\]	# The closing link tags
$		# Anchor for the end of the line

/xi";
		
		// Check for a link only to a site [[site:my_other_site]]
		if (preg_match($siteOnlyRegexp, $wikiText, $matches)) {
			
			$targetSite = $matches[1];
			
			if ($matches[2]) {
				$display = $matches[2];
			} else {
				$display = $targetSite;
			}
			
			ob_start();
			print "<a href='".$cfg['full_uri']."/index.php?&amp;action=site";
			print "&amp;site=".$targetSite;
			print "'>";
			print $display;
			print "</a>";
			return ob_get_clean();
		}
		
		// Links of the form [[Assignments]]
		else if (preg_match($regexp, $wikiText, $matches)) {
			
			if ($matches[1]) {
				$targetSite = $matches[1];
			} else {
				$targetSite = $this->_currentSlotName;
			}
			
			$targetTitle = $matches[2];
			
			$targetPost = $matches[3];
			
			if ($matches[4]) {
				$display = $matches[4];
			} else {
				$display = $targetTitle;
				if ($targetPost)
					$display .= " &#187; Discussion";
			}
			
			$targetPath = $this->_getPath($targetSite, $targetTitle);
			
			if ($targetPath) {
				ob_start();
				print "<a href='".$cfg['full_uri']."/index.php?&amp;action=site";
				print "&amp;site=".$targetPath['site'];
				if ($targetPath['section'])
					print "&amp;section=".$targetPath['section'];
				if ($targetPath['page'])
					print "&amp;page=".$targetPath['page'];
				if ($targetPath['story']) {
					print "&amp;story=".$targetPath['story'];
					print "&amp;detail=".$targetPath['story'];
				}
				if ($targetPost) {
					print "#".$targetPost;
				}
				print "'>";
				print $display;
				print "</a>";
				
				return ob_get_clean();
			} 
			// Return an add-node link instead
			else {
				ob_start();
				print "<a href='".$cfg['full_uri']."/index.php?&amp;action=add_node";
				print "&amp;site=".$targetSite;
				print "&amp;section=".$this->_currentSectionId;
				print "&amp;page=".$this->_currentPageId;
				print "&amp;link_title=".urlencode($targetTitle);
				print "'>";
				print $display;
				print " ?</a>";
				
				return ob_get_clean();
			}
		} 
		
		// If invalid, just return the wiki text.
		else {
			return $wikiText;
		}
	}
	
	/**
	 * Answer the path that matches the given title in the given site.
	 * 
	 * @param string $site The slot_name of the site to search
	 * @param string $title The title to search for.
	 * @return mixed array or null
	 * @access private
	 * @since 4/23/07
	 */
	function _getPath( $site, $title ) {
		if (!isset($this->_cachedSites[$site])) {
			$this->_loadSiteTitles($site);
		}
		
		if ($site == $this->_currentSlotName) {
			// if we have a current page, first check the stories of that page
			if ($this->_currentPageId) {
				$section = $this->_cachedSites[$site]->getChild($this->_currentSectionId);
				$page = $section->getChild($this->_currentPageId);
				$path = $page->searchBelow($title);
			}
			
			// Then check all pages, then stories below the current section
			if ((!isset($path) || !$path) && $this->_currentSectionId) {
				$section = $this->_cachedSites[$site]->getChild($this->_currentSectionId);
				$path = $section->searchBelow($title);
			}
			
			// Then check all sections, then pages, then stories below the current site
			if (!isset($path) || !$path) {
				$path = $this->_cachedSites[$site]->searchBelow($title);
			}
			
			return $path;
			
		} else {
			if (is_object($this->_cachedSites[$site]))
				return $this->_cachedSites[$site]->searchBelow($title);
			else
				return null;
		}
	}
	
	/**
	 * Run a SQL query to fetch a site hierarchy with titles
	 * 
	 * @param string $slotName The slot_name for the site
	 * @return void
	 * @access private
	 * @since 4/23/07
	 */
	function _loadSiteTitles ($slotName) {
		$query = "
SELECT
	slot_name,
	section_id,
	section_title,
	page_id,
	page_title,
	story_id,
	story_title
FROM
	slot
	LEFT JOIN section ON slot.FK_site = section.FK_site
	LEFT JOIN page ON page.FK_section = section_id
	LEFT JOIN story ON story.FK_page = page_id

WHERE
	slot_name = '".addslashes($slotName)."'

ORDER BY
	section_id, page_id, story_id
	
";
		$result = db_query($query);
		while ($row = db_fetch_assoc($result)) {
			if (!isset($site))
				$site = new SiteNode("", $row["slot_name"], 
					array("site"=>$row["slot_name"]));
			
			if ($row['section_id'] && !$site->childExists($row['section_id'])) {
				$section =& $site->addChild(
					 new SiteNode($row['section_title'], $row['section_id'],
						array("site"=>$row["slot_name"], "section" => $row['section_id'])));
			}
			
			if ($row['page_id'] && !$section->childExists($row['page_id'])) {
				$page =& $section->addChild(
					 new SiteNode($row['page_title'], $row['page_id'],
						array("site"=>$row["slot_name"], "section" => $row['section_id'], "page" => $row['page_id'])));
			}
			
			if ($row['story_id'] && !$section->childExists($row['story_id'])) {
				$story =& $page->addChild(
					 new SiteNode($row['story_title'], $row['story_id'],
						array("site"=>$row["slot_name"], "section" => $row['section_id'], "page" => $row['page_id'], "story" => $row['story_id'])));
			}
			
		}
		mysql_free_result($result);
		
		if (is_object($site))		
			$this->_cachedSites[$slotName] =& $site;
		else
			$this->_cachedSites[$slotName] = null;
	}
}


/**
 * A data container for caching information on a section page or story in a site.
 * 
 * @since 4/23/07
 * @package segue
 * 
 * @copyright Copyright &copy; 2005, Middlebury College
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License (GPL)
 *
 * @version $Id$
 */
class SiteNode {
		
	var $title;
	var $id;
	var $path;
	var $children;
	
	/**
	 * The constructor. 
	 * 
	 * @param string $title
	 * @param string $id
	 * @param string $path
	 * @return void
	 * @access public
	 * @since 4/23/07
	 */
	function SiteNode ( $title, $id, $path ) {
		$this->title = strtolower(trim($title));
		$this->id = $id;
		$this->path = $path;
		$this->children = array();
	}
	
	/**
	 * Add a new child
	 * 
	 * @param object SiteNode $child
	 * @return void
	 * @access public
	 * @since 4/23/07
	 */
	function &addChild ( &$child ) {
		$this->children[] =& $child;
		
		return $child;
	}
	
	/**
	 * Answer true if the child with the id passed exists yet
	 * 
	 * @param string id
	 * @return boolean
	 * @access public
	 * @since 4/23/07
	 */
	function childExists ($id) {
		foreach ($this->children as $child) {
			if ($child->id == $id)
				return true;
		}
		
		return false;
	}
	
	/**
	 * Answer the child of the given id
	 * 
	 * @param string id
	 * @return void
	 * @access public
	 * @since 4/23/07
	 */
	function &getChild ($id) {
		foreach ($this->children as $key => $child) {
			if ($child->id == $id)
				return $this->children[$key];
		}
		
		$null = null;
		return $null;
	}
	
	/**
	 * Do a breadth-first search for the given title, returning the path where found.
	 * 
	 * @param string $title
	 * @return array or null The path array or null if not found
	 * @access public
	 * @since 4/23/07
	 */
	function searchBelow ($title) {
		$title = strtolower(trim($title));
		
		if ($this->title == $title)
			return $this->path;
		
		foreach ($this->children as $child) {
			if ($child->title == $title)
				return $child->path;
		}
		
		foreach ($this->children as $child) {
			$path = $child->searchBelow($title);
			if ($path)
				return $path;
		}
	}
}

?>