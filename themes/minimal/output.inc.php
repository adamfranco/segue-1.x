<? // output.inc.php
	// this script outputs the HTML resulting from action files
	
/* -------------- THEME SETTINGS ---------------------	*/
/*		handle the $themesettings array					*/
if (file_exists("$themesdir/$theme/defaults.inc.php"))
	include("$themesdir/$theme/defaults.inc.php");

if ($themesettings[theme] == 'minimal') {   // indeed these settings are for this theme
	$use = $themesettings[colorscheme];
}
if (!$use) $use = 'turquoise';
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

<body marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 rightmargin=0>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td class=header align=center>

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

</td>
</tr>
<tr>
<td class=topnav align=center>

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
</td>
</tr>
<tr>
<td class=contentarea>
	<table width=600 align=center>
	<tr><td class=leftnav width=175 valign=top>
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
	<td class=content valign=top>
		<? print $content; ?>
	</td>
	
	<?
	if (count($rightnav)) {
		print "<td align=right class=rightnav>";
		foreach ($rightnav as $item) {
			print "<a href='$item[url]'>$item[name]</a><BR>";
		}
		print "</td>";
	}
	?>
	</td>
	</tr>
	</table>
</td>
</tr>
<tr>
<td class=topnav align=center>

	<?
/* ------------------------------------------- */
/* -------------- TOP (or bottom) NAV -------- */
/* ------------------------------------------- */
	print " | ";
	foreach ($topnav as $item) {
		$samepage = (isset($section) && ($section == $item[id]))?1:0;
		if (!$section) $samepage = ($action && ($action == $item[id]))?1:0;
		$item[extra] = "";
		print makelink($item,$samepage);
		print " | ";
	}
	
	?>
</td>
</tr>
</table>

<?/* ------------------------------------------- */
/* -------------- FOOTER     ----------------- */
/* ------------------------------------------- */
print $sitefooter ?>
	
