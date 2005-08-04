<? /* $Id$ */

/******************************************************************************
 * table & form definition
 ******************************************************************************/
print "<form action='$PHP_SELF?$SID' medthod=post name='addform'>";

print "<input type=hidden name=edaction value=''>";
print "<input type=hidden name=edname value=''>";
print "<input type=hidden name=site value='".$_SESSION[obj]->getField("name")."'>";

print "<table cellspacing=1 width='100%'>";
print "<tr>";
print "<th colspan=3>Editors";

print "<div align='right' style='padding: 5px;'>";
print "<input type=button value='".(($isOwner)?"Cancel":"Close")."' onClick='document.location=\"edit_permissions.php?cancel=1\"'>";
if ($isOwner) print "\n<input type=button name='savepermissions' value='Save All Changes' onClick='document.location=\"edit_permissions.php?savechanges=1\"'>";
print "</div>";

print "</th>";

print "</tr>";

/******************************************************************************
 * Buttons:
 * check all/uncheck all buttons, edit permissions for checked editors button
 * add editor button
 ******************************************************************************/
$buttons = "<tr>";
$buttons .= "<th align='left' colspan=2>";
$buttons .= "<input type=button name='checkall' value='Check All' onClick='checkAll()'> ";
$buttons .= "<input type=button name='uncheckall' value='Uncheck All' onClick='uncheckAll()'> ";
$buttons .= "<input type=button name='add' value='Add Editor' onClick=\"sendWindow('addeditor',400,250,'add_editor.php?$sid')\">";
$edlist = $_SESSION[obj]->getEditors();
$buttons .= (($className && !in_array($className,$edlist))?"<div><a href='#' onClick='addClassEditor();'>Add students in ".$className."</a></div>":"");
$buttons .= "</th><th align='right'>";
$buttons .= "<input type=submit name='editpermissions' value='Edit Permissions of Checked -&gt;'>";
$buttons .= "</th></tr>";
print $buttons;
//printpre ($edlist);
//printpre ($className);

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
		print "<td class=td$color align='center'><input type=checkbox name='editors[]' value='$e' ".((in_array($e,$_SESSION[editors]))?" checked":"")."></td>";
		print "<td class=td$color>";
		if ($e == "everyone")
			print "Everyone (everyone) - will override other entries</td>";
		else if ($e == "institute")
			print $cfg[inst_name]." Users (institute)</td>";
		else
			print ldapfname($e)." ($e)</td>";
		
		// Remove links
		print "<td class=td$color align='center'>";
		if ($e == 'everyone' || $e == 'institute') print "&nbsp;";
		else print "<a href='#' onClick='delEditor(\"$e\");'>remove</a>";
		print "</td>";
		
		print "</tr>";
		$color = 1-$color;
	}
} else  print "<tr><td class=td1 > &nbsp; </td><td class=td1 colspan=2>no editors added</td></tr>";

/******************************************************************************
 * Buttons:
 * print again.
 ******************************************************************************/
print $buttons;

print "<th align='center' colspan=3 style='text-size: 25; font-weight: bold'>";

print "<div align='right' style='padding: 5px;'>";
if ($isOwner) {
	print "\n\t<input type=button style='width: $btnw' class='button' name='preview_as' value=' &nbsp; Preview Saved Permissions As... &nbsp;' onClick='sendWindow(\"preview_as\",400,300,\"preview.php?$sid&site=$site&query=".urlencode("&action=site&site=".$site)."\")' target='preview_as' style='text-decoration: none'> ";
}
print "<input type=button value='".(($isOwner)?"Cancel":"Close")."' onClick='document.location=\"edit_permissions.php?cancel=1\"'>";
if ($isOwner) print "\n<input type=button name='savepermissions' value='Save All Changes' onClick='document.location=\"edit_permissions.php?savechanges=1\"'>";
print "</div>";

print "</th>";

/******************************************************************************
 * close up form & table
 ******************************************************************************/
print "</table></form>";