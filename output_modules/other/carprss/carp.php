<?php
/*
CaRP GPL v3.5.2
Copyright (c) 2002-4 Antone Roundy

This program may be copied, modified and redistributed under the terms of the
GNU General Public License. This program is distributed with NO WARRANTY
WHATSOEVER, including the implied warranty of merchantability or fitness for
a particular purpose.
See http://www.gnu.org/copyleft/gpl.html for details.

A commercial version of the program with additional features is available
via our website.

http://www.mouken.com/rss/
Installation & Configuration Manual: http://www.mouken.com/rss/manual/
Also available as a remotely hosted service for sites that cannot run
scripts. See http://www.mouken.com/rss/jawfish/
*/

$carpversion='3.5.2GPL';

function CarpConfReset() {
	global $carpconf;
	$carpconf=array(

	'cachepath'=>'',
	'cacherelative'=>1,
	'cacheinterval'=>60,
	'cacheerrorwait'=>30,
	'cachetime'=>'',
	
	'carperrors'=>1,
	'phperrors'=>E_ERROR,

	'basicauth'=>'',
	'proxyauth'=>'',
	'proxyserver'=>'',

	'filterin'=>'',
	'filterout'=>'',
	'skipdups'=>1,

	'descriptiontags'=>'b|/b|i|/i|br|p|/p|hr|span|/span|font|/font|a|/a|img|table|/table|tr|/tr|td|/td|tbody|/tbody',
	'linktarget'=>0,

	// replaced by cborder, bcb and acb - these will be removed eventually
	'corder'=>false,
	'bc'=>false,
	'ac'=>false,
	
	'cborder'=>'image,link,desc',
	'bcb'=>'',
	'acb'=>'<br />',
	'caorder'=>'',
	'bca'=>'',
	'aca'=>'<br />',
	'bcurl'=>'',
	'acurl'=>'<br />',
	'bctitle'=>'',
	'actitle'=>'<br />',
	'clinkclass'=>'',
	'clinkstyle'=>'',
	'clinktitles'=>1,
	'maxctitle'=>80,
	'atruncctitle'=>'...',
	'bcdate'=>'<i>',
	'acdate'=>'</i><br />',
	'cdateformat'=>'j M Y \a\t g:ia',
	'bcdesc'=>'',
	'acdesc'=>'<br />',
	'maxcdesc'=>0,
	'atrunccdesc'=>'...',
	'bcimage'=>'',
	'acimage'=>'<br />',
	'maxcimagew'=>144,
	'maxcimageh'=>400,
	'defcimagew'=>0,
	'defcimageh'=>0,
	'setcimagew'=>0,
	'setcimageh'=>0,

	'maxitems'=>15,
	'noitems'=>'No news items found',
	'shownoitems'=>0,
	'iorder'=>'image,link,author,date,desc',
	'bitems'=>'',
	'aitems'=>'',
	'bi'=>'',
	'ai'=>'',
	'biurl'=>'',
	'aiurl'=>'<br />',
	'bilink'=>'',
	'ailink'=>'<br />',
	'ilinkclass'=>'',
	'ilinkstyle'=>'',
	'ilinktitles'=>1,
	'defaultititle'=>'(no title)',
	'maxititle'=>80,
	'atruncititle'=>'...',
	'biauthor'=>'<i>by ',
	'aiauthor'=>'</i><br />',
	'bidate'=>'<i>',
	'aidate'=>'</i><br />',
	'idateformat'=>'j M Y \a\t g:ia',
	'bidesc'=>'',
	'aidesc'=>'<br />',
	'maxidesc'=>0,
	'atruncidesc'=>'<i>... continues</i>',
	'biimage'=>'',
	'aiimage'=>'<br />',
	'maxiimagew'=>144,
	'maxiimageh'=>400,
	'defiimagew'=>0,
	'defiimageh'=>0,
	'setiimagew'=>0,
	'setiimageh'=>0,

	'encodingin'=>'',
	'encodingout'=>'utf-8',
	'maxredir'=>10,
	'timeout'=>15,
	'sendhost'=>1,
	'removebadtags'=>1,
	'outputformat'=>0,
	
	'cachefunctions'=>array('CarpAggregatePath','CarpCachePath','CarpAutoCachePath'),

	// the following are both replaced by linktarget and will be removed eventually
	'clinktarget'=>-1,
	'ilinktarget'=>-1,

	/* If you change poweredby, we'd appreciate a link from elsewhere on your
	site. If you incorporate CaRP into another product and change it, please
	ensure that a link is shown by default somewhere. Thanks! */

	'poweredby'=>'<br /><i><a href="http://www.mouken.com/rss/" target="_blank">Newsfeed display by CaRP</a></i>'
	);
	
}

