<? /* $Id$ */

$st = $o->getField("shorttext");
$st = convertTagsToInteralLinks($site, $st);

if ($o->getField("texttype") == 'text')
	print nl2br($st);
else
	print $st;
