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

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

/******************************************************************************
 * create the site object if it doesn't exist.
 ******************************************************************************/



/******************************************************************************
 * add editor 
 ******************************************************************************/



/******************************************************************************
 * delete editor
 ******************************************************************************/



/******************************************************************************
 * switch to form 2 with selected editors
 ******************************************************************************/



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
</head>

<? 

/******************************************************************************
 * include the appropriate page:
 ******************************************************************************/

if ($step == 2) require("edit_permissions_form2.inc.php");
else require("edit_permissions_form1.inc.php");

?>

<div align=right><input type=button value='Close Window' onClick='window.close()'></div>