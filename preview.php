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
	error("You are not an editor for this site. You may not view any permissions.");
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
		print "<tr>";
		print "<td class=td$color>";
		
		if (ereg('viewsite',$previousLocation))
			$previewAction = "preview_edit_as";
		else
			$previewAction = "preview_as";
		
		$url = "index.php?$sid".$previousLocation;
		$url = ereg_replace("&action=[^&]*", "&action=".$previewAction."&previewuser=$e", $url);
		
		print "<a href='#' onClick=\"sendWindow('sitepreview',800,600,'".$url."')\">";
		
		print "Preview As: &nbsp; &nbsp; &nbsp; &nbsp; ";

		if ($e == "everyone")
			print "Everyone (everyone)";
		else if ($e == "institute")
			print $cfg[inst_name]." Users (institute)";
		else
			print ldapfname($e)." ($e)";
		
		print "</a>";
		print "</td>";
		
		print "</tr>";
		$color = 1-$color;
	}
?>
	</table>
<?
}
?>