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
	
	require_once("updates/update_1.3.0.inc.php");
	require_once("updates/update_1.1.0.inc.php");
	require_once("updates/update_1.0.3.inc.php");
	
	$updates[] =& new Update130;
	$updates[] =& new Update110;
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

// include all necessary files
include("includes.inc.php");

if ($_SESSION['ltype'] != 'admin') {
	// take them right to the user lookup page
	header("Location: username_lookup.php");
	exit;
}

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

/******************************************************************************
 * Run any requested updates
 ******************************************************************************/
if ($_REQUEST[action] == "update") {
	$updates[$_REQUEST[update]]->run();
}

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

<?
/******************************************************************************
 * Get site id for links to participation section
 ******************************************************************************/
	
	$siteObj =&new site($site);
	$siteid = $siteObj->id;

if ($_SESSION['ltype']=='admin') {
	print "<table width=100%  class='bg'><tr><td class='bg'>
	Logs: <a href='viewsites.php?$sid&site=$site'>sites</a>
	 | <a href='viewusers.php?$sid&site=$site'>users</a>
	</td><td align=right class='bg'>
	<a href='users.php?$sid&site=$site'>add/edit users</a> | 
	<a href='classes.php?$sid&site=$site'>add/edit classes</a> |  
	<a href='add_slot.php?$sid&site=$site'>add/edit slots</a> | 
	 segue updates 
	</td></tr></table>";
}

if ($site) {
	print "<div align=right>";
	print "<a href=add_students.php?$sid&name=$site>Roster</a>";
	print " | <a href='email.php?$sid&siteid=$siteid&site=$site&action=list&scope=site'>Participation</a>";
	print " | Logs";
	print "</div><br>";
}

?>

<?=$content?>

<table cellspacing=1 width='100%' id='maintable'>
<tr><td>

<table width='90%' align=center>
<?
// print out the updates
foreach ($updates as $key => $obj) {
	print "<tr><td colspan=2><hr></td></tr>\n";
	print "<tr>\n<td width='50%'>\n";
	print "<b>".$obj->getName()."</b>\n";
	print "<br>".$obj->getDescription();
	print "\n</td>\n<td width='50%' align=center valign=center>\n";
	
	if ($obj->hasRun())
		print "<b>This update is in place</b>\n";
	else
		print "<a href='".$_SERVER[PHP_SELF]."?&action=update&update=".$key."'><b>Run this update</b></a>\n";
		
	print "</td>\n</tr>\n";
}

?>
</table>

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