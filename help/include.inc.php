<script lang="JavaScript">
<!--
function doHelpWin() {
	var newWin = window.open("","help","height=300,width=400,scrollbars=yes,toolbar=no,location=no,directories=no,status=no,resizable=no,copyhistory=no");
	newWin.focus();
}
//-->
</script>

<?

// this function is used to create a link to the help page
function helplink($topic,$text="help") {
	$var= " <span style='font-variant: small-caps; font-weight: bold; font-size: 16px;'>(<a href='help/help.php?helptopic=$topic' onClick='doHelpWin()' target='help'>$text</a>)</span> ";
	return $var;
}
?>
