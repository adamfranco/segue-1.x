<?

require_once('SiteExporter.class.php');

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

// temporary configs
$sitename = 'segue';
$exportpath = '/www/afranco/segue_backup/';
$compressCommand = 'tar -czf ';
$compressOrder = 'destFirst'; // 'destFirst' or 'destLast'

// Fetch the site.
$site =& new Site($sitename);
$site->fetchDown(TRUE);
$site->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen();

// Fetch the location of the media files.
$imagepath = $uploaddir.$sitename.'/';

// Get the XML for the site
$siteExporter =& new SiteExporter();
$siteXML =& $siteExporter->export($site);

// make a temporary directory for the site contents
if (file_exists($exportpath.'tmp'))
	deletePath($exportpath.'tmp');
mkdir ($exportpath.'tmp');
$siteDir = $exportpath.'tmp/'.$sitename.'_'.date('YmdHis').'/';
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

exit(0);