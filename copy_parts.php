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

if ($domove) {
	// move or copy the part
	if ($type == "section") {
		$newid = copyPart($action,$type,$section,$site);
	}
	else if ($type == "page") {
		$newid = copyPart($action,$type,$page,$section);
	}
	else if ($type == "story") {
		$newid = copyPart($action,$type,$story,$page);
	} else 
		print "Major Error!!!!!!!!!!!!!!!!!!!!!!  AHHHHHhhhhhhhh!!!!!!!!!!!!!!!!!!!!";
		
}

if (!isset($action)) $action = "MOVE";

if (!isset($origionalsite)) $origionalsite = $site;
if (!isset($origionalsection)) $origionalsection = $section;
if (!isset($origionalpage)) $origionalpage = $page;

// Get the sites that a person is the editor of.
if (!isset($sites)) {
	$query = "select * from sites where (editors LIKE '%$auser') || (addedby='$auser')";
	$r=db_query($query);
	$sites = array();
	while ($a=db_fetch_assoc($r)) {
//		print "possible site = $a[name]<br>";			//Debug
		if (is_editor($auser,$a[name])) {
//			print "$auser is an editor for $a[name]<br>";	// Debug
			if ($type == "section" && permission($auser,SITE,ADD,$a[name])) {
				array_push($sites,$a[name]);
			} else if ($type != "section") {
				array_push($sites,$a[name]);
			}
//			$perm = permission($auser,SITE,ADD,$a[name]);
//			print "permission = $perm<br>";
//			print_r($sites); print "<br>";
		}
	}
} else {
	$sites = decode_array($sites);
}

if ($type != "section") {
	$sections = decode_array(db_get_value("sites","sections","name='$site'"));
	$newsections = array();
	foreach ($sections as $s) {
		if ($type == "page" && permission($auser,SECTION,ADD,$s))
			array_push($newsections,$s);
		else if ($type == "story") 
			array_push($newsections,$s);
	}
	$sections = $newsections;
	if ($selecttype == "site")
		$section = $sections[0];
}

if ($type == "story") {
	$pages = decode_array(db_get_value("sections","pages","id=$section"));
	$newpages = array();
	foreach ($pages as $p) {
		if (permission($auser,PAGE,ADD,$p))
			array_push($newpages,$p);
	}
	$pages = $newpages;
	if ($selecttype == "site" || $selecttype == "section")
		$page = $pages[0];
}

$actionlc = strtolower($action);

// variables for debugging
//print "\$type = $type <br>\$site = $site<br>\$section = $section<br>\$page = $page<br>\$story = $story<br>\$selecttype = $selecttype<br>\$auser = $auser<br>";

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
 
function updateForm(type) { 
	f = document.moveform; 
	f.selecttype.value=type;
	f.submit();  
} 
 
function showError(type,action) { 
	if (type == "story")
		var error = "You do not have permission to "+action+" this content block to any pages in this section.";
	else
		var error = "You do not have permission to "+action+" this page to any sections in this site.";
	alert(error);
} 

function finishUp() {
	opener.history.go(0);
}

function followLink(url) {
	opener.location=url;
	window.close();
}
 
</script> 

</head>

<?
if ($domove)
	print "<body onLoad=\"finishUp()\">";
else
	print "<body>";
 
print "<form action='$PHP_SELF?$sid' name='moveform' method='post'>";
print "<input type=hidden name='type' value='$type'>";
print "<input type=hidden name='origionalsite' value='$origionalsite'>";
print "<input type=hidden name='origionalsection' value='$origionalsection'>";
print "<input type=hidden name='origionalpage' value='$origionalpage'>";
print "<input type=hidden name='selecttype'>";
print "<input type=hidden name='sites' value='".encode_array($sites)."'>";
if ($type == "story")
	print "<input type=hidden name='story' value='$story'>";
if ($type == "page")
	print "<input type=hidden name='page' value='$page'>";
