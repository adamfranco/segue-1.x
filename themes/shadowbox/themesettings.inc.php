<?
include("$themesdir/$theme/colors.inc.php");

$schemes = array_keys($_theme_colors);

if ($themesettings[theme] == 'shadowbox') {
	$colorscheme = $themesettings[colorscheme];
} else {
	$themesettings[theme] = 'shadowbox';
	$themesettings[colorscheme] = $colorscheme;
}

?>

Choose the color scheme you wish to use: <select name='colorscheme'>
<?
foreach ($schemes as $s) {
	print "<option value='$s'".(($colorscheme==$s)?" selected":"").">$s\n";
}
?>
</select>