<?

require_once(dirname(__FILE__).'/DomitSiteExporter.class.php');

require_once(dirname(__FILE__).'/domit/xml_domit_getelementsbypath.php');
require_once(dirname(__FILE__).'/domit/xml_domit_nodemaps.php');
require_once(dirname(__FILE__).'/domit/xml_domit_parser.php');
require_once(dirname(__FILE__).'/domit/xml_domit_utilities.php');

require_once(dirname(__FILE__).'/../config.inc.php');
require_once(dirname(__FILE__).'/../dbwrapper.inc.php');
require_once(dirname(__FILE__).'/../functions.inc.php');
require_once(dirname(__FILE__).'/../objects/slot.inc.php');
require_once(dirname(__FILE__).'/../objects/segue.inc.php');
require_once(dirname(__FILE__).'/../objects/site.inc.php');
require_once(dirname(__FILE__).'/../objects/section.inc.php');
require_once(dirname(__FILE__).'/../objects/page.inc.php');
require_once(dirname(__FILE__).'/../objects/story.inc.php');
require_once(dirname(__FILE__).'/../permissions.inc.php');

// print help if requested/needed
if ($argc != 3 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {

?>

This script will export the specified Segue site 
and its media to the specified directory. Be sure 
to run this script as a user who has permission to
access the segue media directory or media files will
not be copied.

Usage:

<?php echo $argv[0]; ?> <site name> <output directory>

With the --help, -help, -h,
or -? options, you can get this help.

<?
} else {
	
	$sitename = $argv[1];
	$exportpath = $argv[2];

	// Fetch the site.
	$site =& new Site($sitename);
	$site->fetchDown(TRUE);
	$site->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen();
	
	// Fetch the location of the media files.
	$imagepath = $uploaddir.'/'.$sitename.'/';
	
		
	// Get the XML for the site
	$siteExporter =& new DomitSiteExporter();
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
	
	
	if (!$handle = fopen($xmlFile, 'a')) {
		 echo "Cannot open file ($xmlFile)";
		 exit;
	}
	
	// Write $somecontent to our opened file.
	if (!fwrite($handle, $siteXML)) {
	   echo "Cannot write to file ($xmlFile)";
	   exit;
	}
	
	fclose($handle);

}
exit(0);
