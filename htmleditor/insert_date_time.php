<html STYLE="width: 435px; height: 147px; ">
<head><title>Insert Date & Time</title><head>
<style>
  html, body, button, div, input, select, td, fieldset { font-family: MS Shell Dlg; font-size: 8pt; };
</style>
<script>

// if we pass the "window" object as a argument and then set opener to
// equal that we can refer to dialogWindows and popupWindows the same way
opener = window.dialogArguments;

var _editor_url = opener._editor_url;
var objname     = location.search.substring(1,location.search.length);
var config      = opener.document.all[objname].config;
var editor_obj  = opener.document.all["_" +objname+  "_editor"];
var editdoc     = editor_obj.contentWindow.document;

function _CloseOnEsc() {
  if (event.keyCode == 27) { window.close(); return; }
}

window.onerror = HandleError

function HandleError(message, url, line) {
  var str = "An error has occurred in this dialog." + "\n\n"
  + "Error: " + line + "\n" + message;
  alert(str);
  return true;
}

function Init() {
  document.body.onkeypress = _CloseOnEsc;
}

function btnOKClick() {
  var curRange = editdoc.selection.createRange();

  // delete selected content (if applicable)
  if (editdoc.selection.type == "Control" || curRange.htmlText) {

    if (!confirm("Easy Editor Alert!\nThis will overwrite the currently selected text!")) { return; }

    curRange.execCommand('Delete');
    curRange = editdoc.selection.createRange();
  }


  var datetime = '' +document.all.date.value+ '';

  // insert the date and time
  opener.editor_insertHTML(objname, datetime);


  // close popup window
  window.close();
}
</SCRIPT>
</HEAD>
<BODY id=bdy onload="Init()" style="background: threedface; color: windowtext; margin: 10px; BORDER-STYLE: none" scroll=no>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
 <tr>
  <td><FIELDSET style="text-align: center"><LEGEND>Insert Date & Time&nbsp;</LEGEND>
   <table border=0 cellspacing=0 cellpadding=5 style="margin: 5 5 5 5;">
    <tr>
     <td>Please Pick A Format: &nbsp;</td>
         <td><select name="date" size="1" style="width: 220px">
              <option value="<?php echo date("F j, Y"); ?>"><?php echo date("F j, Y"); ?></option>
              <option value="<?php echo date("jS o\f\ F, Y"); ?>"><?php echo date("jS o\f\ F, Y"); ?></option>
              <option value="<?php echo date("m. d. y"); ?>"><?php echo date("m. d. y"); ?> --- (US Short Date Format)</option>
              <option value="<?php echo date("d. m. y"); ?>"><?php echo date("d. m. y"); ?> --- (European Short Date Format)</option>
              <option value="<?php echo date("Y.m.d"); ?>"><?php echo date("Y.m.d"); ?></option>
              <option value="<?php echo date("D M j G:i:s T Y"); ?>"><?php echo date("D M j G:i:s T Y"); ?></option>
              <option value="<?php echo date("H:i:s"); ?>"><?php echo date("H:i:s"); ?></option>
              <option value="<?php echo date("g:i a"); ?>"><?php echo date("g:i a"); ?></option>
             </select>
    </tr>
    <tr>
     <td colspan="2" valign="middle" align="center"><BUTTON style="width: 7em; height: 2.2em;" type=submit onclick="btnOKClick()" tabindex=3>OK</BUTTON> <BUTTON style="width: 7em; height: 2.2em; " type=reset onClick="window.close();" tabindex=4>Cancel</BUTTON></td>
    </tr>
   </table>
   </FIELDSET>
  </td>
 </tr>
</table>
</BODY>
</HTML>