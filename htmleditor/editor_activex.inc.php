<? // active-x editor.inc --

//these script functions are included below
//printc("<script language=\"Javascript1.2\" src=\"htmleditor/editor.js\"></script>\n");
//printc("<script>\n");
//printc("_editor_url = \"htmleditor/\";\n");
//printc("</script>\n");

function editor_activex($textarea,$cols,$rows,$text) {

	//style sheet for active-x editor
	printc("<style type=\"text/css\">\n");
	printc("<!--\n");
	printc("  .btn   { BORDER-WIDTH: 1; width: 26px; height: 24px; }\n");
	printc("  .btnDN { BORDER-WIDTH: 1; width: 26px; height: 24px; BORDER-STYLE: inset; BACKGROUND-COLOR: buttonhighlight; }\n");
	printc("  .btnNA { BORDER-WIDTH: 1; width: 26px; height: 24px; filter: alpha(opacity=25); }\n");
	printc("-->\n");
	printc("</style>\n");
	printc("<style type=\"text/css\">\n");
	printc("<!--\n");
	printc("   body, td { font-family: arial; font-size: 12px; }\n");
	printc("  .headline { font-family: arial black, arial; font-size: 28px; letter-spacing: -2px; }\n");
	printc("  .subhead  { font-family: arial, verdana; font-size: 12px; let!ter-spacing: -1px; }\n");
	printc("-->\n");
	printc("</style>\n");
	
	//	Begin modification 4 - 11.11.02 - afranco
	//	Add explanation of how to get <br>s
	printc("<div class=desc><b>Note:</b> To get a single line break, hold down SHIFT while pressing enter</div>");	
	
			   
	//	Begin modification 2 - 10.26.02 - rlange
	//	Added an id tag to the textarea definition	
	printc("<textarea name=$textarea id=$textarea cols=$cols rows=$rows>");
	printc(spchars($text));
	printc("</textarea>");
	//	End modification 2 - 10.26.02 - rlange	
				
	//	Begin modification 3 - 10.26.02 - rlange
	//	Create the iView from JavaScript for the above textarea
	printc("<script language=\"javascript1.2\">\n");
	printc("editor_generate('$textarea');\n");
	printc("</script>\n");
}


?>

<!--

/* ---------------------------------------------------------------------- *\
  Function    : editor_generate
  Description : replace textarea with wysiwyg editor
  Usage       : editor_generate("textarea_id",[height],[width]);
  Arguments   : objname - ID of textarea to replace
                w       - width of wysiwyg editor
                h       - height of wysiwyg editor
\* ---------------------------------------------------------------------- */
-->

<script>
_editor_url = "htmleditor/";
</script>

<script language="Javascript1.2">

