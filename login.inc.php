<? include("login_css.inc.php"); ?>

<title>SitesDB Login</title>

<body bgcolor="#006699" link="#FFCC00" vlink="#FFCC00" alink="#FFFFFF" background="images/bg01.gif">
<div align="center">
<table width="700" border="0" cellpadding="0" cellspacing="0">
    <tr> 
      <td background="images/seal02bg.jpg" valign="top"> 
        <p align="center">SitesDB!!! WOOOOOOO!</p>
        <BR>
        <table border=0 align=center width=500>
          <tr> 
            <td>
              <p>Welcome to SitesDB! Please enter your login information below and press the 
                &quot;CONTINUE &gt;&gt;&quot; button.</p>
            </td>
          </tr>
          <tr> 
            <td> 
<!--              <div align="center"><font color="#FFFFFF" size="4">Login</font> 
              </div>-->
              <form action=<?echo "$PHP_SELF?$sid"?> method=post name="login">
                <? printerr() ?>
                <div align="center"> 
<!--                  <input type=hidden name="viewpage" value="<? echo $viewpage; ?>">
                  <input type=hidden name="getquery" value="<? echo $getquery; ?>">-->
                  Username:
                  <input type=text size=20 class="formsmall" name="name" value="<?echo $name;?>">
                  Password:
                  <input type=password size=20 class="formsmall" name="password">
                  <input type=submit value="CONTINUE &gt;&gt;" class="buttonsmall" name="button">
                </div>
<!--                <p align="center"> <span class=smaller><a href="<?echo "forgot_pwd.php"?>"><font color="#FFCC00">Forgot 
                  your password?</font></a><font color="#FFFFFF"> |</font> <a href="<?echo "newuser.php"?>"><font color="#FFCC00">New 
                  user?</font></a> </span> -->
              </form>
            </td>
          </tr>
          <tr>
            <td>
              <div align="center">
                <hr>
                <a href="http://sqlserver.middlebury.edu/studydb"><font color="#FFCC00">StudyDB</font></a> 
                | <a href="http://et.middlebury.edu/mediadb"><font color="#FFCC00">MediaDB</font></a> 
                | <a href="http://itweb.middlebury.edu/mots/login.php"><font color="#FFCC00">MOTS</font></a></div>
            </td>
          </tr>
        </table>
        <p>&nbsp;</p>
        <p align="center">NOTE: This site can only be viewed 
          with either Internet Explorer version 5 and up, or Netscape version 
          6 and up.<br><BR>
          <i><font size="2">&copy; 2002 Middlebury College, Gabriel Schine, Alex 
          Chapin </font></i></font></p>
        
      </td>
    </tr>
  </table>
  <font color="#FFFFFF"> </font> </div>
