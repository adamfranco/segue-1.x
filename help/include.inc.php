<?

// this function is used to create a link to the help page
function helplink($topic,$text="help") {
	$var= " <span style='font-variant: small-caps; font-weight: bold; font-size: 16px;'>(<a href='help/help.php?helptopic=$topic' onclick='window.open(\"\",\"help\",\"height=300,width=400,scrollbars=yes,toolbar=no,location=no,directories=no,status=no,resizable=yes,copyhistory=no\");' target='help'>$text</a>)</span> ";
	return $var;
}
?>
