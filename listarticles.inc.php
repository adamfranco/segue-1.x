<? /* $Id$ */

if ($thisSite) $thisSite->fetchDown();
if ($thisSection) $thisSection->fetchDown();
/* $thisPage->fetchDown(); */

$envvars = "site=".$thisSite->name;
if ($thisSection) $envvars .= "&amp;section=".$thisSection->id;
if ($thisPage) $envvars .= "&amp;page=".$thisPage->id;

// make the navbars!
include("output_modules/publication/navbars.inc.php");

if ($thisSection) {
	printc("<div class='title'>Article listing:</div>");
	$newaction = $action;
	foreach ($thisSection->pages as $p=>$o) {
		printc("<div class='articleitem'>");
		printc("<b><a href='$PHP_SELF?$sid&amp;site=$site&amp;section=$section&amp;page=$p&amp;action=$newaction'>".$o->getField("title")."</a></b>");
		list($first,$story) = each($o->stories);
		if ($story) printc("<br />".$story->getField("title"));
		if (($author = $o->getField("url")) && $author != "http://") printc("<br /><div class='leftmargin'>by $author</div>");
		if ($isediting) {
			printc("<div align='right' class='smaller'>");
			if ($o->hasPermission("edit")) printc("<a href='$PHP_SELF?$sid&amp;action=edit_page&amp;site=$site&amp;section=$section&amp;page=$p&amp;edit_page=$p&amp;comingFrom=viewsite%26supplement%3Dlistarticles'>edit</a>\n");
			if ($o->hasPermission("delete")) printc("<a href='$PHP_SELF?$sid&amp;action=delete_page&amp;site=$site&amp;section=$section&amp;page=$p&amp;delete_page=$p&amp;comingFrom=viewsite%26supplement%3Dlistarticles'>delete</a>\n");
			printc("</div>");
		}
			
		printc("</div>");
	}
	
	if ($isediting && $thisSection->hasPermission("add")) {
		printc("<br /><div align='right'><a href='$PHP_SELF?$sid&amp;site=$site&amp;section=$section&amp;action=add_page&amp;comingFrom=viewsite%26supplement%3Dlistarticles'>+ add article</a>");
	}
}
