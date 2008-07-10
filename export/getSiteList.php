<?php
/**
 * Answer an XML document with a listing of the slots that the user specified is 
 * authorized to migrate.
 *
 *
 * @since 3/13/08
 * 
 * @copyright Copyright &copy; 2007, Middlebury College
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License (GPL)
 *
 * @version $Id$
 */ 


// require_once(dirname(__FILE__).'/DomitSiteListGenerator.class.php');

require_once(dirname(__FILE__).'/domit/xml_domit_getelementsbypath.php');
require_once(dirname(__FILE__).'/domit/xml_domit_nodemaps.php');
require_once(dirname(__FILE__).'/domit/xml_domit_parser.php');
require_once(dirname(__FILE__).'/domit/xml_domit_utilities.php');

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

/**
 * Print out a slot line
 * 
 * @param array $slotInfo
 * @return void
 * @access public
 * @since 3/13/08
 */
function printSlotLine ($info) {
	print "\n\t<slot shortname='".$info['slot_name']."'";
	print " siteExists='".(($info['site_exists'])?'true':'false')."'";
	print " type='".$info['slot_type']."'>";
	print "\n\t\t<owner>".$info['slot_owner']."</owner>";
	if ($info['site_exists']) {
		print "\n\t\t<site>";
		print "\n\t\t\t<title><![CDATA[".$info['site_title']."]]></title>";
		print "\n\t\t\t<history>";
		print "\n\t\t\t\t<creator>".$info['site_addedby']."</creator>";
		print "\n\t\t\t\t<created_time>".$info['site_added_timestamp']."</created_time>";
		print "\n\t\t\t\t<last_editor>".$info['site_editedby']."</last_editor>";
		print "\n\t\t\t\t<last_edited_time>".$info['site_edited_timestamp']."</last_edited_time>";
		print "\n\t\t\t</history>";
		print "\n\t\t</site>";
	}	
	print "\n\t</slot>";
}


db_connect($dbhost, $dbuser, $dbpass, $dbdb);

$errorPrinter = new SegueErrorPrinter;

if (!defined('DATAPORT_SEGUE1_SECRET_KEY'))
	$errorPrinter->doError(500, 'Invalid configuration: DATAPORT_SEGUE1_SECRET_KEY is not defined.');

if (!defined('DATAPORT_SEGUE1_SECRET_VALUE'))
	$errorPrinter->doError(500, 'Invalid configuration: DATAPORT_SEGUE1_SECRET_VALUE is not defined.');

if (!isset($_GET[DATAPORT_SEGUE1_SECRET_KEY]))
	$errorPrinter->doError(403, 'Invalid Key/Password combination.');

if ($_GET[DATAPORT_SEGUE1_SECRET_KEY] != DATAPORT_SEGUE1_SECRET_VALUE)
	$errorPrinter->doError(403, 'Invalid Key/Password combination.');
	
if (!isset($_GET['user']) || !strlen($_GET['user']))
	$errorPrinter->doError(400, 'No user specified.');

// Validate the username
$usernames = @userlookup($_REQUEST['user'],LDAP_BOTH,LDAP_WILD,LDAP_LASTNAME,0);
if (!is_array($usernames))
	$errorPrinter->doError(400, "Invalid user, '".$_GET['user']."'.");
$nameGood = false;
foreach ($usernames as $uname => $fname) {
	if ($_GET['user'] === $uname && strlen($fname)) {
		$nameGood = true;
		break;
	}
}
if (!$nameGood)
	$errorPrinter->doError(400, "Invalid user, '".$_GET['user']."'. Did you mean one of the following? <div style='margin-left: 20px;'>".implode(" <br/>", array_keys($usernames))."</div>");
	


// Start the output
header('Content-Type: text/xml');
print "<slotList>";

/*********************************************************
 * Fetch all of the info for all of the sites and slots
 * that the user is an editor or owner for, so we don't have
 * to get them again.
 *********************************************************/
// this should include all sites that the user owns as well.
$userOwnedSlots = slot::getSlotInfoWhereUserOwner($_GET['user']);
if (!array_key_exists($_GET['user'], $userOwnedSlots)) {
		$userOwnedSlots[$_GET['user']] = array();
		$userOwnedSlots[$_GET['user']]['slot_name'] = $_GET['user'];
		$userOwnedSlots[$_GET['user']]['slot_type'] = 'personal';
		$userOwnedSlots[$_GET['user']]['slot_owner'] = $_GET['user'];
		$userOwnedSlots[$_GET['user']]['site_exits'] = false;		
}


// Add any user-owned groups that aren't already in the slot list
$userOwnedGroups = group::getGroupsOwnedBy($_GET['user']);
foreach ($userOwnedGroups as $classSiteName) {
	if (!isset($userOwnedSlots[$classSiteName])) {
		$userOwnedSlots[$classSiteName] = array();
		$userOwnedSlots[$classSiteName]['slot_name'] = $classSiteName;
		$userOwnedSlots[$classSiteName]['slot_type'] = 'class';
		$userOwnedSlots[$classSiteName]['slot_owner'] = $_GET['user'];
		$userOwnedSlots[$classSiteName]['site_exits'] = false;
	}
}
$siteLevelEditorSites = segue::getSiteInfoWhereUserIsSiteLevelEditor($_GET['user']);
if (!is_array($siteLevelEditorSites))
	$siteLevelEditorSites = array();
// $anyLevelEditorSites = segue::getSiteInfoWhereUserIsEditor($_GET['user']);

$allSlots = array_merge($userOwnedSlots, $userOwnedGroups, $siteLevelEditorSites);
foreach ($allSlots as $slotInfo)
	printSlotLine($slotInfo);

print "\n</slotList>";


?>
