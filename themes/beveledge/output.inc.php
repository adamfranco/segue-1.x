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

if (file_exists("$themesdir/$theme/colors.inc.php"))
	include("$themesdir/$theme/colors.inc.php");
	
//$nav_arrange=2;

if ($themesettings[theme] == 'beveledge') {   // indeed these settings are for this theme

	$usebg = $themesettings[bgcolor];
	$usecolor = $themesettings[colorscheme];
	$useborder = $themesettings[borderstyle];
	$usebordercolor = $themesettings[bordercolor];
	$usetextcolor = $themesettings[textcolor];
	$uselinkcolor = $themesettings[linkcolor];
	$usenav = $themesettings[nav_arrange];
	$usenavwidth = $themesettings[nav_width];
	$usesitewidth = $themesettings[site_width];
	$usesectionnavsize = $themesettings[sectionnav_size];	
	$usenavsize = $themesettings[nav_size];	

	
}
if (!$usebg) $usebg = 'white';
$bg = $_bgcolor[$usebg];

if (!$usecolor) $usecolor = 'white';
$c = $_theme_colors[$usecolor];

if (!$useborder) $useborder = 'solid';
$borders = $_borderstyle[$useborder];

if (!$usebordercolor) $usebordercolor = 'blue';
$bordercolor = $_bordercolor[$usebordercolor];

if (!$usetextcolor) $usetextcolor = 'black';
$textcolor = $_textcolor[$usetextcolor];

if (!$uselinkcolor) $uselinkcolor = 'red';
$linkcolor = $_linkcolor[$uselinkcolor];

if (!$usenav) $usenav = 'Top Sections';
$nav_arrange = $_nav_arrange[$usenav];

if (!$usenavwidth) $usenavwidth = '150 pixels';
$navwidth = $_nav_width[$usenavwidth];

if (!$usesitewidth) $usesitewidth = 'variable';
$sitewidth = $_site_width[$usesitewidth];

if (!$usesectionnavsize) $usesectionnavsize = '12 pixels';
$sectionnavsize = $_sectionnav_size[$usesectionnavsize];

if (!$usenavsize) $usenavsize = '12 pixels';
$navsize = $_nav_size[$usenavsize];


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
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<?
/******************************************************************************
 * Commom header stuff
 ******************************************************************************/

include("themes/common/header.inc.php");
include("themes/$theme/css.inc.php"); 
?>
<title><? echo $pagetitle; ?></title>
</head>
<body style='margin: 1px'>

<? print $obContent; ?>

<table width='<?php echo $sitewidth ?>' cellpadding='0' cellspacing='0' align='center'>
<tr><td>

<!--  Status Layout -->
<table width='<?php echo $sitewidth ?>' cellpadding='0' cellspacing='0' align='center'>
	<tr>
		<td width='22'></td>
		<td></td>
		<td width='22'></td>
	</tr>
	<tr>
		<td width='22'>
		</td>
		<td class='status'>
			<? include("themes/common/status.inc.php"); ?>		
		</td> 
		<!-- end content table cell -->
		<td width='22'>
		</td>
	</tr>
	<tr>
		<td width='22'></td>
		<td></td>
		<td width='22'></td>
	</tr>
</table>
	
</td></tr>
<tr><td>

<!--  Body Layout -->
<table width='<?php echo $sitewidth ?>' cellpadding='0' cellspacing='0' align='center'>
	<tr>
		<td class='topleft'></td>
		<td class='top'></td>
		<td class='topright'></td>
	</tr>
	<tr>
		<td class='left'><img class='lefttop' src='<? echo "$themesdir/$theme/images/$bg[bgshadow]/lefttop.gif"?>' alt='border' /></td>
		<td class='content'>
			<div class='header'>
			
<?

/******************************************************************************
 * Site Header, Status bar, crumbs
 ******************************************************************************/ 
			print $siteheader; 
			
			print "\n\t\t\t<div style='float: right; height: 20px; overflow: hidden;'>";
			include("themes/common/search.inc.php"); 
			print "\n\t\t\t</div>";
			
			print "\n\t\t\t<div style='float: left;'>";
			print $sitecrumbs;
			print "\n\t\t\t</div>";
			?>
			</div>

			<div class='topnav' align='center' style='clear: both;'>
<?
/******************************************************************************
 * Section Navigation
 ******************************************************************************/
if ($nav_arrange==1) horizontal_nav($section, $topnav, $topnav_extra, $hide_sidebar);
?>
			</div>
			<table width='100%' class='contenttable'>
				<tr>
<?
/******************************************************************************
 * Left Column
 ******************************************************************************/
 if ($action == "viewsite" || $leftnav && ($hide_sidebar != 1 || $nav_arrange==2)) {
	print "\n\t\t\t\t\t<td class='leftnav'>\n";
	if ($nav_arrange==1) {
		vertical_nav($page, $leftnav, $leftnav_extra, $bordercolor, $hide_sidebar);		
	} else {
		side_nav($section, $topnav, $leftnav, $topnav_extra, $leftnav_extra, $bordercolor);
	}
	print "\n\t\t\t\t\t</td>";	
} 

/******************************************************************************
 * Center Column
 ******************************************************************************/
?>
					<td class='contentarea'>
<?
print $content;
?>

					</td>
<?
/******************************************************************************
 * Right Column
 ******************************************************************************/
 
if ($rightnav && ($hide_sidebar != 1 || $action == "viewsite")) {
	print "\n\t\t\t\t\t<td class='rightnav'>\n";
	print vertical_nav($page, $rightnav, $leftnav_extra, $bordercolor, $hide_sidebar);
	print "\n\t\t\t\t\t</td>";
}
?>
				</tr>
			</table>

			<div class='topnav' align='center'>
<?
/******************************************************************************
 * Bottom section navigation
 ******************************************************************************/
if ($nav_arrange==1) 
	horizontal_nav($section, $topnav2, $topnav2_extra, $hide_sidebar);
?>

			</div>
<?
/******************************************************************************
 * Footer
 ******************************************************************************/
print $sitefooter 
?>
		</td> 
<!-- end content table cell -->
		<td class='right'>
			<img class='righttop' src='<? echo "$themesdir/$theme/images/$bg[bgshadow]/righttop.gif"?>' alt='rightop' />
		</td>
	</tr>
	<tr>
		<td class='bottomleft'></td>
		<td class='bottom'></td>
		<td class='bottomright'></td>
	</tr>
</table>
</body>
</html>
