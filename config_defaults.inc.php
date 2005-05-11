<?php

/******************************************************************************
 * Default Blacklist 
 *					- This is used if Segue is in Blacklist Mode. If it is in
 *						the [default] Whitelist Mode, then this list is ignored.
 *
 *					- To prevent PHP scripts from being uploaded to Segue
 * 					   no files with extensions that Apache associates with
 *					   PHP should be allowed to be uploaded.  
 ******************************************************************************/
$cfg['defaultBlacklist'] = $defaultBlacklist = array(
	'php.*',
	'phtml',
	'py',
	'sh',
	'exe',
	'ttml',
	'tcl',
	'phps',
	'pl',
	'crt',
	'crl',
	'cgi',
	'rb',
	'bat',
	'rl',
	'f77',
	'lisp',
);


/******************************************************************************
 * Default Whitelist 
 *					- This is used if Segue is in Whitelist Mode. If it is in
 *						the Blacklist Mode, then this list is ignored.
 *
 *					- To prevent PHP scripts from being uploaded to Segue
 * 					   no files with extensions that Apache associates with
 *					   PHP should be allowed to be uploaded.  
 *
 * See http://www.wotsit.org/ for more info on file formats
 ******************************************************************************/
$cfg['defaultWhitelist'] = $defaultWhitelist = array(

// Graphics Formats
	'bmp',			// Windows Bitmap-File Formats (.BMP/.CUR/.ICO)
	'ico',
	'fits',			// Flexible Image Transport System
	'gif',			// Graphics Interchange Format
	'jpeg',			// 
	'jpg',			
	'png',			// 
	'psd',			// 
	'raw',			// 
	'tif',			// 
	'tiff',			// 

	
// Audio/Video Formats
	'aac',			// 
	'aiff',			// 
	'avi',			// 
	'm4a',			// 
	'mov',			// 
	'mng',			//
	'mp2',			// 
	'mp3',			// 
	'mp4',			// 
	'mpg',			// 
	'ogg',			// 
	'ram',			// 
	'rm',			// 
	'wav',			// 
	'wma',			// 
	'wmv',			// 


// Text/Document
	'css',			// 
	'doc',			// 
	'dvi',			// 
	'htm',			// 
	'html',			// 
	'pdf',			// 
	'ppt',			// 
	'ps',			// 
	'rss',			// 
	'rtf',			// 
	'txt',			// 
	'xls',			// 
	'xml',			// 
	
// Archives
	'bz2',			// 
	'dmg',			// 
	'gz',			// 
	'sit',			// 
	'tar',			// 
	'zip',			// 
);
