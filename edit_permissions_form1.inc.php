<? /* $Id$ */
//printpre($_SESSION[obj]);
/******************************************************************************
 * table & form definition
 ******************************************************************************/
print "<form action='$PHP_SELF?$SID' method='post' name='addform'>";

print "\n\t<input type='hidden' name='edaction' value='' />";
print "\n\t<input type='hidden' name='edname' value='' />";
print "\n\t<input type='hidden' name='site' value='".$_SESSION[obj]->getField("name")."' />";

print "\n\t<table cellspacing='1' width='100%'>";
print "\n\t\t<tr>";
print "\n\t\t\t<th colspan='3'>Editors";

print "\n\t\t\t\t<div align='right' style='padding: 5px;'>";
print "\n\t\t\t\t\t<input type='button' value='".(($isOwner)?"Cancel":"Close")."' onclick='document.location=\"edit_permissions.php?cancel=1\"' />";
if ($isOwner) print "\n\t\t\t\t\t<input type='button' name='savepermissions' value='Save All Changes' onclick='document.location=\"edit_permissions.php?savechanges=1\"' />";
print "\n\t\t\t\t</div>";

print "\n\t\t\t</th>";

print "\n\t\t</tr>";

/******************************************************************************
 * Buttons:
 * check all/uncheck all buttons, edit permissions for checked editors button
 * add editor button
 ******************************************************************************/
$buttons = "\n\t\t<tr>";
$buttons .= "\n\t\t\t<th align='left' colspan='2'>";
$buttons .= "\n\t\t\t\t<input type='button' name='checkall' value='Check All' onclick='checkAll()' /> ";
$buttons .= "\n\t\t\t\t<input type='button' name='uncheckall' value='Uncheck All' onclick='uncheckAll()' /> ";
$buttons .= "\n\t\t\t\t<input type='button' name='add' value='Add Editor' onclick=\"sendWindow('addeditor',400,250,'add_editor.php?$sid')\" />";
$edlist = $_SESSION[obj]->getEditors();
$buttons .= ((isclass ($className) && !in_array($className,$edlist))?"\n\t\t\t\t<div><a href='#' onclick='addClassEditor();'>Add students in ".$className."</a></div>":"");
$buttons .= "\n\t\t\t</th>\n\t\t\t<th align='right'>";
$buttons .= "\n\t\t\t\t<input type='submit' name='editpermissions' value='Edit Permissions of Checked -&gt;' />";
$buttons .= "\n\t\t\t</th>\n\t\t</tr>";
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
		$e = htmlentities($e);
		print "\n\t\t<tr>";
		print "\n\t\t\t<td class='td$color' align='center'>\n\t\t\t\t<input type='checkbox' name='editors[]' value='$e' ".((in_array($e,$_SESSION[editors]))?" checked='checked'":"")." />\n\t\t\t</td>";
		print "\n\t\t\t<td class='td$color'>";
		if ($e == "everyone")
			print "Everyone (everyone) - will override other entries</td>";
		else if ($e == "institute")
			print $cfg[inst_name]." Users (institute)</td>";
		else
			print ldapfname($e)." ($e)</td>";
		
		// Remove links
		print "\n\t\t\t<td class='td$color' align='center'>";
		if ($e == 'everyone' || $e == 'institute') print "&nbsp;";
		else print "<a href='#' onclick='delEditor(\"$e\");'>remove</a>";
		print "</td>";
		
		print "\n\t\t</tr>";
		$color = 1-$color;
	}
} else  print "\n\t\t<tr>\n\t\t\t<td class='td1' > &nbsp; </td>\n\t\t\t<td class='td1' colspan='2'>no editors added</td>\n\t\t</tr>";

/******************************************************************************
 * Buttons:
 * print again.
 ******************************************************************************/
print $buttons;

print "\n\t\t<tr>";
print "\n\t\t\t<th align='center' colspan='3' style='text-size: 25; font-weight: bold'>";

print "\n\t\t\t\t<div align='right' style='padding: 5px;'>";
if ($isOwner) {
	print "\n\t\t\t\t\t<input type='button' style='width: $btnw; text-decoration: none;' class='button' name='preview_as' value=' &nbsp; Preview Saved Permissions As... &nbsp;' onclick='sendWindow(\"preview_as\",400,300,\"preview.php?$sid&amp;site=$site&amp;query=".urlencode("&amp;action=site&amp;site=".$site)."\")' /> ";
}
print "\n\t\t\t\t\t<input type='button' value='".(($isOwner)?"Cancel":"Close")."' onclick='document.location=\"edit_permissions.php?cancel=1\"' />";
if ($isOwner) print "\n\t\t\t\t\t<input type='button' name='savepermissions' value='Save All Changes' onclick='document.location=\"edit_permissions.php?savechanges=1\"' />";
print "\n\t\t\t\t</div>";

print "\n\t\t\t</th>";
print "\n\t\t</tr>";

/******************************************************************************
 * close up form & table
 ******************************************************************************/
print "\n\t</table>\n</form>";