<? /* $Id$ */

/******************************************************************************
 * table & form definition
 ******************************************************************************/
print "<form action='$PHP_SELF?$SID' medthod=post name='addform'>";

print "<input type=hidden name=edaction value=''>";
print "<input type=hidden name=edname value=''>";

print "<table cellspacing=1 width='100%'>";
print "<tr>";
print "<th colspan=3>Editors</th>";
print "</tr>";

/******************************************************************************
 * Buttons:
 * check all/uncheck all buttons, edit permissions for checked editors button
 * add editor button
 ******************************************************************************/
$buttons = "<tr>";
$buttons .= "<th align=left colspan=2>";
$buttons .= "<input type=button name='checkall' value='Check All' onClick='checkAll()'> ";
$buttons .= "<input type=button name='uncheckall' value='Uncheck All' onClick='uncheckAll()'> ";
$buttons .= ((isclass($sitename))?"<a href='#' onClick='addClassEditor();'>Add students in ".$sitename."</a>":"");
$buttons .= "<input type=button name='add' value='Add Editor' onClick=\"sendWindow('addeditor',400,250,'add_editor.php?$sid')\">";
$buttons .= "</th><th align=right>";
$buttons .= "<input type=submit name='editpermissions' value='Edit Permissions of Checked'>";
$buttons .= "</th></tr>";
print $buttons;

/******************************************************************************
 * editor table headings
 ******************************************************************************/
?>
<tr>
<th>edit</th>
<th>name</th>
<th> &nbsp; </th>
</tr>
<?

/******************************************************************************
 * print out list of editors, checkboxes for choosing, delete editor links.
 ******************************************************************************/
if ($edlist = $_SESSION[obj]->getEditors()) {
	$color = 0;
	foreach ($edlist as $e) {
		print "<tr>";
		print "<td class=td$color align=center><input type=checkbox name='editors[]' value='$e' ".((in_array($e,$_SESSION[editors]))?" checked":"")."></td>";
		print "<td class=td$color>";
		if ($e == "everyone")
			print "Everyone (everyone) - will override other entries</td>";
		else if ($e == "institute")
			print $cfg[inst_name]." Users (institute)</td>";
		else
			print ldapfname($e)." ($e)</td>";
		print "<td class=td$color align=center>";
		if ($e == 'everyone' || $e == 'institute') print "&nbsp;";
		else print "<a href='#' onClick='delEditor(\"$e\");'>remove</a>";
		print "</td>";
		print "</tr>";
		$color = 1-$color;
	}
} else  print "<tr><td class=td1 > &nbsp; </td><td class=td1 colspan=3>no editors added</td></tr>";

/******************************************************************************
 * Buttons:
 * print again.
 ******************************************************************************/
print $buttons;

/******************************************************************************
 * close up form & table
 ******************************************************************************/
print "</table></form>";