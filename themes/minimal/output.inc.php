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

if ($themesettings[theme] == 'minimal') {   // indeed these settings are for this theme

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
if (!$usebg) $usebg = 'turquoise';
$bg = $_bgcolor[$usebg];

if (!$usecolor) $usecolor = 'turquoise';
$c = $_theme_colors[$usecolor];

if (!$useborder) $useborder = 'dashed';
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
<head>
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

<body marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 rightmargin=0>

<table width=100% cellpadding=0 cellspacing=0>
<tr><td class=header>
<?
/* ------------------------------------------- */
/* ------SITE HEADER/STATUS BAR/CRUMBS ------- */
/* ------------------------------------------- */
print $siteheader; 
include("themes/common/status.inc.php"); 
print $sitecrumbs;
?>
</td></tr>

<tr><td class=topnav align=center>
<?
/* ------------------------------------------- */
/* --------- TOP SECTION NAV ---------------- */
/* ------------------------------------------- */
if ($nav_arrange==1) horizontal_nav($section,$topnav, $topnav_extra);
?>
</td></tr>

<tr><td class=contentarea>

<table width=600 class=contenttable align=center>
<tr><td class=leftnav valign=top> 
<?

/* ------------------------------------------- */
/* --------------- LEFT NAV ------------------ */
/* ------------------------------------------- */
if ($nav_arrange==1) {
	vertical_nav($page,$leftnav, $leftnav_extra);		
} else { 
	vertical_nav($section,$topnav, $topnav_extra);
}
?>
</td>

<td class=content valign=top>
<div align=center>
<?
/* ------------------------------------------- */
/* ------------ TOP PAGE NAV ---------------- */
/* ------------------------------------------- */
if ($nav_arrange==2) horizontal_nav($page,$leftnav, $leftnav_extra);
?>
</div>
<?
/* ------------------------------------------- */
/* -------------- CONTENT AREA   ------------- */
/* ------------------------------------------- */

print $content; 

?>
<div align=center>
<?
/* ------------------------------------------- */
/* ------------ BOTTOM PAGE NAV -------------- */
/* ------------------------------------------- */
if ($nav_arrange==2) horizontal_nav($page,$leftnav, $leftnav_extra);
?>
</td>
<?
/* ------------------------------------------- */
/* -------------- RIGHT NAV (OPT)  ----------- */
/* ------------------------------------------- */
if (count($rightnav)) {
	print "<td style='margin-left: 20px'>";
	horizontal_nav('pages',$rightnav, $rightnav_extra);
	print "</td>";
}
?>
</div>
</table>
<tr><td>
<div class=topnav align=center>
<?
/* ------------------------------------------- */
/* ------------ BOTTOM SECTION NAV ----------- */
/* ------------------------------------------- */
if ($nav_arrange==1) horizontal_nav($section,$topnav, $topnav_extra);
?>

<?
/* ------------------------------------------- */
/* -------------- FOOTER     ----------------- */
/* ------------------------------------------- */
print $sitefooter ?>
</td></tr>
</div>
</table>