function editor_generate(objname,w,h) {


  // Default Settings
  var imgURL = _editor_url + 'images/';       // images url

  // set size to specified size or size of original object
  var obj    = document.all[objname];
  if (!w) {
    if      (obj.style.width) { width = obj.style.width; }      // use css style
    else if (obj.cols)        { width = (obj.cols * 8) + 22; }  // col width + toolbar
    else                      { width = '100%'; }               // default
  }
    if (!h) {
    if      (obj.style.height) { height = obj.style.height; }   // use css style
    else if (obj.rows)         { height = obj.rows * 17 }       // row height
    else                       { height = '200'; }              // default
  }

  // Check for IE 5.5+ on Windows
  var Agent, VInfo, MSIE, Ver, Win32, Opera;
  Agent = navigator.userAgent;
  VInfo = Array();                              // version info
  VInfo = Agent.split(";")
  MSIE  = Agent.indexOf('MSIE') > 0;
  Ver   = VInfo[1].substr(6,3);
  Win32 = Agent.indexOf('Windows') > 0 && Agent.indexOf('Mac') < 0 && Agent.indexOf('Windows CE') < 0;
  Opera = Agent.indexOf('Opera') > -1;
  if (!MSIE || Opera || Ver < 5.5 || !Win32) { return; }

  var editor = ''
  + '<table border=0 cellspacing=0 cellpadding=0 bgcolor="buttonface" style="padding: 1 0 0 0" width=' + width + ' unselectable="on">\n'
  + '<tr><td>\n'

  + '<table border=0 cellspacing=2 cellpadding=0 bgcolor="buttonface" style="float: left;"  unselectable="on">\n'
  + ' <tr>\n'
  + '  <td style="border-width: 0; padding: 2 0 0 3;">\n'
  + '   <select id="_' +objname+ '_FontName" onChange="editor_action(this.id)" unselectable="on">\n'
  + '   <option value="arial, helvetica, sans-serif">Arial</option>\n'
  + '   <option value="courier new, courier, mono">Courier New</option>\n'
  + '   <option value="Georgia, Times New Roman, Times, Serif">Georgia</option>\n'
  + '   <option value="Tahoma, Arial, Helvetica, sans-serif">Tahoma</option>\n'
  + '   <option value="times new roman, times, serif">Times</option>\n'
  + '   <option value="Verdana, Arial, Helvetica, sans-serif">Verdana</option>\n'
  + '   <option value="wingdings">WingDings</option>\n'
  + '   </select>'
  + '  </td>\n'
  + ' </tr>\n'
  + '</table>\n'

  + '<table border=0 cellspacing=2 cellpadding=0 bgcolor="buttonface" style="float: left;"  unselectable="on">\n'
  + ' <tr>\n'
  + '  <td style="border-width: 0; padding: 2 1 0 0;">\n'
  +    '<select id="_' +objname+ '_FontSize" onChange="editor_action(this.id)" style="width:38px"  unselectable="on">\n'
  + '   <option value=1>1</option><option value=2>2</option><option value=3>3</option><option value=4>4</option><option value=5>5</option><option value=6>6</option><option value=7>7</option>\n'
  + '   </select>\n\n'
  + '  </td>\n'
  + ' </tr>\n'
  + '</table>\n'


  + '<table border=0 cellspacing=2 cellpadding=0 bgcolor="buttonface" style="float: left;"  unselectable="on"><tr><td style="border: inset 1px;">\n'
  +    '<button title="Bold" id="_' +objname+ '_Bold" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_format_bold.gif" unselectable="on"></button>'
  +    '<button title="Italic" id="_' +objname+ '_Italic" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_format_italic.gif" unselectable="on"></button>'
  +    '<button title="Underline" id="_' +objname+ '_Underline" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_format_underline.gif" unselectable="on">\n'
  + '</td></tr></table>\n'


  + '<table border=0 cellspacing=2 cellpadding=0 bgcolor="buttonface" style="float: left;"  unselectable="on"><tr><td style="border: inset 1px;">\n'
  +    '<button title="Strikethrough" id="_' +objname+ '_StrikeThrough" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_format_strike.gif" unselectable="on"></button>'
  +    '<button title="Subscript" id="_' +objname+ '_SubScript" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_format_sub.gif" unselectable="on"></button>'
  +    '<button title="Superscript" id="_' +objname+ '_SuperScript" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_format_sup.gif" unselectable="on">\n'
  + '</td></tr></table>\n'

  + '<table border=0 cellspacing=2 cellpadding=0 bgcolor="buttonface" style="float: left;"  unselectable="on"><tr><td style="border: inset 1px;">\n'
  +    '<button title="Justify Left" id="_' +objname+ '_JustifyLeft" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_align_left.gif" unselectable="on"></button>'
  +    '<button title="Justify Center" id="_' +objname+ '_JustifyCenter" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_align_center.gif" unselectable="on"></button>'
  +    '<button title="Justify Right" id="_' +objname+ '_JustifyRight" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_align_right.gif" unselectable="on">\n'
  + '</td></tr></table>\n'

  + '<table border=0 cellspacing=2 cellpadding=0 bgcolor="buttonface" style="float: left;"  unselectable="on" unselectable="on"><tr><td style="border: inset 1px;">\n'
  +    '<button title="Ordered List" id="_' +objname+ '_InsertOrderedList" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_list_num.gif" unselectable="on"></button>'
  +    '<button title="Bulleted List" id="_' +objname+ '_InsertUnorderedList" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_list_bullet.gif" unselectable="on">\n'
  +    '<button title="Decrease Indent" id="_' +objname+ '_Outdent" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_indent_less.gif" unselectable="on"></button>'
  +    '<button title="Increase Indent" id="_' +objname+ '_Indent" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_indent_more.gif" unselectable="on">\n'
  + '</td></tr></table>\n'

  + '<table border=0 cellspacing=2 cellpadding=0 bgcolor="buttonface" style="float: left;"  unselectable="on" unselectable="on"><tr><td style="border: inset 1px;">\n'
  +    '<button title="Font Color" id="_' +objname+ '_ForeColor" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_color_fg.gif" unselectable="on"></button>'
  +    '<button title="Background Color" id="_' +objname+ '_BackColor" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_color_bg.gif" unselectable="on">\n'
  + '</td></tr></table>\n'

  + '<table border=0 cellspacing=2 cellpadding=0 bgcolor="buttonface" style="float: left;" unselectable="on"><tr><td style="border: inset 1px;">\n'
  +    '<button title="Horizontal Rule" id="_' +objname+ '_InsertHorizontalRule" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_hr.gif" unselectable="on"></button>'
  +    '<button title="Insert Web Link" id="_' +objname+ '_CreateLink" class="btn" onClick="editor_action(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_link.gif" unselectable="on"></button>'
  + '</td></tr></table>\n'

  + '<table border=0 cellspacing=2 cellpadding=0 bgcolor="buttonface" style="float: left;" unselectable="on"><tr><td style="border: inset 1px;">\n'
  +    '<button title="View HTML Source" id="_' +objname+ '_HtmlMode" class="btn" onClick="editor_setmode(this.id)" unselectable="on"><img src="' +imgURL+ 'ed_html.gif" unselectable="on"></button>'

  + '</td></tr></table>\n'
  + '</td></tr></table>\n'

  + '<textarea ID="_' +objname + '_editor" style="width:' +width+ '; height:' +height+ '; margin-top: -1px; margin-bottom: -1px;"></textarea>'

  + '<input type="hidden" name="' +objname+ '" value="">'
//  + '<textarea ID="' +objname+ '" rows=12 cols=80></textarea>'
  ;

  // create editor
  var contents = document.all[objname].value;             // get original contents
  document.all[objname].outerHTML = editor;               // create editor frame
  document.all['_'+objname+'_editor'].value = contents;   // set contents

  editor_setmode('_' +objname+ '_HtmlMode', 'init');      // switch to wysiwyg mode

}

