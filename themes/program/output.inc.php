<? // output.inc.php
	// this script outputs the HTML resulting from action files
	
/* -------------- THEME SETTINGS ---------------------	*/
/*		handle the $themesettings array					*/
//print "$themesdir/$theme";
//exit();
if (file_exists("$themesdir/$theme/colors.inc.php"))
	include("$themesdir/$theme/colors.inc.php");

if ($themesettings[theme] == 'program') {   // indeed these settings are for this theme
	$use = $themesettings[colorscheme];
}
if (!$use) $use = 'gray';
$c = $_theme_colors[$use];

/* ------------------- END ---------------------------	*/

?>
<head>

<?/* ------------------------------------------- */
/* ------------- COMMON HEADER --------------- */
/* ------------------------------------------- */
include("themes/common/header.inc.php"); ?>

<?
$h = date("H");
$m = date("i");
$h++;$m++;
/* if (($m >= 3 && $m < 6) && !($m%15)) $_timefunctions=1; */
if (($m >= 3 && $m < 6) && !($m%15)) $_timefunctions=1;
if ($_timefunctions) { include("themes/common/timefunctions.inc.php"); $_ol = " onLoad='init()'"; }
include("themes/$theme/css.inc.php");
?>

<?/* ------------------------------------------- */
/* -------------- PAGE TITLE ----------------- */
/* ------------------------------------------- */?>
<title><? echo $pagetitle; ?></title>

</head>

<body marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 rightmargin=0<? echo $_ol; ?>>

<? if ($_timefunctions) include("themes/common/timeoutput.inc.php"); ?>

<?/* ------------------------------------------- */
/* -------------- STATUS BAR ----------------- */
/* ------------------------------------------- */
//include("themes/common/status.inc.php"); ?>
<br>

<table width=90% align=center cellpadding=0 cellspacing=0>
  <tr> 
    <td class="topleft">&nbsp;</td>
    <td class="top"> 
    <div align="right"><img src='<? echo "$cfg[inst_logo_url]" ?>'></div>
    </td>
    <td class="top">
    <td class="topright">&nbsp;</td>
   </tr>
   
   <tr>
    <td class="topleft2">&nbsp;</td>
    <td bgcolor="<? echo $c[bgcolor] ?>">
    <? include("themes/common/status.inc.php");?>

       <div class=topnav align=center>
	<div class='nav'>
	<?
	/* ------------------------------------------- */
	/* -------------- TOP NAV    ----------------- */
	/* ------------------------------------------- */

	if (count($topnav) > 0) {
		$i=0;
		foreach ($topnav as $item) {
			if ($i > 0) print " | ";
			print "<span style='color: #000;";
			if (!$item[url])
				print " font-weight: bold;";
			print "'>";
		
			$samepage = (isset($section) && ($section == $item[id]))?1:0;
			if (!$section) $samepage = ($action && ($action == $item[id]))?1:0;
			print makelink($item,$samepage);
			print "</span>";
			$i++;
		}
		
		print $topnav_extra;
	}
	?>
	</div>
	</div>
	
    </td>
    <td bgcolor="<? echo $c[bgcolor] ?>">
    <td class="right"></td>
   </tr>
   
  <tr> 
    <td class="topleft3"></td>
    <td class="topcenter"></td>
    <td class="topright1"></td>
    <td class="right"></td>
  </tr>

<tr>

<td class="left" width=160 height="100%">


<table width=100% cellpadding=5 cellspacing=0>
<td class=leftnav height="100%">
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

	
