<? /* $Id$ */

include("output_modules/common.inc.php");

$t = makedownloadbar($o);

$st = convertTagsToInteralLinks($site, $t);
if ($o->getField("texttype") == 'text')
	$st = nl2br($st);	

$wikiResolver =& WikiResolver::instance();
$st = $wikiResolver->parseText($st, $site, $section, $page);

printc($st);


if ($o->getField("discuss")) {
	include (dirname(__FILE__)."/discussionLink.inc.php");
}
