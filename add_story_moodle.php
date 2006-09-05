<?php
/******************************************************************************
* 
* This is the api file for creating Moodle quizzes independant of Moodle framework
* requires: course id, section id, sesskey
*
*
******************************************************************************/
require("moodle/config.php");
require("moodle/course/lib.php");
require_once("config.inc.php");
require_once("dbwrapper.inc.php");

$sesskey =$USER->sesskey;

$course_id = $_REQUEST[course_id];
$section = 1;
$shortname = $_REQUEST[shortname];
$fullname = $_REQUEST[fullname];
$moodle_id = $_REQUEST[module_id];
$moodle_type = $_REQUEST[module_type];
print "Moodle Module API<br><br>";
print "To create an instance of a Moodle module requires: course_id and section<br>";
print "If the course_id passed is not in Moodle course table, then new Moodle course needs to be created<br><br>";
print "course_id = ".$course_id."<br>";
print "section = ".$section."<br>";
print "shortname = ".$shortname."<br>";
print "fullname = ".$fullname."<br>";

print "<br><hr>moodle_id= ".$moodle_id."<br>";
print "<br>moodle_type= ".$moodle_type."<br>";

//$course_id=2;
//$section = 1;

$addquiz = "<a href='$CFG->wwwroot/course/mod.php?id=$course_id&amp;section=$section&amp;sesskey=$USER->sesskey&amp;add=quiz'>";
$addquiz .= "add quiz</a>";

print $addquiz."<br><br>";


if (!isset($straddactivity)) {
	$straddactivity = get_string('addactivity');
	$straddresource = get_string('addresource');
}

get_all_mods($course_id, $mods, $modnames, $modnamesplural, $modnamesused);


//    $output .= ' ';
    $activities = popup_form2("$CFG->wwwroot/course/mod.php?id=$course_id&amp;section=$section&amp;sesskey=$USER->sesskey&amp;add=",
                $modnames, "section$section", "", $straddactivity, 'mods', $straddactivity, true);


//print "modnames: ".print_r($modnames)."<br>";
//print "mods: ".print_r($modnames)."<br>";

print $activities;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Segue-Moodle API</title> 

<script lang="JavaScript"> 

function useFile(fileID,fileName) { 
	o = opener.document.addform; 
	o.moodleid.value=fileID; 
	o.moodletype.value=fileName; 
	o.submit(); 
	window.close(); 
} 
</script>
<form action="add_story_moodle.php" name='addform' method="POST" enctype="multipart/form-data">
<?
print "<input type=button name='use' value='use' onClick=\"useFile('".$moodle_id."','".$moodle_type."')\">\n";
print "</form>";




/**
 * Implements a complete little popup form
 *
 * @uses $CFG
 * @param string $common  The URL up to the point of the variable that changes
 * @param array $options  Alist of value-label pairs for the popup list
 * @param string $formname Name must be unique on the page
 * @param string $selected The option that is already selected
 * @param string $nothing The label for the "no choice" option
 * @param string $help The name of a help page if help is required
 * @param string $helptext The name of the label for the help button
 * @param boolean $return Indicates whether the function should return the text
 *         as a string or echo it directly to the page being rendered
 * @param string $targetwindow The name of the target page to open the linked page in.
 * @return string If $return is true then the entire form is returned as a string.
 * @todo Finish documenting this function<br>
 */
function popup_form2($common, $options, $formname, $selected='', $nothing='choose', $help='', $helptext='', $return=false, $targetwindow='self') {

    global $CFG;
    static $go, $choose;   /// Locally cached, in case there's lots on a page

    if (empty($options)) {
        return '';
    }

    if (!isset($go)) {
        $go = get_string('go');
    }

    if ($nothing == 'choose') {
        if (!isset($choose)) {
            $choose = get_string('choose');
        }
        $nothing = $choose.'...';
    }

    $startoutput = '<form action="'.$CFG->wwwroot.'/course/jumpto.php"'.
                        ' method="get"'.
                        ' target="self"'.
                        ' name="'.$formname.'"'.
                        ' class="popupform">';

    $output = '<select name="jump" onchange="'.$targetwindow.'.location=document.'.$formname.
                       '.jump.options[document.'.$formname.'.jump.selectedIndex].value;">'."\n";

    if ($nothing != '') {
        $output .= "   <option value=\"javascript:void(0)\">$nothing</option>\n";
    }

    $inoptgroup = false;
    foreach ($options as $value => $label) {

        if (substr($label,0,2) == '--') { /// we are starting a new optgroup

            /// Check to see if we already have a valid open optgroup
            /// XHTML demands that there be at least 1 option within an optgroup
            if ($inoptgroup and (count($optgr) > 1) ) {
                $output .= implode('', $optgr);
                $output .= '   </optgroup>';
            }

            unset($optgr);
            $optgr = array();

            $optgr[]  = '   <optgroup label="'. substr($label,2) .'">';   // Plain labels

            $inoptgroup = true; /// everything following will be in an optgroup
            continue;

        } else {
            $optstr = '   <option value="' . $common . $value . '"';

            if ($value == $selected) {
                $optstr .= ' selected="selected"';
            }

            if ($label) {
                $optstr .= '>'. $label .'</option>' . "\n";
            } else {
                $optstr .= '>'. $value .'</option>' . "\n";
            }

            if ($inoptgroup) {
                $optgr[] = $optstr;
            } else {
                $output .= $optstr;
            }
        }

    }

    /// catch the final group if not closed
    if ($inoptgroup and count($optgr) > 1) {
        $output .= implode('', $optgr);
        $output .= '    </optgroup>';
    }

    $output .= '</select>';
    $output .= '<noscript id="noscript'.$formname.'" style="display: inline;">';
    $output .= '<input type="submit" value="'.$go.'" /></noscript>';
    $output .= '<script type="text/javascript">'.
               "\n<!--\n".
               'document.getElementById("noscript'.$formname.'").style.display = "none";'.
               "\n-->\n".'</script>';
    $output .= '</form>' . "\n";

    if ($help) {
        $button = helpbutton($help, $helptext, 'moodle', true, false, '', true);
    } else {
        $button = '';
    }

    if ($return) {
        return $startoutput.$button.$output;
    } else {
        echo $startoutput.$button.$output;
    }
}


?>