<? /* $Id$ */

require("objects/objects.inc.php");

ob_start();
session_start();

//output a meta tag
print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

// include all necessary files
include("includes.inc.php");
//printpre ($_REQUEST);

/******************************************************************************
 * actions: 
 * login -> auth
 * register -> newuser, 
 * reset -> send,
 * change -> newpassword
 ******************************************************************************/

if ($_REQUEST[reset] == "reset") $reset = $_REQUEST[reset];
//if ($_REQUEST[email]) $email = $_REQUEST[email];
//if ($_REQUEST[uname]) $uname = $_REQUEST[uname];

/******************************************************************************
 * newpassword - changes password
 ******************************************************************************/

if ($_REQUEST[action] == "newpassword") {
	$authtype = db_get_value("user","user_authtype","user_uname = '$_SESSION[auser]'");
	$db_pass = db_get_value("user","user_pass","user_uname = '$_SESSION[auser]'");
	if ($authtype != "db") {
		$message = "<div align=center>This password cannot be reset here.</div>";
	} else {
		$origPassValid = !strcmp($_REQUEST[oldpass],$db_pass);
		if ($origPassValid) {
			$oldpass = $_REQUEST[oldpass];
			$passwordsMatch = !strcmp($_REQUEST[newpass1],$_REQUEST[newpass2]);
			if ($passwordsMatch) {
				$validLength = ereg(".{8,200}",$_REQUEST[newpass1]);
				if ($validLength) {
					$validChars = !ereg("[\'\"]",$_REQUEST[newpass1]);
					if ($validChars) {
						$passwordGood = 1;
						$query = "UPDATE user SET user_pass='$_REQUEST[newpass1]' where user_uname='$_SESSION[auser]'";
						db_query($query);
					} else {
						unset($newpass1);
						unset($newpass2);
						$message = "<div align=center>New password contains prohibited characters</div>";
					}
				} else {
					unset($newpass1);
					unset($newpass2);
					$message = "<div align=center>New password is not between 8 and 200 characters</div>";
				}
			} else {
				unset($newpass1);
				unset($newpass2);
				$message = "<div align=center>New passwords don't match</div>";
			}
		} else {
			unset($oldpass);
			$message = "<div align=center>Your old password is invalid</div>";
		} // end origPassValid
	}// end authtype
	
	if ($passwordGood) {
		$message = "<div align=center>Your password has been changed<br><br></div>";
		$message .= "<div align=center><input type=button value='Return' onClick='window.close()'></div>";
	}

/******************************************************************************
 * send || newuser - generates a password and sends to existing or new user
 ******************************************************************************/
	
} else if ($_REQUEST[action] == "send" || $_REQUEST[action] == "newuser") {
	$name = $_REQUEST['uname'];
	$email = $_REQUEST['email'];
	
	$error = FALSE;
	if (!$_REQUEST['uname']) {
		$message = "<div align=center>You must enter a Name.<br><br></div>";
		$error = TRUE;
	} else if (!$_REQUEST['email'] || !ereg("@", $_REQUEST['email'])) {
		 $message = "<div align=center>You must enter a valid email address.<br><br></div>";
		 $error = TRUE;
	}
	

	foreach ($cfg[visitor_email_excludes] as $visitor_email_exclude) {
		if ($exclude = ereg($visitor_email_exclude, $email)) {
			if ($_REQUEST[action] == "send") {
				$message = "<div align=center>You cannot reset your $cfg[inst_name] account password here.<br></div>";
			} else {
				$message = "You cannot register your $cfg[inst_name] account here. ";
				$message .= "(<a href=passwd.php?$sid&action=login>Login</a>).<br><br>";
			}
			$id = 0;
			$error = TRUE;
			break;
		}
	}
	
	$id = db_get_value("user","user_id","user_email='$email'");
	if ($id) $authtype = db_get_value("user", "user_authtype", "user_id = $id");
	if ($id && $authtype != "db") {
		$error=TRUE;
		$message = "<div align=left>This user account is not authenticated by Segue and so you cannot reset this account's password here.<br></div>";
	}
	 	
	/******************************************************************************
	 * a matching user was found and email not excluded
	 ******************************************************************************/
	 
	if ($id > 0 && $error != TRUE) {
		
		if ($_REQUEST[action] == "send") {
			$obj = &new user();
			$obj->fetchUserID($id);
			$obj->randpass(5,3);
			$obj->updateDB();
			$obj->sendemail(1);
			$message = "<div align=left>A new random password has been sent to the email address you entered above.<br></div>";
		} else {
			$message = "<div align=left>Your email address is already in our database.";
			$message .= " (<a href=passwd.php?$sid&action=reset&email=$email>Forgot your password?</a>).</div>";
		}	
	
	/******************************************************************************
	 * reset password (send) no matching user found ...
	 ******************************************************************************/
	
	} else if ($_REQUEST[action] == "send" && $error != TRUE) {
		$message = "<div align=center>There is no visitor user account for this email address...<br></div>";
	
	/******************************************************************************
	 * register -> newuser: no matching user found therefore create new user
	 * and authenticate
	 ******************************************************************************/
	
	} else if ($_REQUEST[action] == "newuser" && $error != TRUE) {
		$name = $email;
		$obj = &new user();
		$obj->uname = $_REQUEST['email'];
		$obj->fname = $_REQUEST['uname'];
		$obj->email = $_REQUEST['email'];
		$obj->type = "visitor";
		$obj->authtype = 'db';
		$obj->randpass(5,3);
		$obj->insertDB();
		$obj->sendemail();
		$visitor_id = lastid();
		
		$valid = 0;
		foreach ($_auth_mods as $_auth) {
		$func = "_valid_".$_auth;
			//print "<BR>AUTH: trying ".$_auth ."..."; //debug
			if ($x = $func($name,"",1)) {
				$valid = 1;
				break;
			}
		}
		if ($valid) {
			$_SESSION[luser] = $x[user];
			$_SESSION[lemail] = $x[email];
			$_SESSION[lfname] = $x[fullname];
			$_SESSION[ltype] = $x[type];
			$_SESSION[lid] = $x[id];
			$_SESSION[lmethod] = $x[method];
			$_SESSION[auser] = $x[user];
			$_SESSION[aemail] = $x[email];
			$_SESSION[afname] = $x[fullname];
			$_SESSION[atype] = $x[type];
			$_SESSION[aid] = $x[id];
			$_SESSION[amethod] = $x[method];
		}
		$message = "Thank you for registering. Your user account information has been emailed to you.  Use this information to log into Segue.<br><br>";
		$message .= "<div align=center><input type=button value='Return' onClick='refreshParent()'></div>";

	}

/******************************************************************************
 * log in -> auth
 ******************************************************************************/

} else if ($_REQUEST[action] == "auth") {
	$name = $_REQUEST['uname'];
	$pass = $_REQUEST['password'];
	$valid = 0;
	foreach ($_auth_mods as $_auth) {
		$func = "_valid_".$_auth;
		//print "<BR>AUTH: trying ".$_auth ."..."; //debug
		if ($x = $func($name,$pass)) {
			$valid = 1;
			break;
		}
	}
	if ($valid) {
		$_SESSION[luser] = $x[user];
		$_SESSION[lemail] = $x[email];
		$_SESSION[lfname] = $x[fullname];
		$_SESSION[ltype] = $x[type];
		$_SESSION[lid] = $x[id];
		$_SESSION[lmethod] = $x[method];
		$_SESSION[auser] = $x[user];
		$_SESSION[aemail] = $x[email];
		$_SESSION[afname] = $x[fullname];
		$_SESSION[atype] = $x[type];
		$_SESSION[aid] = $x[id];
		$_SESSION[amethod] = $x[method];
		$message = "<div align=left>Your login information was correct. Use the Return button below to complete authentication.<br><br></div>";
		$message .= "<div align=center><input type=button value='Return' onClick='refreshParent()'></div>";
	} else {
		$message = "<div align=left>Your password/username was incorrect.<br>";
		//$message .= " (<a href=passwd.php?$sid&action=reset&email=$email>Forgot your password?</a>).</div>";
	}

}

