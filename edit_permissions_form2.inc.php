<? /* $Id$ */

/******************************************************************************
 * table & form definition
 ******************************************************************************/
$totalcolumns = count($_SESSION[editors])*4 + 2;

print "<form action='$PHP_SELF?$SID' medthod=post name=addform>";
print "<input type=hidden name='step' value=$step>";

?>
<input type=hidden name=fieldchange value=0>
<input type=hidden name=pscope value=''>
<input type=hidden name=psite value=''>
<input type=hidden name=psection value=0>
<input type=hidden name=ppage value=0>
<input type=hidden name=pstory value=0>
<input type=hidden name=pfield value=0>
<input type=hidden name=pwhat value=0>
<input type=hidden name=puser value=''>
<?


print "<table cellspacing=1 width='100%'>";
print "<tr>";
print "<td align=center colspan=$totalcolumns style='text-size: 25; font-weight: bold'>Permissions</td>";
print "</tr>";

/******************************************************************************
 * Buttons:
 * back to form1 (editor choosing)
 ******************************************************************************/
$buttons = "<tr>";
$c = $totalcolumns - 1;
$buttons .= "<td align=left>";
$buttons .= "<input type=submit name='chooseeditors' value='<- Choose Editors'> ";
$buttons .= "<input type=submit name='savepermissions' value='Save Changes'>";
$buttons .= "</td><td align=left colspan=$c>";
$buttons .= "L = Locked; v = View; a = Add; e = Edit; d = Delete; ".helplink("editors","help");
$buttons .= "</td></tr>";
print $buttons;

/******************************************************************************
 * print out names of editors in top row
 ******************************************************************************/
print "<tr><td id='collabel'> &nbsp; </td><td id='collabel'> &nbsp; </td>";
foreach ($_SESSION[editors] as $e) {
	print "<td colspan=4 class='edname' id='collabel'>$e</td>";
}
print "</tr>";

/******************************************************************************
 * print out column headers for each editor
 ******************************************************************************/
print "<tr><td id='collabel'> &nbsp; </td><td align=center class='lockedcol' style='background-color: #dbb'>L</td>";
foreach ($_SESSION[editors] as $e) {
	print "<td class='viewcol' id='collabel' style='background-color: #bdb'>v</td>";
	print "<td id='collabel'>a</td>";
	print "<td id='collabel'>e</td>";
	print "<td id='collabel'>d</td>";
}
print "</tr>";

/******************************************************************************
 * print out site/section/page/story name & link, "locked" column
 ******************************************************************************/
$_SESSION[obj]->fetchDown();

doEditorLine($_SESSION[obj]);

$sections = &$_SESSION[obj]->sections;
foreach ($sections as $i=>$o) {
	$sections[$i]->buildPermissionsArray();
	doEditorLine($sections[$i]);
	$pages = &$sections[$i]->pages;
	foreach ($pages as $pi=>$po) {
		$pages[$pi]->buildPermissionsArray();
		doEditorLine($pages[$pi]);
		$stories = &$pages[$pi]->stories;
		foreach ($stories as $si=>$so) {
			$stories[$si]->buildPermissionsArray();
			doEditorLine($stories[$si]);
		}
	}
}

/******************************************************************************
 * Buttons:
 * print again.
 ******************************************************************************/
print $buttons;

/******************************************************************************
 * close up form & table
 ******************************************************************************/
print "</table></form>";


/******************************************************************************
 * doEditorLine - prints out the line for each object.
 * | view | add | edit | delete |
 ******************************************************************************/
function doEditorLine(&$o) {
	$class = get_class($o);
	print "<tr>";
	print "<td>".$o->getField("title")."</td>";
	// reference $args = "'scope',site,section,page,story";
	if ($class == 'site') 
		$args = "'$class','".$o->name."',0,0,0";
	if ($class == 'section')
		$args = "'$class','".$o->owning_site."',".$o->id.",0,0";
	if ($class == 'page')
		$args = "'$class','".$o->owning_site."',".$o->owning_section.",".$o->id.",0";
	if ($class == 'story')
		$args = "'$class','".$o->owning_site."',".$o->owning_section.",".$o->owning_page.",".$o->id;
	print "<td align=center class='lockedcol'>".(($class!='site')?"<input type=checkbox".(($o->getField("locked"))?" checked":"")." onChange=\"doFieldChange('',$args,'locked',".(($o->getField("locked"))?"0":"1").");\">":"-")."</td>";
	
	$o->buildPermissionsArray();
	$p = $o->getPermissions();
	
	$_a = array("view"=>3,"add"=>0,"edit"=>1,"delete"=>2);
	foreach ($_SESSION[editors] as $e) {
		$args1 = "'$e',".$args;
		foreach ($_a as $v=>$i) {
			print "<td align=center".(($i==3)?" class='viewcol'":"")."><input type=checkbox".(($p[$e][$i])?" checked":"")." onChange=\"doFieldChange($args1,'perms-$v',".(($p[$e][$i])?"0":"1").");\" ".(($o->getField("l-$e-$v"))?"disabled":"")."></td>";
		}
	}
	print "</tr>";
}