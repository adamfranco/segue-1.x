<script type='text/javascript'>
// <![CDATA[


function addTag(tag) { 
	o = document.addform; 
	tags = o.story_tags.value;
	while (tags.substring(tags.length-1, tags.length) == ' ') {
		tags = tags.substring(0,tags.length-1);
	}	
	tags = tags + " " + tag + " ";
	o.story_tags.value=tags; 
	document.addform.submit();
} 

function deleteTag(tag) { 
	o = document.addform; 
	tags = o.story_tags.value;
	newtags = tags.replace(tag,"");
	o.story_tags.value=newtags;
	o.tag_update.value=1;
	document.addform.submit();
} 


function doconfirm(text,url) {
	if (confirm(text)) document.location = url;
}

function doWindow(name,width,height) {
	var win = window.open("",name,"toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width="+width+",height="+height);
	win.focus();
}

function sendWindow(name,width,height,url) {
	var win = window.open("",name,"toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width="+width+",height="+height);
	win.document.location=url.replace(/&amp;/, '&');
	win.focus();
}

function typeChange() {
	if (document.addform) f = document.addform;
	else f = document.storyform;
	f.typeswitch.value=1;
	f.submit();
}

function submitForm() {
	if (typeof(window.editor) != "undefined") {
		window.editor._textArea.value = editor.getHTML();
	}
	document.addform.submit();
}

function submitFormLink(step) {
	if (typeof(window.editor) != "undefined") {
		window.editor._textArea.value = editor.getHTML();
	}
	document.addform.step.value = step;
	document.addform.submit();
}

function submitPrevButton() {
	document.addform.prevbutton.value = '1';
	if (typeof(window.editor) != "undefined") {
		window.editor._textArea.value = editor.getHTML();
	}
	document.addform.submit();
}

function submitNextButton() {
	document.addform.nextbutton.value = '1';
	if (typeof(window.editor) != "undefined") {
		window.editor._textArea.value = editor.getHTML();
	}
	document.addform.submit();
}

function cancelForm() {
	document.addform.cancel.value = '1';
	if (typeof(window.editor) != "undefined") {
		window.editor._textArea.value = editor.getHTML();
	}
	document.addform.submit();
}

function delEditor(n) {
	if (confirm('ALERT: Removing an editor will completely remove all their permissions from every part of your site! If you wish to revoke privileges for this part only, uncheck all the associated boxes instead of removing them. Continue if you are sure you want to remove all privileges for this user.')) {		
		f = document.addform;
		f.edaction.value = 'del';
		f.edname.value = n;
		document.forms["addform"].submit();
	}
}

/*
    Copyright Robert Nyman, http://www.robertnyman.com
    Free to use if this text is included
*/
function getElementsByAttribute(oElm, strTagName, strAttributeName, strAttributeValue){
    var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
    var arrReturnElements = new Array();
    var oAttributeValue = (typeof strAttributeValue != "undefined")? new RegExp("(^|\\s)" + strAttributeValue + "(\\s|$)") : null;
    var oCurrent;
    var oAttribute;
    for(var i=0; i<arrElements.length; i++){
        oCurrent = arrElements[i];
        oAttribute = oCurrent.getAttribute(strAttributeName);
        if(typeof oAttribute == "string" && oAttribute.length > 0){
            if(typeof strAttributeValue == "undefined" || (oAttributeValue && oAttributeValue.test(oAttribute))){
                arrReturnElements.push(oCurrent);
            }
        }
    }
    return arrReturnElements;
}

// ]]>
</script>
<style type="text/css">
.desc {
	padding-left: 20px;
	font-size: 11px;
	margin-bottom: 5px;
}

input.small {
	font-size: 10px;
	vertical-align: middle;	
}

.small { font-size: 10px; }

.title {
	font-size: 16px;
}

.smaller {font-size: smaller;}
.contenttitle {font-weight: bolder;}
.contentinfo {
	margin-top: 10px;
	font-size: 10px;
}

.content {padding-bottom: 5px;}

.red {color: #900;}
.green {color: #070;}

</style>
