<? /* $Id$ */


$content = '';

ob_start();
session_start();

// include all necessary files
include("includes.inc.php");
include("objects/objects.inc.php");
db_connect($dbhost, $dbuser, $dbpass, $dbdb);

/* $sa = db_get_line("sites","name='$site'"); */
$siteObj = new site($site);
$siteObj->fetchDown();
?>
<html>
<head>
<title>Site Map - <? echo $siteObj->getField("title") ?></title>

<style type='text/css'>
a {
	color: #a33;
	text-decoration: none;
}

a:hover {text-decoration: underline;}

table {
	border: 1px solid #555;
}

th, td {
	border: 0px;
	background-color: #ddd;
}

.td1 { 
	background-color: #ccc; 
}

.td0 { 
	background-color: #ddd; 
}

th { 
	background-color: #bbb; 
	font-variant: small-caps;
}

body { 
	background-color: white; 
}

body, table, td, th, input {
	font-size: 12px;
	font-family: "Verdana", "sans-serif";
}

input {
	border: 1px solid black;
	background-color: white;
	font-size: 10px;
}

</style>

<? print $content; ?>
<? 
$sections = decode_array($sa['sections']);
	
print "<table cellspacing=1 width='100%'>";	
print "<tr>";
	print "<th>Site Map - ".$siteObj->getField("title")."</th>";
print "</tr>";

/* if (count($sections)) { */
/* 	foreach ($sections as $sec) { */
/* 		print "<tr>"; */
/* 		$seca = db_get_line("sections","id=$sec"); */
/* 		$secp = decode_array($seca[permissions]); */
/* 		print "<td class=td$color style='padding-left: 10px'>"; */
/* 		if ($seca[type]=='section') print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$sec\"'>"; */
/* 		print "$seca[title]"; */
/* 		if ($seca[type]=='section') print "</a>"; */
/* //		print "<br><pre>";print_r($secp);print "</pre>"; */
/* 		print "</td>"; */
/* 		print "</tr>"; */
/* 		$color = 1-$color; */
/* 		$pages = decode_array($seca['pages']); */
/* 		foreach ($pages as $p) { */
/* 			$pa = db_get_line("pages","id=$p"); */
/* 			$pp = decode_array($pa[permissions]); */
/* 			if ($pa[type]=='divider' || $pa[type]=='heading') next; */
/* 			print "<tr>"; */
/* 			print "<td class=td$color style='padding-left: 20px'>"; */
/* 			print "-&gt; "; */
/* 			if ($pa[type]=='page') print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$s&page=$p\"'>"; */
/* 			print "$pa[title]"; */
/* 			if ($pa[type]=='page') print "</a>"; */
/* 			print "</td>"; */
/* 			print "</tr>"; */
/* 			$color = 1-$color; */
/* 	 */
/* 			$stories = decode_array($pa['stories']); */
/* 			$j=1; */
/* 			foreach ($stories as $s) { */
/* 				print "<tr>"; */
/* 				$sa = db_get_line("stories","id=$s"); */
/* 				$sp = decode_array($sa[permissions]); */
/* 				print "<td class=td$color style='padding-left: 40px'>"; */
/* 				 print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=$site&section=$sec&page=$p\"'>"; */
/* 				print "$j. &nbsp; $sa[title]"; */
/* 				 print "</a>"; */
/* //				print "<br><pre>";print_r($sp);print "</pre>"; */
/* 				print "</td>";					 */
/* 				print "</tr>"; */
/* 				$color = 1-$color; */
/* 				$j++; */
/* 			} */
/* 		} */
/* 	} */
/******************************************************************************
 * print out site/section/page/story name & link, "locked" column
 ******************************************************************************/
$siteObj->fetchDown();

doEditorLine($siteObj);

$sections = &$siteObj->sections;
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
} else {
	print "<tr><td class=td$color colspan=4>No sections in this site.</td></tr>";
}

print "</table><BR>";

?>

<div align=right><input type=button value='Close Window' onClick='window.close()'></div>

