<?

require_once(dirname(__FILE__).'/DomitSiteImporter.class.php');

require_once(dirname(__FILE__).'/domit/xml_domit_getelementsbypath.php');
require_once(dirname(__FILE__).'/domit/xml_domit_nodemaps.php');
require_once(dirname(__FILE__).'/domit/xml_domit_parser.php');
require_once(dirname(__FILE__).'/domit/xml_domit_utilities.php');

require_once(dirname(__FILE__)."/../includes.inc.php");
require_once(dirname(__FILE__).'/../objects/slot.inc.php');
require_once(dirname(__FILE__).'/../objects/segue.inc.php');
require_once(dirname(__FILE__).'/../objects/site.inc.php');
require_once(dirname(__FILE__).'/../objects/section.inc.php');
require_once(dirname(__FILE__).'/../objects/page.inc.php');
require_once(dirname(__FILE__).'/../objects/story.inc.php');
require_once(dirname(__FILE__).'/../permissions.inc.php');

// print help if requested/needed
if ($argc != 5 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {

?>

This script will export the specified Segue site 
and its media to the specified directory. Be sure 
to run this script as a user who has permission to
access the segue media directory or media files will
not be copied.

This script assumes several things:
	1. All users specified in the XML file exist in the Segue instance
	   being imported to.
	2. All media specified in the "<media>" elements of the XML file exist 
	   in the <media directory>.

	If the conditions above are not met, then the site will not fully import
	and this script will throw errors.

	If it is desired to import a site anyway, edit the XML file to remove 
	references to the non-existant users/media.

Usage:

	<?php echo $argv[0]; ?> <apache uid> <apache gid> <xml file path> <media directory>

With the --help, -help, -h, or -? options, you can get this help.

<?
} else {
	$apacheUser = $argv[$argc-4];
	$apacheGroup = $argv[$argc-3];
	$xmlFile = $argv[$argc-2];
	$mediaDir = $argv[$argc-1];
	
	// Get the XML for the site
	$siteImporter =& new DomitSiteImporter();
	$successfull =& $siteImporter->importFile($xmlFile, $mediaDir, $apacheUser, $apacheGroup);
	
	
	if (!$successfull) {
		print "\n";
		print"Failure. Could not fully import site. Please consult the messages above.";
		print "\n";
	}
}
exit(0);