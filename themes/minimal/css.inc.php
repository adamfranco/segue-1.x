<style type='text/css'>

body {
	 background-color: #<? echo $bg['bg']; ?>; 
}


<? 

if (!defined("CONFIGS_INCLUDED"))
	die("Error: improper application flow. Configuration must be included first.");

include("$themesdir/common/css.inc.php"); ?>

/* ------------------------------------------ */
/* THEME-SPECIFIC CLASSES */
/* ------------------------------------------ */


.leftnav {
/* 	float: left; */
	border-right: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
	width: <? echo $navwidth; ?>px;
	padding: 5px;
	vertical-align: top;
}
.rightnav {
/* 	float: left; */
	border-left: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
	width: <? echo $navwidth; ?>px;
	padding: 5px;
	vertical-align: top;
}


.header {
	margin-bottom: 0px;
	background: #<? echo $bg['bg']; ?>;	
}

.heading {
	border-top: 1px solid #<? echo $bordercolor; ?>; 
	border-bottom: 1px solid #<? echo $bordercolor; ?>; 
	padding-top: 2px; padding-bottom: 2px;
}

.heading2 {
	border-top: 1px solid #<? echo $bordercolor; ?>; 
	padding-top: 2px; padding-bottom: 2px;
}


.topnav {
	padding: 5px;
	background: #<? echo $bg['bg']; ?>;

}

.contentarea {
	vertical-align: top;
	padding: 10px;
	background-color: #<? echo $c['contentarea']; ?>;
	border-bottom: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
	border-top: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
//	border-right: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
//	border-left: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;


}

.contenttable {
	background-color: #<? echo $c['contentarea']; ?>;
	border-right: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;
	border-left: 1px <? echo $borders; ?> #<? echo $bordercolor; ?>;

}


.content {
	padding: 10px;

}

.status {
	padding: 5px;
	margin-right: 5px;

	background: #<? echo $bg['bg']; ?>;
}

.subject { 
	font-weight: bolder; 
}

.dtext {
	padding-top: 0px;
	padding-bottom: 20px;
	padding-left: 0px;
	padding-right: 0px;
}

.dheader {
	font-size: 14px;
	border-top: 1px solid #<? echo $bordercolor; ?>;
	border-bottom: 1px solid #<? echo $bordercolor; ?>;
	background: #<? echo $bg['bg']; ?>;
	padding-left: 5px;
	padding-top: 5px;
}

.dheader2 {
	border-top: 1px solid #<? echo $bordercolor; ?>;
	border-bottom: 1px solid #<? echo $bordercolor; ?>;
	background: #<? echo $bg['bg']; ?>;
	padding-right: 5px;
	padding-top: 5px;
}

.dheader3 {
	border: 1px solid #<? echo $bordercolor; ?>;
	background: #<? echo $bg['bg']; ?>;
	padding-left: 5px;
	padding-right: 5px;
}

.dinfo1 {
	border: 1px solid #000;
	padding-left: 2px;
	padding-right: 2px;
}


</style>