<table width="95%" border="0" cellpadding="0" cellspacing="5"></td></tr>
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


<tr><td align='left'>
Left Navigation Column Width:</td><td>
<select name='nav_width' onchange="document.settings.submit()">
<?
foreach ($nav_widths as $s) {
	print "<option value='$s'".(($nav_width==$s || ((!$nav_width || $nav_width == "") && $s=="150 pixels"))?" selected":"").">$s\n";
}
?>
</select>
</td></tr>

<tr><td align='left'>
Section Navigation Text Size:</td><td>
<select name='sectionnav_size' onchange="document.settings.submit()">
<?
foreach ($sectionnav_sizes as $s) {
	print "<option value='$s'".(($sectionnav_size==$s || ((!$sectionnav_size || $sectionnav_size == "") && $s=="12 pixels"))?" selected":"").">$s\n";
}
?>
</select>
</td></tr>

<tr><td align='left'>
Page Navigation Text Size:</td><td>
<select name='nav_size' onchange="document.settings.submit()">
<?
foreach ($nav_sizes as $s) {
	print "<option value='$s'".(($nav_size==$s || ((!$nav_size || $nav_size == "") && $s=="12 pixels"))?" selected":"").">$s\n";
}
?>
</select>
</td></tr>


<tr><td align='left'>
Navigation Arrangement:</td><td>
<select name='nav_arrange' onchange="document.settings.submit()">
<?
foreach ($nav_arranges as $s) {
	print "<option value='$s'".(($nav_arrange==$s)?" selected":"").">$s\n";
}
?>
</select>
</td></tr>
</table>
Note: <b>Top Sections</b> navigation is fine for most sites.  Use <b>Side Sections</b> for sites that you anticipate
having alot of sections (e.g. 10 or more) each of which has only a few pages.<br />
<i>The navigation arrangement can always be changed at any time.</i>
<hr noshade size='1' />