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
//printpre($classname);
// include all necessary files
require("includes.inc.php");

if ($_REQUEST[cancel]) {
	unset($_SESSION[obj],$_SESSION[editors]);
	header("Location: close.php");
	exit;
}

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

if ($_REQUEST[site] && isset($_SESSION[obj])) {
	if ($_REQUEST[site] != $_SESSION[obj]->name)
		unset($_SESSION[obj],$_SESSION[editors]);
}

/******************************************************************************
 * create the site object if it doesn't exist.
 ******************************************************************************/

if (!is_object($_SESSION[obj])) {
	$_SESSION[obj] =& new site($_REQUEST[site]);
	$_SESSION[obj]->fetchSiteAtOnceForeverAndEverAndDontForgetThePermissionsAsWell_Amen(0,1);
	$_SESSION[obj]->spiderDownLockedFlag();
}

$site_owner = $_SESSION[obj]->owner;

$isOwner = $isEditor = 1;

if ($site_owner != $_SESSION[auser]) {
/* 	error("You are not allowed to edit permissions for this site!"); */
	$isOwner = 0;
}

if (!$isOwner && !$_SESSION[obj]->isEditor()) {
	error("You are not an editor for this site. You may not view any permissions.");
	$isEditor=0;
}

/* $_SESSION[obj]->buildPermissionsArray(0,1); */
if (!isset($_SESSION[editors])) $_SESSION[editors] = array();

//print "here";exit;

if ($error) { printerr2(); return; }

//printpre($_SESSION[editors]);
//printpre($_REQUEST);

/******************************************************************************
 * Save changes to the DB
 ******************************************************************************/

if ($_REQUEST[savechanges]) {
	if ($isOwner) {
		/* print "<pre>"; print_r($_SESSION[obj]); print "</pre>"; */
		/* begin bug-fix X-294273alpha. thank you, Adam. */
		// go through each editor and make sure that they are in the local DB.
		print_r($_SESSION[obj]->getEditors());
		foreach ($_SESSION[obj]->getEditors() as $_editor) {
			if(!$_editor) continue;
			print "synchronizing $_editor...<br />";
			synchronizeLocalUserAndClassDB($_editor);
		}
		/* end bug-fix. Again, thank you, Adam. */
		$_SESSION[obj]->updateDB(1);
//		print_r($_SESSION[obj]->editorsToDelete);
		$_SESSION[obj]->deletePendingEditors();
//		echo "<pre>";
//		print_r($_SESSION[obj]);
		unset($_SESSION[obj],$_SESSION[editors]);
		Header("Location: close.php");
		exit;
	}
}

/******************************************************************************
 * Editor Actions:
 ******************************************************************************/
if ($isOwner && $_REQUEST[edaction] == 'add') {
	if (isgroup($_REQUEST[edname])) {
		$classes = group::getClassesFromName($_REQUEST[edname]);
		foreach ($classes as $class) {
			$_SESSION[obj]->addEditor($class);
		}
	} else {
		$_SESSION[obj]->addEditor($_REQUEST[edname]);
	}
}

if ($isOwner && $_REQUEST[edaction] == 'del') {
	$_SESSION[obj]->delEditor($_REQUEST[edname]);
}

/******************************************************************************
 * switch between forms 1 and 2
 ******************************************************************************/
$step = $_REQUEST['step'];
if (!$isOwner && $isEditor) {
	if (!count($_SESSION[editors])) {
		if (in_array($_SESSION[auser],$_SESSION[obj]->getEditors())) 
			$_SESSION[editors][] = $_SESSION[auser];
		
		$groupsAndClasses = array_unique(
								array_merge(
									$_SESSION[obj]->returnEditorOverlap(
										getuserclasses($_SESSION[auser],"all")), 
									getusergroups($_SESSION[auser])));
		foreach ($groupsAndClasses as $groupOrClass) {
			if (in_array($groupOrClass, $_SESSION[obj]->getEditors()))
				$_SESSION[editors][] = $groupOrClass;
		}
		
		// done... now send them to step 2
		$step = 2;
	}
}
 
if($isOwner && $_REQUEST[editpermissions]) {
	if (!count($_REQUEST[editors])) {
		error("You must choose some editors.");
	} else {
		$_SESSION[editors] = $_REQUEST[editors];
		$step = 2;
	}
}

if ($isOwner && $_REQUEST[chooseeditors]) {
	$step = 1;
}

if (!$isOwner)
	$step = 2;

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

//print "$pscope - $psite - $psection - $ppage - $pstory - $pfield - $pwhat - $puser";
if ($isOwner) {
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
			$theObj->setFieldDown("l%$puser%$perm",$pwhat);
//			echo "l-$puser-$perm: ".$pwhat;
			if ($pwhat ==1) $theObj->setField("l%$puser%$perm",(1-$pwhat));
		}
	}
}

/******************************************************************************
 * common styles/javascripts:
 ******************************************************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? print ($isOwner)?"Edit Permissions - ":"Your Permissions - "; print $_SESSION[obj]->getField("title"); ?></title>

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

.collabel {
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

<script type='text/javascript'>
// <![CDATA[

function doWindow(name,width,height) {
	var win = window.open("",name,"toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width="+width+",height="+height);
	win.focus();
}

function sendWindow(name,width,height,url) {
	var win = window.open("",name,"toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width="+width+",height="+height);
	win.document.location=url.replace(/&amp;/, '&');
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

// ]]>
</script>

<?
if ($isOwner && $className = $_SESSION[obj]->name) {
	print "\n<script type='text/javascript'>";
	print "\n// <![CDATA[";
	print "\n\nfunction addClassEditor() {";
	print "\n	f = document.addform;";
	print "\n	f.edaction.value='add';";
	print "\n	f.edname.value='".$className."';";
	print "\n	f.submit();";
	print "\n}";
	print "\n\n// ]]>";
	print "\n</script>";
}
?>

</head>
<body>

<? 

/******************************************************************************
 * output any errors
 ******************************************************************************/

printerr();
print $content;

/******************************************************************************
 * include the appropriate page:
 ******************************************************************************/
if ($step == 2) require("edit_permissions_form2.inc.php");
else require("edit_permissions_form1.inc.php");


// debug output -- handy :)
/* print "<pre>"; */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* if (is_object($thisSection)) { */
/* 	print "\n\n"; */
/* 	print "thisSection:\n"; */
/* 	print_r($thisSection); */
/* } else if (is_object($thisSite)) { */
/* 	print "\n\n"; */
/* 	print "thisSite:\n"; */
/* 	print_r($thisSite); */
/* } */
/* print "</pre>"; */

?>
</body>
</html>