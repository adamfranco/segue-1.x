<? 
/* output.inc.php
 this script outputs the HTML resulting from action files
 output script needs to include the following:
 -common theme functions.inc, header.inc status.inc
 -particular theme colors.inc, css.inc
 -needs to define default theme settings to use
 -needs to call horizontal and vertical navigation function
 for both Top Sections and Side Sections navigation arrangements 
 */
 
if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");
	
/* -------------- THEME SETTINGS ---------------------	*/
/*		handle the $themesettings array					*/
//print "$themesdir/$theme";
include("$themesdir/common/functions.inc.php");

include("$themesdir/common/nav.inc.php");

if (!$themesettings['nav_arrange']) $nav_arrange = '1';
$nav_arrange = $_nav_arrange[$themesettings['nav_arrange']];



/* ------------------- END THEME SETTINGS---------------------	*/

/*********************************************************
 * get all of the existing output buffers and place them inside our body
 *********************************************************/
$obContent = '';
while (ob_get_level())
	$obContent .= ob_get_clean();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?
/******************************************************************************
 * Commom header stuff
 ******************************************************************************/

include("themes/common/header.inc.php");
?>

	<style type='text/css'>
		/* User-defined styles */
		
		<? if ($themesettings['css'])
				echo str_replace("\n", "\n\t\t", $themesettings['css']); 
			else {
				include("$themesdir/$theme/defaults.inc.php");
				echo str_replace("\n", "\n\t\t", $css); 
			}
		?>

	</style>
	<title><? echo $pagetitle; ?></title>
</head>
<body>

<? print $obContent; ?>

	<div class='header'>
		<? print $siteheader; ?>
		
		<div class='status'>
			<? include("themes/common/status.inc.php"); ?>
		
		</div>
		
		<? print $sitecrumbs; ?>
		
	</div>

<?
/******************************************************************************
 * Section Navigation
 ******************************************************************************/
if ($nav_arrange==1) {
	print "\n\t<div class='topnav'>";
	horizontal_nav($section, $topnav, $topnav_extra, $hide_sidebar);
	print "\n\t</div>";
}
?>


	<div class='contentarea'>
	
<?
/******************************************************************************
 * Left Column
 ******************************************************************************/

if ($action == "viewsite" || $leftnav && ($hide_sidebar != 1 || $nav_arrange==2)) {
	print "\n\t\t<div class='leftnav_container'>";
	print "\n\t\t\t<div class='leftnav'>\n";
	if ($nav_arrange==1) {
		vertical_nav($page, $leftnav, $leftnav_extra, $bordercolor, $hide_sidebar);		
	} else {
		side_nav($section, $topnav, $leftnav, $topnav_extra, $leftnav_extra, $bordercolor);
	}
	print "\n\t\t\t</div>";	
	print "\n\t\t</div>\n";	
} 

/******************************************************************************
 * Center Column
 ******************************************************************************/
?>

		<div class='content_container'>
			<div class='content'>

<?
/******************************************************************************
 * Center Column
 ******************************************************************************/
print $content; 
?>
			</div>
		</div>

<?

/******************************************************************************
 * Right Column
 ******************************************************************************/
if ($rightnav && ($hide_sidebar != 1 || $action == "viewsite")) {
	print "\n\t\t<div class='rightnav_container'>";
	print "\n\t\t\t<div class='rightnav'>\n";
	print vertical_nav($page, $rightnav, $leftnav_extra, $bordercolor, $hide_sidebar);
	print "\n\t\t\t</div>";
	print "\n\t\t</div>";
}
?>

	</div>

<?
/******************************************************************************
 * Section Navigation
 ******************************************************************************/
if ($nav_arrange==1) {
	print "\n\t<div class='bottomnav'>";
	horizontal_nav($section, $topnav2, $topnav2_extra, $hide_sidebar);
	print "\n\t</div>";
}
?>

	<div class='footer'>

<?
/******************************************************************************
 * Footer
 ******************************************************************************/
print $sitefooter 
?>
	
	</div>
</body>
</html>

