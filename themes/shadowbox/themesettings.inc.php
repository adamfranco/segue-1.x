<?
include("$themesdir/$theme/colors.inc.php");

$bgcolors = array_keys($_bgcolor);
$colorschemes = array_keys($_theme_colors);
$borderstyles = array_keys($_borderstyle);
$bordercolors = array_keys($_bordercolor);
$textcolors = array_keys($_textcolor);
$linkcolors = array_keys($_linkcolor);

if ($themesettings[theme] == 'shadowbox') {
	$bgcolor = $themesettings[bgcolor];
	$colorscheme = $themesettings[colorscheme];
	$borderstyle = $themesettings[borderstyle];
	$bordercolor = $themesettings[bordercolor];
	$textcolor = $themesettings[textcolor];
	$linkcolor = $themesettings[linkcolor];
} else {
	$themesettings[theme] = 'shadowbox';
	$themesettings[bgcolor] = $bgcolor;
	$themesettings[colorscheme] = $colorscheme;
	$themesettings[borderstyle] = $borderstyle;
	$themesettings[bordercolor] = $bordercolor;
	$themesettings[textcolor] = $textcolor;
	$themesettings[linkcolor] = $linkcolor;
}

?>
<b>ShadowBox</b><br>
This theme creates the illusion of a page floating on top of a horizontal surface.  This illusion is created by a drop shadow with following settings:<br>
offset=4 pixels, blur/spread=8 pixels, opacity=42%
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
</td></tr>

</table>
<hr noshade size=1>
Note: Make sure that your text stands out from background and that your text and link colors are contrasting.