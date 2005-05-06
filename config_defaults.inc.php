<?php

/******************************************************************************
 * Banned Extensions - To prevent PHP scripts from being uploaded to Segue
 * 					   no files with extensions that Apache associates with
 *					   PHP should be allowed to be uploaded.  
 ******************************************************************************/
$cfg['defaultBannedExtensions'] = $defaultBannedExtensions = array(
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
