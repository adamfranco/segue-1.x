<? // filebrowser.php 
 
$content = ''; 
 
ob_start(); 
session_start(); 
 
//output a meta tag 
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'; 
 
// include all necessary files 
include("includes.inc.php"); 
include("sniffer.inc.php");
 
//if ($ltype != 'admin') exit; 
 
db_connect($dbhost, $dbuser, $dbpass, $dbdb); 

if (!isset($action)) $action = "MOVE";

if ($type == "page")
	if (!isset($section)) $section = db_get_value("pages","section_id","id=$page");
if ($type == "story") {
	if (!isset($section)) $section = db_get_value("stories","section_id","id=$story");
	if (!isset($page)) $page = db_get_value("stories","page_id","id=$story");
}

$sites = array("$site");
if ($type != "section")
	$sections = decode_array(db_get_value("sites","sections","name='$site'"));
if ($type == "story")
	$pages = decode_array(db_get_value("sections","pages","id=$section"));

?> 
<html> 
<head> 
<title>Move/Copy</title> 
 
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
	text-align: center; 
} 
 
.td1 {  
	background-color: #ccc;  
} 
 
.td0 {  
	background-color: #ddd;  
} 
 
th {  
	background-color: #ccc;  
	font-variant: small-caps; 
} 

.sizebox1 {
	text-align: left;
	padding-right: 5px;
}

.sizebox2 {
	text-align: right;
}

body {  
	background-color: white;  
} 
 
body, table, td, th, input { 
	font-size: 10px; 
	font-family: "Verdana", "sans-serif"; 
} 
 
/* td { font-size: 10px; } */ 
 
input,select { 
	border: 1px solid black; 
	background-color: white; 
	font-size: 10px; 
} 
 
</style> 
 
<script lang="JavaScript"> 
 
<? if ($editor == 'none') { ?> 
	function useFile(fileID,fileName) { 
		o = opener.document.addform; 
		o.libraryfileid.value=fileID; 
		o.libraryfilename.value=fileName; 
		o.submit(); 
		window.close(); 
	} 

<? } else if ($editor == 'text') { 
	// if using the non-IE text editor 
?> 

<? } else { 
	// if using the activeX editor 
?> 
	function useFile(fileID,fileName) { 
		o = opener.document.addform; 
		o.libraryfileid.value=fileID; 
		o.libraryfilename.value=fileName; 
		o.submit(); 
		window.close();
	} 
<? } ?> 
 
function deleteFile(fileID,fileName) { 
	if (confirm("Are you sure that you want to delete "+fileName+"? If this file is in use anywhere in your site, it will no longer appear.")) { 
		f = document.deleteform; 
		f.filetodelete.value=fileID; 
		f.submit(); 
	} 
} 
 
function changeOrder(order) { 
	f = document.searchform; 
	f.order.value=order; 
	f.submit(); 
} 
 
</script> 
 
<form action='$PHP_SELF?$sid' name='moveform' method='post'>
<table cellspacing=1 width='100%'> 
<tr> 
	<td>
		<input type=radio name='action' <? print (($action=="MOVE")?" checked":""); ?>> Move &nbsp; &nbsp; 
		<input type=radio name='action' <? print (($action=="COPY")?" checked":""); ?>> Copy
	</td>
</tr> 
<tr>
	<th style='text-align: left'>
		Move/Copy <? echo $type; ?> to:
	</th>
</tr>
<tr>
	<td>
		<select name='site'>
		<option value="<? echo $site; ?>"  selected><? echo $site; ?>
		</select>
	</td>
</tr>
<?
if ($type != "section") {
	print "<tr>";
		print "<td>";
			print "<select name='section'>";
			foreach ($sections as $s) {
				$name = db_get_value("sections","title","id=$s");
				print "<option value='$s'".(($s==$section)?" selected":"").">$name\n";
			}
			print "</select>";
		print "</td>";
	print "</tr>";
}
if ($type == "story") {
	print "<tr>";
		print "<td>";
			print "<select name='page'>";
			foreach ($pages as $p) {
				$name = db_get_value("pages","title","id=$p");
				print "<option value='$p'".(($p==$page)?" selected":"").">$name\n";
			}
			print "</select>";
		print "</td>";
	print "</tr>";
}
?>
</table>
</form>

<BR> 
 

 
<div align=right><input type=button value='Close Window' onClick='window.close()'></div> 
