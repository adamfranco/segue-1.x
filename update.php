<? /* $Id$ */
include("objects/objects.inc.php");

/******************************************************************************
 * Updates and statuses
 * 
 * All updates should have functions:
 *		getName() 			@return string Name of the update.
 *		getDescription()	@return string Description of the update.
 *		test()				@return boolean True if in place
 *		run()				runs the update
 ******************************************************************************/
	$updates = array();
	
	include("updates/update_1.0.3.inc.php");
	$updates[] =& new Update103;
	
/******************************************************************************
 * End of update list
 ******************************************************************************/
	

$content = '';
$message = '';

ob_start();
session_start();

//output a meta tag
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

/* // debug output -- handy :) */
/* print "<pre>"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* print "\n\n"; */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n"; */
/* print "</pre>"; */

// include all necessary files
include("includes.inc.php");

if ($_SESSION['ltype'] != 'admin') {
	// take them right to the user lookup page
	header("Location: username_lookup.php");
	exit;
}

db_connect($dbhost, $dbuser, $dbpass, $dbdb);



/* 	$query = " */
/* 		SELECT */
/* 			class_id, */
/* 			class_external_id, */
/* 			class_name, */
/* 			class_department, */
/* 			class_number, */
/* 			class_section, */
/* 			class_semester, */
/* 			class_year, */
/* 			classowner.user_id AS classowner_id, */
/* 			classowner.user_uname AS classowner_uname, */
/* 			classowner.user_fname AS classowner_fname, */
/* 			classgroup_id, */
/* 			classgroup_name, */
/* 			ugroup_id */
/* 		FROM */
/* 			class */
/* 				LEFT JOIN */
/* 			user AS classowner */
/* 				ON */
/* 			class.FK_owner = user_id */
/* 				LEFT JOIN */
/* 			classgroup */
/* 				ON */
/* 			FK_classgroup = classgroup_id */
/* 				LEFT JOIN */
/* 			ugroup */
/* 				ON */
/* 			FK_ugroup = ugroup_id */
/* 		WHERE */
/* 			$where */
/* 		ORDER BY */
/* 			class_year DESC, class_department ASC, class_number ASC, class_section ASC */
/* 		$limit"; */
/* 			 */
/* 	 */
/* 	$r = db_query($query); */

printerr();

?>

<html>
<head>
<title>Segue Updates</title>
<? 
include("themes/common/logs_css.inc.php"); 
include("themes/common/header.inc.php");
?>
</head>
<!-- <body onLoad="document.addform.external_id.focus()"> -->
<body onLoad="document.searchform.name.focus()">

<?=($_SESSION['ltype']=='admin')?
	"<div align=right>
		<a href='username_lookup.php?$sid'>user lookup</a> | 
		<a href='users.php?$sid'>add/edit users</a> | 
		<a href='classes.php?$sid'>add/edit classes</a> | 
		<a href='add_slot.php?$sid'>add/edit slots</a> |
		segue updates
	</div>"
:""?>

<?=$content?>

<table cellspacing=1 width='100%' id='maintable'>
<tr><td>


</td></tr>
</table>

<BR>
<div align=right><input type=button value='Close Window' onClick='window.close()'></div>

<?
/* // debug output -- handy :) */
/* print "<pre>"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* print "\n\n"; */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n"; */
/* print "</pre>"; */
?>