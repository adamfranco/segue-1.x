<? /* $Id$ */

if ($o->getField("texttype"))
	print nl2br($o->getField("shorttext"));
else
	print $o->getField("shorttext");