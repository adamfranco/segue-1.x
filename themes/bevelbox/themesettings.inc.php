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

$colorschemes = array_keys($_theme_colors);
$linkcolors = array_keys($_linkcolor);
$nav_arranges = array_keys($_nav_arrange);
$sectionnav_sizes = array_keys($_sectionnav_size);
$nav_sizes = array_keys($_nav_size);
$nav_widths = array();

$colorscheme = $_REQUEST['colorscheme'];
$linkcolor = $_REQUEST['linkcolor'];
$nav_arrange = $_REQUEST['nav_arrange'];
$sectionnav_size = $_REQUEST['sectionnav_size'];
$nav_size = $_REQUEST['nav_size'];

if ($themesettings[theme] == 'bevelbox') {
	$colorscheme = $themesettings[colorscheme];
	$linkcolor = $themesettings[linkcolor];
	$nav_arrange = $themesettings[nav_arrange];
	$sectionnav_size = $themesettings[sectionnav_size];
	$nav_size = $themesettings[nav_size];

} else {
	$themesettings[theme] = 'bevelbox';
	$themesettings[colorscheme] = $colorscheme;
	$themesettings[linkcolor] = $linkcolor;
	$themesettings[nav_arrange] = $nav_arrange;
	$themesettings[sectionnav_size] = $sectionnav_size;
	$themesettings[nav_size] = $nav_size;
}

?>
<b>Bevel Box</b><br />
This theme creates the illusion of a box overlaying the page.  
This illusion is created by surrounding box with a bevelled edge.
<hr noshade size='1' />
<table width="95%" border="0" cellpadding="0" cellspacing="5"><tr><td align='left'>
Box Color:</td><td> 
<select name='colorscheme' onchange="document.settings.submit()">
<?
foreach ($colorschemes as $s) {
	print "<option value='$s'".(($colorscheme==$s)?" selected":"").">$s\n";
}
?>
</select>
</table>
<?

//All settings for Navigation are included here
include("$themesdir/common/settings.inc.php");
?>
