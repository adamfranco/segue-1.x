<? /* $Id$ */

/******************************************************************************
 * edit_permissions takes in one variable: $site
 *
 * The first screen is for the adding, delete, and choosing of editors
 * The second screen is for the editing of the permissions for selected editors
 ******************************************************************************/

require("objects/objects.inc.php");

ob_start();
session_start();

// include all necessary files
require("includes.inc.php");

if ($_REQUEST[cancel]) {
	header("Location: close.php");
}

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

/******************************************************************************
 * create the site object if it doesn't exist.
 ******************************************************************************/

$siteObj =& new site($_REQUEST[site]);
$siteObj->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen(0,1);
$site_owner = $siteObj->owner;

if ($site_owner != $_SESSION[auser]) {
	error("You are not the owner of this site, you can not use this function.");
} else {
	$editors = $siteObj->getEditors();
	
	$previousLocation = urldecode($_REQUEST['query']);
	
	
	/******************************************************************************
	 * common styles/javascripts:
	 ******************************************************************************/
	?>
	<html>
	<head>
	<title><? print $siteObj->getField("title"); print " - Preview Site As..."; ?></title>
	
	<? include("themes/common/logs_css.inc.php"); ?>
	
	<script lang='JavaScript'>
	
	function sendWindow(name,width,height,url) {
		var win = window.open("",name,"toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width="+width+",height="+height);
		win.document.location=url;
		win.focus();
	}
	
	</script>
	
	</head>
	
	<? 
	
	/******************************************************************************
	 * output any errors
	 ******************************************************************************/
	
	printerr();
	print $content;
	
	/******************************************************************************
	 * Print out the table of editors.
	 ******************************************************************************/
	?>
	Preview this site as another user...
	<table width='100%' id='maintable'>	
<?
	$color = 0;
	foreach ($editors as $e) {
		print "\n<tr>";
		print "\n\t<td class=td$color>";
				
		print "\n\tPreview &nbsp; ";
		
		$startingUrl = "index.php?$sid".$previousLocation;
		$actions = array (	"preview_as" => "View Mode",
							"preview_edit_as" => "Edit Mode");
		$i=0;
		foreach ($actions as $previewAction => $name) {
			$url = ereg_replace("&action=[^&]*", "&action=".$previewAction."&previewuser=$e", $startingUrl);
			
			if ($i > 0)
				print " &nbsp; | &nbsp; ";
			print "\n\t<a href='#' onClick=\"sendWindow('sitepreview',800,600,'".$url."')\">";
			print $name;
			print "</a>";
			$i++;
		}
		
		print "\n\t &nbsp;  As: &nbsp; &nbsp;";

		if ($e == "everyone")
			print "Everyone (everyone)";
		else if ($e == "institute")
			print $cfg[inst_name]." Users (institute)";
		else
			print ldapfname($e)." ($e)";
		
		print "\n\t</td>";
		
		print "\n</tr>";
		$color = 1-$color;
	}
?>
	</table>
	<br />Note: Only 'View Mode' and 'Edit Mode' can be previewed. 
	<br />If you click on a link to Discuss, Edit, Add, etc, you will leave the preview and execute do that function as yourself.
<?
}
?>