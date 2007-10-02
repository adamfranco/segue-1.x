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

//$nav_arrange = 2;	
/* -------------- THEME SETTINGS ---------------------	*/
/*		handle the $themesettings array					*/

if (!preg_match('/^[a-z_0-9]+$/i', $theme))
		die ('Error: invalid theme, "'.$theme.'".');

include("themes/common/functions.inc.php");

if (file_exists("themes/$theme/colors.inc.php"))
	include("themes/$theme/colors.inc.php");

if ($themesettings[theme] == 'bevelbox') {   // indeed these settings are for this theme

	$use = $themesettings[colorscheme];
	$uselinkcolor = $themesettings[linkcolor];
	$usenav = $themesettings[nav_arrange];
	$usesectionnavsize = $themesettings[sectionnav_size];	
	$usenavsize = $themesettings[nav_size];	
	$usebordercolor = $themesettings[bordercolor];

}

if (!$usebg) $usebg = 'white';
$bg = $_bgcolor[$usebg];

if (!$use) $use = 'gray';
$c = $_theme_colors[$use];

if (!$uselinkcolor) $uselinkcolor = 'red';
$linkcolor = $_linkcolor[$uselinkcolor];

if (!$usebordercolor) $usebordercolor = 'black';
$bordercolor = '000000';

if (!$usenav) $usenav = 'Top Sections';
$nav_arrange = $_nav_arrange[$usenav];

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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?
/******************************************************************************
 * Commom header stuff
 ******************************************************************************/
include("themes/common/header.inc.php");
include("themes/$theme/css.inc.php"); 
/* ------------------------------------------- */
/* -------------- PAGE TITLE ----------------- */
/* ------------------------------------------- */?>
<title><? echo $pagetitle; ?></title>
</head>

<body style='margin: 0px'>

<? print $obContent; ?>

<table  width='90%' align='center' cellpadding='0' cellspacing='0'>
<tr><td>

<!--  Status Layout -->
<table width='90%' cellpadding='0' cellspacing='0' align='center'>
	<tr>
		<td width='10'></td>
		<td></td>
		<td width='10'></td>
	</tr>
	<tr>
		<td width='10'>
		</td>
		<td class='status'>
			<? if ($_SESSION['ltype'] == "admin" || !isset($_SESSION['ltype'])) {
					print "<div style='height: 32px; overflow: hidden;'>";
				} else {
					print "<div style='height: 16px; overflow: hidden;'>";
				}
				include("themes/common/status.inc.php");
				print "</div>";
				?>
		</td> 
		<!-- end content table cell -->
		<td width='10'>
		</td>
	</tr>
	<tr>
		<td width='10'></td>
		<td></td>
		<td width='10'></td>
	</tr>
</table>
	
	
</td></tr>
<tr><td>

<table width='90%' align='center' cellpadding='0' cellspacing='0'>
<tr><td>
<?
/******************************************************************************
 * Site Header
 ******************************************************************************/ 
print $siteheader; 
?>
</td></tr>
</table>
<table width='90%' align='center' cellpadding='0' cellspacing='0'>
  <tr> 
    <td class="topleft" height='43'></td>
    <td class="top" height='43'> 
	<table width='100%' align='center' cellpadding='0' cellspacing='0'>
    <tr>
	<?
	/******************************************************************************
	 * Site Header
	 ******************************************************************************/ 
	?>
    <td class="sitetitle" height='43'>
    <? echo $title ?>
	</td>
	<td>
    <div align="right"><img src='<? echo "themes/$theme/images/$c[bg]/midd.gif" ?>' alt='midd' width="121px" height="43px" /></div>
    </td>
    </tr>
    </table>
    </td>
    <td class="top"></td>
    <td class="topright"></td>
  </tr>
  <tr> 
    <td class="topleft2">&nbsp;</td>    
    <td style="background-color: #<? echo $c[bgcolor] ?>">
    <div class='nav'>
   <?
	/******************************************************************************
	 * Status Bar, crumbs
	 ******************************************************************************/	
	print "\n\t\t\t<div style='float: right; height: 20px; overflow: hidden;'>";
	include("themes/common/search.inc.php"); 
	print "\n\t\t\t</div>";
	
	print "\n\t\t\t<div style='float: left;'>";
	print $sitecrumbs;
	print "\n\t\t\t</div>"; 
	
 	?>
	</div>
    <div class='topnav' align='center' style='clear: both;'>
	<div class='nav'>
	<?
	/******************************************************************************
	 * Section Navigation
	 ******************************************************************************/
	if ($nav_arrange==1) horizontal_nav($section, $topnav, $topnav_extra);	
	?>	
	</div>
	</div>    
    </td>
   <td style="background-color: #<? echo $c[bgcolor] ?>">&nbsp;</td>
    <td class="right">&nbsp;</td>
  </tr>
  <tr> 
    <td class="topleft3">&nbsp;</td>
    <td class="topcenter"></td>
    <td class="topright1">&nbsp;</td>
    <td class="right">&nbsp;</td>
  </tr>
<tr>

<td class="left" valign='top'>
	<table width='100%' cellpadding='5' cellspacing='0'>
		<tr>
			<td class='leftnav'>
<?

/******************************************************************************
 * Left Column
 ******************************************************************************/
$hide_sidebar = 2;
if ($nav_arrange==1) {
	vertical_nav($page, $leftnav, $leftnav_extra, $bordercolor, $hide_sidebar);		
} else {
	side_nav($section, $topnav, $leftnav, $topnav_extra, $leftnav_extra, $bordercolor);
}
?>
			</td>
		</tr>
	</table>
</td>

<td valign='top'>
	<table width='100%' cellspacing='0' cellpadding='0' border='0'>
		<tr>
			<td class='contentarea'>
			
<?
/******************************************************************************
 * Center Column
 ******************************************************************************/
print $content; 
?>

			</td>
<?
/******************************************************************************
 * Right Column
 ******************************************************************************/
if ($rightnav) {
	print<<<END
			<td class='rightnav'>
				<table width='100%' cellspacing='0' cellpadding='2' border='0'>
					<tr>
						<td valign='top' class='rightnavcolor'>
						
END;
	vertical_nav($page, $rightnav, $leftnav_extra, $bordercolor, $hide_sidebar);
	print<<<END
						
						</td>
					</tr>
				</table>
			</td>

END;
} else {
	print "\n\t\t\t<td></td>";
}

?>

		</tr>
	</table>
</td>
<td class="right2">&nbsp;</td>
<td class="right">&nbsp;</td>

</tr>
<tr> 

<td class="bottomleft">&nbsp;</td>
<td class="bottom">
<div class='topnav' align='center'>
<div class='nav'>
<?
/******************************************************************************
 * Bottom section navigation
 ******************************************************************************/
if ($nav_arrange==1) horizontal_nav($section, $topnav2, $topnav2_extra);
?>
</div>
</div>      
</td>
<td class="bottomright1">&nbsp;</td>
<td class="bottomright2">&nbsp;</td>
</tr>
</table>
<br />

<table width='90%' align='center' cellpadding='0' cellspacing='0'>
<tr><td>
<?
/******************************************************************************
 * Footer
 ******************************************************************************/
print $sitefooter 
?>
</td></tr>
</table>
<br />
</td></tr>
</table>
</body>
</html>

	
