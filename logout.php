<?
session_start();
session_unregister("luser");
session_unregister("mots");
session_unregister("lpass");
header("Location: login.php");
?>
