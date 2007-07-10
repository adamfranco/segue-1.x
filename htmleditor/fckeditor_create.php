<?php 
/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: sample01.php
 * 	Sample page.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

include("fckeditor/fckeditor.php") ;
include("config.inc.php") ;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title>FCKeditor - Sample</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="robots" content="noindex, nofollow">
		<link href="fckeditor.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript">

function FCKeditor_OnComplete( editorInstance )
{
//	var oCombo = document.getElementById( 'cmbLanguages' ) ;
//	for ( code in editorInstance.Language.AvailableLanguages )
//	{
//		AddComboOption( oCombo, editorInstance.Language.AvailableLanguages[code] + ' (' + code + ')', code ) ;
//	}
//	oCombo.value = editorInstance.Language.ActiveLanguage.Code ;
}	

function AddComboOption(combo, optionText, optionValue)
{
	var oOption = document.createElement("OPTION") ;

	combo.options.add(oOption) ;

	oOption.innerHTML = optionText ;
	oOption.value     = optionValue ;
	
	return oOption ;
}

function ChangeLanguage( languageCode )
{
	window.location.href = window.location + "&Lang=" + languageCode ;
}

</script>		
</head>

<body>
<!--
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>
				Select a language:&nbsp;
			</td>
			<td>
				<select id="cmbLanguages" onchange="ChangeLanguage(this.value);">
				</select>
			</td>
		</tr>
	</table>
-->

	
	
<?php
// Automatically calculates the editor base path based on the _samples directory.
// This is usefull only for these samples. A real application should use something like this:
//$oFCKeditor->BasePath = '/FCKeditor/' ;	// '/FCKeditor/' is the default value.

//$sBasePath = $_SERVER['PHP_SELF'] ;
//$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, "_samples" ) ) ;
$Lang = $_REQUEST['Lang'];
$oFCKeditor = new FCKeditor($textarea) ;
$oFCKeditor->Config['CustomConfigurationsPath'] = $cfg[full_uri]."/htmleditor/fckeditor_config.js";
$oFCKeditor->BasePath	= $cfg[full_uri]."/htmleditor/fckeditor/" ;
$oFCKeditor->Value		= $text ;

/******************************************************************************
 * set up the appropriate editor toolbar 
 ******************************************************************************/

// story
if ($textarea == "shorttext" || $textarea == "longertext") { 
	$oFCKeditor->Height		= '400' ;
	$oFCKeditor->ToolbarSet		= 'Story' ;
// discussion	
} else if ($textarea == "content" || $textarea == "body") { 
	$oFCKeditor->Height		= '200' ;
	$oFCKeditor->ToolbarSet		= 'Discuss' ;
// page
} else if ($textarea == "text") {  //page
	$oFCKeditor->Height		= '400' ;
	$oFCKeditor->ToolbarSet		= 'Page' ;
// Initial site setup (no media upload button)
} else if ($context == "initsite") {
	$oFCKeditor->Height		= '400' ;
	$oFCKeditor->ToolbarSet		= 'Initsite' ;	
// header and footer
} else if ($textarea == "header" || $textarea == "footer") { 
	$oFCKeditor->Height		= '400' ;
	$oFCKeditor->ToolbarSet		= 'Story' ;
}

if ( isset($_GET['Lang']) )
{
	$oFCKeditor->Config['AutoDetectLanguage']	= false ;
	$oFCKeditor->Config['DefaultLanguage']		= $_GET['Lang'] ;
}
else
{
	$oFCKeditor->Config['AutoDetectLanguage']	= true ;
	$oFCKeditor->Config['DefaultLanguage']		= 'en' ;
}


$oFCKeditor->Create() ;

?>
</body>
</html>