function CarpConf($n,$v) {
	global $carpconf;
	if (is_null($v)) $v='';
	$n=explode('|',strtolower(str_replace(' ','',$n)));
	for ($i=count($n)-1;$i>=0;$i--) {
		if (isset($carpconf[$n[$i]])) $carpconf[$n[$i]]=$v;
		else CarpError("Unknown option ($n[$i]). Please check the spelling of the option name and that the version of CaRP you are using supports this option.",0);
	}
}

function CarpConfAdd($n,$v,$ba=1) {
	global $carpconf;
	$n=explode('|',strtolower(str_replace(' ','',$n)));
	for ($i=count($n)-1;$i>=0;$i--) {
		if (isset($carpconf[$n[$i]])) {
			if ($ba) $carpconf[$n[$i]].=$v;
			else $carpconf[$n[$i]]=$v.$carpconf[$n[$i]];
		} else CarpError("Unknown option ($n[$i]). Please check the spelling of the option name and that the version of CaRP you are using supports this option.",0);
	}
}

function CarpConfRemove($n,$v) {
	global $carpconf;
	$n=explode('|',strtolower(str_replace(' ','',$n)));
	for ($i=count($n)-1;$i>=0;$i--) {
		if (isset($carpconf[$n[$i]])) $carpconf[$n[$i]]=str_replace($v,'',$carpconf[$n[$i]]);
		else CarpError("Unknown option ($n[$i]). Please check the spelling of the option name and that the version of CaRP you are using supports this option.",0);
	}
}


function CarpDirName() { return str_replace("\\","/",dirname(__file__)); }


function CarpOutput($t) {
	global $carpconf,$carpoutput;

	if (is_array($t)) { for ($i=0,$j=count($t);$i<$j;$i++) $t[$i]=ereg_replace("&apos;","'",$t[$i]); }
	else $t=ereg_replace("&apos;","'",$t);
	switch ($carpconf['outputformat']) {
	case 1:
		if (!is_array($t)) $t=explode("\n",$t);
		for ($i=0,$j=count($t);$i<$j;$i++) echo 'document.writeln("'.str_replace('"','\"',trim($t[$i]))."\");\n";
		break;
	case 2:
		if (is_array($t)) for ($i=0,$j=count($t);$i<$j;$i++) $carpoutput.=$t[$i];
		else $carpoutput.=$t;
		break;
	default:
		if (is_array($t)) for ($i=0,$j=count($t);$i<$j;$i++) echo $t[$i];
		else echo $t;
	}
}

function CarpError($s,$c=1) {
	global $carpconf;
	if ($carpconf['carperrors']) CarpOutput("<br />\n[CaRP] $s<br />\n");
	if ($c&&$carpconf['cacheerrorwait']&&strlen($carpconf['cachefile']))
		touch($carpconf['cachefile'],time()+60*($carpconf['cacheerrorwait']-$carpconf['cacheinterval']));
}

function CarpDataPath() {
	global $carpconf;
	return preg_replace("#/{2,}#",'/',($carpconf['cacherelative']?(CarpDirName().'/'):'').$carpconf['cachepath'].'/');
}

function CarpAggregatePath() { return CarpDataPath().'aggregatecache/'; }
function CarpCachePath() { return CarpDataPath().'manualcache/'; }
function CarpAutoCachePath() { return CarpDataPath().'autocache/'; }

function CarpClearCache($which) {
	global $carpconf;
	if ($d=opendir($dir=call_user_func($carpconf['cachefunctions'][$which]))) {
		while (false!==($fn=readdir($d))) if (($fn!='.')&&($fn!='..')) unlink($dir.$fn);
	} else CarpError('Unable to access cache folder.',0);
}

function CarpClearCacheFile($which,$filename) {
	global $carpconf;
	unlink(call_user_func($carpconf['cachefunctions'][$which]).(($which==2)?md5($filename):$filename));
}

function CarpSetCache($cachefile,$cachefunction=1) {
	global $carpconf;
	$cache=0;
	$cachefile=preg_replace("/\.+/",'.',$cachefile);
	$carpconf['cachefile']=call_user_func($carpconf['cachefunctions'][$cachefunction]).$cachefile;
	if (file_exists($carpconf['cachefile'])) {
		$carpconf['mtime']=filemtime($carpconf['cachefile']);
		$nowtime=time();
		if (strlen($carpconf['cachetime'])) {
			list($hour,$min)=explode(':',$carpconf['cachetime']);
			$limtime=mktime($hour,$min,0);
			$cache=($carpconf['mtime']>$limtime-(($nowtime<$limtime)?86400:0))?1:2;
		} else $cache=(($nowtime-$carpconf['mtime'])<($carpconf['cacheinterval']*60))?1:2;
	} else $cache=2;
	return $cache;
}

