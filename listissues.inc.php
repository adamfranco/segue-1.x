<? /* $Id$ */

if ($thisSite) $thisSite->fetchDown();
if ($thisSection) $thisSection->fetchDown();
/* $thisPage->fetchDown(); */

if ($thisSite) $site=$thisSite->name;
if ($thisSection) $section=$thisSection->id;
if ($thisPage) $page = $thisPage->id;

$envvars = "site=".$thisSite->name;
if ($thisSection) $envvars .= "&section=".$thisSection->id;
if ($thisPage) $envvars .= "&page=".$thisPage->id;

// make the navbars!
include("output_modules/publication/navbars.inc.php");

if ($thisSite) {
	printc("<div class=title>Issue listing:</div>");
	$newaction = ($isediting)?"viewsite":"site";
	$i = 0;
	$total = count($thisSite->sections);
	foreach (array_reverse($thisSite->sections,TRUE) as $s=>$o) {
		if ($i != $total - 1) {
			printc("<div class='articleitem bottommargin5'>");
			printc("<b><a href='$PHP_SELF?$sid&site=$site&section=$s&action=listarticles'>".$o->getField("title")."</a></b>");
			if ($isediting) {
				printc("<div align=right class=smaller>");
				if ($o->hasPermission("edit")) printc("<a href='$PHP_SELF?$sid&action=edit_section&site=$site&section=$s&edit_section=$s&comingFrom=listissues'>edit</a>\n");
				if ($o->hasPermission("delete")) printc("<a href='$PHP_SELF?$sid&action=delete_section&site=$site&section=$s&delete_section=$s&comingFrom=listissues'>delete</a>\n");
				printc("</div>");
			}
				
			printc("</div>");
		}
		$i++;
	}
	
	if ($thisSite->hasPermission("add")) {
		printc("<div align=right><a href='$PHP_SELF?$sid&site=$site&action=add_section&comingFrom=listarticles'>+ add article</a>");
	}
}