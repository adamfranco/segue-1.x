<? /* $Id$ */

if ($thisSite) $thisSite->fetchDown();
if ($thisSection) $thisSection->fetchDown();
/* $thisPage->fetchDown(); */

$envvars = "site=".$thisSite->name;
if ($thisSection) $envvars .= "&amp;section=".$thisSection->id;
if ($thisPage) $envvars .= "&amp;page=".$thisPage->id;

// make the navbars!
include("output_modules/publication/navbars.inc.php");

if ($thisSite) {
	printc("<div class='title'>Issue listing:</div>");
	$newaction = ($isediting)?"viewsite":"site";
	$i = 0;
	$total = count($thisSite->sections);
	printc("<table border='0' width='100%'>");
	foreach (array_reverse($thisSite->sections,TRUE) as $s=>$o) {
		if ($i != $total - 1) {
			$pdfname = $filename = $fileurl = $extra = '';
			$pdfname = createPdfName($o->getField("title"));
			$filename = "$uploaddir/".$thisSite->name."/$pdfname";
			$fileurl = "$uploadurl/".$thisSite->name."/$pdfname";
			if (file_exists($filename)) {
				$extra .= pdflink($filename,$fileurl,1);
			}
			printc("<b><a href='$PHP_SELF?$sid&amp;site=$site&amp;section=$s&amp;action=$newaction&amp;supplement=listarticles'>".$o->getField("title")."</a></b>");
			if ($isediting) {
				printc("<div align='right' class='smaller'>");
				if ($o->hasPermission("edit")) printc("<a href='$PHP_SELF?$sid&amp;action=edit_section&amp;site=$site&amp;section=$s&amp;edit_section=$s&amp;comingFrom=viewsite%26supplement%3Dlistissues'>edit</a>\n");
				if ($o->hasPermission("delete")) printc("<a href='$PHP_SELF?$sid&amp;action=delete_section&amp;site=$site&amp;section=$s&amp;delete_section=$s&amp;comingFrom=viewsite%26supplement%3Dlistissues'>delete</a>\n");
				printc("</div>");
			}
				
			printc("</td>");
			printc("<td>$extra</td></tr>");
		}
		$i++;
	}
	printc("</table>");
	
	if ($isediting && $thisSite->hasPermission("add")) {
		printc("<br /><div align='right'><a href='$PHP_SELF?$sid&amp;site=$site&amp;action=add_section&amp;comingFrom=viewsite%26supplement%3Dlistissues'>+ add issue</a>");
	}
}