/* ---------------------------------------------------------------------- *\
  Function    : editor_action
  Description : perform an editor command on selected editor content
  Usage       :
  Arguments   : button_id - button id string with editor and action name
\* ---------------------------------------------------------------------- */

function editor_action(button_id) {

  var BtnParts = Array();
  BtnParts = button_id.split("_");
  var objname    = button_id.replace(/^_(.*)_[^_]*$/, '$1');
  var cmdID      = BtnParts[ BtnParts.length-1 ];
  var button_obj = document.all[button_id];
  var editor_obj = document.all["_" +objname + "_editor"];

  // check editor mode (don't perform actions in textedit mode)
  if (editor_obj.tagName.toLowerCase() == 'textarea') { return; }

  var editdoc = editor_obj.contentWindow.document;
  _editor_focus(editor_obj);

  // execute command for font pulldowns
  var idx = button_obj.selectedIndex;
  if (idx != null) {
    var val = button_obj[ idx ].value;
    editdoc.execCommand(cmdID,0,val);
  }

  // execute command for fgcolor & bgcolor buttons
  else if (cmdID == 'ForeColor' || cmdID == 'BackColor') {
    // figure our optimal window placement for popup dialog
    var posX    = event.screenX;
    var posY    = event.screenY + 20;
    var screenW = screen.width;                                 // screen size
    var screenH = screen.height - 20;                           // take taskbar into account
    if (posX + 232 > screenW) { posX = posX - 232 - 40; }       // if mouse too far right
    if (posY + 164 > screenH) { posY = posY - 164 - 80; }       // if mouse too far down
    var wPosition   = "dialogLeft:" +posX+ "; dialogTop:" +posY;

    var oldcolor = _dec_to_rgb(editdoc.queryCommandValue(cmdID));
    var newcolor = showModalDialog(_editor_url + "select_color.html", oldcolor,
                                   "dialogWidth:238px; dialogHeight: 187px; "
                                   + "resizable: no; help: no; status: no; scroll: no; "
                                   + wPosition);
    if (newcolor != null) { editdoc.execCommand(cmdID, false, "#"+newcolor); }
  }

  // execute command for buttons
  else {
    // subscript & superscript, disable one before enabling the other
    if (cmdID.toLowerCase() == 'subscript' && editdoc.queryCommandState('superscript')) { editdoc.execCommand('superscript'); }
    if (cmdID.toLowerCase() == 'superscript' && editdoc.queryCommandState('subscript')) { editdoc.execCommand('subscript'); }

    // insert link
    if (cmdID.toLowerCase() == 'createlink'){
      editdoc.execCommand(cmdID,1);
    }
    
    if (cmdID.toLowerCase() == 'insertfilebrowser'){
      showModalDialog(_editor_url + "../filebrowser.php?",  "resizable: no; help: no; status: no; scroll: no; ");
    }

    // insert image
    else if (cmdID.toLowerCase() == 'insertimage'){
      showModalDialog(_editor_url + "insert_image.html", editdoc, "resizable: no; help: no; status: no; scroll: no; ");
    }
     

    // insert image
    else if (cmdID.toLowerCase() == 'about'){
      var html = '<HTML><HEAD><TITLE>About Embiodea</TITLE>\n'
               + '<style>\n'
               + '  html,body,textarea { font-family: verdana,arial; font-size: 9pt; };\n'
               + '</style></HEAD>\n'
               + '<BODY style="background: threedface; color: #000000"  topmargin=5 leftmargin=12>\n\n'
	       + 'About Embiodea\n\n'
               + '</body></html>\n\n';

      var popup = window.open('', 'ColorPicker',
                  "location=no,menubar=no,toolbar=no,directories=no,status=no," +
                  "height=275,width=450,resizable=no,scrollbars=no");
      popup.document.write(html);
    }

    // all other commands
    else {
      editdoc.execCommand(cmdID);
    }
  }

  editor_updateUI(objname);
}

