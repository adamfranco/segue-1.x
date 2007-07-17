/* FCKConfig.ToolbarSets["default"] = [ */
/* 	['Source','DocProps','-','Save','NewPage','Preview','-','Templates'], */
/* 	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'], */
/* 	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'], */
/* 	['Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField'], */
/* 	'/', */
/* 	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'], */
/* 	['OrderedList','UnorderedList','-','Outdent','Indent'], */
/* 	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'], */
/* 	['Link','Unlink','Anchor'], */
/* 	['Image','Flash','Table','Rule','Smiley','SpecialChar','PageBreak'], */
/* 	'/', */
/* 	['Style','FontFormat','FontName','FontSize'], */
/* 	['TextColor','BGColor'], */
/* 	['FitWindow','-','About'] */
/* ] ; */


FCKConfig.ToolbarSets["Story"] = [
	['Source','-','Preview','-','Templates'],
	['Cut','Copy','Paste','PasteText','PasteWord','-'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	'/',
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
	['Image','Flash','Table','Rule','SpecialChar'],['TextColor','BGColor'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['Style','FontFormat','FontName','FontSize','FitWindow','About']
] ;

FCKConfig.ToolbarSets["Story2"] = [
	['Source','-','Preview','-','Templates'],
	['Cut','Copy','Paste','PasteText','PasteWord','-'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	'/',
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
	['Image','Flash','Table','Rule','SpecialChar','UniversalKey'],
	'/',
	['Style','FontFormat','FontName','FontSize'],
	['TextColor','BGColor'],
	['FitWindow','-','About']
] ;

FCKConfig.ToolbarSets["Page"] = [
	['Source','-','Preview','-','Templates'],
	['Cut','Copy','Paste','PasteText','PasteWord','-'],
	['Undo','Redo','-','Find','Replace'],
	'/',
	['SelectAll','RemoveFormat','-','Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript','-'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	'/',
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
	['Image','Flash','Table','Rule','SpecialChar'],
	['Style','FontFormat'],
	'/',
	['FontName','FontSize'],['TextColor','BGColor'],
	['FitWindow','-','About']
] ;

FCKConfig.ToolbarSets["Discuss"] = [
	['Source','-','Bold','Italic','-','OrderedList','UnorderedList','-','Link','Unlink','-'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Cut','Copy','Paste','PasteText','PasteWord','-'],
	'/',
	['FontName','FontSize'],['TextColor','BGColor'],['Table','Rule','SpecialChar','FitWindow','About']
] ;

FCKConfig.ToolbarSets["Initsite"] = [
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],['TextColor','BGColor'],
	['Bold','Italic','-','-','Link','Unlink','-'],['Table','Rule','SpecialChar'],
	['FontFormat','FontName','FontSize']
] ;

FCKConfig.EnterMode = 'br' ;			// p | div | br
FCKConfig.ShiftEnterMode = 'p' ;	// p | div | br

FCKConfig.LinkDlgHideTarget		= false ;
FCKConfig.LinkDlgHideAdvanced	= false ;

FCKConfig.ImageDlgHideLink		= false ;
FCKConfig.ImageDlgHideAdvanced	= false ;

FCKConfig.FlashDlgHideAdvanced	= false ;

// The following value defines which File Browser connector and Quick Upload 
// "uploader" to use. It is valid for the default implementaion and it is here
// just to make this configuration file cleaner. 
// It is not possible to change this value using an external file or even 
// inline when creating the editor instance. In that cases you must set the 
// values of LinkBrowserURL, ImageBrowserURL and so on.
// Custom implementations should just ignore it.
var _FileBrowserLanguage	= 'php' ;	// asp | aspx | cfm | lasso | perl | php | py
var _QuickUploadLanguage	= 'php' ;	// asp | aspx | cfm | lasso | php

// Don't care about the following line. It just calculates the correct connector 
// extension to use for the default File Browser (Perl uses "cgi").
var _FileBrowserExtension = _FileBrowserLanguage == 'perl' ? 'cgi' : _FileBrowserLanguage ;


FCKConfig.LinkBrowser = false ;
FCKConfig.LinkBrowserURL = FCKConfig.BasePath + 'filemanager/browser/default/browser.html?Connector=connectors/' + _FileBrowserLanguage + '/connector.' + _FileBrowserExtension ;
FCKConfig.LinkBrowserWindowWidth	= FCKConfig.ScreenWidth * 0.4 ;		// 70%
FCKConfig.LinkBrowserWindowHeight	= FCKConfig.ScreenHeight * 0.5 ;	// 70%

FCKConfig.ImageBrowser = true ;
//FCKConfig.ImageBrowserURL = FCKConfig.BasePath + 'filemanager/browser/default/segue_filebrowser.php?Type=Image&Connector=connectors/' + _FileBrowserLanguage + '/connector.' + _FileBrowserExtension ;
FCKConfig.ImageBrowserURL = '../../../../filebrowser.php?Type=Image&Connector=connectors/' + _FileBrowserLanguage + '/connector.' + _FileBrowserExtension ;
//FCKConfig.ImageBrowserURL = FCKConfig.BasePath + 'filemanager/browser/default/fckeditor_filebrowser.php?Type=Image&Connector=connectors/' + _FileBrowserLanguage + '/connector.' + _FileBrowserExtension ;
FCKConfig.ImageBrowserWindowWidth  = 700 ;
FCKConfig.ImageBrowserWindowHeight = 600 ;

FCKConfig.FlashBrowser = true ;
FCKConfig.FlashBrowserURL = '../../../../filebrowser.php?Type=Flash&Connector=connectors/' + _FileBrowserLanguage + '/connector.' + _FileBrowserExtension ;
//FCKConfig.FlashBrowserURL = FCKConfig.BasePath + 'filemanager/browser/default/browser.html?Type=Flash&Connector=connectors/' + _FileBrowserLanguage + '/connector.' + _FileBrowserExtension ;
FCKConfig.FlashBrowserWindowWidth  = 700 ;
FCKConfig.FlashBrowserWindowHeight  = 600;

FCKConfig.LinkUpload = false ;
FCKConfig.LinkUploadURL = FCKConfig.BasePath + 'filemanager/upload/' + FCKConfig.QuickUploadLanguage + '/upload.' + _QuickUploadLanguage ;
FCKConfig.LinkUploadAllowedExtensions	= "" ;			// empty for all
FCKConfig.LinkUploadDeniedExtensions	= ".(php|php3|php5|phtml|asp|aspx|ascx|jsp|cfm|cfc|pl|bat|exe|dll|reg|cgi)$" ;	// empty for no one

FCKConfig.ImageUpload = false ;
FCKConfig.ImageUploadURL = FCKConfig.BasePath + 'filemanager/upload/' + FCKConfig.QuickUploadLanguage + '/upload.' + _QuickUploadLanguage + '?Type=Image' ;
FCKConfig.ImageUploadAllowedExtensions	= ".(jpg|gif|jpeg|png)$" ;		// empty for all
FCKConfig.ImageUploadDeniedExtensions	= "" ;							// empty for no one

FCKConfig.FlashUpload = false ;
FCKConfig.FlashUploadURL = FCKConfig.BasePath + 'filemanager/upload/' + FCKConfig.QuickUploadLanguage + '/upload.' + _QuickUploadLanguage + '?Type=Flash' ;
FCKConfig.FlashUploadAllowedExtensions	= ".(swf|fla)$" ;		// empty for all
FCKConfig.FlashUploadDeniedExtensions	= "" ;					// empty for no one

FCKConfig.SmileyPath	= FCKConfig.BasePath + 'images/smiley/msn/' ;
FCKConfig.SmileyImages	= ['regular_smile.gif','sad_smile.gif','wink_smile.gif','teeth_smile.gif','confused_smile.gif','tounge_smile.gif','embaressed_smile.gif','omg_smile.gif','whatchutalkingabout_smile.gif','angry_smile.gif','angel_smile.gif','shades_smile.gif','devil_smile.gif','cry_smile.gif','lightbulb.gif','thumbs_down.gif','thumbs_up.gif','heart.gif','broken_heart.gif','kiss.gif','envelope.gif'] ;
FCKConfig.SmileyColumns = 8 ;
FCKConfig.SmileyWindowWidth		= 320 ;
FCKConfig.SmileyWindowHeight	= 240 ;
