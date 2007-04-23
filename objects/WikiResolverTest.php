<?php
/**
 * This is a wiki-text test 
 *
 * @since 4/23/07
 * @package segue
 * 
 * @copyright Copyright &copy; 2005, Middlebury College
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License (GPL)
 *
 * @version $Id$
 */ 
 
require_once(dirname(__FILE__)."/WikiResolver.class.php");
require_once(dirname(__FILE__)."/../config.inc.php");
require_once(dirname(__FILE__)."/../dbwrapper.inc.php");
require_once(dirname(__FILE__)."/../functions.inc.php");

db_connect($dbhost, $dbuser, $dbpass, $dbdb);
 

$text = '<p>This template is designed for a [[course]] that makes [[Really Big|extensive]] use of the web.  It includes the following sections:</p>
<ul>
    <li><span style="font-style: italic;">Introduction</span>: includes pages for course description (this page), [[site:template3 syllabus]], requirements, grading and [[site:template3 professor|prof]], as well as announcments, links and a list of participants</li>
    <li><span style="font-style: italic;">[[Assignments]]</span>: includes pages for each week of the semester</li>
    <li><span style="font-style: italic;">[[Discussions|discuss]]</span>: includes a page for discussion topics</li>
    <li><span style="font-style: italic;">[[Blog]]</span>: includes a page for a course blog with lists of blog categories and recent posts</li>
    <li><span style="font-style: italic;">Presentations</span>: includes a sample slide show</li>
</ul>
<p>[http://google.com]</p>
<p>[http://google.com The Google Search Engine]</p>

<p>[[site:template4]]</p>
<p>[[site:template4|My other template]]</p>

<p><span style="color: rgb(51, 51, 51);">(Any of the sections and pages in this site can be edited and deleted as needed... As well additional sections and pages can be added)</span></p>';

$currentSite = "template1";
$currentSection = "12";
$currentPage = "33";


$wikiResolver =& WikiResolver::instance();
$newText = $wikiResolver->parseText($text, $currentSite, $currentSection, $currentPage);

print "\n\n<h2>Old Text: </h2>";
print "\n".$text;
print "\n\n<h2>New Text: </h2>";
print "\n".$newText;

?>