<? /* $Id$ */

class user {
	var $id,$uname,$fname,$pass,$email,$type,$authtype;
	var $randpassgen = false;
	
	function user() {
		$this->authtype = 'db';
		$this->type = 'stud';
	}
	
	function fetchUserID($id) {
		$this->id = $id;
		$this->_fetch();
	}
	
	function _fetch() {
		$query = "
			SELECT
				user_uname,user_fname,user_email,user_type,user_authtype
			FROM user WHERE user_id=".$this->id;
		$r = db_query($query);
		if (db_num_rows($r)) {
			$a = db_fetch_assoc($r);
		} else return false;
		
		$this->uname = $a['user_uname'];
		$this->email = $a['user_email'];
		$this->type = $a['user_type'];
		$this->fname = $a['user_fname'];
		$this->authtype = $a['user_authtype'];
	}
	
	function _insert() {
		if ($this->authtype != 'db') return false; // we only edit db-type users
		$data = "user_uname='".$this->uname."'";
		$data .= ",user_fname='".$this->fname."'";
		$data .= ",user_email='".$this->email."'";
		$data .= ",user_type='".$this->type."'";
		if ($this->randpassgen) $data .= ",user_pass='".$this->pass."'";
		$data .= ",user_authtype='db'";
		
		if ($this->id) { // are we updating?
			$query = "
				UPDATE
					user
					SET $data
					WHERE user_id=".$this->id;
		} else $query = "
				INSERT
					INTO user
					SET $data";
		
//		print $query;
		return db_query($query);
	}
	
	function updateDB() { $this->_insert(); }
	function insertDB() { $this->_insert(); }
	
	function randpass($chars=6,$nums=0) {
		// generate a random password with $chars characters followed by $nums digits
		$thepass = '';
		for ($i = 0; $i < $chars; $i++) {
			$ch = 'a';
			$ch = chr(rand(ord('a'),ord('z')));
			$thepass .= $ch;
		}
		
		for ($i = 0; $i < $nums; $i++) {
			$n = 0;
			$n = rand(0,9);
			$thepass .= $n;
		}
		
		$this->pass = $thepass;
		$this->randpassgen = true;
		return $thepass;
	}
	
	function sendemail($u=0) {
		global $_full_uri;
		if (!$this->randpassgen || $this->authtype != 'db') return false;
		
		// send an email to the user with their new random password!!
		$subject = "IMPORTANT: (Segue) ".(($u)?"Password Reset":"New User Account");
		$to = $this->fname." <".$this->email.">";
		$from = "segue@".$_SERVER['SERVER_NAME'];
		$body = " -- ".(($u)?"YOUR PASSWORD HAS BEEN RESET":"A USER ACCOUNT HAS BEEN CREATED FOR YOU")." --\n\nIn order to log into Segue, click the link below (or enter the URL into your browser) and enter the username and randomly generated password given:\n\n";
		$body .= "   username: ".$this->uname."\n";
		$body .= "   password: ".$this->pass."\n\n";
		$body .= "$_full_uri/index.php\n\n";
		$body .= "IMPORTANT: Please change your password as soon as you log in. DO NOT continue using this random password as email is insecure.\n\n  Thanks and enjoy using Segue";
		
		// send it!
		mail($to,$subject,$body,"From: $from");
	}
	
	function userExists($u) {
		if (!$u) return false;
		$query = "
	SELECT
		COUNT(*) as count
	FROM
		user
	WHERE
		user_uname='$u'";
		$r = db_query($query);
		$a = db_fetch_assoc($r);
		if ($a['count'] != 0) return true;
		return false;
	}
	
	function delUser($id) {
		$query = "
	DELETE FROM
		user
	WHERE
		user_id=$id";
		db_query($query);
	}
		
}