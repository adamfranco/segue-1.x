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
//	document.forms[0].submit();
//	document.forms['addform'].submit();
	document.addform.submit();	// IE for Mac does not like this reference.
}

function delEditor(n) {
	f = document.addform;
	f.edaction.value = 'del';
	f.edname.value = n;
	document.forms["addform"].submit();
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
.contentinfo {font-size: 10px;}
.content {padding-bottom: 5px;}

.red {color: #900;}
.green {color: #070;}

</style>
