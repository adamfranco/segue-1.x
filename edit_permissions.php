<? /* $Id$ */

/******************************************************************************
 * edit_permissions takes in one variable: $site
 *
 * The first screen is for the adding, delete, and choosing of editors
 * The second screen is for the editing of the permissions for selected editors
 ******************************************************************************/


require("objects/objects.inc.php");

ob_start();
session_start();

// include all necessary files
require("includes.inc.php");

if ($_REQUEST[cancel]) {
	unset($_SESSION[obj],$_SESSION[editors]);
	header("Location: close.php");
}

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

/******************************************************************************
 * create the site object if it doesn't exist.
 ******************************************************************************/
/* unset($_SESSION[obj]); */
/* unset($_SESSION[editors]); */
if (!is_object($_SESSION[obj])) {
	$_SESSION[obj] = new site($_REQUEST[site]);
/* 	$_SESSION[obj] = new site('gabe'); */
	$_SESSION[obj]->fetchDown();
	$_SESSION[obj]->buildPermissionsArray();
}

$_SESSION[obj]->buildPermissionsArray();
if (!isset($_SESSION[editors])) $_SESSION[editors] = array();

/******************************************************************************
 * Editor Actions:
 ******************************************************************************/
if ($_REQUEST[edaction] == 'add') {
	$_SESSION[obj]->addEditor($_REQUEST[edname]);
}

if ($_REQUEST[edaction] == 'del') {
	$_SESSION[obj]->delEditor($_REQUEST[edname]);
}

if (isclass($_SESSION[obj]->name)) {
	print "<script lang='javascript'>";
	print "function addClassEditor() {";
	print "	f = document.addform;";
	print "	f.edaction.value='add';";
	print "	f.edname.value='$sitename';";
	print "	f.submit();";
	print "}";
	print "</script>";
}


/******************************************************************************
 * switch between forms 1 and 2
 ******************************************************************************/
if($_REQUEST[editpermissions]) {
	$_SESSION[editors] = $_REQUEST[editors];
	$step = 2;
}

if ($_REQUEST[chooseeditors]) {
	$step = 1;
}

/******************************************************************************
 * catch any change field functionality
 ******************************************************************************/
$fieldchange = $_REQUEST[fieldchange];
$pscope = $_REQUEST[pscope];
$psite = $_REQUEST[psite];
$psection = $_REQUEST[psection];
$ppage = $_REQUEST[ppage];
$pstory = $_REQUEST[pstory];
$pfield = $_REQUEST[pfield];
$pwhat = $_REQUEST[pwhat];
$puser = $_REQUEST[puser];

print "$pscope - $psite - $psection - $ppage - $pstory - $pfield - $pwhat - $puser";

if ($fieldchange) {	 // we're supposed to change a field
	$_a = array("story"=>0,"page"=>1,"section"=>2,"site"=>3);
	if ($pscope == 'site') $theObj = &$_SESSION[obj];
	if ($pscope == 'section') $theObj = &$_SESSION[obj]->sections[$psection];
	if ($pscope == 'page') $theObj = &$_SESSION[obj]->sections[$psection]->pages[$ppage];
	if ($pscope == 'story') $theObj = &$_SESSION[obj]->sections[$psection]->pages[$ppage]->stories[$pstory];
	if ($pfield == 'locked') {
		$theObj->setField('locked',$pwhat);
	}
	if (ereg("perms-([a-z]){1,}",$pfield)) {
		$regs = split('-',$pfield);
		$perm = $regs[1];
		$theObj->setUserPermissionDown($perm,$puser,$pwhat);
		$theObj->setFieldDown("l-$puser-$perm",$pwhat);
		if ($pwhat ==1) $theObj->setField("l-$puser-$perm",(1-$pwhat));
	}
}

/******************************************************************************
 * common styles/javascripts:
 ******************************************************************************/
?>
<html>
<head>
<title>Edit Permissions - </title>

<style type='text/css'>
a {
	color: #a33;
	text-decoration: none;
}

a:hover {text-decoration: underline;}

table {
	border: 1px solid #555;
}

th, td {
	border: 0px;
	background-color: #ddd;
}

.viewcol {
	background-color: #cec;
	border-left: 2px solid #FFF;
}

.lockedcol {
	background-color: #ecc;
}

#collabel {
	text-align: center;
	background-color: #bbb;
}

.edname {
	border-left: 2px solid #FFF;
}

.td1 { 
	background-color: #ccc; 
}

.td0 { 
	background-color: #ddd; 
}

th { 
	background-color: #bbb; 
	font-variant: small-caps;
}

body { 
	background-color: white; 
}

body, table, td, th, input {
	font-size: 12px;
	font-family: "Verdana", "sans-serif";
}

input {
	border: 1px solid black;
	background-color: white;
	font-size: 10px;
}

</style>

<script lang='JavaScript'>

function doWindow(name,width,height) {
	var win = window.open("",name,"toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width="+width+",height="+height);
	win.focus();
}

function sendWindow(name,width,height,url) {
	var win = window.open("",name,"toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width="+width+",height="+height);
	win.document.location=url;
	win.focus();
}

function checkAll() {
	field = document.forms[0].elements['editors[]'];
	for (i = 0; i < field.length; i++)
		field[i].checked = true ;
}

function uncheckAll() {
	field = document.forms[0].elements['editors[]'];
	for (i = 0; i < field.length; i++)
		field[i].checked = false ;
}

function delEditor(n) {
	if (confirm('ALERT: Removing an editor will completely remove all their permissions from every part of your site! If you wish to revoke privileges for this part only, uncheck all the associated boxes instead of removing them. Continue if you are sure you want to remove all privileges for this user.')) {		
		f = document.addform;
		f.edaction.value = 'del';
		f.edname.value = n;
		document.forms["addform"].submit();
	}
}

function doFieldChange(user,scope,site,section,page,story,field,what) {
	f = document.addform;
	f.fieldchange.value = 1;
	f.puser.value = user;
	f.pscope.value = scope;
	f.psite.value = site;
	f.psection.value = section;
	f.ppage.value = page;
	f.pstory.value = story;
	f.pfield.value = field;
	f.pwhat.value = what;
	f.submit();
}

</script>

</head>

<? 

/******************************************************************************
 * include the appropriate page:
 ******************************************************************************/

if ($step == 2) require("edit_permissions_form2.inc.php");
else require("edit_permissions_form1.inc.php");

?>

<div align=right><input type=button value='Cancel' onClick='document.location="edit_permissions.php?cancel=1"'></div>

<?
// debug output -- handy :)
print "<pre>";
print "session:\n";
print_r($_SESSION);
print "\n\n";
print "request:\n";
print_r($_REQUEST);
if (is_object($thisSection)) {
	print "\n\n";
	print "thisSection:\n";
	print_r($thisSection);
} else if (is_object($thisSite)) {
	print "\n\n";
	print "thisSite:\n";
	print_r($thisSite);
}
print "</pre>";