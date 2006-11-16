<? // text editor 

function editor_txt($textarea,$cols,$rows,$text) {
		   
	printc("<textarea name='$textarea' id='$textarea' cols='$cols' rows='$rows'>");
	printc($text);
	printc("</textarea>");

}

?>


