<? /* $Id$ */

include("output_modules/common.inc.php");

$filename = urldecode(db_get_value("media","media_tag","media_id=".$o->getField("longertext")));
$dir = db_get_value("media INNER JOIN slot ON media.FK_site = slot.FK_site","slot_name","media_id=".$o->getField("longertext"));
$imagepath = "$uploadurl/$dir/$filename";
printc("<table align=center><tr><td align=center><img src='$imagepath' border=0></td></td>");
if ($o->getField("title")) printc("<tr><td align=center><b>".spchars($o->getField("title"))."</b></td></tr>");
if ($o->getField("shorttext")) printc("<tr><td align=left>".stripslashes($o->getField("shorttext"))."</td></tr>");
printc("</table>");
if ($o->getField("discuss")) {
	include (dirname(__FILE__)."/discussionLink.inc.php");
}