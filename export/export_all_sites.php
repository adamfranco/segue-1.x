<?

require_once('DomitSiteExporter.class.php');

require_once('domit/xml_domit_getelementsbypath.php');
require_once('domit/xml_domit_nodemaps.php');
require_once('domit/xml_domit_parser.php');
require_once('domit/xml_domit_utilities.php');

require_once('../config.inc.php');
require_once('../dbwrapper.inc.php');
require_once('../functions.inc.php');
require_once('../objects/slot.inc.php');
require_once('../objects/segue.inc.php');
require_once('../objects/site.inc.php');
require_once('../objects/section.inc.php');
require_once('../objects/page.inc.php');
require_once('../objects/story.inc.php');
require_once('../permissions.inc.php');

// print help if requested/needed
if ($argc != 2 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {

?>

This script will export all Segue sites 
and their media to the specified directory.

Usage:

<?php echo $argv[0]; ?> <output directory>

With the --help, -help, -h,
or -? options, you can get this help.

<?
} else {
	
	$exportpath = $argv[1];
	
	db_connect($dbhost, $dbuser, $dbpass, $dbdb);
	$query = "
		SELECT 
			slot.slot_name AS name
		FROM 
			slot
				INNER JOIN
			site
				ON slot.FK_site=site.site_id
		ORDER BY
			name
	";
	$r = db_query($query);
	
	$i = 1;
	while ($a = db_fetch_assoc($r)) {
	
		$sitename = $a['name'];
		print "\n".$i."\t".$sitename."\t";
		$i++;
	
		// Fetch the site.
		$site =& new Site($sitename);
		$site->fetchDown(TRUE);
		$site->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen();
		
		// Fetch the location of the media files.
		$imagepath = $uploaddir.$sitename.'/';
		
		// Get the XML for the site
		$siteExporter =& new DomitSiteExporter();
		$siteXML =& $siteExporter->export($site);
		
		
		
		// make a directory for the site contents
		$siteDir = $exportpath.$sitename.'/';
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

}
exit(0);