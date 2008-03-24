<?php
/**
 * Answer an tar.gz archive of a site export and media.
 *
 *
 * @since 3/13/08
 * 
 * @copyright Copyright &copy; 2007, Middlebury College
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License (GPL)
 *
 * @version $Id$
 */ 

require_once("Archive/Tar.php");

require_once(dirname(__FILE__).'/DomitSiteExporter.class.php');
require_once(dirname(__FILE__).'/Segue2DomitSiteExporter.class.php');

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
	 * Recursively delete a directory
	 * 
	 * @param string $path
	 * @return void
	 * @since 1/18/08
	 */
	function deleteRecursive ($path) {
		if (is_dir($path)) {
			$entries = scandir($path);
			foreach ($entries as $entry) {
				if ($entry != '.' && $entry != '..') {
					deleteRecursive($path.DIRECTORY_SEPARATOR.$entry);
				}
			}
			rmdir($path);
		} else {
			unlink($path);
		}
	}


db_connect($dbhost, $dbuser, $dbpass, $dbdb);

$errorPrinter = new SegueErrorPrinter;

if (!defined('DATAPORT_SEGUE1_SECRET_KEY'))
	$errorPrinter->doError(500, 'Invalid configuration: DATAPORT_SEGUE1_SECRET_KEY is not defined.');

if (!defined('DATAPORT_SEGUE1_SECRET_VALUE'))
	$errorPrinter->doError(500, 'Invalid configuration: DATAPORT_SEGUE1_SECRET_VALUE is not defined.');

if (!defined('DATAPORT_TMP_DIR'))
	$errorPrinter->doError(500, 'Invalid configuration: DATAPORT_TMP_DIR is not defined.');

if (!is_writable(DATAPORT_TMP_DIR))
	$errorPrinter->doError(500, 'Invalid configuration: DATAPORT_TMP_DIR ('.DATAPORT_TMP_DIR.') is writable.');

if (!isset($_GET[DATAPORT_SEGUE1_SECRET_KEY]))
	$errorPrinter->doError(403, 'Invalid Key/Password combination.');

if ($_GET[DATAPORT_SEGUE1_SECRET_KEY] != DATAPORT_SEGUE1_SECRET_VALUE)
	$errorPrinter->doError(403, 'Invalid Key/Password combination.');
	
if (!isset($_GET['site']) || !strlen($_GET['site']))
	$errorPrinter->doError(400, 'No site specified.');

/*********************************************************
 * Do the export
 *********************************************************/
$sitename = $_GET['site'];
$exportpath = DATAPORT_TMP_DIR;

// Fetch the site.
$site =& new Site($sitename);
$site->fetchDown(TRUE);
$site->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen();

// Fetch the location of the media files.
$imagepath = $uploaddir.'/'.$sitename.'/';

	
// Get the XML for the site
$siteExporter =& new Segue2DomitSiteExporter();
$siteXML =& $siteExporter->export($site);

// make a directory for the site contents
$siteDir = $exportpath.'/'.$sitename.'/';
if (file_exists($siteDir))
	deletePath($siteDir);
mkdir ($siteDir);

// Copy the Media Files to the sitedir.
dir_copy( $imagepath, $siteDir.'media/');

// Save the XML to a file.
$xmlFile = $siteDir.'site.xml';


if (!$handle = fopen($xmlFile, 'a'))
	$errorPrinter->doError(500, "Cannot open file ($xmlFile)");

// Write $somecontent to our opened file.
if (!fwrite($handle, $siteXML)) {
	$errorPrinter->doError(500, "Cannot write to file ($xmlFile)");
}
fclose($handle);

/*********************************************************
 * Compress the export and send it
 *********************************************************/
$archiveName = basename(trim($siteDir, '/')).".tar.gz";
$archive = new Archive_Tar($exportpath.'/'.$archiveName);
$archive->createModify($siteDir, '', DATAPORT_TMP_DIR);

// Remove the directory
deleteRecursive($siteDir);

header("Content-Type: application/x-gzip;");
header('Content-Disposition: attachment; filename="'.$archiveName.'"');
print file_get_contents($exportpath.'/'.$archiveName);

// Clean up the archive
unlink($exportpath.'/'.$archiveName);

exit;
?>