function CarpCacheFilter($url,$cachefile) { CarpCacheShow($url,$cachefile,0); }
function CarpFilter($url,$cachefile) { CarpShow($url,$cachefile,0); }

function CarpCacheShow($url,$cachefile='',$showit=1, $user='everyone') {
	global $carpconf;
	CarpCache($url,$autocache=md5($url.$user),2);
	CarpShow(call_user_func($carpconf['cachefunctions'][2]).$autocache,$cachefile,$showit);
}

function CarpShow($url,$cachefile='',$showit=1) {
	global $carpconf,$carpoutput;

	if ($carpconf['phperrors']>=0) $carpconf['savephperrors']=error_reporting($carpconf['phperrors']);
	$carpoutput='';
	$cache=0;
	if (strlen($cachefile)) $cache=CarpSetCache($cachefile,$showit);
	else if (!$showit) {
		CarpError('No cache file indicated when calling CarpFilter or CarpShow with showit=0.',0);
		$carpconf['cachefile']='';
		if ($carpconf['phperrors']>=0) error_reporting($carpconf['savephperrors']);
		return 0;
	}
	if ($cache%2==0) {
		require_once CarpDirName().'/carpinc.php';
		GetRSSFeed($url,$cache,$showit);
	} else if ($showit) CarpOutput(file($carpconf['cachefile']));
	$carpconf['cachefile']='';
	if ($carpconf['phperrors']>=0) error_reporting($carpconf['savephperrors']);
}

function CarpAggregateSort($a,$b) {
	$na=floor($a);
	$nb=floor($b);
	return ($a==$b)?0:(($a>$b)?-1:1);
}

function CarpAggregate($feeds) {
	global $carpconf,$carpoutput;
	if ($carpconf['phperrors']>=0) $carpconf['savephperrors']=error_reporting($carpconf['phperrors']);
	$carpoutput='';
	$fl=explode('|',$feeds);
	$id=$il=$cb=$ca=$ci=array();
	$j=0;
	for ($i=count($fl)-1;$i>=0;$i--) {
		if ($f=fopen(call_user_func($carpconf['cachefunctions'][0]).$fl[$i],'r')) {
			for (;$l=fgets($f,10000);$j++) {
				list($datetime,$reserved,$data)=explode(':',$l,3);
				switch ($datetime) {
				case 'cb': $cb[$i]=$data; break;
				case 'ca': $ca[$i]=$data; break;
				default:
					$il[$j]=$data;
					$id["$datetime.$j"]=$j;
					$ci[$j]=$i;
				}
			}
			fclose($f);
		}
	}
	if ($carpconf['shownoitems']&&!count($id)) CarpOutput($carpconf['noitems']);
	else {
		uksort($id,'CarpAggregateSort');
		CarpOutput($carpconf['bitems']);
		for ($i=0;($i<$carpconf['maxitems'])&&(list($k,$v)=each($id));$i++)
			CarpOutput((isset($cb[$ci[$v]])?$cb[$ci[$v]]:'').$il[$v].(isset($ca[$ci[$v]])?$ca[$ci[$v]]:''));
		CarpOutput($carpconf['aitems'].$carpconf['poweredby']);
	}
	if ($carpconf['phperrors']>=0) error_reporting($carpconf['savephperrors']);
}

function CarpCache($url,$cachefile,$cachefunction=1) {
	global $carpconf;
	if ($carpconf['phperrors']>=0) $carpconf['savephperrors']=error_reporting($carpconf['phperrors']);
	if (strlen($cachefile)) {
		$cache=CarpSetCache($cachefile,$cachefunction);
		if ($cache%2==0) {
			require_once CarpDirName().'/carpinc.php';
			CacheRSSFeed($url);
			$carpconf['cachefile']='';
			if ($carpconf['phperrors']>=0) error_reporting($carpconf['savephperrors']);
			return 1;
		}
	} else CarpError('No cache file indicated when calling CarpCache.',0);
	$carpconf['cachefile']='';
	if ($carpconf['phperrors']>=0) error_reporting($carpconf['savephperrors']);
	return 0;
}

if (file_exists(CarpDirName()."/carpconf.php")) require_once CarpDirName()."/carpconf.php";
else CarpConfReset();
?>