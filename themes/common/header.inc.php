<script lang='JavaScript'>
function doconfirm(text,url) {
	if (confirm(text)) document.location = url;
}

function doWindow(name,width,height) {
	var win = window.open("",name,"toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width="+width+",height="+height);
	win.focus();
}

function sendWindow(name,width,height,url) {
	var win = window.open("",name,"toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width="+width+",height="+height);
	win.document.location=url;
	win.focus();
}

function typeChange() {
	if (document.addform) f = document.addform;
	else f = document.storyform;
	f.typeswitch.value=1;
	f.submit();
}

function submitForm() {
	document.addform.submit();
}

function submitFormLink(step) {
	document.addform.step.value = step;
	document.addform.submit();
}

function submitPrevButton() {
	document.addform.prevbutton.value = '1';
	document.addform.submit();
}

function submitNextButton() {
	document.addform.nextbutton.value = '1';
	document.addform.submit();
}

function cancelForm() {
	document.addform.cancel.value = '1';
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

</script>
<style type="text/css">
.desc {
	padding-left: 40px;
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

#contentinfo2 {
	margin-top: 0px;
	font-size: 10px;
}

.content {padding-bottom: 5px;}

.red {color: #900;}
.green {color: #070;}

</style>
