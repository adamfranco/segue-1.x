<? // output.inc.php
	// this script outputs the HTML resulting from action files
	
/* -------------- THEME SETTINGS ---------------------	*/
/*		handle the $themesettings array					*/
//print "$themesdir/$theme";
//exit();
if (file_exists("$themesdir/$theme/colors.inc.php"))
	include("$themesdir/$theme/colors.inc.php");

if ($themesettings[theme] == 'bevelbox') {   // indeed these settings are for this theme
	$use = $themesettings[colorscheme];
}
if (!$use) $use = 'blue';
$c = $_theme_colors[$use];

/* ------------------- END ---------------------------	*/

?>
<head>

<?/* ------------------------------------------- */
/* ------------- COMMON HEADER --------------- */
/* ------------------------------------------- */
include("themes/common/header.inc.php"); ?>

<? 
include("themes/$theme/css.inc.php");
?>

<?/* ------------------------------------------- */
/* -------------- PAGE TITLE ----------------- */
/* ------------------------------------------- */?>
<title><? echo $pagetitle; ?></title>

</head>

<body marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 rightmargin=0>

<?/* ------------------------------------------- */
/* -------------- STATUS BAR ----------------- */
/* ------------------------------------------- */
include("themes/common/status.inc.php"); ?>

<table width=90% align=center cellpadding=0 cellspacing=0>
<tr><td>
    <?
    /* ------------------------------------------- */
	/* ------------- SITE HEADER ----------------- */
	/* ------------------------------------------- */
	print $siteheader; 
	print $sitecrumbs; 
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
  
    
    <div class=topnav align=center>
	<div class='nav'>
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
<div class='nav'>
	<?
/* ------------------------------------------- */
/* -------------- LEFT NAV   ----------------- */
/* ------------------------------------------- */
		
	foreach ($leftnav as $item) {
		if ($item[type] == 'normal') {
			$samepage = (isset($page) && ($page == $item[id]))?1:0;
			if (!$page) $samepage = ($action && ($action == $item[id]))?1:0;
			print "<div class='nav'>";
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
</div>
</td>
</table>

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
</div> 
       
</td>
<td class="bottomright1">&nbsp;</td>
<td class="bottomright2">&nbsp;</td>
</tr>
</table>
<br>

<table width=90% align=center cellpadding=0 cellspacing=0>
<tr><td>
<?/* ------------------------------------------- */
/* -------------- FOOTER     ----------------- */
/* ------------------------------------------- */
print $sitefooter ?>
</tr></td>
</table>
<br>

	
