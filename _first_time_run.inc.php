<? /* $Id$ */

// segue first-time run stuff

$u = new user();
$u->_genDefaultAdminUser();
$u->insertDB();