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

$sitename = 'segue';

$site =& new Site($sitename);
$site->fetchDown(TRUE);
$site->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen();

//printpre($site);

$siteExporter =& new SiteExporter();

$siteXML =& $siteExporter->export($site);

print $siteXML;