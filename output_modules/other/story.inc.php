<? // output module

include("output_modules/common.inc.php");

$st = stripslashes(urldecode($a['shorttext']));
$st = str_replace("src='####","####",$st);
$st = str_replace("src=####","####",$st);
$st = str_replace("####'","####",$st);
$textarray1 = explode("####", $st);
if (count($textarray1) > 1) {
	for ($i=1; $i<count($textarray1); $i=$i+2) {
		$id = $textarray1[$i];
		$filename = urldecode(db_get_value("media","name","id=$id"));
		$userdir = db_get_value("media","site_id","id=$id");
		$filepath = $uploadurl."/".$userdir."/".$filename;
		$textarray1[$i] = "src='".$filepath."'";
	}		
	$st = implode("",$textarray1);
}

if ($a[title]) printc("<div class=leftmargin><b>".spchars($a[title])."</b></div>");
if ($a[texttype] == 'text') $st = htmlbr($st);

printc("<div>" . stripslashes($st) . "</div>");
if ($a[discuss] || $a[longertext]) {
	printc("<div class=contentinfo align=right>");
	$link = "fullstory.php?$sid&action=fullstory&site=$site&section=$section&page=$page&story=$s";
	$link = "<a href='$link' target='story' onClick='doWindow(\"story\",720,600)'>";
	$l = array();
	if ($a[discuss]) $l[] = $link."discussions</a>";
	if ($a[longertext]) $l[] = $link."full text</a>";
	printc(implode(" | ",$l));
	printc("</div>");
}
