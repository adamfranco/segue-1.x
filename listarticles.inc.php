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

if ($thisSection) {
	printc("<div class=title>Article listing:</div>");
	$newaction = ($isediting)?"viewsite":"site";
	foreach ($thisSection->pages as $p=>$o) {
		printc("<div class=articleitem>");
		printc("<b><a href='$PHP_SELF?$sid&site=$site&section=$section&page=$p&action=$newaction'>".$o->getField("title")."</a></b>");
		if (($author = $o->getField("url")) && $author != "http://") printc("<br><div class=leftmargin>by $author</div>");
		if ($isediting) {
			printc("<div align=right class=smaller>");
			if ($o->hasPermission("edit")) printc("<a href='$PHP_SELF?$sid&action=edit_page&site=$site&section=$section&page=$p&edit_page=$p&comingFrom=listarticles'>edit</a>\n");
			if ($o->hasPermission("delete")) printc("<a href='$PHP_SELF?$sid&action=delete_page&site=$site&section=$section&page=$p&delete_page=$p&comingFrom=listarticles'>delete</a>\n");
			printc("</div>");
		}
			
		printc("</div>");
	}
	
	if ($thisSection->hasPermission("add")) {
		printc("<div align=right><a href='$PHP_SELF?$sid&site=$site&section=$section&action=add_page&comingFrom=listarticles'>+ add article</a>");
	}
}