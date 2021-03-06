<style type='text/css'>
div, p, td, span, input { 
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
}
</style>
<?

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

if (!preg_match('/^[a-z_0-9]+$/i', $theme))
		die ('Error: invalid theme, "'.$theme.'".');
		
include("themes/$theme/colors.inc.php");

$bgcolors = array_keys($_bgcolor);
$colorschemes = array_keys($_theme_colors);
$borderstyles = array_keys($_borderstyle);
$bordercolors = array_keys($_bordercolor);
$textcolors = array_keys($_textcolor);
$linkcolors = array_keys($_linkcolor);
$nav_arranges = array_keys($_nav_arrange);
$nav_widths = array_keys($_nav_width);
$site_widths = array_keys($_site_width);
$sectionnav_sizes = array_keys($_sectionnav_size);
$nav_sizes = array_keys($_nav_size);

$bgcolor = $_REQUEST['bgcolor'];
$colorscheme = $_REQUEST['colorscheme'];
$borderstyle = $_REQUEST['borderstyle'];
$bordercolor = $_REQUEST['bordercolor'];
$textcolor = $_REQUEST['textcolor'];
$linkcolor = $_REQUEST['linkcolor'];
$nav_arrange = $_REQUEST['nav_arrange'];
$nav_width = $_REQUEST['nav_width'];
$sectionnav_size = $_REQUEST['sectionnav_size'];
$nav_size = $_REQUEST['nav_size'];
$site_width = $_REQUEST['site_width'];

if ($themesettings[theme] == 'tornpieces') {
	$bgcolor = $themesettings[bgcolor];
	$colorscheme = $themesettings[colorscheme];
	$borderstyle = $themesettings[borderstyle];
	$bordercolor = $themesettings[bordercolor];
	$textcolor = $themesettings[textcolor];
	$linkcolor = $themesettings[linkcolor];
	$nav_arrange = $themesettings[nav_arrange];
	$nav_width = $themesettings[nav_width];
	$sectionnav_size = $themesettings[sectionnav_size];
	$nav_size = $themesettings[nav_size];
	$site_width = $themesettings[site_width];
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
	$themesettings[sectionnav_size] = $sectionnav_size;
	$themesettings[nav_size] = $nav_size;
	$themesettings[site_width] = $site_width;
}

?>
<b>Torn Pieces</b><br />
This theme is meant to look like a pieces of torn paper.  Edges are a random pattern.
Also see Torn Paper theme.
<hr noshade size='1' />
<table width="95%" border="0" cellpadding="0" cellspacing="5"><tr><td align='left'>
Background Color:</td><td> 
<select name='bgcolor' onchange="document.settings.submit()">
<?
foreach ($bgcolors as $s) {
	print "<option value='$s'".(($bgcolor==$s)?" selected":"").">$s\n";
}
?>
</select>
</td></tr>
<input name="colorscheme" type="hidden" value="white" /><!--
<tr><td align='left'>
Foreground Color:</td><td> 
<select name='colorscheme'>
<?
foreach ($colorschemes as $s) {
	print "<option value='$s'".(($colorscheme==$s)?" selected":"").">$s\n";
}
?>
</select>
</td></tr>

<tr><td align='left'>
Border Style:</td><td>
<select name='borderstyle'>
<?
foreach ($borderstyles as $s) {
	print "<option value='$s'".(($borderstyle==$s)?" selected":"").">$s\n";
}
?>
</select>
</td></tr>
<tr><td align='left'>
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
<tr><td align='left'>
Text Color:</td><td>
<select name='textcolor' onchange="document.settings.submit()">
<?
foreach ($textcolors as $s) {
	print "<option value='$s'".(($textcolor==$s)?" selected":"").">$s\n";
}
?>
</select>
</td></tr>
<tr><td align='left'>
Link Color:</td><td>
<select name='linkcolor' onchange="document.settings.submit()">
<?
foreach ($linkcolors as $s) {
	print "<option value='$s'".(($linkcolor==$s)?" selected":"").">$s\n";
}
?>
</select>
</table>
Note: Make sure that your text stands out from background and that your text and link colors are contrasting.
<hr noshade size='1' />
<?

//All settings for Navigation are included here
include("themes/common/settings.inc.php");
?>