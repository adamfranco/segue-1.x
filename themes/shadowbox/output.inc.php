<? // output.inc.php
	// this script outputs the HTML resulting from action files
	
/* -------------- THEME SETTINGS ---------------------	*/
/*		handle the $themesettings array					*/
//print "$themesdir/$theme";
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

if (!$usesectionnavsize) $usesectionnavsize = '12 pixels';
$sectionnavsize = $_sectionnav_size[$usesectionnavsize];

if (!$usenavsize) $usenavsize = '12 pixels';
$navsize = $_nav_size[$usenavsize];

/* ------------------- END ---------------------------	*/

?>
<head>
<?
/* ------------------------------------------- */
/* ------------- COMMON HEADER --------------- */
/* ------------------------------------------- */
include("themes/common/header.inc.php"); ?>


<? include("themes/$theme/css.inc.php"); ?>

<?
/* ------------------------------------------- */
/* -------------- PAGE TITLE ----------------- */
/* ------------------------------------------- */
?>
<title><? echo $pagetitle; ?></title>

</head>

<body marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 rightmargin=0>

<!--<tr>
<td class=header align=center> -->
<?
/* ------------------------------------------- */
/* -------------- STATUS BAR ----------------- */
/* ------------------------------------------- */
//include("themes/common/status.inc.php"); 
?>
<!-- </td>
</tr>
</table>-->

<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td class=topleft>&nbsp;</td>
<td class=top>&nbsp;</td>
<td class=topright>&nbsp;</td>
</tr>
<tr>
<td class=left><img class=lefttop src='<? echo "$themesdir/$theme/images/$bg[bgshadow]/lefttop.gif"?>'></td>
<td class=content>

<div class=header>
<?
/* ------------------------------------------- */
/* ------------- SITE HEADER ----------------- */
/* ------------------------------------------- */
print $siteheader; 
/* ------------------------------------------- */
/* -------------- STATUS BAR ----------------- */
/* ------------------------------------------- */

include("themes/common/status.inc.php"); 
print $sitecrumbs;
?>
</div>
<div class=topnav align=center>
<?
//use this if Section navigation is in left nav
if ($nav_arrange==1) {
	/* ------------------------------------------- */
	/* -------------- TOP NAV-sections ------------ */
	/* ------------------------------------------- */
	//print " | ";
	$totalnav = count($topnav);
	$next=1;
	foreach ($topnav as $item) {
		$samepage = (isset($section) && ($section == $item[id]))?1:0;
		if (!$section) $samepage = ($action && ($action == $item[id]))?1:0;
		print makelink($item,$samepage);
		if ($next != $totalnav) print " | ";
		$next=$next+1;
	}	
	print $topnav_extra;
}
?>
</div>

<table width=100% class=contenttable>
<tr>
<td class=leftnav>
<table width=100% cellpadding=2 cellspacing=0>	
<?
//use this if page navigation is in left nav
if ($nav_arrange==1) {
	/* ------------------------------------------- */
	/* -------------- LEFT NAV   ----------------- */
	/* ------------------------------------------- */
			
	foreach ($leftnav as $item) {
		print "<tr><td>";
		if ($item[type] == 'normal') {
			$samepage = (isset($page) && ($page == $item[id]))?1:0;
			if (!$page) $samepage = ($action && ($action == $item[id]))?1:0;
			print "<div>";
			print makelink($item,$samepage,'',1);
			print "</div>";
		}
		if ($item[type] == 'divider') {
			print "$item[extra]<br>";
		}
		if ($item[type] == 'heading') {
			print "<img src='$themesdir/breadloaf/images/bullet.gif' border=0 align=absmiddle> $item[name] :";
			if ($item[extra]) print "<div align=right>$item[extra]</div>";
		}
		print "</tr></td>";
	}
	print "</table>";
	print "<br>$leftnav_extra";
}
//use this if Section navigation is in left nav
if ($nav_arrange==2) {

/* ------------------------------------------- */
/* -------------- LEFT NAV-sections ---------- */
/* ------------------------------------------- */
		
	foreach ($topnav as $item) {
		print "<tr><td>";
		if ($item[type] == 'normal') {
			$samepage = (isset($section) && ($section == $item[id]))?1:0;
			//$samepage = (isset($page) && ($page == $item[id]))?1:0;
			if (!$page) $samepage = ($action && ($action == $item[id]))?1:0;
			print "<div>";
			print makelink($item,$samepage,'',1);
			print "</div>";
		}
		if ($item[type] == 'divider') {
			print "$item[extra]<br>";
		}
		if ($item[type] == 'heading') {
			print "<img src='$themesdir/breadloaf/images/bullet.gif' border=0 align=absmiddle> $item[name] :";
			if ($item[extra]) print "<div align=right>$item[extra]</div>";
		}
		print "</tr></td>";
	}
	print "</table>";
	print "<br>$topnav_extra";
	//print "<br>$leftnav_extra";
}
?>
</td>
<td class=contentarea>
<div class=topnav align=center>
<?
//use this if page navigation on top
if ($nav_arrange==2) {
	/* ------------------------------------------- */
	/* -------------- TOP NAV-pages -------------- */
	/* ------------------------------------------- */
	//print " | ";
	$totalnav = count($leftnav);
	$next=1;
	foreach ($leftnav as $item) {
		$samepage = (isset($page) && ($page == $item[id]))?1:0;
		//$samepage = (isset($section) && ($section == $item[id]))?1:0;
		if (!$section) $samepage = ($action && ($action == $item[id]))?1:0;
		print makelink($item,$samepage);
		if ($next != $totalnav) print " | ";
		$next=$next+1;
	}
	print $leftnav_extra;
	//print $topnav_extra;
}
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
//use this if page navigation on top and bottom
if ($nav_arrange==2) {
	/* ------------------------------------------- */
	/* -------------- Bottom NAV-pages -------------- */
	/* ------------------------------------------- */
	//print " | ";
	$totalnav = count($leftnav);
	$next=1;
	foreach ($leftnav as $item) {
		$samepage = (isset($page) && ($page == $item[id]))?1:0;
		//$samepage = (isset($section) && ($section == $item[id]))?1:0;
		if (!$section) $samepage = ($action && ($action == $item[id]))?1:0;
		print makelink($item,$samepage);
		if ($next != $totalnav) print " | ";
		$next=$next+1;
	}
	print $leftnav_extra;
	//print $topnav_extra;
}
 
?>
</div>
</td>
<?
/* ------------------------------------------- */
/* -------------- RIGHT NAV      ------------- */
/* ------------------------------------------- */

if (count($rightnav)) {
	print "<td style='margin-left: 20px'>";
	foreach ($rightnav as $item) {
		print "<a href='$item[url]'>$item[name]</a><BR>";
	}
	print "</td>";
}
?>
</tr>
</table>
<?

?>
<div class=topnav align=center>
<?
//use this if Section navigation is top an bottom

/* ------------------------------------------- */
/* --------- bottom NAV -sections --- */
/* ------------------------------------------- */
if ($nav_arrange==1) {
	//print " | ";
	$totalnav = count($topnav);
	$next=1;
	foreach ($topnav as $item) {
		$samepage = (isset($section) && ($section == $item[id]))?1:0;
		if (!$section) $samepage = ($action && ($action == $item[id]))?1:0;
		$item[extra] = "";
		print makelink($item,$samepage);
		if ($next != $totalnav) print " | ";
		$next=$next+1;
	}
}

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

	