?>
<html>
<head>
<title>
<?
if ($_REQUEST[action] == "reset" || $reset) {
	print "Reset Password";
} else if ($_REQUEST[action] == "login" || $auth) {
	print "Log In";
} else if ($_REQUEST[action] == "register" || $newuser) {
	print "Register";
} else if ($_REQUEST[action] == "change" || $change) {
	print "Change My Password";
}
?>
</title>
<? include("themes/common/logs_css.inc.php"); ?>
<script lang='JavaScript'>

function refreshParent() {
	window.opener.location.href = window.opener.location.href;
	if (window.opener.progressWindow) {
		window.opener.progressWindow.close();
	}
	window.close();
}

</script>
</head>
<body onLoad="document.passform.oldpass.focus()">
<?
//printerr2();
?>

<form action="<? echo $PHP_SELF ?>" method=post name="passform">
<table cellspacing=1 width='100%'>
<tr><th colspan=2>
	<? 
	if ($_REQUEST[action] == "reset" || $reset) {
		print "Forgot My Password";
	} else if ($_REQUEST[action] == "login" || $auth) {
		print "Log In";
	} else if ($_REQUEST[action] == "register" || $newuser) {
		print "Visitor Registratoin";
	} else if ($_REQUEST[action] == "change" || $change) {
		print "Change My Password";
	}
	?>	
