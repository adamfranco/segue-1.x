<? /* $Id$ */

$content = ''; 

$metadata = array (	"is_published"	=> "Is this from a published source?",
					"title_whole" 	=> "Book/Joural Title",
					"title_part" 	=> "Article/Chapter Title",
					"author"		=> "Author",
					"pagerange"		=> "Pages",
					"publisher"		=> "Publisher",
					"pubyear"		=> "Pub. Year",
					"isbn"			=> "ISBN/ISSN"
				);
 
ob_start(); 
session_start(); 
 
// include all necessary files 
include("includes.inc.php"); 
include("sniffer.inc.php");
include("objects/objects.inc.php");
 
//$siteObj = new site ($site);
//if ($_SESSION['ltype'] != 'admin') exit; 
 
db_connect($dbhost, $dbuser, $dbpass, $dbdb); 

if ($_REQUEST['delete']) { 
	deleteuserfile($_REQUEST['filetodelete']); 
	printerr2(); 
} 

$sitelist = array();
$owner = $_REQUEST[owner];
$editor = $_REQUEST[editor];
$site = $_SESSION[settings][site];
$order = $_REQUEST[order];
$lowerlimit = $_REQUEST[lowerlimit];
$user = $_REQUEST[user];
$name = $_REQUEST[name];
$upload = $_REQUEST[upload];

/* if (isset($_SESSION[settings][sitename])) { */
/* 	$site = $_SESSION[settings][sitename]; */
/* } else if (isset($_SESSION[settings][site])) { */
/* 	$site = $_SESSION[settings][site]; */
/* } */

//printpre($_SESSION[settings]);
//printpre($_REQUEST);

if ($_REQUEST[site]) {
	$site = $_REQUEST[site];
} else if ($_SESSION[settings][sitename]) {
	$site = $_SESSION[settings][sitename];
} else {
	$site = $_SESSION[settings][site];
}

//print $owner;
//printpre($settings[site]);

$w = array(); 
if ($_SESSION['ltype'] == 'admin') { 
	if ($_REQUEST[site]) 
		$w[]="slot_name='".addslashes($site)."'"; 
	else if ($all) $w[]="slot_name like '%'"; 
	else $w[]="slot_name='".addslashes($settings[site])."'"; 
} else $w[]="slot_name='".(($site)?"".addslashes($site)."":"".addslashes($settings[site])."")."'"; 
if (count($w)) $where = " WHERE ".implode(" and ",$w); 

$query = "
	SELECT 
		media_id,
		media_tag,
		media_type,
		media_size,
		slot_name,
		slot_uploadlimit
	FROM 
		media
			INNER JOIN
		slot
			ON media.FK_site = slot.FK_site		
	$where AND media_location = 'local'
"; 
$r = db_query($query); 
//printpre($query);
$totalsize = 0;
while ($a = db_fetch_assoc($r)) {
	$totalsize = $totalsize + $a[media_size];
}

/******************************************************************************
 * if source = discuss then show only files uploaded by currently authed user
 ******************************************************************************/
if ($_REQUEST[source]) {		
	$user_id = $_SESSION[aid];
	$username = $_SESSION[auser];
	if ($username == $owner) {
		$userfilter = "";
	} else if (!$user_id) {		
		$userfilter = "AND user_id = 'anonymous'";
	} else {
		$userfilter = "AND user_id = '".addslashes($user_id)."'";
	}
	//print "useruname=".$username;
} else {
	$userfilter = "";
}

if ($_REQUEST[comingFrom]) {		
	//print $_REQUEST[comingFrom];
}

/******************************************************************************
 * Uploads files: check if media limit is reached..
 ******************************************************************************/
 
