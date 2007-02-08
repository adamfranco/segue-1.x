<style type='text/css'>
div, p, td, span, input { 
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
}
</style>
<?

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

/*********************************************************
 * Navigation defaults
 *********************************************************/
include("$themesdir/common/nav.inc.php");
include("$themesdir/$theme/defaults.inc.php");

$nav_arranges = array_keys($_nav_arrange);

/*********************************************************
 * Stored theme settings
 *********************************************************/
if ($themesettings[theme] == 'customcss') {
	$css = $themesettings['css'];
	
	$nav_arrange = $themesettings['nav_arrange'];

}
/*********************************************************
 * Defaults OR form data
 *********************************************************/
else {
	$themesettings[theme] = 'customcss';
	
	if ($_REQUEST['restore'])
		$themesettings['css'] = $css;
	else if ($_REQUEST['css'])
		$css = $themesettings['css'] = $_REQUEST['css'];
	else
		$themesettings['css'] = $css;
		
	if ($_REQUEST['nav_arrange'])
		$nav_arrange = $themesettings['nav_arrange'] = $_REQUEST['nav_arrange'];
	else
		$themesettings['nav_arrange'] = $nav_arrange;

}
?>

<b>Custom CSS</b><br />
Enter your custom CSS <? print helplink("customcss","?"); ?> into the text box.
<textarea name='css' cols='43' rows='20'><?php print $css; ?></textarea>
<br/><input type='submit' value='Update Preview'/>
<input type='button' value='Restore Defaults' onclick="if (confirm('Are you sure you want to loose your changes and restore the default CSS?')) {this.nextSibling.value='true'; this.form.submit();}"/><input type='hidden' name='restore' value=''/>
<hr noshade size='1' />
<?


//All settings for Navigation are included here
?>

<table width="95%" border="0" cellpadding="0" cellspacing="5"></td></tr>

<tr><td align='left'>
Navigation Arrangement:</td><td>
<select name='nav_arrange' onchange="document.settings.submit()">
<?
foreach ($_nav_arrange as $key => $num) {
	print "<option value='$key'".(($nav_arrange==$key || $nav_arrange==$num)?" selected":"").">$key\n";
}
?>
</select>
</td></tr>
</table>
Note: <b>Top Sections</b> navigation is fine for most sites.  Use <b>Side Sections</b> for sites that you anticipate
having alot of sections (e.g. 10 or more) each of which has only a few pages.<br />
<i>The navigation arrangement can always be changed at any time.</i>
<hr noshade size='1' />

