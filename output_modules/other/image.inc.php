<? // output module

include("output_modules/common.inc.php");

$filename = urldecode(db_get_value("media","name","id=$a[longertext]"));
$dir = db_get_value("media","site_id","id=$a[longertext]");
$imagepath = "$uploadurl/$dir/$filename";
printc("<table align=center><tr><td align=center><img src='$imagepath' border=0></td></td>");
if ($a[title]) printc("<tr><td align=center><b>".spchars($a[title])."</b></td></tr>");
if ($a[shorttext]) printc("<tr><td align=left>".stripslashes(urldecode($a[shorttext]))."</td></tr>");
printc("</table>");