/* ---------------------------------------------------------------------- *\
  Function    : editor_updateUI
  Description : update button status, selected fonts, and hidden output field.
  Usage       :
  Arguments   : objname - ID of textarea to replace
                runDelay: -1 = run now, no matter what
                          0  = run now, if allowed
                        1000 = run in 1 sec, if allowed at that point
\* ---------------------------------------------------------------------- */

function editor_updateUI(objname,runDelay) {
  var editor_obj  = document.all["_" +objname+  "_editor"];       // html editor object
  if (runDelay == null) { runDelay = 0; }
  var editdoc, editEvent;
  
  // setup timer for delayed updates (some events take time to complete)
  if (runDelay > 0) { return setTimeout(function(){ editor_updateUI(objname); }, runDelay); }

  // don't execute more than 3 times a second (eg: too soon after last execution)
  if (this.tooSoon == 1 && runDelay >= 0) { this.queue = 1; return; } // queue all but urgent events
  this.tooSoon = 1;
  setTimeout(function(){
    this.tooSoon = 0;
    if (this.queue) { editor_updateUI(objname,-1); };
    this.queue = 0;
    }, 333);  // 1/3 second

  // check editor mode and update hidden output field
  if (editor_obj.tagName.toLowerCase() == 'textarea') {                       // textedit mode
    document.all[objname].value = editor_obj.value;                           // update hidden output field
    return;
  } else {                                                                 // WYSIWYG mode
    editdoc = editor_obj.contentWindow.document;                          // get iframe editor document object
    editEvent = editor_obj.contentWindow.event;
    _fix_placeholder_urls(editdoc);
    document.all[objname].value = editdoc.body.innerHTML;                     // update hidden output field
  }

  // update button states
  var IDList = Array('Bold','Italic','Underline','JustifyLeft','JustifyCenter','JustifyRight','InsertOrderedList','InsertUnorderedList');
  for (i=0; i<IDList.length; i++) {                                 // for each button
    var button_obj = document.all["_" +objname+ "_" +IDList[i]];    // get button object
    if (button_obj == null) { continue; }                           // if no btn obj???
    var cmdActive = editdoc.queryCommandState( IDList[i] );

    if (!cmdActive)  {                                  // option is OK
      if (button_obj.className != 'btn') { button_obj.className = 'btn'; }
      if (button_obj.disabled  != false) { button_obj.disabled = false; }
    } else if (cmdActive)  {                            // option already applied or mixed content
      if (button_obj.className != 'btnDN') { button_obj.className = 'btnDN'; }
      if (button_obj.disabled  != false)   { button_obj.disabled = false; }
    }

  }

  // Loop over font pulldowns
  var IDList = Array('FontName','FontSize');
  for (i=0; i<IDList.length; i++) {
    var cmdActive = editdoc.queryCommandState( IDList[i] );
    var button_obj = document.all["_" +objname+ "_" +IDList[i]];   // button object
    button_obj.disabled = false;
  }

  // Get Font Name and Size
  var fontname = editdoc.queryCommandValue('FontName');
  var fontsize = editdoc.queryCommandValue('FontSize');
  if (fontname != null) { fontname = fontname.toLowerCase(); }

  // Set Font face pulldown
  var fontname_obj = document.all["_" +objname+ "_FontName"];
  if (fontname == null) { fontname_obj.value = fontname; }
  else {
    var foundfont;
    var fonts = fontname_obj.length;
    for (i=0; i<fonts; i++) {
      var thisfont = fontname_obj[i].text.toLowerCase();
      if (thisfont == fontname) {
        fontname_obj.selectedIndex = i;
        foundfont = 1;
      }
    }
    if (foundfont != 1) { fontname_obj.value = fontname; }     // for fonts not in list
  }

  // Set Font size pulldown
  var fontsize_obj = document.all["_" +objname+ "_FontSize"];
  if (fontsize == null) { fontsize_obj.value = fontsize;}
  else {
    for (i=0; i<7; i++) {
      var thissize = fontsize_obj[i].text;
      if (thissize == fontsize) { fontsize_obj.selectedIndex = i; }
    }
  }
}

