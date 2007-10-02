<style type='text/css'>
div, p, td, span, input { 
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
}
</style>
<?

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

include("$themesdir/$theme/colors.inc.php");

$bgcolors = array_keys($_bgcolor);
$colorschemes = array_keys($_theme_colors);
$borderstyles = array_keys($_borderstyle);
$bordercolors = array_keys($_bordercolor);
$textcolors = array_keys($_textcolor);
$linkcolors = array_keys($_linkcolor);
$nav_arranges = array_keys($_nav_arrange);
$nav_widths = array_keys($_nav_width);
$sectionnav_sizes = array_keys($_sectionnav_size);
$nav_sizes = array_keys($_nav_size);
$site_widths = array_keys($_site_width);

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

if ($themesettings[theme] == 'midd') {
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
	$themesettings[theme] = 'midd';
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
<b>Midd CMS</b><br />
This theme is reserved for Segue sites at Middllebury that need to be similar
in appearance to the official college website.
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
<tr><td align='left'>
Menu Color:</td><td> 
<select name='colorscheme' onchange="document.settings.submit()">
<?
foreach ($colorschemes as $s) {
	print "<option value='$s'".(($colorscheme==$s)?" selected":"").">$s\n";
}
?>
</select>

</td></tr>
<tr><td align='left'>
Site Width:</td><td> 
<select name='site_width' onchange="document.settings.submit()">
<?
foreach ($site_widths as $s) {
	print "<option value='$s'".(($site_width==$s || ((!$site_width || $site_width == "") && $s=="variable"))?" selected":"").">$s\n";
}
?>
</select>
</td></tr>

</table>

<?

//All settings for Navigation are included here
//include("$themesdir/common/settings.inc.php");
?>