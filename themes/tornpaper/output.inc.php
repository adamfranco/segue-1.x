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
	
/* -------------- THEME SETTINGS ---------------------	*/
/*		handle the $themesettings array					*/
//print "$themesdir/$theme";
include("$themesdir/common/functions.inc.php");

if (file_exists("$themesdir/$theme/colors.inc.php"))
	include("$themesdir/$theme/colors.inc.php");
	
//$nav_arrange=2;

if ($themesettings[theme] == 'tornpaper') {   // indeed these settings are for this theme

	$usebg = $themesettings[bgcolor];
	$usecolor = $themesettings[colorscheme];
	$useborder = $themesettings[borderstyle];
	$usebordercolor = $themesettings[bordercolor];
	$usetextcolor = $themesettings[textcolor];
	$uselinkcolor = $themesettings[linkcolor];
	$usenav = $themesettings[nav_arrange];
	$usenavwidth = $themesettings[nav_width];
	$usesectionnavsize = $themesettings[sectionnav_size];	
	$usenavsize = $themesettings[nav_size];	
}
if (!$usebg) $usebg = 'gray';
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

if (!$usesectionnavsize) $usesectionnavsize = '12 pixels';
$sectionnavsize = $_sectionnav_size[$usesectionnavsize];

if (!$usenavsize) $usenavsize = '12 pixels';
$navsize = $_nav_size[$usenavsize];



/* ------------------- END THEME SETTINGS---------------------	*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?
/* ------------------------------------------- */
/* ------------- COMMON HEADER --------------- */
/* ------------------------------------------- */
include("themes/common/header.inc.php");
include("themes/$theme/css.inc.php"); 
/* ------------------------------------------- */
/* -------------- PAGE TITLE ----------------- */
/* ------------------------------------------- */
?>
<title><? echo $pagetitle; ?></title>
</head>

<body style='margin: 0px'>

<table width=95% cellpadding=0 cellspacing=0 align='center'>
<tr>
<td class=topleft></td>
<td class=top></td>
<td class=topright></td>
</tr>
<tr>
<td class=left><img class=lefttop src='<? echo "$themesdir/$theme/images/$bg[bgshadow]/lefttop.gif"?>'></td>
<td class=content>

<div class=header>
<?
/* ------------------------------------------- */
/* ------SITE HEADER/STATUS BAR/CRUMBS ------- */
/* ------------------------------------------- */
print $siteheader; 
include("themes/common/status.inc.php"); 
print $sitecrumbs;
?>
</div>

<div class=topnav align='center'>
<?
/* ------------------------------------------- */
/* --------- TOP SECTION NAV ---------------- */
/* ------------------------------------------- */
if ($nav_arrange==1) horizontal_nav($section, $topnav, $topnav_extra);

?>
</div>

<table width=100% class=contenttable>
<tr>
<td class=leftnav>
<?

/* ------------------------------------------- */
/* --------------- LEFT NAV ------------------ */
/* ------------------------------------------- */
if ($nav_arrange==1) {
	vertical_nav($page, $leftnav, $leftnav_extra);		
} else { 
	side_nav($section, $topnav, $leftnav, $topnav_extra, $leftnav_extra);
}
?>
</td>
<td class=contentarea>
<div class=topnav align='center'>
<?
/* ------------------------------------------- */
/* ------------ TOP PAGE NAV ---------------- */
/* ------------------------------------------- */
//if ($nav_arrange==2) horizontal_nav($page, $leftnav, $leftnav_extra);
?>
</div>
<?
/* ------------------------------------------- */
/* -------------- CONTENT AREA   ------------- */
/* ------------------------------------------- */

print $content; 

?>
<div class=topnav align='center'>
<?
/* ------------------------------------------- */
/* ------------ BOTTOM PAGE NAV -------------- */
/* ------------------------------------------- */
//if ($nav_arrange==2) horizontal_nav($page, $leftnav2, $leftnav2_extra);
?>
</div>
</td>
<?
/* ------------------------------------------- */
/* -------------- RIGHT NAV (OPT)  ----------- */
/* ------------------------------------------- */
/* if (count($rightnav)) { */
/* 	print "<td style='margin-left: 20px'>"; */
/* 	horizontal_nav('pages',$rightnav, $rightnav_extra); */
/* 	print "</td>"; */
/* } */
?>
</tr>
</table>
<div class=topnav align='center'>
<?
/* ------------------------------------------- */
/* ------------ BOTTOM SECTION NAV ----------- */
/* ------------------------------------------- */
if ($nav_arrange==1) horizontal_nav($section, $topnav2, $topnav2_extra);
?>
</div>
<?
/* ------------------------------------------- */
/* -------------- FOOTER     ----------------- */
/* ------------------------------------------- */
print $sitefooter ?>

</td> <!-- end content table cell -->
<td class=right><img class=righttop src='<? echo "$themesdir/$theme/images/$bg[bgshadow]/righttop.gif"?>'></td>
</tr>
<tr>
<td class=bottomleft>&nbsp;</td>
<td class=bottom>&nbsp;</td>
<td class=bottomright>&nbsp;</td>
</tr>

</table>

	
