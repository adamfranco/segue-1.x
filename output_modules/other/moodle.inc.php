<? /* $Id$ */

include("output_modules/common.inc.php");
//require_once("moodle/config.php");  // both segue and moodle have error functions...
//require_once("moodle/locallib.php");
$vars = "";
$st = $o->getField("shorttext");

/******************************************************************************
 * replace general media library urls (i.e. $mediapath/$sitename/filename)
 * replace general with specific
 ******************************************************************************/
$st = convertTagsToInteralLinks($site, $st);
if ($o->getField("texttype") == 'text')
	$st = nl2br($st);

$module_id = $o->getField("FK_module");
$mod = $o->getField("longertext");
$site_id = db_get_value("slot", "FK_site", "slot_name = '$site'");
$user_id = $_SESSION[aid];

$vars .= "&site_id=$site_id&user_id=$user_id&module_id=$module_id&mod=$mod";

$title = $o->getField("title");
$module_url = "moodle/mod/$mod/attempt.php?id=$module_id";
$module_link = urlencode("<a href=".$module_url." target='new_window'>".$title."</a>");

/******************************************************************************
 * Get Module Quiz info
 ******************************************************************************/

//if ($module_id) {
//	if (! $cm = get_record("course_modules", "id", $module_id)) {
//		error2("There is no coursemodule with id $module_id");
//	}
//
//	if (! $course = get_record("course", "id", $cm->course)) {
//		error2("Course is misconfigured");
//	}
//
//	if (! $quiz = get_record("quiz", "id", $cm->instance)) {
//		error2("The quiz with id $cm->instance corresponding to this coursemodule $id is missing");
//	}
//
//}

//printpre($quiz);

/******************************************************************************
 * get quiz info from module
 ******************************************************************************/

//$timenow = time();
//$available = ($quiz->timeopen < $timenow and $timenow < $quiz->timeclose) || $isteacher;
//
//if ($quiz->attempts > 1) {
//	$attempt_info = "<div align='right'>attempts: $quiz->attempts<br>";
//	$attempt_info .= "<div align='right'>grade method: ".$QUIZ_GRADE_METHOD[$quiz->grademethod]."</div><br>";
//}
//
//if ($available) {
//	if ($quiz->timelimit) {
//		$timelimit_info = get_string("quiztimelimit","quiz", format_time($quiz->timelimit * 60))."<br>";
//	}
//	$available_info = get_string("quizavailable", "quiz", userdate($quiz->timeclose))."<br>";
//} else if ($timenow < $quiz->timeopen) {
//	$available_info = get_string("quiznotavailable", "quiz", userdate($quiz->timeopen))."<br>";
//} else {
//	$available_info = get_string("quizclosed", "quiz", userdate($quiz->timeclose))."<br>";
//}

/******************************************************************************
 * print out module content info and links
 ******************************************************************************/


printc("<div class=leftmargin><b><a name=".$o->id."></a><a href='moodle_auth.inc.php?$vars' target='new_window'>$title</a></b></div>");
printc("<table cellspacing='0' cellpadding='0' width=100%><tr><td>");
printc(stripslashes($st));
printc("<div class=contentinfo>");
printc($available_info);
printc($timelimit_info);
printc($attempt_info);
printc("</div>");
printc("<br><div align=right><b><a name=".$o->id."></a><a href='moodle_auth.inc.php?$vars' target='new_window'>View</a></b></div>");
if ($o->getField("discuss")) {
			
	include (dirname(__FILE__)."/discussionLink.inc.php");
	
}
printc("</td></tr></table><br />");
