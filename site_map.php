<? /* $Id$ */


$content = '';

ob_start();
session_start();

// include all necessary files
include("includes.inc.php");
include("objects/objects.inc.php");
db_connect($dbhost, $dbuser, $dbpass, $dbdb);

$siteObj =& new site($site);
$siteObj->fetchDown();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
	
print "<table cellspacing='1' width='100%'>";	
print "<tr>";
	print "<th>Site Map - ".$siteObj->getField("title")."</th>";
print "</tr>";

/******************************************************************************
 * print out site/section/page/story name & link
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
	print "<tr><td class='td$color' colspan='4'>No sections in this site.</td></tr>";
}

print "</table><br />";

?>

<div align='right'><input type='button' value='Close Window' onclick='window.close()' /></div>

<? 
/******************************************************************************
 * doEditorLine - prints out the line for each object.
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

	print "\n\n<tr>";
	print "\n<td style='background-color: $bgColor; padding-left: ".$indent."px; font-size: $textSize'>";
	$noLinkTypes = array("url","heading","divider");
	if (!in_array($o->getField("type"),$noLinkTypes)) {
		print "\n\t<a href='#' onClick$nl='opener.window.location=\"index.php?$sid&action=viewsite&site=".$o->owning_site;
		if ($level == 1) print "&section=".$o->id; 
		if ($level > 1) print "&section=".$o->owning_section;
		if ($level == 2) print "&page=".$o->id; 
		if ($level > 2) print "&page=".$o->owning_page;
		print "\"'>";
	}
	if ($o->getField("type") == "url") 
		print "\n\t<a href='#' onclick='opener.window.location=\"".$o->getField("url")."\"'>";
		
	if ($o->getField("type") == "divider") 
		print " &nbsp; ";
		
	if ($class == "story") {
		if ($o->getField("title") !="") 
			print $o->getField("title");
		else 
			print $o->getFirst(25);		
	} else
		print "\n\t".$o->getField("title");
	print "\n\t</a>";
	print "\n</td>";
	print "\n</tr>";
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