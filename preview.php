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
	
	<style type='text/css'>
	a {
		color: #a33;
		text-decoration: none;
	}
	
	a:hover {text-decoration: underline;}
	
	table {
		border: 1px solid #555;
	}
	
	th, td {
		border: 0px;
		background-color: #ddd;
	}
	
	.viewcol {
		background-color: #cec;
		border-left: 2px solid #FFF;
	}
	
	.lockedcol {
		background-color: #ecc;
	}
	
	#collabel {
		text-align: center;
		background-color: #bbb;
	}
	
	.edname {
		border-left: 2px solid #FFF;
	}
	
	.td1 { 
		background-color: #ccc; 
	}
	
	.td0 { 
		background-color: #ddd; 
	}
	
	th { 
		background-color: #bbb; 
		font-variant: small-caps;
	}
	
	body { 
		background-color: white; 
	}
	
	body, table, td, th, input {
		font-size: 12px;
		font-family: "Verdana", "sans-serif";
	}
	
	input {
		border: 1px solid black;
		background-color: white;
		font-size: 10px;
	}
	
	</style>
	
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
	<table width='100%'>	
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
		
		print "Preview As: ";

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