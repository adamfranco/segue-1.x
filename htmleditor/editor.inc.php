<title>Easy Editor</title>

<?

/* $smalltext = "hello!"; */

/* $body = urldecode($text); */

function printc($t) { print $t . "\n"; }


/* print "e: $element<BR>"; */
/* printc("<form method=post name=storyform>"); */
/* if ($smalltext) printc($smalltext); */
/* if ($longertext) printc("<hr>".$longertext); */
printEditor(1,"body");
/* printc("</form>"); */
/* printc("<script lang='javascript'>Init1();Init2();</script>"); */

function printEditor($n,$bodyvar) {
	global $$bodyvar,$element;
	printc('<script language="javascript">');
	printc("<!--");
	printc("function Init$n() {");
	printc("	iView$n.document.open();");
/* 	printc("	iView$n.document.write(\"".addslashes (STR_REPLACE("\r\n","",$$bodyvar))."\");"); */
	printc("	iView$n.document.write(opener.document.storyform.$element.value);");
	printc("	iView$n.document.close();");
	printc("	iView$n.document.designMode=\"On\";");
	printc("}");
	
	printc("function sendForm$n() {  ");
	printc("		var htmlCode = iView$n.document.body.innerHTML; ");
/* 	printc("		document.storyform.$bodyvar.value = htmlCode; "); */
	printc("		opener.document.storyform.$element.value=htmlCode;");
	printc("		opener.document.storyform.texttype.value='html';");
/* 	printc("		document.storyform.submit();"); */
	printc("}	");
	
	printc("function readyDiv$n() {");
	printc("var theHTML;");
	printc("theHTML = document.all.tags('div')['pageHTML'].innerText;");
	printc("document.all.tags('div')['pageHTML'].innerHTML = theHTML;");
	printc("}");
	
	printc("function cmdExec$n(cmd,opt) {");
	printc("iView$n.document.execCommand(cmd,'',opt);");
	printc("iView$n.focus();");
	printc("}");
	
/* 	printc("function createLink() {"); */
/* 	printc("//cmdExec('CreateLink');"); */
/* 	printc("cmdExec('CreateLink','http://www.toto.com');"); */
	
/* 	printc("}"); */
	
/* 	printc("function doUpload(pictureName) {"); */
/* 	printc("	imgSrc = '". SITE_URL ."/data/'+pictureName ;"); */
/* 	printc("	iView$n.document.execCommand('InsertImage',false,imgSrc);"); */
/* 	printc("}"); */

	
	printc("//-->");
	printc("</script>");
	printc("");
	printc('<input type="hidden" name="'.$bodyvar.'" value="">');
	printc('  <table width="600" border="0" cellspacing="0" cellpadding="1" align="center">');
	printc('    <tr>');
	printc('      <td bgcolor="#AAAAAA">');
	printc('      <table width="100%" cellpadding="3" cellspacing="2" align="center" bgcolor="#CCCCCC" bordercolor="#000000" >');
	printc('        <tr> ');
	printc('          <td align="center"> ');
	printc('            <table cellpadding="0" cellspacing="0" border="1" bordercolor="#CCCCCC">');
	printc('              <tr> ');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'cut\')"> <img src="img/cut.gif" alt="CUT - Ctrl + X" width="20" height="20"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center" width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';">  ');
	printc('                  <div onClick="cmdExec'.$n.'(\'copy\')"> <img src="img/copy.gif" alt="COPY - Ctrl + C" width="20" height="20"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'paste\')"> <img src="img/paste.gif" alt="PASTE - Ctrl + V"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'bold\')"> <img src="img/bold.gif" alt="BOLD - Ctrl + B"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'italic\')"> <img src="img/italic.gif" alt="ITALIC - Ctrl + I"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'underline\')"> <img src="img/underline.gif" alt="UNDERLINE - Ctrl + U"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'StrikeThrough\')"> <img src="img/Strikethrough.gif" alt="STRIKE THROUGH"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'InsertHorizontalRule\')"> <img src="img/hr.gif" alt="INSERT HORIZONTAL RULE"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'justifyleft\')"> <img src="img/left.gif" alt="Justify Left"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'justifycenter\')"> <img src="img/center.gif" alt="Center"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'justifyright\')"> <img src="img/right.gif" alt="Justify Right"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'insertorderedlist\')"> <img hspace="2" vspace="1" src="img/numlist.gif" alt="Ordered List" width="20" height="20"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'insertunorderedlist\')"> <img hspace="2" vspace="1" src="img/bullist.gif" alt="Unordered List" width="20" height="20"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'outdent\')"> <img hspace="2" vspace="1" src="img/unindent.gif" alt="Decrease Indent"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'indent\')"> <img hspace="2" vspace="1" src="img/indent.gif" alt="Increase Indent"> ');
	printc('                  </div>');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'createLink\')"> <img hspace="2" vspace="1" src="img/link.gif" alt="LINK - Ctrl + K"> ');
	printc('                  </div>  ');
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
/* 	printc('                  <div onClick="window.open(\'../data/upload.php\',\'Upload\',\'width=350,height=50\')"> <img hspace="2" vspace="1" align="absmiddle" src="img/image.gif" alt="Image" width="20" height="20"> '); */
/* 	printc('                  </div>'); */
	printc('                </td>');
	printc('                <td valign="middle" align="center"  width="20" height="20" onMouseOver="this.bgColor=\'AAAAAA\';" onMouseOut="this.bgColor=\'#CCCCCC\';"> ');
	printc('                  <div onClick="cmdExec'.$n.'(\'Undo\')"> <img hspace="0" vspace="0" src="img/Undo.gif" alt="UNDO LAST CHANGE"> ');
	printc('                  </div>  ');
	printc('                </td>');
	printc('              </tr>');
	printc('              <tr valign="middle"> ');
	printc('                <td colspan="18" width="100%" align="center"> ');
	printc('                  <select onChange="cmdExec'.$n.'(\'foreColor\',this[this.selectedIndex].value);this.selectedIndex=0">');
	printc('                    <option value="0">color</option>');
	printc('                    <option value="#000000">black</option>');
	printc('                    <option value="#FF0000">red</option>');
	printc('                    <option value="blue">blue</option>');
	printc('		    <option value="green">green</option>');
	printc('		    <option value="yellow">yellow</option>');
	printc('                  </select>');
	printc('                  &nbsp; ');
	printc('                  <select onChange="cmdExec'.$n.'(\'fontname\',this[this.selectedIndex].value);">');
	printc('                    <option selected>-Choose Font-</option>');
	printc('                    <option value="Arial">Arial</option>');
	printc('                    <option value="Times New Roman">Times New Roman</option>');
	printc('                    <option value="Verdana">Verdana</option>');
	printc('                  </select>');
	printc('                  &nbsp; ');
	printc('                  <select onChange="cmdExec'.$n.'(\'fontsize\',this[this.selectedIndex].value);">');
	printc('                    <option selected>-size-</option>');
	printc('                    <option value="1">1</option>');
	printc('                    <option value="2">2</option>');
	printc('                    <option value="3">3</option>');
	printc('                    <option value="4">4</option>');
	printc('                    <option value="5">5</option>');
	printc('                    <option value="6">6</option>');
	printc('                    <option value="7">7</option>');
	printc('                    <option value="8">8</option>');
	printc('                  </select>');
	printc('                  </td>');
	printc('              </tr>');
	printc('            </table>');
	printc('          </td>');
	printc('        </tr>');
	printc('        <tr> ');
	printc('          <td align="center"> ');
	printc('            <iframe id="iView'.$n.'" style="width: 570px; height:365px"></iframe>');
	printc('          </td>');
	printc('        </tr>');
	printc('        <tr> ');
/* 	printc('          <td align="center">&nbsp;</td>'); */
	printc("			<td align=right><input type=button value='Done' onClick='sendForm1();window.close();'></td>");
	printc('        </tr>');
	printc('      </table>');
	printc('      </td>');
	printc('    </tr>');
	printc('  </table>');
	printc("<script lang=javascript>Init1();</script>");
/* 	printc('  <input type=button value=\'init\' onClick=\'Init();\'>'); */
}