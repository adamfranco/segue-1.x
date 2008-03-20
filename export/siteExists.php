<?php
/**
 * Answer a text file with 'true' or 'false' if a site exists for the parameters sent.
 *
 *
 * @since 3/13/08
 * 
 * @copyright Copyright &copy; 2007, Middlebury College
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License (GPL)
 *
 * @version $Id$
 */ 

require_once(dirname(__FILE__).'/../config.inc.php');
require_once(dirname(__FILE__)."/../includes.inc.php");
require_once(dirname(__FILE__).'/../objects/slot.inc.php');
require_once(dirname(__FILE__).'/../objects/segue.inc.php');
require_once(dirname(__FILE__).'/../objects/site.inc.php');
require_once(dirname(__FILE__).'/../objects/section.inc.php');
require_once(dirname(__FILE__).'/../objects/page.inc.php');
require_once(dirname(__FILE__).'/../objects/story.inc.php');
require_once(dirname(__FILE__).'/../objects/group.inc.php');

require_once(dirname(__FILE__).'/SegueErrorPrinter.class.php');

$errorPrinter = new SegueErrorPrinter;
if (!isset($_GET['site']) || !strlen($_GET['site']))
	$errorPrinter->doError(400, 'No site specified.');

$site = new Site($_GET['site']);
header("Content-Type: text/plain");
if (isset($site->site_does_not_exist) && $site->site_does_not_exist)
	print "false";
else
	print "true";
	