<? 
/******************************************************************************
 * doEditorLine - prints out the line for each object.
 * | view | add | edit | delete |
 ******************************************************************************/
function doEditorLine(&$o) {
	global $isEditor,$isOwner;
	$class = get_class($o);
	if ($class == "site") $level = 0;
	if ($class == "section") $level = 1;
	if ($class == "page") $level = 2;
	if ($class == "story") $level = 3;
	
	$bgColor = getBgColor($class,"normal");
	$bgColorL = getBgColor($class,"locked");
	$bgColorV = getBgColor($class,"view");
	$indent = getIndent($class);
	$textSize = getTextSize($class);
	if ($class == "story") {
		if ($o->getField("title") !="") $extra = $o->getField("title");
		else $extra = $o->getFirst(25);		
	} else $extra = "";

	print "<tr>";
	print "<td style='background-color: $bgColor; padding-left: ".$indent."px; font-size: $textSize'>";
	$noLinkTypes = array("url","heading","divider");
	if (!in_array($o->getField("type"),$noLinkTypes)) {
		print "<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=".$o->owning_site;
		if ($level == 1) print "&section=".$o->id; 
		if ($level > 1) print "&section=".$o->owning_section;
		if ($level == 2) print "&page=".$o->id; 
		if ($level > 2) print "&page=".$o->owning_page;
		print "\"'>";
	}
	if ($o->getField("type") == "url") print "<a href='#' onClick='opener.window.location=\"".$o->getField("url")."\"'>";
	if ($o->getField("type") == "divider") print " &nbsp; ";
	print $o->getField("title").$extra;
	print "</a>";
	print "</td>";
	// reference $args = "'scope',site,section,page,story";
/* 	if ($class == 'site')  */
/* 		$args = "'$class','".$o->name."',0,0,0"; */
/* 	if ($class == 'section') */
/* 		$args = "'$class','".$o->owning_site."',".$o->id.",0,0"; */
/* 	if ($class == 'page') */
/* 		$args = "'$class','".$o->owning_site."',".$o->owning_section.",".$o->id.",0"; */
/* 	if ($class == 'story') */
/* 		$args = "'$class','".$o->owning_site."',".$o->owning_section.",".$o->owning_page.",".$o->id; */
/* 	print "<td align=center class='lockedcol' style='background-color: $bgColorL' width=18>".(($class!='site')?"<input type=checkbox".(($o->getField("locked"))?" checked":"")." onClick=\"doFieldChange('',$args,'locked',".(($o->getField("locked"))?"0":"1").");\" ".((!$isOwner)?"disabled":"").">":"&nbsp;")."</td>"; */
/* 	 */
/* 	$type = $o->getField("type"); */
/* 	 */
/* 	$o->buildPermissionsArray(); */
/* 	$p = $o->getPermissions(); */
/* 	 */
/* 	$_a = array("view"=>3,"add"=>0,"edit"=>1,"delete"=>2); */
/* 	foreach ($_SESSION[editors] as $e) { */
/* 		$args1 = "'$e',".$args; */
/* 		foreach ($_a as $v=>$i) { */
/* 			$skip = 0; */
/* 			if (($e == 'everyone' || $e == 'institute') && $i<3) $skip = 1; */
/* 			if ($class=='story' && $v == 'add') $skip = 1; */
/* 			if ($type != 'story' && $type != 'page' && $type != 'section' && $class != 'site') $skip=1; */
/* 			if ($skip) print "<td width=18 align=center".(($i==3)?" class='viewcol' style='background-color: $bgColorV'":" style='background-color: $bgColor'").">&nbsp;</td>"; */
/* 			else print "<td width=18 align=center".(($i==3)?" class='viewcol' style='background-color: $bgColorV'":" style='background-color: $bgColor'")."><input type=checkbox".(($p[$e][$i])?" checked":"")." onClick=\"doFieldChange($args1,'perms-$v',".(($p[$e][$i])?"0":"1").");\" ".(($o->getField("l-$e-$v") || !$isOwner)?"disabled":"")."></td>"; */
/* 		} */
/* 	} */
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