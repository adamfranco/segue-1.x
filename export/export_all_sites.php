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
if ($argc < 2 || $argc > 4 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {

?>

This script will export all Segue sites and 
their media to the specified directory. Be sure 
to run this script as a user who has permission to
access the segue media directory or media files will
not be copied.

Usage:

<?php echo $argv[0]; ?> [-v] [-z] <output directory>

Options:
	-v	Verbose output. Will print out the number 
		and name of the sites being export.
	-z	Export sites to /tmp/segue_tmp/, then create
		a allsites.tar.gz file in <output directory>.
		This option requires the ability to execute a
		command of the form "tar -czf filename.tar.gz dirname". 

With the --help, -help, -h,
or -? options, you can get this help.

<?
} else {
	
	// get our options
	if ($argv[1] == '-v' || $argv[2] == '-v')
		$verbose = TRUE;
	else 
		$verbose = FALSE;
	
	if ($argv[1] == '-z' || $argv[2] == '-z')
		$compress = TRUE;
	else 
		$compress = FALSE;
	
	// Set up our output directories and make a tmp dir
	// if we will be creating a tarball
	if ($compress) {
		$backupdir = $argv[$argc-1];
		$exportpath = $backupdir."/segue_backup/";
		
		mkdir ($exportpath, 0700);
	} else {
		$backupdir = $argv[$argc-1];
		$exportpath = $argv[$argc-1];
	}
	
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
		if ($verbose)
			print "\n".$i."\t".$sitename."\t";
		
		$i++;
		
		$cmd="php";
		if ($_ENV["_"]) $cmd=$_ENV["_"];
//		print $php . "\n";
		print shell_exec("php ".dirname(__FILE__)."/export_site.php ".$sitename." ".$exportpath);
	}
	
	// If we are compressing, make a tarball and delete the temp directory
	if ($compress) {
		if ($verbose)
			print "\ncd ".$backupdir;
		print shell_exec("cd ".$backupdir);
		if ($verbose)
			print "\ntar -czf ".$backupdir."/segue_backup.tar.gz segue_backup";
		print shell_exec("tar -czf ".$backupdir."/segue_backup.tar.gz segue_backup");
		deletePath($exportpath);
	}
	
	if ($verbose)
		print "\n";
}
exit(0);
