<? /* $Id$ */

include("output_modules/common.inc.php");

$t = makedownloadbar($o);
$t = convertTagsToInteralLinks($site, $t);
printc($t);
if ($o->getField("discuss")) {
	include (dirname(__FILE__)."/discussionLink.inc.php");
}
