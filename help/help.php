<? // help handler

$file = "topics/$helptopic.inc.php";
/* print $file; */
if (!file_exists($file))
	$file = "topics/notfound.inc.php";

ob_start();
include($file);
$content = ob_get_contents();
ob_end_clean();

?>
<HTML>
<head><title>Segue Help: <?echo $title?></title>
<?/*include("css.php");*/?>
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
}

</style>
</head>
<BODY marginheight=0 marginwidth=0 leftmargin=0 rightmargin=0 topmargin=0 bottommargin=0>
<div class=title>Help Topic: <?echo $title?></div>
<div class=content><?echo $content?></div>
<div align=right><input type=button class=button value='close' onClick='window.close()'></div>
</body>
</html>
