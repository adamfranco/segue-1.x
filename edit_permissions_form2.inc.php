<? /* $Id$ */

/******************************************************************************
 * table & form definition
 ******************************************************************************/
$totalcolumns = count($_SESSION[editors])*4 + 2;

print "<form action='$PHP_SELF?$sid' medthod=post name=addform>";
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
if ($isOwner) $buttons .= "<input type=submit name='chooseeditors' value='<- Choose Editors'> ";
$buttons .= "</td><td align=left colspan=$c>";
$buttons .= "L = Locked; v = View; a = Add; e = Edit; d = Delete; ".helplink("editors","help");
$buttons .= "</td></tr>";
print $buttons;

/******************************************************************************
 * print out names of editors in top row
 ******************************************************************************/
print "<tr><td id='collabel'>";
if (!$_SESSION[obj]->getField("active"))
	print "<span style='color: #D00; font-size: 90%;'>This site is hidden. Hiding overides all View permissions.</span>";
else
	print " &nbsp; ";
print "</td><td id='collabel'> &nbsp; </td>";
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
if ($sections) {
	foreach ($sections as $i=>$o) {
		$sections[$i]->buildPermissionsArray();
		if ($isOwner || ($sections[$i]->canview() || $sections[$i]->hasPermissionDown("add or edit or delete"))) {
			doEditorLine($sections[$i]);
			$pages = &$sections[$i]->pages;
			if ($pages) {
				foreach ($pages as $pi=>$po) {
					$pages[$pi]->buildPermissionsArray();
					if ($isOwner || ($pages[$pi]->canview() || $pages[$pi]->hasPermissionDown("add or edit or delete"))) {
						doEditorLine($pages[$pi]);
						$stories = &$pages[$pi]->stories;
						if ($stories) {
							foreach ($stories as $si=>$so) {
								$stories[$si]->buildPermissionsArray();
								if ($isOwner || ($stories[$si]->canview() || $stories[$si]->hasPermissionDown("add or edit or delete")))
									doEditorLine($stories[$si]);
							}
						}
					}
				}
			}
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
	global $isEditor,$isOwner;
	$class = get_class($o);
	$bgColor = getBgColor($class,"normal");
	$bgColorL = getBgColor($class,"locked");
	$bgColorV = getBgColor($class,"view");
	$indent = getIndent($class);
	$textSize = getTextSize($class);
	if ($class == "story") {
/* 		if ($o->getField("title") !="") $extra = $o->getField("title"); */
/* 		else $extra = $o->getFirst(25);		 */
		if ($o->getField("title") == "") $extra = $o->getFirst(25);
		else $extra = '';
	} else $extra = "";

	print "<tr>";
	print "<td style='background-color: $bgColor; padding-left: ".$indent."px; font-size: $textSize'>".$o->getField("title").$extra."</td>";
	// reference $args = "'scope',site,section,page,story";
	if ($class == 'site') 
		$args = "'$class','".$o->name."',0,0,0";
	if ($class == 'section')
		$args = "'$class','".$o->owning_site."',".$o->id.",0,0";
	if ($class == 'page')
		$args = "'$class','".$o->owning_site."',".$o->owning_section.",".$o->id.",0";
	if ($class == 'story')
		$args = "'$class','".$o->owning_site."',".$o->owning_section.",".$o->owning_page.",".$o->id;
	print "<td align=center class='lockedcol' style='background-color: $bgColorL' width=18>".(($class!='site')?"<input type=checkbox".(($o->getField("locked"))?" checked":"")." onClick=\"doFieldChange('',$args,'locked',".(($o->getField("locked"))?"0":"1").");\" ".((!$isOwner)?"disabled":"").">":"&nbsp;")."</td>";
	
	$type = $o->getField("type");
	
	$o->buildPermissionsArray();
	$p = $o->getPermissions();
	
	$_a = array("view"=>3,"add"=>0,"edit"=>1,"delete"=>2);
	foreach ($_SESSION[editors] as $e) {
		$args1 = "'$e',".$args;
		foreach ($_a as $v=>$i) {
			$skip = 0;
			if (($e == 'everyone' || $e == 'institute') && $i<3) $skip = 1;
			if ($class=='story' && $v == 'add') $skip = 1;
			if ($type != 'story' && $type != 'page' && $type != 'section' && $class != 'site') $skip=1;
			if ($skip) print "<td width=18 align=center".(($i==3)?" class='viewcol' style='background-color: $bgColorV'":" style='background-color: $bgColor'").">&nbsp;</td>";
			else print "<td width=18 align=center".(($i==3)?" class='viewcol' style='background-color: $bgColorV'":" style='background-color: $bgColor'")."><input type=checkbox".(($p[$e][$i])?" checked":"")." onClick=\"doFieldChange($args1,'perms-$v',".(($p[$e][$i])?"0":"1").");\" ".(($o->getField("l-$e-$v") || !$isOwner)?"disabled":"")."></td>";
		}
	}
	print "</tr>";
}


/******************************************************************************
 * set up colors for rows
 ******************************************************************************/
function getBgColor($class,$special=0) {
	$baseR = 7;
	$baseG = 7;
	$baseB = 7;
	$step = 2;
	$lockedR = 1;  
	$viewG = 1;
	if ($class == "site") $level = 0;
	if ($class == "section") $level = 1;
	if ($class == "page") $level = 2;
	if ($class == "story") $level = 3;
	
	$finalR = $baseR + $step*$level;
	$finalG = $baseG + $step*$level;
	$finalB = $baseB + $step*$level;

	if ($special == "locked") {
		$finalR = $finalR + $lockedR;
		$finalG = $finalG - $lockedR;
		$finalB = $finalB - $lockedR;
	}

	if ($special == "view") {
                $finalR = $finalR - $viewG;
                $finalG = $finalG + $viewG;
                $finalB = $finalB - $viewG;
        }

	$finalR = dechex($finalR);
        $finalG = dechex($finalG);
        $finalB = dechex($finalB);

	$color = "#".$finalR.$finalG.$finalB;
	return $color;
}

/******************************************************************************
 * getIndent - Returns proper indentation of levels
 ******************************************************************************/
function getIndent($class) {
	$multiplier = 20;

        if ($class == "site") $level = 0;
        if ($class == "section") $level = 1;
        if ($class == "page") $level = 2;
        if ($class == "story") $level = 3;

	$indent = $level*$multiplier;
	return $indent;
}

/******************************************************************************
 * getTextSize - Returns the proper size percentage
 ******************************************************************************/
function getTextSize($class) {
	$initialSize = 125;
	$multiplier = 10;

        if ($class == "site") $level = 0;
        if ($class == "section") $level = 1;
        if ($class == "page") $level = 2;
        if ($class == "story") $level = 3;

	$size = $initialSize - $level*$multiplier;
	$size = $size."%";
	return $size;
}