/* ---------------------------------------------------------------------- *\
  Function    : editor_setmode
  Description : change mode between WYSIWYG and HTML editor
  Usage       : editor_setmode(object_id, mode);
  Arguments   : button_id - button id string with editor and action name
                mode      - init, textedit, or wysiwyg
\* ---------------------------------------------------------------------- */

function editor_setmode(button_id, mode) {

  var BtnParts = Array();
  BtnParts = button_id.split("_");
  var objname     = button_id.replace(/^_(.*)_[^_]*$/, '$1');
  var cmdID       = BtnParts[ BtnParts.length-1 ];
  var editor_obj = document.all["_" +objname + "_editor"];
  var editdoc;    // set below


  // define different editors
  var TextEdit   = '<textarea ID="_' +objname + '_editor" style="width:' +editor_obj.style.width+ '; height:' +editor_obj.style.height+ '; margin-top: -1px; margin-bottom: -1px;"></textarea>';
  var RichEdit   = '<iframe ID="_' +objname+ '_editor"    style="width:' +editor_obj.style.width+ '; height:' +editor_obj.style.height+ ';"></iframe>';

  //
  // Switch to TEXTEDIT mode
  //

  if (mode == "textedit" || editor_obj.tagName.toLowerCase() == 'iframe') {
    editdoc = editor_obj.contentWindow.document;
    var contents = editdoc.body.createTextRange().htmlText;
    editor_obj.outerHTML = TextEdit;
    editor_obj = document.all["_" +objname + "_editor"];
    editor_obj.value = contents;
    editor_updateUI(objname);

    // disable buttons
    var IDList = Array('Bold','Italic','Underline','StrikeThrough','SubScript','SuperScript','JustifyLeft','JustifyCenter','JustifyRight','InsertOrderedList','InsertUnorderedList','Outdent','Indent','ForeColor','BackColor','InsertHorizontalRule','CreateLink','InsertImage');
    for (i=0; i<IDList.length; i++) {                                // for each button
      var button_obj = document.all["_" +objname+ "_" +IDList[i]];   // get button object
      if (button_obj == null) { continue; }                          // if no btn obj???
      button_obj.className = 'btnNA';
      button_obj.disabled = true;
    }

    // disable font pulldowns
    var IDList = Array('FontName','FontSize');
    for (i=0; i<IDList.length; i++) {
      var button_obj = document.all["_" +objname+ "_" +IDList[i]];   // button object
      if (button_obj == null) { continue; }                          // if no btn obj???
      button_obj.disabled = true;
    }

    // set event handlers
    editor_obj.onkeypress  = function() { editor_updateUI(objname); }
    editor_obj.onkeyup     = function() { editor_updateUI(objname); }
    editor_obj.onmouseup   = function() { editor_updateUI(objname); }
    editor_obj.ondrop      = function() { editor_updateUI(objname, 100); }     // these events fire before they occur
    editor_obj.oncut       = function() { editor_updateUI(objname, 100); }
    editor_obj.onpaste     = function() { editor_updateUI(objname, 100); }
    editor_obj.onblur      = function() { editor_updateUI(objname, -1); }

    // update hidden output field
    document.all[objname].value = editor_obj.value;

    _editor_focus(editor_obj);
  }

  //
  // Switch to WYSIWYG mode
  //

  else {
    var contents = editor_obj.value;

    // create editor
    editor_obj.outerHTML = RichEdit;
    editor_obj = document.all["_" +objname + "_editor"];

    // get iframe document object
    editdoc    = editor_obj.contentWindow.document;

    // set editor contents (and default styles for editor)
    editdoc.open();
    editdoc.write(''
      + '<html><head>\n'
      + '<style>\n'
      + 'body { background-color: #FFFFFF; font-family: "Verdana"; font-size: x-small; } \n'
      + '</style>\n'
      + '</head>\n'
      + '<body contenteditable="true" topmargin=1 leftmargin=1>'
      + contents
      + '</body>\n'
      + '</html>\n'
      );
    editdoc.close();

    // enable buttons
    var IDList = Array('Bold','Italic','Underline','StrikeThrough','SubScript','SuperScript','JustifyLeft','JustifyCenter','JustifyRight','InsertOrderedList','InsertUnorderedList','Outdent','Indent','ForeColor','BackColor','InsertHorizontalRule','CreateLink','InsertImage');
    for (i=0; i<IDList.length; i++) {
      var button_obj = document.all["_" +objname+ "_" +IDList[i]];
      if (button_obj == null) { continue; }
      button_obj.className = 'btn';
      button_obj.disabled = false;
    }

    // set event handlers
    editdoc.onkeypress     = function() { editor_updateUI(objname); }
    editdoc.onkeyup        = function() { editor_updateUI(objname); }
    editdoc.onmouseup      = function() { editor_updateUI(objname); }
    editdoc.body.ondrop    = function() { editor_updateUI(objname, 100); }     // these events fire before they occur
    editdoc.body.oncut     = function() { editor_updateUI(objname, 100); }
    editdoc.body.onpaste   = function() { editor_updateUI(objname, 100); }
    editdoc.body.onblur    = function() { editor_updateUI(objname, -1); }

    // set initial value
    editor_obj.onload      = function() { editdoc.body.innerHTML = document.all[objname].value; }

    // update hidden output field
    _fix_placeholder_urls(editdoc);
    document.all[objname].value = editdoc.body.innerHTML;                     // update hidden output field

    // bring focus to editor
    if (mode != 'init') {             // don't focus on page load, only on mode switch
      _editor_focus(editor_obj);
    }

  }

  // Call update UI
  if (mode != 'init') {             // don't update UI on page load, only on mode switch
    editor_updateUI(objname);
  }

}

