<?
include("$themesdir/$theme/colors.inc.php");

$schemes = array_keys($_theme_colors);

if ($themesettings[theme] == 'program') {
	$colorscheme = $themesettings[colorscheme];
} else {
	$themesettings[theme] = 'program';
	$themesettings[colorscheme] = $colorscheme;
}

?>

Choose the color scheme you wish to use: <select name='colorscheme' onChange="document.settings.submit()">
<?
foreach ($schemes as $s) {
	print "<option value='$s'".(($colorscheme==$s)?" selected":"").">$s\n";
}
?>
</select>