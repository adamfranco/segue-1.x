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

//$nav_arrange = 2;	
/* -------------- THEME SETTINGS ---------------------	*/
/*		handle the $themesettings array					*/
//print "$themesdir/$theme";
include("$themesdir/common/functions.inc.php");

if (file_exists("$themesdir/$theme/colors.inc.php"))
	include("$themesdir/$theme/colors.inc.php");

if ($themesettings[theme] == 'bevelbox') {   // indeed these settings are for this theme

	$use = $themesettings[colorscheme];
	$uselinkcolor = $themesettings[linkcolor];
	$usenav = $themesettings[nav_arrange];
	$usesectionnavsize = $themesettings[sectionnav_size];	
	$usenavsize = $themesettings[nav_size];	

}
if (!$use) $use = 'gray';
$c = $_theme_colors[$use];

if (!$uselinkcolor) $uselinkcolor = 'red';
$linkcolor = $_linkcolor[$uselinkcolor];

if (!$usenav) $usenav = 'Top Sections';
$nav_arrange = $_nav_arrange[$usenav];

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
/* ------------------------------------------- */?>
<title><? echo $pagetitle; ?></title>
</head>

<body marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 rightmargin=0>

<table width=90% align=center cellpadding=0 cellspacing=0>
<tr><td>
<?
/* ------------------------------------------- */
/* ------SITE HEADER/STATUS BAR/CRUMBS ------- */
/* ------------------------------------------- */
print $siteheader; 
include("themes/common/status.inc.php"); 
?>
</tr></td>
</table>

<table width=90% align=center cellpadding=0 cellspacing=0>
  <tr> 
    <td class="topleft"></td>
    <td class="top"> 
	<table width=100% align=center cellpadding=0 cellspacing=0>
    <tr>
    <td class="sitetitle">
    <? echo $title ?>
	</td>
	<td>
    <div align="right"><img src='<? echo "$themesdir/$theme/images/$c[bg]/midd.gif" ?>' width="121" height="43"></div>
    </td>
    </tr>
    </table>
    </td>
    <td class="top"></td>
    <td class="topright"></td>
  </tr>
  <tr> 
    <td class="topleft2">&nbsp;</td>
    <td bgcolor="<? echo $c[bgcolor] ?>">&nbsp;
  	<? print $sitecrumbs; ?>
    
    <div class=topnav align=center>
	<div class='nav'>
	<?
	/* ------------------------------------------- */
	/* --------- TOP SECTION NAV ---------------- */
	/* ------------------------------------------- */
	if ($nav_arrange==1) horizontal_nav($section, $topnav, $topnav_extra);
	
	?>	
	</div>
	</div>
    
    </td>
    <td bgcolor="<? echo $c[bgcolor] ?>">&nbsp;</td>
    <td class="right">&nbsp;</td>
  </tr>
  <tr> 
    <td class="topleft3">&nbsp;</td>
    <td class="topcenter"></td>
    <td class="topright1">&nbsp;</td>
    <td class="right">&nbsp;</td>
  </tr>
<tr>

<td class="left">


<table width=100% cellpadding=5 cellspacing=0>
<td class=leftnav>
<?

/* ------------------------------------------- */
/* --------------- LEFT NAV ------------------ */
/* ------------------------------------------- */
if ($nav_arrange==1) {
	vertical_nav($page, $leftnav, $leftnav_extra);		
} else { 
	vertical_nav($section, $topnav, $topnav_extra);
}
?>
</td>
</table>

</td>

<td class=contentarea>
<div class=topnav align=center>
<?
/* ------------------------------------------- */
/* ------------ TOP PAGE NAV ---------------- */
/* ------------------------------------------- */
if ($nav_arrange==2) horizontal_nav($page, $leftnav, $leftnav_extra,'navlink2','navlink2 a');
?>
</div>
<?
/* ------------------------------------------- */
/* -------------- CONTENT AREA   ------------- */
/* ------------------------------------------- */
print $content; 

?>
<div class=topnav align=center>
<?
/* ------------------------------------------- */
/* ------------ BOTTOM PAGE NAV -------------- */
/* ------------------------------------------- */
if ($nav_arrange==2) horizontal_nav($page, $leftnav, $leftnav_extra);
?>
</div>
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
<td class="right2">&nbsp;</td>
<td class="right">&nbsp;</td>
</tr>
<tr> 
    <td class="bottomleft">&nbsp;</td>
    <td class="bottom">
<div class=topnav align=center>
<div class='nav'>
<?
/* ------------------------------------------- */
/* ------------ BOTTOM SECTION NAV ----------- */
/* ------------------------------------------- */
if ($nav_arrange==1) horizontal_nav($section, $topnav, $topnav_extra);
?>
</div>
</div>
       
</td>
<td class="bottomright1">&nbsp;</td>
<td class="bottomright2">&nbsp;</td>
</tr>
</table>
<br>

<table width=90% align=center cellpadding=0 cellspacing=0>
<tr><td>
<?
/* ------------------------------------------- */
/* -------------- FOOTER     ----------------- */
/* ------------------------------------------- */
print $sitefooter 
?>
</tr></td>
</table>
<br>

	
