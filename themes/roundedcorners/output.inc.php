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
	die("Error: improper application flow. Configuration must be included first.");;
	
/* -------------- THEME SETTINGS ---------------------	*/
/*		handle the $themesettings array					*/
//print "$themesdir/$theme";
include("$themesdir/common/functions.inc.php");

if (file_exists("$themesdir/$theme/colors.inc.php"))
	include("$themesdir/$theme/colors.inc.php");
	
//$nav_arrange=2;

if ($themesettings[theme] == 'shadowbox') {   // indeed these settings are for this theme

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

//printpre($c);


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
<body style='margin: 2px'>

<? print $obContent; ?>
<table  width='<?php echo $sitewidth ?>' align='center' cellpadding='0' cellspacing='0'>
<tr><td>
	<table width='<?php echo $sitewidth ?>' cellpadding='0' cellspacing='0' align='center'>
		<tr>
			<td class='r3c1'> &nbsp; </td>
			<td class='r2c2'> &nbsp; </td>
			<td class='status' rowspan='3'>
			<? if ($_SESSION['ltype'] == "admin" || !isset($_SESSION['ltype'])) {
					print "<div style='height: 32px; overflow: hidden;'>";
				} else {
					print "<div style='height: 16px; overflow: hidden;'>";
				}
				include("themes/common/status.inc.php"); ?>
				</div>
			</td>		
			<td class='r2c4'> &nbsp; </td>
			<td class='r3c5'> &nbsp; </td>
		</tr>	
		<tr>
			<td class='r3c1'> &nbsp; </td>
			<td class='r3c2'> &nbsp; </td>
			<!--<td class='r4c3'></td>-->
			<td class='r3c4'> &nbsp; </td>
			<td class='r3c5'> &nbsp; </td>
		</tr>
		<tr>
			<td class='r4c1'> &nbsp; </td>
			<td class='r4c2'> &nbsp; </td>
			<!--<td class='r4c3'></td>-->
			<td class='r4c4'> &nbsp; </td>
			<td class='r4c5'> &nbsp; </td>
		</tr>
	</table>
</td></tr>
<tr><td>	
	<table width='<?php echo $sitewidth ?>' cellpadding='0' cellspacing='0' align='center'>	
	<tr>
		<td class='r1c1'></td>
		<td class='r1c2'></td>
		<td class='r1c3'></td>
		<td class='r1c4'></td>
		<td class='r1c5'></td>
	</tr>
	<tr>
		<td class='r2c1'></td>
		<td class='r2c2'></td>
		<td class='r2c3'></td>
		<td class='r2c4'></td>
		<td class='r2c5'></td>
	</tr>
	<tr>
		<td class='r3c1'></td>
		<td class='r3c2'></td>

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
				print "\n\t\t\t\t\t<td class='leftnav'>";
				
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
			
				// show right side bar only if not sidebar hidden or edit mode
			if ($rightnav && ($hide_sidebar != 1 || $action == "viewsite")) {
				print "\n\t\t\t\t\t<td class='rightnav'>";
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
		
		<td class='r3c4'></td>
		<td class='r3c5'></td>

	</tr>
	<tr>
		<td class='r4c1'></td>
		<td class='r4c2'></td>
		<td class='r4c3'></td>
		<td class='r4c4'></td>
		<td class='r4c5'></td>
	</tr>
	<tr>
		<td class='r5c1'></td>
		<td class='r5c2'></td>
		<td class='r5c3'></td>
		<td class='r5c4'></td>
		<td class='r5c5'></td>
	</tr>
	</table>
	<br />
</td></tr>
</table>
</body>
</html>

	