if ($type == "section")
	print "<input type=hidden name='section' value='$section'>";

print "<table cellspacing=1 width='100%'>";

if (!$domove) {
	print "<tr> ";
		print "<td colspan=2>";
			print "<input type=radio value='MOVE' name='action'".(($action=="MOVE")?" checked":"")." onClick=\"updateForm('move')\"> Move &nbsp; &nbsp; ";
			print "<input type=radio value='COPY' name='action'".(($action=="COPY")?" checked":"")." onClick=\"updateForm('move')\"> Copy";
		print "</td>";
	print "</tr> ";
}
	print "<tr>";
		print "<th style='text-align: left' colspan=2>";
		if (!$domove) {
			print ucwords($actionlc)." ".ucwords($type);
			print "to:";
		} else {
			print ucwords($actionlc)." ".ucwords($type)." Successfull";
		}
		print "</th>";
	print "</tr>";

	print "<tr>";
		print "<td style='text-align: left'>Site: </td>";
		print "<td>";
		if (!$domove) {
			print "<select name='site' onClick=\"updateForm('site')\">";
			foreach ($sites as $s) {
				$name = db_get_value("sites","title","name='$s'");
				print "<option value='$s'".(($s==$site)?" selected":"").">$name\n";
			}
			print "</select>";
		} else {
			$oname = db_get_value("sites","title","name='$origionalsite'");
			$name = db_get_value("sites","title","name='$site'");
			print "$oname => $name";
		}	
		print "</td>";
	print "</tr>";
	
if ($type != "section") {
	print "<tr>";
		print "<td style='text-align: left'>Section: </td>";
		print "<td>";
		if (!$domove) {
			if (count($sections)) {
				print "<select name='section' onClick=\"updateForm('section')\">";
				foreach ($sections as $s) {
					$name = db_get_value("sections","title","id=$s");
					print "<option value='$s'".(($s==$section)?" selected":"").">$name\n";
				}
				print "</select>";
			} else {
				print "No permission to $actionlc here.";
				$cantmovehere=1;
			}
		} else {
			$oname = db_get_value("sections","title","id=$origionalsection");
			$name = db_get_value("sections","title","id=$section");
			print "$oname => $name";
		}
		print "</td>";
	print "</tr>";
}
if ($type == "story") {
	print "<tr>";
		print "<td style='text-align: left'>Page: </td>";
		print "<td>";
		if (!$domove) {
			if (count($pages)) {
				print "<select name='page' onClick=\"updateForm('page')\">";
				foreach ($pages as $p) {
					$name = db_get_value("pages","title","id=$p");
					print "<option value='$p'".(($p==$page)?" selected":"").">$name\n";
				}
				print "</select>";
			} else {
				print "No permission to $actionlc here.";
				$cantmovehere=1;
			}
		} else {
			$oname = db_get_value("pages","title","id=$origionalpage");
			$name = db_get_value("pages","title","id=$page");
			print "$oname => $name";
		}
		print "</td>";
	print "</tr>";
}

	print "<tr>";
		print "<td colspan=2>";
		if (!$domove) {
			if (!$cantmovehere)
				print "<input type=submit name='domove' value='$action'>";
			else 
				print "<input type=button value='$action' style='background-color: #F00;' onClick=\"showError('$type','$actionlc')\">";				
		} else {
			print "<input type=button value='Go To ".(($action=="MOVE")?"Moved":"Copied")." $type' onClick=\"followLink('";
			if ($type == "story")
				print "index.php?$sid&action=viewsite&site=$site&section=$section&page=$page";
			if ($type == "page")
				print "index.php?$sid&action=viewsite&site=$site&section=$section&page=$newid";
			if ($type == "section")
				print "index.php?$sid&action=viewsite&site=$site&section=$newid";
			print "')\">";
		}
		print "</td>";
	print "</tr>";
?>
</table>
</form>
 
<input type=button value='Cancel' onClick='window.close()' align=right><BR> 

</body>
</html>