if ($_REQUEST['upload']) { 
	
	$query = "
		SELECT 
			media_tag,
			media_id,
			media_size,
			media_type,
			slot_name,
			user_id,
			user_fname,
			user_uname,
			slot_uploadlimit
		FROM 
			media
				INNER JOIN
			slot
				ON media.FK_site = slot.FK_site
				INNER JOIN
			user
				ON media.FK_createdby = user_id
		WHERE
			slot_name='".addslashes((($_REQUEST[site])?$_REQUEST[site]:$settings[site]))."' 
		AND 
			media_location = 'local' 
		$userfilter
	"; 
			
			
//	print "$query <br />"; 
	$r = db_query($query); 
	$filename = ereg_replace("[\x27\x22]",'',trim($_FILES[file][name])); 
	
// 	printpre ($_REQUEST);
// 	exit;
	
	if ($_FILES['file']['tmp_name'] == 'none') { 
		$upload_results = "<li>No file selected";
	} else {
		
	/*********************************************************
	 * Check for file validity before uploading.
	 * There are two modes that this can run in:
	 *
	 *		- Whitelist - Only file extensions specified are allowed. 
	 *						All others are blocked.
	 *
	 *		- Blacklist - Only file extensions specified are blocked. 
	 *						All others are allowed.
	 *
	 *********************************************************/
	 
		/*********************************************************
		 * Blacklist mode
		 *********************************************************/
		if ($cfg['useBlacklistMode']) {
			if (is_array($cfg['additionalBlacklist']))
				$expressionsToCheck = array_merge($cfg['defaultBlacklist'], 
												$cfg['additionalBlacklist']);
			else
				$expressionsToCheck = $cfg['defaultBlacklist'];
			
			$isBlocked = nameMatches($filename, $expressionsToCheck);
		}
		/*********************************************************
		 * Whitelist (default) mode.
		 *********************************************************/
		else {
			if (is_array($cfg['additionalWhitelist']))
				$expressionsToCheck = array_merge($cfg['defaultWhitelist'], 
												$cfg['additionalWhitelist']);
			else
				$expressionsToCheck = $cfg['defaultWhitelist'];
			
			$isBlocked = !(nameMatches($filename, $expressionsToCheck));
		}
		
		if ($isBlocked) { 
			ereg("\.([^\.]+)$", $filename, $filenameParts);
				$extension = $filenameParts[1];
			$upload_results = "
			<li>For security reasons, file-upload types must be approved by the system administrator.
			<br />".strtoupper($extension)." files have not [yet] been approved.
			<br />Please contact the system administrator if you feel that this is in error.
			<br /><b>File, $filename, was NOT uploaded.</b>"; 
		} else {
		
		
			// Check to see if the name is used.
			$nameUsed = 0; 
			while ($a = db_fetch_assoc($r)) { 
				if ($a[media_tag] == $filename) {
					$nameUsed = 1;
					$usedId = $a[media_id];
				}
			}
			
			$q = "
				SELECT 
					slot_uploadlimit 
				FROM 
					slot 
				WHERE 
					slot_name='".(($_REQUEST[site])?"".addslashes($_REQUEST[site])."":"".addslashes($settings[site])."")."'";
			$res = db_query($q);
			$b = db_fetch_assoc($res);
			if ($b[slot_uploadlimit]) {
				$dirlimit = $b[slot_uploadlimit];
			} else {
				$dirlimit = $userdirlimit;
			}
			
			
			if (($_FILES[file][size] + $totalsize) > $dirlimit) {
				$upload_results = "<li>There is not enough room in your directory for $filename."; 

			} else if ($_REQUEST[overwrite] && $nameUsed) {
				
				$newID = copyuserfile($_FILES['file'],(($_REQUEST[site])?"$_REQUEST[site]":"$settings[site]"),1,$usedId,0);
				if ($newID && $newID != 'ERROR') {
					$upload_results = "<li>$filename successfully uploaded to ID $newID. <li>The origional file was overwritten. <li>If the your new version does not appear, please reload your page. If the new version still doesn't appear, clear your browser cache."; 
					
					
				
				} else {
					$upload_results = "<li>An error occurred when trying to upload ".$filename.". <li>Please see above for any additional messages.";
				}
			} else if ($nameUsed) { 
				$upload_results = "<li>Filename, $filename, is already in use. <li>Please change the filename before uploading or check \"overwrite\" to OVERWRITE"; 
			} else { 
				$newID = copyuserfile($_FILES['file'],(($_REQUEST[site])?"$_REQUEST[site]":"$settings[site]"),0,0);
				printpre($newID);
				if ($newID && $newID != 'ERROR') {
					$upload_results = "<li>$filename successfully uploaded to ID $newID"; 
				} else {
					$upload_results = "<li>An error occurred when trying to upload ".$filename.". <li>Please see above for any additional messages.";
				}
			}
		}
	}
}

// If we've uploaded a file, then add any specified metadata
if (($upload && $newID) || $_REQUEST['update_id']) {
	if ($_REQUEST['update_id']) {
		$newID = $_REQUEST['update_id'];
		
		// Clear out existing metadata
		$query = "UPDATE media SET ";
		$query .= implode("=NULL, ", array_keys($metadata));
		$query .= "=NULL WHERE media_id='".addslashes($newID)."'";
//		printpre($query);
		db_query($query);
	}
	
	// Add new metada
	$arguments = array();
	
	if ($_REQUEST['is_published'] == '1') 
		$arguments[] = "is_published=1";
	else
		$arguments[] = "is_published=0";
		
	if ($_REQUEST['title_whole']) 
		$arguments[] = "title_whole='".addslashes($_REQUEST['title_whole'])."'";
	if ($_REQUEST['title_part']) 
		$arguments[] = "title_part='".addslashes($_REQUEST['title_part'])."'";
	if ($_REQUEST['author']) 
		$arguments[] = "author='".addslashes($_REQUEST['author'])."'";
	if ($_REQUEST['pagerange']) 
		$arguments[] = "pagerange='".addslashes($_REQUEST['pagerange'])."'";
	if ($_REQUEST['publisher']) 
		$arguments[] = "publisher='".addslashes($_REQUEST['publisher'])."'";
	if (preg_match('/^[0-9]{4}$/', $_REQUEST['pubyear']))
		$arguments[] = "pubyear='".addslashes($_REQUEST['pubyear'])."'";
	if ($_REQUEST['isbn']) 
		$arguments[] = "isbn='".addslashes($_REQUEST['isbn'])."'";
	
	// Set the values if any are in the request
	if (count($arguments)) {
		$query = "UPDATE media SET ";
		$query .= implode(", ", $arguments);
		$query .= " WHERE media_id='".addslashes($newID)."'";
//		printpre($query);
		db_query($query);
	}
}

