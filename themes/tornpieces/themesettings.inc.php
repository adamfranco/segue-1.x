<?
include("$themesdir/$theme/colors.inc.php");

$bgcolors = array_keys($_bgcolor);
$colorschemes = array_keys($_theme_colors);
$borderstyles = array_keys($_borderstyle);
$bordercolors = array_keys($_bordercolor);
$textcolors = array_keys($_textcolor);
$linkcolors = array_keys($_linkcolor);
$nav_arranges = array_keys($_nav_arrange);
$nav_widths = array_keys($_nav_width);


if ($themesettings[theme] == 'tornpieces') {
	$bgcolor = $themesettings[bgcolor];
	$colorscheme = $themesettings[colorscheme];
	$borderstyle = $themesettings[borderstyle];
	$bordercolor = $themesettings[bordercolor];
	$textcolor = $themesettings[textcolor];
	$linkcolor = $themesettings[linkcolor];
	$nav_arrange = $themesettings[nav_arrange];
	$nav_width = $themesettings[nav_width];
} else {
	$themesettings[theme] = 'tornpieces';
	$themesettings[bgcolor] = $bgcolor;
	$themesettings[colorscheme] = $colorscheme;
	$themesettings[borderstyle] = $borderstyle;
	$themesettings[bordercolor] = $bordercolor;
	$themesettings[textcolor] = $textcolor;
	$themesettings[linkcolor] = $linkcolor;
	$themesettings[nav_arrange] = $nav_arrange;
	$themesettings[nav_width] = $nav_width;
}

?>
<b>Torn Pieces</b><br>
This theme is meant to look like a pieces of torn paper.  Edges are a random pattern.
Also see Torn Paper theme.
<hr noshade size=1>
<table width="95%" border="0" cellpadding="0" cellspacing="5">    
<tr><td align=left>
Background Color:</td><td> 
<select name='bgcolor'>
<?
foreach ($bgcolors as $s) {
	print "<option value='$s'".(($bgcolor==$s)?" selected":"").">$s\n";
}
?>
</select>
</td></tr>
<input name="colorscheme" type="hidden" value="white">  
<!--
<tr><td align=left>
Foreground Color:</td><td> 
<select name='colorscheme'>
<?
foreach ($colorschemes as $s) {
	print "<option value='$s'".(($colorscheme==$s)?" selected":"").">$s\n";
}
?>
</select>
</td></tr>

<tr><td align=left>
Border Style:</td><td>
<select name='borderstyle'>
<?
foreach ($borderstyles as $s) {
	print "<option value='$s'".(($borderstyle==$s)?" selected":"").">$s\n";
}
?>
</select>
</td></tr>
<tr><td align=left>
Border Color:</td><td>
<select name='bordercolor'>
<?
foreach ($bordercolors as $s) {
	print "<option value='$s'".(($bordercolor==$s)?" selected":"").">$s\n";
}
?>
</select>
</td></tr>
-->
<tr><td align=left>
Text Color:</td><td>
<select name='textcolor'>
<?
foreach ($textcolors as $s) {
	print "<option value='$s'".(($textcolor==$s)?" selected":"").">$s\n";
}
?>
</select>
</td></tr>
<tr><td align=left>
Link Color:</td><td>
<select name='linkcolor'>
<?
foreach ($linkcolors as $s) {
	print "<option value='$s'".(($linkcolor==$s)?" selected":"").">$s\n";
}
?>
</select>
</table>
Note: Make sure that your text stands out from background and that your text and link colors are contrasting.
<hr noshade size=1>
<table width="95%" border="0" cellpadding="0" cellspacing="5">    
</td></tr>
<tr><td align=left>
Left Navigation Width:</td><td>
<select name='nav_width'>
<?
foreach ($nav_widths as $s) {
	print "<option value='$s'".(($nav_width==$s)?" selected":"").">$s\n";
}
?>
</select>
</td></tr>

<tr><td align=left>
Navigation Arrangement:</td><td>
<select name='nav_arrange'>
<?
foreach ($nav_arranges as $s) {
	print "<option value='$s'".(($nav_arrange==$s)?" selected":"").">$s\n";
}
?>
</select>
</td></tr>
</table>
Note: <b>Top Sections</b> navigation is fine for most sites.  Use <b>Side Sections</b> for sites that you anticipate
having alot of sections (e.g. 10 or more) each of which has only a few pages.<br>
<i>The navigation arrangement can always be changed at any time.</i>
<hr noshade size=1>