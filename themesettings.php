<?
$content = '';

session_start();

// include all necessary files
include("includes.inc.php");

include("$themesdir/common/header.inc.php");

db_connect($dbhost, $dbuser, $dbpass, $dbdb);

/*
vars passed to this script:
$theme -- the theme to edit
$updatemethod -- either "javascript" or "db".. how to update the theme
$site -- the site for which we are editing the theme
$themesettings -- existing theme settings

*/

/* ------ debug ------- */
/* $themesettings = $HTTP_GET_VARS['themesettings']; */
/* print "$themesettings<br>"; */
/* print_r(unserialize(stripslashes($themesettings)))."<br>"; */
/* print "$theme - $updatemethod - $site<br>"; */

$themesettings = unserialize(stripslashes(($themesettings)));
$filename = "$themesdir/$theme/themesettings.inc.php";


if (file_exists($filename)) {
	ob_start();
	include($filename);
	$settings_form=ob_get_contents();
	ob_end_clean();
} else
	printc("<b>There are no additional options available for this theme.</b>");



if ($submitted) {
	$onLoad = ' onLoad="';
	$themesettings = encode_array($themesettings);
	if ($updatemethod == 'javascript') {
		$onLoad .= 'updateAndClose()';
	}
	if ($updatemethod == 'db') {
		$onLoad .= 'window.close()';
		// update the database
	}
	$onLoad .= '"';
} else {
	// print out theme form
	printc("<form action='$PHP_SELF?$sid' method=post name='settings'>");
	printc("<input type=hidden name='submitted' value=1>");
	printc("<input type=hidden name='updatemethod' value='$updatemethod'>");
	printc("<input type=hidden name='site' value='$site'>");
	printc("<input type=hidden name='theme' value='$theme'>");
	printc($settings_form);
	printc("<div align=right><input type=submit value='Update & Close' class=button></div>");
	printc("</form>");
}


?>

<head>
<script lang='JavaScript'>
function updateAndClose() {
	var ts = '<?echo $themesettings?>';
	opener.document.addform.themesettings.value = ts;
	window.close();
}
</script>
<style type='text/css'>
.title {
	color: #555;
	font-size: 16px;
	font-weight: bold;
	padding: 5px 5px 5px 10px;
	border-bottom: 1px dashed #999;
}

.content {
	color: #333;
	font-size: 11px;
	padding-left: 20px;
	margin-top: 5px;
	margin-right: 10px;
	margin-bottom: 10px;
}

.title, .content {
	font-family: "Verdana";
}

.desc { margin-bottom: 8px; }

body { background-color: white; }

.button {
	margin-right: 40px;
	margin-bottom: 10px;
	font-size: 10px;
	margin-top: 20px;
}

select {font-size: 10px; }

</style>
<title>Theme Settings</title>
</head>

<body<?echo $onLoad?> marginheight=0 marginwidth=0 leftmargin=0 rightmargin=0 topmargin=0 bottommargin=0>
<div class=title>Theme Settings</div>

<div class=content>
<? print $content ?>
<div style='margin-left: 220px; margin-right: 30px'>
After updating you can click the thumbnail to preview your new settings.
</div>
</div>
</body>