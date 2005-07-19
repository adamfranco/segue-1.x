<? /* $Id$ */

$st = $o->getField("shorttext");
$st = convertTagsToInteralLinks($site, $st);

if ($o->getField("texttype"))
	print nl2br($st);
else
	print $st;
