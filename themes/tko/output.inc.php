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

include("$themesdir/common/functions.inc.php");

if (file_exists("$themesdir/$theme/colors.inc.php"))
	include("$themesdir/$theme/colors.inc.php");


/* ------------------- END THEME SETTINGS---------------------	*/

?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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

<table border=0 width=700 cellpadding=0 cellspacing=0>
<tr>
<td width=700 height=150 background='<? echo "$themesdir/$theme/images/banner.gif"; ?>'>
	<img src='<? echo "$themesdir/$theme/images/150spacer.gif"; ?>' border=0 height=120 width=1><BR>
	<!-- main header content -->
	<table border=0 width=100% height=30 cellpadding=0 cellspacing=0>
	<tr>
	<td class=topbar width=180 align=center valign=middle>
	&nbsp; <!-- search bar -->
	</td>
	<td class=topnav>
	<table border=0 width=100% height=30 cellpadding=0 cellspacing=0>
	<tr>
	<?
/******************************************************************************
 * horizontal nav
 ******************************************************************************/

//	print "<div class='sectionnav'>";
	//print "<div class=$navlinka>";
	foreach ($topnav as $item) {
		$samepage = (isset($navtype) && ($page == $item[id]))?1:0;
		if (!$page) $samepage = ($action && ($action == $item[id]))?1:0;
		print "<td class=topbar width=180 align=center valign=middle><nobr>";
		print makelink($item,$samepage, " class='navlink' ");
		print "</nobr></td>";
		$next=$next+1;
	}
	if ($topnav_extra) print "<td class=topbar align=center valign=middle><nobr>$topnav_extra</nobr></td>";
//	print "</div>";

	?>
	</tr>
	</table>
	
	</td>
	</tr>
	</table>
</td></tr>


<tr>
<td width=100%>
<!-- content and nav bar -->
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
	<td width=180 class=leftnav valign=top>
	
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<!-- left navbar -->
	<?
	foreach ($leftnav as $item) {
		print "<tr>";
		if ($item[type] == 'normal') {
			$samepage = (isset($page) && ($page == $item[id]))?1:0;
			if (!$page) $samepage = ($action && ($action == $item[id]))?1:0;
			$gif = "$themesdir/$theme/images/". (($samepage)?"lis.gif":"li.gif");
			print "<td valign=middle><img src=$gif border=0 style='padding-right: 4px; padding-top: 1px'></td>";
			print "<td class='nav'>";
			print tkoMakelink($item,0," class='navlink' ",1);
			print "</td>";
		}
		if ($item[type] == 'divider') {
			print "<td colspan=2>$item[extra]<br></td>";
		}
		if ($item[type] == 'heading') {
			print "<td colspan=2><div class='leftmargin bottommargin5'>$item[name]</div>";
			if ($item[extra]) print "<div align=right>$item[extra]</div>";
			print "</td>";
		}
		print "</tr>";
	}
	
	foreach ($leftnav2 as $item) {
		print "<tr>";
		if ($item[type] == 'normal') {
			$samepage = (isset($section) && ($section == $item[id]))?1:0;
			if (!$section) $samepage = ($action && ($action == $item[id]))?1:0;
			$gif = "$themesdir/$theme/images/". (($samepage)?"lis.gif":"li.gif");
			print "<td valign=middle><img src=$gif border=0 style='padding-right: 4px; padding-top: 1px'></td>";
			print "<td class='nav'>";
			print tkoMakelink($item,0," class='navlink' ",1);
			print "</td>";
		}
		if ($item[type] == 'divider') {
			print "<td colspan=2>$item[extra]<br></td>";
		}
		if ($item[type] == 'heading') {
			print "<td colspan=2><div class='leftmargin bottommargin5'>$item[name]</div>";
			if ($item[extra]) print "<div align=right>$item[extra]</div>";
			print "</td>";
		}
		print "</tr>";
	}
	
	?>
	</table>
	
	<? 	if ($leftnav_extra) print "<br>$leftnav_extra"; ?>
	
	<!-- end left navbar -->
	</td>
	
	<td width=520 class=content valign=top>
	<!-- content goes here -->
	<? print $sitecrumbs; ?>
	<? print $content; ?>

	<!-- end content -->
	</td>
	</tr>
	</table>
	
<!-- end content and nav bar -->
<? print $sitefooter; ?>

</td>
</tr>
</table>

<?
function tkoMakelink($i,$samepage=0,$e='',$newline=0) {
	$s = '';
	$s=(!$samepage&&$i[url])?"<a href='$i[url]' target='$i[target]'".(($e)?" $e":"").">":"";
	$s.=$i[name];
	$s.=(!$samepage&&$i[url])?"</a>":"";
	$s.=($i[extra])?(($newline)?"</td></tr><tr><td></td><td class='nav'><div align=right>":" ").$i[extra].(($newline)?"</div>":""):"";
	return $s;
}