/* ---------------------------------------------------------------------- *\
  Function    : _editor_focus
  Description : bring focus to the editor
  Usage       : editor_focus(editor_obj);
  Arguments   : editor_obj - editor object
\* ---------------------------------------------------------------------- */

function _editor_focus(editor_obj) {

  // check editor mode
  if (editor_obj.tagName.toLowerCase() == 'textarea') {         // textarea
    var myfunc = function() { editor_obj.focus(); };
    setTimeout(myfunc,100);                                     // doesn't work all the time without delay
  }

  else {                                                        // wysiwyg
    var editdoc = editor_obj.contentWindow.document;            // get iframe editor document object
    var editorRange = editdoc.body.createTextRange();           // editor range
    var curRange    = editdoc.selection.createRange();          // selection range

    if (curRange.length == null &&                              // make sure it's not a controlRange
        !editorRange.inRange(curRange)) {                       // is selection in editor range
      editorRange.collapse();                                   // move to start of range
      editorRange.select();                                     // select
      curRange = editorRange;
    }
  }

}

/* ---------------------------------------------------------------------- *\
  Function    : _dec_to_rgb
  Description : convert dec color value to rgb hex
  Usage       : var hex = _dec_to_rgb('65535');   // returns FFFF00
  Arguments   : value   - dec value
\* ---------------------------------------------------------------------- */

