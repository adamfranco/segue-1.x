<?
include("$themesdir/$theme/defaults.inc.php");

$schemes = array_keys($_theme_colors);

if ($themesettings[theme] == 'minimal') {
	$colorscheme = $themesettings[colorscheme];
} else {
	$themesettings[theme] = 'minimal';
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