</th></tr>

<?
/******************************************************************************
 * Change Password UI
 ******************************************************************************/

if ($_REQUEST[action] == "change" || $change) {
	if (!isset($_SESSION[ltype])) {
		print "<tr><td colspan=2 align=center> You must already be authenticated in order to change your password.<br>";
		print" <a href=passwd.php?$sid&action=reset&email=$email>Forgot your password?</a>";
		print "</td></tr>";
	} else {	
		print "<tr><td colspan=2 align=center> Chose a new password below.<br>";
		print "<br><div align=left>(".$cfg[auth_help].")</div><br>";
		print "<tr><td> User Name: </td>";
		print "<td><input type=text name='uname' size=30 value='".$_SESSION['auser']."' readonly></td>"; 
		print "</tr>";
		print "<tr><td>Full Name: </td>";
		print "<td><input type=text name='fname' size=30 value='".$_SESSION['afname']."' readonly></td>";
		print "</tr>";
		print "<tr><td>Email Address:</td>";
		print"<td><input type=text name='email' size=30 value='".$_REQUEST['email']."' readonly></td> ";
		print"<tr>";
		print"<td>Old Password:</td>";
		print"<td><input type=password name='oldpass' size=30 value='".$oldpass."'></td></tr>";
		print"<tr>";
		print"<td>New Password:</td>";
		print"<td><input type=password name='newpass1' size=30 value='".$newpass1."'> <span style='color: #a00'>*</span></td>";
		print"</tr>";
		print"<tr>";
		print"<td>Again:</td>";
		print"<td><input type=password name='newpass2' size=30 value='".$newpass2."'>  <span style='color: #a00'>*</span></td>";
		print"</tr>";
		print"<tr>";
		print"<td colspan=2><span style='color: #a00'>* Must be 8-200 characters long and not contain any of the following: \" '</span></td>";
		print"</tr>";
		print"<tr><td colspan=2 align=center><input type=submit value='Change password'><br><br>";
		print"<input type=hidden name='action' value='newpassword'>";
		print"<input type=hidden name='change' value='1'>";
		print" <a href=passwd.php?$sid&action=reset&email=$email>Forgot your password?</a> | ";
		print" <a href=passwd.php?$sid&action=login&email=$email>Log in.</a>";
		print "</td></tr>";
	}

/******************************************************************************
 * Log in UI
 ******************************************************************************/

} else if ($_REQUEST[action] == "login" || $auth) {
	print "<tr><td colspan=2 align=center> Please enter your username and password. <br>";
	print "<br><div align=left>(".$cfg[auth_help].")</div><br>";
	print "</td><tr><td> User Name: </td>";
	print "<td><input type=text name='uname' size=30 value=''></td>";
	print "<tr><td>Password:</td>";
	print"<td><input type=password name='password' size=30 value=''> </td>";
	print"<tr><td colspan=2 align=center><input type=submit value='Log In'><br><br>";
	print" <a href=passwd.php?$sid&action=reset&email=$email>Forgot your password?</a> | ";
	print" <a href=passwd.php?$sid&action=change&email=$email>Change your password.</a>";
	print "</td></tr>";
	print"<input type=hidden name='action' value='auth'>";
	print"<input type=hidden name='auth' value='1'>";

/******************************************************************************
 * Register UI
 ******************************************************************************/

} else if ($_REQUEST[action] == "register" || $newuser) {
	print "<tr><td colspan=2 align=left> Please enter your name and your email address.";
	print "  Once you have registered you will be able to post to this and all other $cfg[inst_name] public forums.";
	print "  Your username will be your email address and a password will be emailed to you.<br><br>";
	print "(".$cfg[auth_help].")<br><br>";
	print "</td><tr><td> User Name: </td>";
	print "<td><input type=text name='uname' size=30 value='".$_REQUEST['uname']."'></td>";
	print "<tr><td>Email Address:</td>";
	print"<td><input type=text name='email' size=30 value='".$_REQUEST['email']."'> </td>";
	print"<tr><td colspan=2 align=center><input type=submit value='Register'></td></tr>";
	print"<input type=hidden name='action' value='newuser'>";
	print"<input type=hidden name='newuser' value='1'>";

/******************************************************************************
 * Reset Password UI
 ******************************************************************************/

} else if ($_REQUEST[action] == "reset" || $reset) {
	print "<tr><td colspan=2 align=left> Please enter your email address and a new password will be sent to you.<br><br>";
	print "(".$cfg[auth_help].")<br><br>";
	print "</td><tr><td>Email Address:</td>";
	print"<td><input type=text name='email' size=30 value='".$_REQUEST['email']."'></td></tr>";
	print"<tr><td colspan=2 align=center><input type=submit name='submit' value='Send new password'><br><br>";
	print"<input type=hidden name='action' value='send'>";
	print"<input type=hidden name='reset' value='1'>";
	print" <a href=passwd.php?$sid&action=change&email=$email>Change your password</a> | ";
	print" <a href=passwd.php?$sid&action=login&email=$email>Log in</a></td></tr>";

}
print "</td></tr>";
print "<tr><td colspan=2>"; 
print"<br>";
print $message;
print"<br>";
print"</td></tr>";


print "</table><BR>";
//print "<div align=right><input type=button value='Close Window' onClick='window.close()'></div>";
?>

<? 
// debug output -- handy :)
/* print "<pre>"; */
/* print "request:\n"; */
/* print_r($_REQUEST); */
/* print "\n\n"; */
/* print "session:\n"; */
/* print_r($_SESSION); */
/* print "\n\n"; */

/* if (is_object($thisPage)) { */
/* 	print "\n\n"; */
/* 	print "thisPage:\n"; */
/* 	print_r($thisPage); */
/* } else if (is_object($thisSection)) { */
/* 	print "\n\n"; */
/* 	print "thisSection:\n"; */
/* 	print_r($thisSection); */
/* } else if (is_object($thisSite)) { */
/* 	print "\n\n"; */
/* 	print "thisSite:\n"; */
/* 	print_r($thisSite); */
/* } */
/* print "</pre>"; */
?>