/******************************************************************************
 * clears filename search UI??
 ******************************************************************************/
 
if ($_REQUEST[clear]) {
	if ($_SESSION['ltype'] == 'admin') {
		$user = ""; 
		$site = ""; 
		$name = ""; 
	} else {
		$name = "";
		$user = $user;
		$site = $site;
	}
} 

/******************************************************************************
 * get media file
 ******************************************************************************/

$w = array(); 
if ($_SESSION['ltype'] == 'admin') { 
	if ($site) $w[]="slot_name='".addslashes($site)."'"; 
	else if ($all) $w[]="slot_name like '%'"; 
	else $w[]="slot_name='".addslashes($settings[site])."'"; 
} else $w[]="slot_name='".(($site)?"".addslashes($site)."":"".addslashes($settings[site])."")."'"; 
if (count($w)) $where = " where ".implode(" and ",$w); 

$query = "
	SELECT 
		media_tag,
		media_id,
		media_size,
		media_type,
		slot_name,
		user_fname,
		user_uname

	FROM 
		media
			INNER JOIN
		slot
			ON media.FK_site = slot.FK_site
			INNER JOIN
		user
			ON media.FK_createdby = user_id
	$where AND media_location = 'local'
"; 
$r = db_query($query); 

$totalsize = 0;
while ($a = db_fetch_assoc($r)) {
	$totalsize = $totalsize + $a[media_size];
}

if (!isset($order)) $order = "media_updated_tstamp desc"; 

$order = addslashes($order);
$orderby = " ORDER BY $order"; 
 
$w = array(); 

if ($_SESSION['ltype'] == 'admin') { 
	if ($site) {
		$w[]="slot_name='".addslashes($site)."'"; 
	} else if ($all) {
		$w[]="slot_name like '%'"; 
	} else {
		$w[]="slot_name='".addslashes($settings[site])."'"; 
	}
	
} else {
	$w[]="slot_name='".(($site)?"".addslashes($site)."":"".addslashes($settings[site])."")."'"; 
}

if ($user) $w[]="user_uname LIKE '%".addslashes($user)."%'"; 
if ($name) $w[]="media_tag LIKE '%".addslashes($name)."%'"; 
 
if (count($w)) $where = " WHERE ".implode(" AND ",$w); 
 
$query = "	
	SELECT 
		COUNT(media_id) AS media_count
	FROM 
		media
			INNER JOIN
		slot
			ON media.FK_site = slot.FK_site
			INNER JOIN
		user
			ON media.FK_createdby = user_id
	$where AND media_location = 'local'
";

$r=db_query($query); 
$a = db_fetch_assoc($r);
$numrows = $a[media_count];
$numperpage = 20; 
 
 
if (!isset($lowerlimit)) $lowerlimit = 0; 
if ($lowerlimit < 0) $lowerlimit = 0; 
$lowerlimit = addslashes($lowerlimit);
$limit = " LIMIT $lowerlimit,$numperpage"; 
 
$query = "
	SELECT 
		media_tag,
		media_id,
		media_size,
		date_format(media_updated_tstamp, '%m/%d/%Y %k:%i') AS media_updated_tstamp_text,
		media_updated_tstamp,
		media_type,
		slot_name,
		user_fname,
		user_uname,
		slot_uploadlimit,
		is_published,
		title_whole,
		title_part,
		author,
		pagerange,
		publisher,
		pubyear,
		isbn
	FROM 
		media
			INNER JOIN
		slot
			ON media.FK_site = slot.FK_site
			INNER JOIN
		user
			ON media.FK_createdby = user_id
	$where AND media_location = 'local'
	$userfilter
	$orderby
	$limit
"; 
// printpre($query);
 
$r = db_query($query); 
 
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>File Browser</title> 
 
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
	background-color: #FFFFFF; 
	text-align: center; 
} 
 
