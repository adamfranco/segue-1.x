<? // default class functions -- they do nothing/return null values


function isclass ($class) {
	return 0;
}

function getuserclasses($user,$time="now") {
	return array();
}

function coursefoldersite($cl) {
	return 0;
}

function ldapfname($uname) {
	return "n/a";
}

function userlookup($name,$type=LDAP_BOTH,$wild=LDAP_WILD,$n=LDAP_LASTNAME,$lc=0) {
	return array();
}

?>
