<? /* $Id$ */

include("output_modules/common.inc.php");

$t = makedownloadbar($o);
printc($t);
if ($o->getField("discuss")) {
	include (dirname(__FILE__)."/discussionLink.inc.php");
}