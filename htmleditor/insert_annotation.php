<html STYLE="width: 406px; height: 235px; ">
<html>
<head>
<title>Insert Annotation</title>
<style>
  html, body, button, div, input, select, td, fieldset { font-family: MS Shell Dlg; font-size: 8pt; };
</style>
	<script language="JavaScript">

		function insertAnnotation()
		{
			opener = window.dialogArguments;
			var _editor_url = opener._editor_url;
			var objname     = location.search.substring(1,location.search.length);
			var config      = opener.document.all[objname].config;
			var editor_obj  = opener.document.all["_" +objname+  "_editor"];
			var editdoc     = editor_obj.contentWindow.document;

			// Add the annotation to the iFrame
			var kindofannotation = document.all.kindofannotation.options[document.all.kindofannotation.selectedIndex].value;
			var text = document.all.text.value;

			var text1 = text.replace(/"/g,"&#34;");
			var newtext = text1.replace(/'/g,"&rsquo;");

			var date = document.all.date.value;
			
			if(!text)
			{
				alert('Please Enter The Text Of Your Annotation!');
				document.all.text.select();
				document.all.text.focus();
				return false;
			}
			
			
			// Build the code for the annotation

			var html = "";
			html = html + "<a href=\"javascript:void(0);\" onclick=\"return overlib('"+newtext+"', STICKY, CAPTION, '"+kindofannotation+"', CENTER);\" onmouseout=\"nd();\"><img src=\"htmleditor/images/ed_annotation.gif\" height=\"20\" width=\"20\" alt=\""+kindofannotation+"\" border=\"0\" align=\"top\" /></a>\r\n"; 


			
			opener.editor_insertHTML(objname, html);
			window.close();
		}

	</script>
</head>
<body id='bdy' style="background: threedface; color: windowtext; margin: 10px; BORDER-STYLE: none" scroll='no'>
<FIELDSET style="width: 1%; text-align: center">
<LEGEND>Insert Annotation&nbsp;</LEGEND>
 <table border="0" cellspacing="0" cellpadding="2" align="center" style="margin: 5 5 5 5; width="100%">
  <tr>
   <td>
    <table width="100%" border="0" cellspacing="0" cellpadding="5">
     <tr>
      <td width="40%" class="text" valign="top" align="right">What Kind Of Annotation:</td>
      <td width="60%"><select id="kindofannotation">
				<option value="Comment">Comment</option>
				<option value="Footnote">Footnote</option>
				<option value="Note">Note</option>
				<option value="Source">Source</option>
				</select></td>
     </tr>
     <tr>
      <td width="40%" class="text" valign="top" align="right">Text:</td>
      <td width="60%"><textarea name="text" rows="5" cols="25" wrap="VIRTUAL" id="text"></textarea></td>
     </tr>
     <tr>
      <td width="40%" class="text"><input type="hidden" value="<? echo date ("l, F d" ,time());?>" id="date"></td>
      <td width="60%" colspan="3"><input type="button" value="Insert" onclick="insertAnnotation()">&nbsp;<input type="button" value="Cancel" onclick="window.close();"></td>
     </tr>
    </table>
   </td>
  </tr>
 </table>
</FIELDSET>
</body>
</html>