function _dec_to_rgb(value) {
  var hex_string = "";
  for (var hexpair = 0; hexpair < 3; hexpair++) {
    var byte = value & 0xFF;            // get low byte
    value >>= 8;                        // drop low byte
    var nybble2 = byte & 0x0F;          // get low nybble (4 bits)
    var nybble1 = (byte >> 4) & 0x0F;   // get high nybble
    hex_string += nybble1.toString(16); // convert nybble to hex
    hex_string += nybble2.toString(16); // convert nybble to hex
  }
  return hex_string.toUpperCase();
}

/* ---------------------------------------------------------------------- *\
  Function    : _fix_placeholder_urls
  Description : editor make relative urls absolute, this change them back
                if the url contains a placeholder ("***")
  Usage       : _fix_placeholder_urls(editdoc)
  Arguments   : editdoc - reference to editor document
\* ---------------------------------------------------------------------- */

function _fix_placeholder_urls(editdoc) {
  var i;

  // for links
  for (i=0; i < editdoc.links.length; i++) {
    editdoc.links[i].href = editdoc.links[i].href.replace(/^[^*]*(\*\*\*)/, "$1");
  }

  // for images
  for (i=0; i < editdoc.images.length; i++) {
    editdoc.images[i].src = editdoc.images[i].src.replace(/^[^*]*(\*\*\*)/, "$1");
  }

}

/* ---------------------------------------------------------------------- *\
  Function    : editor_insertHTML
  Description : insert string at current cursor position in editor.  If
                two strings are specifed, surround selected text with them.
  Usage       : editor_insertHTML(objname, str1, [str2], reqSelection)
  Arguments   : objname - ID of textarea
                str1 - HTML or text to insert
                str2 - HTML or text to insert (optional argument)
                reqSelection - (1 or 0) give error if no text selected
\* ---------------------------------------------------------------------- */

function editor_insertHTML(objname, str1,str2, reqSel) {
  var editor_obj = document.all["_" +objname + "_editor"];    // editor object
  if (str1 == null) { str1 = ''; }
  if (str2 == null) { str2 = ''; }

  // for non-wysiwyg capable browsers just add to end of textbox
  if (document.all[objname] && editor_obj == null) {
    document.all[objname].focus();
    document.all[objname].value = document.all[objname].value + str1 + str2;
    return;
  }

  // error checking  
  if (editor_obj == null) { return alert("Unable to insert HTML.  Invalid object name '" +objname+ "'."); }

  _editor_focus(editor_obj);

  var tagname = editor_obj.tagName.toLowerCase();
  var sRange;

 // insertHTML for wysiwyg iframe
  if (tagname == 'iframe') {
    var editdoc = editor_obj.contentWindow.document;
    sRange  = editdoc.selection.createRange();
    var sHtml   = sRange.htmlText;

    // check for control ranges
    if (sRange.length) { return alert("Unable to insert HTML.  Try highlighting content instead of selecting it."); }

    // insert HTML
    var oldHandler = window.onerror;
    window.onerror = function() { alert("Unable to insert HTML for current selection."); return true; } // partial table selections cause errors
    if (sHtml.length) {                                 // if content selected
      if (str2) { sRange.pasteHTML(str1 +sHtml+ str2) } // surround
      else      { sRange.pasteHTML(str1); }             // overwrite
    } else {                                            // if insertion point only
      if (reqSel) { return alert("Unable to insert HTML.  You must select something first."); }
      sRange.pasteHTML(str1 + str2);                    // insert strings
    }
    window.onerror = oldHandler;
  }

  // insertHTML for plaintext textarea
  else if (tagname == 'textarea') {
    editor_obj.focus();
    sRange  = document.selection.createRange();
    var sText   = sRange.text;

    // insert HTML
    if (sText.length) {                                 // if content selected
      if (str2) { sRange.text = str1 +sText+ str2; }  // surround
      else      { sRange.text = str1; }               // overwrite
    } else {                                            // if insertion point only
      if (reqSel) { return alert("Unable to insert HTML.  You must select something first."); }
      sRange.text = str1 + str2;                        // insert strings
    }
  }
  else { alert("Unable to insert HTML.  Unknown object tag type '" +tagname+ "'."); }

  // move to end of new content
  sRange.collapse(false); // move to end of range
  sRange.select();        // re-select

}
</script>
