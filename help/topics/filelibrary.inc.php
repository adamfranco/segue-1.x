<? 
$title = "Media Library";
	global $HTTP_SERVER_VARS;
	$uagent = $HTTP_SERVER_VARS["HTTP_USER_AGENT"];
	$uagent = explode("; ",$uagent);
	$uagent = explode(" ",$uagent[1]);
	$bname = strtoupper($uagent[0]);
?>
 
<p>The Media Library is the repository of all of the images and files that you can use in your site.

<p>To add files to the Media Library, click the "Browse" button to select a file from your computer.<? print (($bname == "MSIE")?"After selecting a file, click \"Upload\" to add it to the the Media Library":""); ?>

<p>The top right of the Media Library window shows you how much space you have availible for files and images. The red part of the bar-graph represents the percentage of your space used.

<p>To delete media, click the "delete" button at the right side of the row for that media.  WARNING: If this media is used in any part of your site, those parts will not function correctly.

<p>To update media, select media with the same name when browsing, the click "OVERWRITE" to overwrite the file in the Media Library.

<p>To use media, click the "use" button on the left side of the row for that media.