<? // login.php

if (session_is_registered("coursesdb")) {
  session_destroy();
}
include("config.inc.php");
include("dbwrapper.inc.php");
include("error.php");
include("checklogin.inc.php");

print "hello";

?>