.td1 {  
	background-color: #F0F0F0;  
	padding-left: 4px;
	padding-right: 4px;
} 
 
.td0 {  
	border-right: 1px solid #F0F0F0; 
	background-color: #FFFFFF; 
	padding-left: 4px;
	padding-right: 4px;
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
 
<script type="text/javascript">
// <![CDATA[ 

<? 
/******************************************************************************
 * Use button action:
 * if source is discuss then values pasted to discussion post form
 * if source is not discuss, then is one of following:
 *  site header/footer image or text content block
 ******************************************************************************/
 
//if ($source == 'discuss') { 

?> 
	function useFileDiscuss(fileID,fileName) { 
		o = opener.document.postform; 
		o.libraryfileid.value=fileID; 
		o.libraryfilename.value=fileName;  
		window.close(); 
	} 
	 
<? 
//} 

?> 
 
<? 
/******************************************************************************
 * if image content block then editor = none and useFile is used
 * if text content block then editor = html and getUrl is used
 ******************************************************************************/
?> 
function useFile(fileID,fileName) { 
	o = opener.document.addform; 
	o.libraryfileid.value=fileID; 
	o.libraryfilename.value=fileName; 
	o.submit(); 
	window.close(); 
} 
 
function getUrl(url,img_url) { 
	o = opener.document.addform; 
	o.media_url.value=url; 
	window.close(); 
}

function FCKSetUrl(url) {
	window.opener.SetUrl(url);
	window.close();
}

<?
// this function may not be needed....
?> 
function useFile2(siteName,fileName,fileID) { 
	opener = window.dialogArguments;
	var _editor_url = opener._editor_url;
	var objname     = location.search.substring(1,location.search.length);
	var config      = opener.document.all[objname].config;
	var editor_obj  = opener.document.all["_" +objname+  "_editor"];
	var editdoc     = editor_obj.contentWindow.document;
	var image 	= '<img src="<?echo $uploadurl ?>/' +siteName+ '/' +fileName+ '" imageID=\"' +fileID+ '\" />\n';
	opener.editor_insertHTML(objname, image);
	window.close();
} 

<?

?> 
 
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

function changePage(lolim) {
	f = document.searchform;
	f.lowerlimit.value=lolim;
	f.submit();
}
 
// ]]>
</script> 
</head>
<body>
 
<!--  
<table width='100%'> 
<tr><td style='text-align: left'> 
	<? //print $content; ?> 
	<? //print $numrows . " | " . $query;  
	?> 
</td></tr> 
</table> --> 

<table cellspacing='1' width='100%'> 
 
	<tr> 
		<td colspan='<? print (($_SESSION['ltype']=='admin')?"10":"9"); ?>'> 
			<table width='100%' > 
				<tr>
				<td style='text-align: left; border: 0px solid #FFF; margin-bottom: 10px;' valign='top'>
					<div class='desc'>
						Select the file or image you would like to upload by clicking the 'Browse...' button below.
						<br/><em>(Titles, author, etc are optional, but are encouraged for files originating from published sources.)</em>
					</div> 
				</td>
				<td rowspan='3' valign='top' style='text-align: right; border: 0px solid #FFF'>
					<?
					$dirtotal = convertfilesize($totalsize);
					if ($all) {
						
						$res = db_query("SELECT COUNT(site_id) AS num_sites FROM site");
						$b = db_fetch_assoc($res);
						$dirlimit_B = $b['num_sites']*$userdirlimit;
					} else {
//						printpre($dirlimit);
						if ($site) {
							$q = "SELECT slot_uploadlimit FROM slot WHERE slot_name='".addslashes($site)."'";
							$res = db_query($q);
							$b = db_fetch_assoc($res);
							if ($b[slot_uploadlimit])
								$dirlimit_B = $b[slot_uploadlimit];
							else
								$dirlimit_B = $userdirlimit;
						}else
							$dirlimit_B = $userdirlimit;
					}
					$dirlimit = convertfilesize($dirlimit_B);
					
					$percentused = round($totalsize/$dirlimit_B,"4")*100;
					$percentfree = 100-$percentused;
					$space = $dirlimit_B - $totalsize;
					$space = convertfilesize($space);
					//print "<div style='text-align: right;'>";
					print helplink("filelibrary");
					print "<br \><br \>";
					print "<table cellspacing='0' cellpadding='1' align='right'>";
					print "<tr><td class='sizebox1'>Total media allowed: </td><td class='sizebox2'> $dirlimit</td></tr>";
					print "<tr><td class='sizebox1'>Total size of your media: </td><td class='sizebox2'> $dirtotal</td></tr>";
					print "<tr><td class='sizebox1'>Space available: </td><td class='sizebox2' style='border-top: 1px solid #000'> $space</td></tr>";
					print "<tr><td colspan='2'><table width='100%'><tr>";
					if ($percentused == 0)
						print "<td style='background-color: #00C; height: 5px;' width='100%'> </td>";
					else if ($percentused == 100)
						print "<td style='background-color: #F00; height: 5px;' width='100%'> </td>";
					else
						print "<td style='background-color: #F00; height: 5px;' width='$percentused%'> </td><td style='background-color: #00C;' width='$percentfree%'> </td>";
					print "</tr></table></td></tr>";
					print "</table><br />";
					?>
				</td>
				</tr>
				
				<tr> 
				<td style='text-align: left; padding-top: 5px; border: 0px solid #FFF' valign='top'> 
					<form action="filebrowser.php" name='addform' method="post" enctype="multipart/form-data"> 
					<input type='hidden' name='comingfrom' value='<? echo $comingfrom ?>' />
					<input type='hidden' name='site' value='<? echo $site ?>' /> 
					<input type='hidden' name='upload' value='1' /> 
					<input type='hidden' name='order' value='<? echo $order ?>' /> 
					<input type='hidden' name='editor' value='<? echo $editor ?>' /> 
					<input type='hidden' name='source' value='<? echo $source ?>' /> 
					<input type='hidden' name='owner' value='<? echo $owner ?>' /> 
					<input type='file' name='file' class='textfield' style='color: #000' />
					<input type='submit' value='Upload' />
					<input type='checkbox' name='overwrite' value='1' style='border: 0px;' /> Overwrite existing version?
					<div style='margin-top: 10px'>
						Is this file from a published source?
						<input type='radio' name='is_published' value='1' style='border: 0px;' />yes 
						<input type='radio' name='is_published' value='0' checked='checked' style='border: 0px;' />no 
					</div>
					<table cellpadding='0', cellspacing='3' style='border: 0px;'>
					<tr>
					<td style='text-align: right;'>Book/Journal Title</td>
					<td style='text-align: left;'><input type='text' class='textfield small' name='title_whole' value='' /></td>
					<td style='text-align: right;'>Article/Chapter Title</td>
					<td style='text-align: left;'><input type='text' name='title_part' value='' /></td>
					</tr>
					<tr>
					<td style='text-align: right;'>Author</td>
					<td style='text-align: left;'><input type='text' name='author' value='' /></td>
					<td style='text-align: right;'>Publisher</td>
					<td style='text-align: left;'><input type='text' name='publisher' value='' /></td>
					</tr>
					<tr>
					<td style='text-align: right;'>Pages</td>
					<td style='text-align: left;'><input type='text' name='pagerange' value='' /></td>
					<td style='text-align: right;'>Pub.Year</td>
					<td style='text-align: left;'>
						<input type='text' name='pubyear' value='' 
							onchange='if (!this.value.match(/^([0-9]{4})?$/)) {alert("Year must be four digits.\n\""+this.value+"\" is not a valid year."); this.value=""; this.focus();}' />
					</td>
					</tr>
					<tr>
					<td style='text-align: right;'>ISBN/ISSN</td><td><input type='text' name='isbn' value='' /></td>
					<td style='text-align: center; font-style: italic' colspan='2'>
						
					</td>
					</tr>
					</table>	
					
					</form> 
				</td> 
				</tr> 
				<tr>
				<td style='text-align: left;  border: 0px solid #FFF; margin-top: 10px;' valign='top'> 
					<? 					
					if ($upload) { 
						print "Upload Results: <div style='margin-left: 25px'>"; 
						print $upload_results; 
						print "</div>"; 
					} else {
						print " &nbsp; ";
					}
					?> 
				</td> 

				</tr>
			</table> 
		</td> 
	</tr> 
 
<? 
if (1) { 
?> 
<tr> 
	<td colspan='<? print (($_SESSION['ltype']=='admin')?"10":"9"); ?>'> 
		<table width='100%'> 
		<tr><td style='text-align: left'> 
		<form action='<?echo "$PHP_SELF?$sid"?>' method='post' name='searchform'> 
		<? 
		if ($_SESSION['ltype'] == 'admin') { 
		?> 
			filename: <input type='text' name='name' size='15' value='<?echo $name?>' /> 
			site: <input type='text' name='site' size='10' value='<?echo $site?>' /> 
			user: <input type='text' name='user' size='10' value='<?echo $user?>' /> 
		<? } else { ?> 
			filename: <input type='text' name='name' size='10' value='<?echo $name?>' /> 
			<input type='hidden' name='site' value='<?echo $site?>' />
		<? } ?> 
		<input type='submit' value='search' /> 
		<input type='submit' name='clear' value='clear' /> 
		<? if ($_SESSION['ltype'] == 'admin') print "Search all sites: <input type='checkbox' name='all' value='all sites'".(($all)?" checked='checked'":"")." style='border: 0px;' />"; ?> 
		<input type='hidden' name='order' value='<? echo $order ?>' /> 
		<input type='hidden' name='editor' value='<? echo $editor ?>' /> 
		<input type='hidden' name='source' value='<? echo $source ?>' /> 
		<input type='hidden' name='comingfrom' value='<? echo $comingfrom ?>' /> 
		<input type='hidden' name='lowerlimit' value='0' />
		</form> 
		</td> 
		<td align='right'> 
		 
		<? 
		$tpages = ceil($numrows/$numperpage); 
		$curr = ceil(($lowerlimit+$numperpage)/$numperpage); 
		$prev = $lowerlimit-$numperpage; 
		if ($prev < 0) $prev = 0; 
		$next = $lowerlimit+$numperpage; 
		if ($next >= $numrows) $next = $numrows-$numperpage; 
		if ($next < 0) $next = 0; 
		print "$curr of $tpages "; 
//		print "$prev $lowerlimit $next "; 
		if ($prev != $lowerlimit) 
			print "<input type='button' value='&lt;&lt;' onclick=\"changePage('$prev')\" />\n"; 
		if ($next != $lowerlimit && $next > $lowerlimit) 
			print "<input type='button' value='&gt;&gt;' onclick=\"changePage('$next')\" />\n"; 
		?> 
		</td> 
		</tr> 
		</table> 
	</td> 
</tr> 
<? } else { ?> 
	<form action='<?echo "$PHP_SELF?$sid"?>' method='post' name='searchform'> 
	<input type='hidden' name='order' value='<? echo $order ?>' /> 
	<input type='hidden' name='editor' value='<? echo $editor ?>' /> 
	<input type='hidden' name='source' value='<? echo $source ?>' /> 
	<input type='hidden' name='comingfrom' value='<? echo $comingfrom ?>' />
	<input type='hidden' name='site' value='<? echo $site ?>' />
	</form>
<? } ?>


<tr> 
	<th> </th> 
	<th> </th> 
<? 
//	print "<th><a href='#' onclick=\"changeOrder('"; 
//	if ($order =='media_id asc') print "media_id desc"; 
//	else print "media_id asc"; 
//	print "')\" style='color: #000'>ID"; 
//	if ($order =='media_id asc') print " &or;"; 
//	if ($order =='media_id desc') print " &and;";	 
//	print "</a></th>"; 
	 
	print "<th><a href='#' onclick=\"changeOrder('"; 
	if ($order =='media_tag asc') print "media_tag desc"; 
	else print "media_tag asc"; 
	print "')\" style='color: #000'>File Name"; 
	if ($order =='media_tag asc') print " &and;"; 
	if ($order =='media_tag desc') print " &or;";	 
	print "</a></th>"; 
	 
	print "<th><a href='#' onclick=\"changeOrder('"; 
	if ($order =='media_type asc') print "media_type desc"; 
	else print "media_type asc"; 
	print "')\" style='color: #000'>Type"; 
	if ($order =='media_type asc')  print " &and;"; 
	if ($order =='media_type desc') print " &or;";	 
	print "</a></th>"; 
	 
	print "<th><a href='#' onclick=\"changeOrder('"; 
	if ($order =='media_size asc') print "media_size desc"; 
	else print "media_size asc"; 
	print "')\" style='color: #000'>Size"; 
	if ($order =='media_size asc') print " &and;"; 
	if ($order =='media_size desc') print " &or;";	 
	print "</a></th>"; 
	 
	if ($_SESSION['ltype'] == 'admin') { 
		print "<th><a href='#' onclick=\"changeOrder('"; 
		if ($order =='slot_name asc') print "slot_name desc"; 
		else print "slot_name asc"; 
		print "')\" style='color: #000'>Site"; 
		if ($order =='slot_name asc') print " &and;"; 
		if ($order =='slot_name desc') print " &or;";	 
		print "</a></th>"; 
	} 
 
	print "<th><a href='#' onclick=\"changeOrder('"; 
	if ($order =='media_updated_tstamp asc') print "media_updated_tstamp desc"; 
	else print "media_updated_tstamp asc"; 
	print "')\" style='color: #000'>Date Modified"; 
	if ($order =='media_updated_tstamp asc') print " &and;"; 
	if ($order =='media_updated_tstamp desc') print " &or;";	 
	print "</a></th>"; 
	 
	print "<th><a href='#' onclick=\"changeOrder('"; 
	if ($order =='user_uname asc') print "user_uname desc"; 
	else print "user_uname asc"; 
	print "')\" style='color: #000'>Added by User:"; 
	if ($order =='user_uname asc') print " &and;"; 
	if ($order =='user_uname desc') print " &or;";	 
	print "</a></th>"; 
?>	 
	<th> </th> 
</tr> 
<? 
$color = 0; 
$today = date(Ymd); 
$yesterday = date(Ymd)-1; 
 
if (db_num_rows($r)) { 
	while ($a=db_fetch_assoc($r)) { 
		$a[media_tag] = urldecode($a[media_tag]); 
		$a[media_size] = convertfilesize($a[media_size]); 
		
		$url = $uploadurl."/".$a[slot_name]."/".rawurlencode($a[media_tag]); 
		if ($a[media_type] == 'image') { 
			$img_path = $uploaddir."/".$a[slot_name]."/".$a[media_tag];
			$img_url = $url;
		} else { 
			$img_path = "images/file.gif";
			$img_url = $img_path;
		}  
		if (file_exists($img_path)) {
			$thumb_size = get_sizes($img_path,'50');
			$img_size = get_size($img_path); 
		} else {
			$img_url = "images/nofile.gif";
			$thumb_size = get_sizes($img_path);
			$img_size = get_size($img_path); 
		}
		
/* 		$img_size = get_size($url);  */
		 
		print "<tr>"; 
		
		/******************************************************************************
		 * Media file USE button
		 * Use button depends on context 
		 * viewsite: no USE button displayed
		 * discussion UI: source = discuss
		 * image content block: editor = none
		 * text content block: editor = html 
		 ******************************************************************************/
 		
		print "<td class='td$color'>\n";
			if ($comingfrom != "viewsite") {
				
				// for discussions, get media filename and id
				if ($source == 'discuss') {
					print "<input type='button' name='use' value='use' onclick=\"useFileDiscuss('".$a[media_id]."','".$a[media_tag]."')\" />\n";	
				
				// for image content blocks				
				} else if ($editor == 'none') {
					print "<input type='button' name='use' value='use' onclick=\"useFile('".$a[media_id]."','".$a[media_tag]."')\" />\n";
				
				// for text editors... (not needed?)
                } else if ($editor == 'text') {
                        print "<input type='button' name='use' value='use' onclick=\"useFile('".$a[media_id]."','".$a[media_tag]."')\" />\n";
                        
                // for HTML editors get media url, mediatype image url,        
                } else if ($editor == 'html') {
                		//printpre($editor);
                        print "<input type='button' name='use' value='use' onclick=\"getUrl('".$url."','".$img_url."')\" />\n";
                        
                // not sure where this function is called
                } else {
                        //print "<input type='button' name='use' value='use' onclick=\"useFile2('".$a[slot_name]."','".$a[media_tag]."','".$a[media_id]."')\" />\n";
               			print "<input type='button' name='use' value='use' onclick=\"FCKSetUrl('".$url."')\" />\n";
               }   
            } else print " &nbsp; ";
//			print "<input type='button' name='use' value='use' onclick=\"useFile()\" />";  
		print "</td>\n"; 
 
		print "<td class='td$color'>"; 
			if ($a[media_type]=='image') { 
				$windowSize[x] = $img_size[x]+15; 
				$windowSize[y] = $img_size[y]+15; 
//				print "<a href='#' onclick=\"window.open('$url','imagewindow',config='width=$img_size[x],height=$img_size[y],resizeable=1,scrollbars=0')\">"; 
				print "<a href=\"JavaScript:window.open('$url','imagewindow',config='width=$windowSize[x],height=$windowSize[y],resizeable=1,scrollbars=0');void('');\">"; 
			} else 
				print "<a href='$url'>"; 
			print "<img src='$img_url' height='$thumb_size[y]' width='$thumb_size[x]' border='0' alt='thumbnail image for file'/>"; 
			print "</a>"; 
		print "</td>\n"; 
 
//		print "<td class='td$color' style='vertical-align: top;'>"; 
//			print "$a[media_id]"; 
//		print "</td>\n"; 
		 
		print "<td class='td$color' style='text-align: left;'>"; 
			print "<strong>$a[media_tag]</strong>"; 
			
			//-----------------------------------------------
			// Metdata
			//-----------------------------------------------
			print "\n<div>";
			printCitation($a);
			print "</div>";
			
			print<<<END
			<div
				style='cursor: pointer; text-align: right; font-size: 9px;'
				onclick="if (this.nextSibling.style.display!='block') {this.nextSibling.style.display='block'; this.innerHTML='cancel';} else {this.nextSibling.style.display='none'; this.innerHTML='edit'}"
			>	
END;
			print "edit</div>";
			print "<div style='display: none'>";
			print "<form action='".$_SERVER['PHP_SELF']."' method='post'>";
			print "\n\t<input type='hidden' name='update_id' value='".$a['media_id']."'/>";
			print <<<END
		<input type='hidden' name='order' value='$order' /> 
		<input type='hidden' name='all' value='$all' /> 
		<input type='hidden' name='editor' value='$editor' />
		<input type='hidden' name='source' value='$source' />
		<input type='hidden' name='site' value='$site' />
		<input type='hidden' name='comingfrom' value='$comingfrom' /> 
		
END;
			print "\n\t\t<dl>";
			foreach ($metadata as $field => $label) {
				print "\n\t\t\t<dt style='font-weight: bold'>".$label."</dt>";
				if ($field == "is_published") {
					print "\n\t\t\t<dd style='margin-left: 10px;'>
					<input type='radio' name='is_published' value='1' ".(($a['is_published'] == '1')?" checked='checked'":"")." style='border: 0px;' />yes 
					<input type='radio' name='is_published' value='0' ".(($a['is_published'] == '0')?" checked='checked'":"")." style='border: 0px;' />no 
				</dd>";
				} else if ($field == 'pubyear') {
					$val = $a[$field];
					print <<<END
					<dd style='margin-left: 10px;'>
						<input type='text' name='pubyear' value='$val' 
							onchange='if (!this.value.match(/^([0-9]{4})?$/)) {alert("Year must be four digits.\\n\\""+this.value+"\\" is not a valid year."); this.value="$val"; this.focus();}' />
					</dd>
END;
				} else {
					print "\n\t\t\t<dd style='margin-left: 10px;'><input type='text' name='".$field."' value=\"".$a[$field]."\" /></dd>";
				}
			}
			print "\n\t\t</dl>";
			print "\n\t\t<input type='submit' value='Update' />";
			print "\n\t\t</form>";
			print "\n\t</div>\n";
//			if ($hasMetadata)
//				ob_end_flush();
//			else
//				ob_end_clean();
			
			
		print "</td>\n"; 
 
		print "<td class='td$color'>"; 
			print "$a[media_type]"; 
		print "</td>\n"; 
 
		print "<td class='td$color'>"; 
			print "$a[media_size]"; 
		print "</td>\n"; 
		 
		if ($_SESSION['ltype'] == 'admin') { 
			print "<td class='td$color'>"; 
			print "$a[slot_name]"; 
		print "</td>\n"; 
		} 
 
		print "<td class='td$color'><span style='white-space: nowrap;'>"; 
			if (strncmp($today, $a[media_updated_tstamp], 8) == 0 || strncmp($yesterday, $a[media_updated_tstamp], 8) == 0) print "<b>"; 
			print $a[media_updated_tstamp_text]; 
			if (strncmp($today, $a[media_updated_tstamp], 8) == 0 || strncmp($yesterday, $a[media_updated_tstamp], 8) == 0) print "</b>"; 
		print "</span></td>\n"; 
		 
		print "<td class='td$color'>"; 
			print "$a[user_fname] ($a[user_uname])"; 
		print "</td>\n"; 
		 
		print "<td class='td$color'>"; 
			print "<input type='button' value='delete' onclick=\"deleteFile('".$a[media_id]."','".$a[media_tag]."')\" />";  
//			print "<input type='button' name='delete' value='delete' />";  
		print "</td>\n"; 
 
		print "</tr>"; 
		$color = 1-$color; 
	} 
} else { 
	print "<tr><td colspan=".(($_SESSION['ltype']=='admin')?"10":"9")." style='text-align: left'>No media.</td></tr>"; 
} 
?> 
 
</table><br /> 
 
<form action='filebrowser.php' name='deleteform' method='post'> 
<input type='hidden' name='filetodelete' /> 
<input type='hidden' name='delete' value='1' /> 
<input type='hidden' name='order' value='<? echo $order ?>' /> 
<input type='hidden' name='all' value='<? echo $all ?>' /> 
<input type='hidden' name='editor' value='<? echo $editor ?>' />
<input type='hidden' name='source' value='<? echo $source ?>' />
<input type='hidden' name='site' value='<? echo $site ?>' />
<input type='hidden' name='comingfrom' value='<? echo $comingfrom ?>' /> 
</form> 
 
<div align='right'><input type='button' value='Close Window' onclick='window.close()' /></div> 

<?

// debug output -- handy :)
/* print "<pre>"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* print "\n\n"; */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n"; */

/* if (is_object($thisPage)) { */
/* 	print "\n\n"; */
/* 	print "thisPage:\n"; */
/* 	print_r($thisPage); */
/* } else if (is_object($thisSection)) { */
/* 	print "\n\n"; */
/* 	print "thisSection:\n"; */
/* 	print_r($thisSection); */
/* } else if (is_object($thisSite)) { */
/* 	print "\n\n"; */
/* 	print "thisSite:\n"; */
/* 	print_r($thisSite); */
/* } */
// print "</pre>";
?>

</body>
</html>