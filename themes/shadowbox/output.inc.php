<? // output.inc.php
	// this script outputs the HTML resulting from action files
	
/* -------------- THEME SETTINGS ---------------------	*/
/*		handle the $themesettings array					*/
//print "$themesdir/$theme";
if (file_exists("$themesdir/$theme/colors.inc.php"))
	include("$themesdir/$theme/colors.inc.php");

if ($themesettings[theme] == 'shadowbox') {   // indeed these settings are for this theme

	$use = $themesettings[colorscheme];
}
if (!$use) $use = 'white';
$c = $_theme_colors[$use];

/* ------------------- END ---------------------------	*/

?>
<head>


<?/* ------------------------------------------- */
/* ------------- COMMON HEADER --------------- */
/* ------------------------------------------- */
include("themes/common/header.inc.php"); ?>


<? include("themes/$theme/css.inc.php"); ?>

<?/* ------------------------------------------- */
/* -------------- PAGE TITLE ----------------- */
/* ------------------------------------------- */?>
<title><? echo $pagetitle; ?></title>

</head>

<body>


<?/* ------------------------------------------- */
/* -------------- STATUS BAR ----------------- */
/* ------------------------------------------- */
include("themes/common/status.inc.php"); ?>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td class=topleft>&nbsp;</td>
<td class=top>&nbsp;</td>
<td class=topright>&nbsp;</td>
</tr>
<tr>
<td class=left><img class=lefttop src='<? echo "$themesdir/$theme/images/lefttop.gif"?>'></td>
<td class=content>

<div class=header>
<?/* ------------------------------------------- */
/* ------------- SITE HEADER ----------------- */
/* ------------------------------------------- */
print $siteheader ?>
</div>

<div class=topnav align=center>
	<?
/* ------------------------------------------- */
/* -------------- TOP NAV    ----------------- */
/* ------------------------------------------- */
	print " | ";
	foreach ($topnav as $item) {
		$samepage = (isset($section) && ($section == $item[id]))?1:0;
		if (!$section) $samepage = ($action && ($action == $item[id]))?1:0;
		print makelink($item,$samepage);
		print " | ";
	}
	
	print $topnav_extra;
	?>
</div>

<table width=100% class=contenttable>
<tr>
<td class=leftnav>
	<?
/* ------------------------------------------- */
/* -------------- LEFT NAV   ----------------- */
/* ------------------------------------------- */
		
	foreach ($leftnav as $item) {
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
	}
	print "<br>$leftnav_extra";
	?>
</td>
<td class=contentarea>

<? print $content; ?>

</td>

<?
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


<div class=topnav align=center>
	<?
/* ------------------------------------------- */
/* -------------- TOP (or bottom) NAV -------- */
/* ------------------------------------------- */
	print " | ";
	foreach ($topnav as $item) {
		$samepage = (isset($section) && ($section == $item[id]))?1:0;
		if (!$section) $samepage = ($action && ($action == $item[id]))?1:0;
		print makelink($item,$samepage);
		print " | ";
	}
	
	print $topnav_extra;
	?>
</div>


<?/* ------------------------------------------- */
/* -------------- FOOTER     ----------------- */
/* ------------------------------------------- */
print $sitefooter ?>

</td> <!-- end content table cell -->
<td class=right><img class=righttop src='<? echo "$themesdir/$theme/images/righttop.gif"?>'></td>
</tr>
<tr>
<td class=bottomleft>&nbsp;</td>
<td class=bottom>&nbsp;</td>
<td class=bottomright>&nbsp;</td>

</tr>

</table>

	
