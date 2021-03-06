<!-- <html> -->
<!-- <head> -->
<!-- <title>Example of HTMLArea 3.0</title> -->
<!--  -->
<!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> -->

<!-- Configure the path to the editor.  We make it relative now, so that the
    example ZIP file will work anywhere, but please NOTE THAT its better to
    have it an absolute path, such as '/htmlarea/'. -->
<? global $cfg;  ?>
<div class='desc'><b>Note:</b> To get a single line break, hold down SHIFT while pressing enters.	

<script type="text/javascript">
/*   _editor_url = "../"; */
  _editor_url = "<? echo $cfg[full_uri] ?>/htmleditor/htmlarea/";
  _editor_lang = "en";

</script>
<!-- <script type="text/javascript" src="../htmlarea.js"></script> -->
<script type="text/javascript" src="<? echo $cfg[full_uri] ?>/htmleditor/htmlarea/htmlarea.js"></script>

<script type="text/javascript">
var editor = null;

function initEditor() {
/*   editor = new HTMLArea("ta"); */  
	var config = new HTMLArea.Config();
	config.width = "600px";
	config.height = "400px";
	
	config.toolbar = [
	[ "fontname", "space",
	  "fontsize", "space",
	  "formatblock", "space",
	  "bold", "italic", "underline", "strikethrough", "separator",
	  "subscript", "superscript"],
	
	["justifyleft", "justifycenter", "justifyright", "justifyfull", "separator", "space", "undo", "redo", "lefttoright", "righttoleft", "separator",
	"insertorderedlist", "insertunorderedlist", "outdent", "indent", "separator",
	"forecolor", "hilitecolor", "separator",
	"inserthorizontalrule", "createlink", "insertimage", "inserttable", "htmlmode", "separator",
	"popupeditor", "separator"]
	];
	
	editor = new HTMLArea("<? echo $textarea ?>", config);
	
	editor.generate();
	return false;
}

function insertHTML() {
  var html = prompt("Enter some HTML code here");
  if (html) {
    editor.insertHTML(html);
  }
}
function highlight() {
  editor.surroundHTML('<span style="background-color: yellow">', '</span>');
}
</script>

</head>

<!-- use <body onload="HTMLArea.replaceAll()" if you don't care about
     customizing the editor.  It's the easiest way! :) -->
<body onload="initEditor()">

<!-- <form action="test.cgi" method="post" id="edit" name="edit"> -->

<!-- <textarea id="ta" name="ta" style="width:100%" rows="20" cols="80"> -->
<textarea id="<? echo $textarea ?>" name="<? echo $textarea ?>" style="width:100%" rows="<? echo $rows ?>" cols="<? echo $cols ?>"><? print spchars($text); ?></textarea>

<!-- <script> -->
<!-- initEditor(); -->
<!-- </script> -->


<p />

<!-- <input type="submit" name="ok" value="  submit  " /> -->
<input type="button" name="ins" value="  insert html  " onclick="return insertHTML();" />
<!-- <input type="button" name="hil" value="  highlight text  " onclick="return highlight();" /> -->

<!-- <a href="javascript:mySubmit()">submit</a> -->

<script type="text/javascript">
function mySubmit() {
/* document.edit.save.value = "yes"; */
/* document.edit.onsubmit(); // workaround browser bugs. */
/* document.edit.submit(); */
};
</script>

<!-- </form> -->

<!-- </body> -->
<!-- </html> -->
