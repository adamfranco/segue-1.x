<? /* $Id$ */

print "<p style='margin: 10px; padding: 5px; font-size: smaller;'>";
$mediaRow = db_get_line(
		"media INNER JOIN slot ON media.FK_site=slot.FK_site",
		"media_id='".addslashes($o->getField("longertext"))."'");
printCitation($mediaRow);
print "</p>";


$st = $o->getField("shorttext");
$st = convertTagsToInteralLinks($site, $st);

if ($o->getField("texttype") == 'text')
	print nl2br($st);
else
	print $st;
	
print "<p>";
printDownloadLink($mediaRow);
print "</p>";
