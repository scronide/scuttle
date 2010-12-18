<?php
class UserService {
  var $db;

  function &getInstance(&$db) {
    static $instance;
    if (!isset($instance)) {
      $instance = new UserService($db);
    }
    return $instance;
  }

  var $fields = array(
    'primary'   => 'uId',
    'username'  => 'username',
    'password'  => 'password'
  );
  var $profileurl;
  var $tablename;
  var $sessionkey;
  var $cookiekey;
  var $cookietime = 1209600; // 2 weeks

    function UserService(&$db) {
        $this->db =& $db;
        $this->tablename = $GLOBALS['tableprefix'] .'users';
        $this->sessionkey = $GLOBALS['cookieprefix'] .'-currentuserid';
        $this->cookiekey = $GLOBALS['cookieprefix'] .'-login';
        $this->profileurl = createURL('profile', '%2$s');
    }

    function _checkdns($host) {
        if (function_exists('checkdnsrr')) {
            return checkdnsrr($host);
        } else {
            return $this->_checkdnsrr($host);
        }
    }

    function _checkdnsrr($host, $type = "MX") {
        if(!empty($host)) {
            @exec("nslookup -type=$type $host", $output);
            while(list($k, $line) = each($output)) {
                if(eregi("^$host", $line)) {
                    return true;
                }
            }
            return false;
        }
    }

    function _getuser($fieldname, $value) {
        $query = 'SELECT * FROM '. $this->getTableName() .' WHERE '. $fieldname .' = "'. $this->db->sql_escape($value) .'"';

        if (! ($dbresult =& $this->db->sql_query($query)) ) {
            message_die(GENERAL_ERROR, 'Could not get user', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        if ($row =& $this->db->sql_fetchrow($dbresult))
            return $row;
        else
            return false;
    }

    function _in_regex_array($value, $array) {
      foreach ($array as $key => $pattern) {
        if (preg_match($pattern, $value)) {
          return TRUE;
        }
      }
      return FALSE;
    }

    function _randompassword() {
        $password = mt_rand(1, 99999999);
        $password = substr(md5($password), mt_rand(0, 19), mt_rand(6, 12));
        return $password;
    }

    function _updateuser($uId, $fieldname, $value) {
        $updates = array ($fieldname => $value);
        $sql = 'UPDATE '. $this->getTableName() .' SET '. $this->db->sql_build_array('UPDATE', $updates) .' WHERE '. $this->getFieldName('primary') .'='. intval($uId);

        // Execute the statement.
        $this->db->sql_transaction('begin');
        if (!($dbresult = & $this->db->sql_query($sql))) {
            $this->db->sql_transaction('rollback');
            message_die(GENERAL_ERROR, 'Could not update user', '', __LINE__, __FILE__, $sql, $this->db);
            return false;
        }
        $this->db->sql_transaction('commit');

        // Everything worked out, so return true.
        return true;
    }

    function getProfileUrl($id, $username) {
        return sprintf($this->profileurl, urlencode($id), urlencode($username));
    }

    function getUserByUsername($username) {
        return $this->_getuser($this->getFieldName('username'), $username);
    }

    function getUser($id) {
        return $this->_getuser($this->getFieldName('primary'), $id);
    }

    function isLoggedOn() {
        return ($this->getCurrentUserId() !== false);
    }

    function &getCurrentUser($refresh = FALSE, $newval = NULL) {
        static $currentuser;
        if (!is_null($newval)) //internal use only: reset currentuser
            $currentuser = $newval;
        else if ($refresh || !isset($currentuser)) {
            if ($id = $this->getCurrentUserId())
                $currentuser = $this->getUser($id);
            else
                return;
        }
        return $currentuser;
    }

    function isAdmin($userid) {
        return false; //not implemented yet
    }

    function getCurrentUserId() {
        if (isset($_SESSION[$this->getSessionKey()])) {
            return $_SESSION[$this->getSessionKey()];
        } else if (isset($_COOKIE[$this->getCookieKey()])) {
            $cook = split(':', $_COOKIE[$this->getCookieKey()]);
            //cookie looks like this: 'id:md5(username+password)'
            $query = 'SELECT * FROM '. $this->getTableName() .
                     ' WHERE MD5(CONCAT('.$this->getFieldName('username') .
                                     ', '.$this->getFieldName('password') .
                     ')) = \''.$this->db->sql_escape($cook[1]).'\' AND '.
                     $this->getFieldName('primary'). ' = '. $this->db->sql_escape($cook[0]);

            if (! ($dbresult =& $this->db->sql_query($query)) ) {
                message_die(GENERAL_ERROR, 'Could not get user', '', __LINE__, __FILE__, $query, $this->db);
                return false;
            }

            if ($row = $this->db->sql_fetchrow($dbresult)) {
                $_SESSION[$this->getSessionKey()] = $row[$this->getFieldName('primary')];
                return $_SESSION[$this->getSessionKey()];
            }
        }
        return false;
    }

    function login($username, $password, $remember = FALSE, $path = '/') {
        $password = $this->sanitisePassword($password);
        $query = 'SELECT '. $this->getFieldName('primary') .' FROM '. $this->getTableName() .' WHERE '. $this->getFieldName('username') .' = "'. $this->db->sql_escape($username) .'" AND '. $this->getFieldName('password') .' = "'. $this->db->sql_escape($password) .'"';

        if (! ($dbresult =& $this->db->sql_query($query)) ) {
            message_die(GENERAL_ERROR, 'Could not get user', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        if ($row =& $this->db->sql_fetchrow($dbresult)) {
            $id = $_SESSION[$this->getSessionKey()] = $row[$this->getFieldName('primary')];
            if ($remember) {
                $cookie = $id .':'. md5($username.$password);
                setcookie($this->cookiekey, $cookie, time() + $this->cookietime, $path);
            }
            return true;
        } else {
            return false;
        }
    }

    function logout($path = '/') {
        @setcookie($this->cookiekey, NULL, time() - 1, $path);
        unset($_COOKIE[$this->cookiekey]);
        session_unset();
        $this->getCurrentUser(TRUE, false);
    }

    function getWatchlist($uId) {
        // Gets the list of user IDs being watched by the given user.
        $query = 'SELECT watched FROM '. $GLOBALS['tableprefix'] .'watched WHERE uId = '. intval($uId);

        if (! ($dbresult =& $this->db->sql_query($query)) ) {
            message_die(GENERAL_ERROR, 'Could not get watchlist', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        $arrWatch = array();
        if ($this->db->sql_numrows($dbresult) == 0)
            return $arrWatch;
        while ($row =& $this->db->sql_fetchrow($dbresult))
            $arrWatch[] = $row['watched'];
        return $arrWatch;
    }

    function getWatchNames($uId, $watchedby = false) {
        // Gets the list of user names being watched by the given user.
        // - If $watchedby is false get the list of users that $uId watches
        // - If $watchedby is true get the list of users that watch $uId
        if ($watchedby) {
            $table1 = 'b';
            $table2 = 'a';
        } else {
            $table1 = 'a';
            $table2 = 'b';
        }
        $query = 'SELECT '. $table1 .'.'. $this->getFieldName('username') .' FROM '. $GLOBALS['tableprefix'] .'watched AS W, '. $this->getTableName() .' AS a, '. $this->getTableName() .' AS b WHERE W.watched = a.'. $this->getFieldName('primary') .' AND W.uId = b.'. $this->getFieldName('primary') .' AND '. $table2 .'.'. $this->getFieldName('primary') .' = '. intval($uId) .' ORDER BY '. $table1 .'.'. $this->getFieldName('username');

        if (!($dbresult =& $this->db->sql_query($query))) {
            message_die(GENERAL_ERROR, 'Could not get watchlist', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        $arrWatch = array();
        if ($this->db->sql_numrows($dbresult) == 0) {
            return $arrWatch;
        }
        while ($row =& $this->db->sql_fetchrow($dbresult)) {
            $arrWatch[] = $row[$this->getFieldName('username')];
        }
        return $arrWatch;
    }

    function getWatchStatus($watcheduser, $currentuser) {
        // Returns true if the current user is watching the given user, and false otherwise.
        $query = 'SELECT watched FROM '. $GLOBALS['tableprefix'] .'watched AS W INNER JOIN '. $this->getTableName() .' AS U ON U.'. $this->getFieldName('primary') .' = W.watched WHERE U.'. $this->getFieldName('primary') .' = '. intval($watcheduser) .' AND W.uId = '. intval($currentuser);
        
        if (! ($dbresult =& $this->db->sql_query($query)) ) {
            message_die(GENERAL_ERROR, 'Could not get watchstatus', '', __LINE__, __FILE__, $query, $this->db);
            return false;
        }

        $arrWatch = array();
        if ($this->db->sql_numrows($dbresult) == 0)
            return false;
        else 
            return true;
    }

    function setWatchStatus($subjectUserID) {
        if (!is_numeric($subjectUserID))
            return false;

        $currentUserID = $this->getCurrentUserId();
        $watched = $this->getWatchStatus($subjectUserID, $currentUserID);

        if ($watched) {
            $sql = 'DELETE FROM '. $GLOBALS['tableprefix'] .'watched WHERE uId = '. intval($currentUserID) .' AND watched = '. intval($subjectUserID);
            if (!($dbresult =& $this->db->sql_query($sql))) {
                $this->db->sql_transaction('rollback');
                message_die(GENERAL_ERROR, 'Could not add user to watch list', '', __LINE__, __FILE__, $sql, $this->db);
                return false;
            }
        } else {
            $values = array(
                'uId' => intval($currentUserID),
                'watched' => intval($subjectUserID)
            ); 
            $sql = 'INSERT INTO '. $GLOBALS['tableprefix'] .'watched '. $this->db->sql_build_array('INSERT', $values);
            if (!($dbresult =& $this->db->sql_query($sql))) {
                $this->db->sql_transaction('rollback');
                message_die(GENERAL_ERROR, 'Could not add user to watch list', '', __LINE__, __FILE__, $sql, $this->db);
                return false;
            }
        }

        $this->db->sql_transaction('commit');
        return true;
    }

    function addUser($username, $password, $email) {
        // Set up the SQL UPDATE statement.
        $datetime = gmdate('Y-m-d H:i:s', time());
        $password = $this->sanitisePassword($password);
        $values = array('username' => $username, 'password' => $password, 'email' => $email, 'uDatetime' => $datetime, 'uModified' => $datetime);
        $sql = 'INSERT INTO '. $this->getTableName() .' '. $this->db->sql_build_array('INSERT', $values);

        // Execute the statement.
        $this->db->sql_transaction('begin');
        if (!($dbresult = & $this->db->sql_query($sql))) {
            $this->db->sql_transaction('rollback');
            message_die(GENERAL_ERROR, 'Could not insert user', '', __LINE__, __FILE__, $sql, $this->db);
            return false;
        }
        $this->db->sql_transaction('commit');

        // Everything worked out, so return true.
        return true;
    }

    function updateUser($uId, $password, $name, $email, $homepage, $uContent) {
        if (!is_numeric($uId))
            return false;

        // Set up the SQL UPDATE statement.
        $moddatetime = gmdate('Y-m-d H:i:s', time());
        if ($password == '')
            $updates = array ('uModified' => $moddatetime, 'name' => $name, 'email' => $email, 'homepage' => $homepage, 'uContent' => $uContent);
        else
            $updates = array ('uModified' => $moddatetime, 'password' => $this->sanitisePassword($password), 'name' => $name, 'email' => $email, 'homepage' => $homepage, 'uContent' => $uContent);
        $sql = 'UPDATE '. $this->getTableName() .' SET '. $this->db->sql_build_array('UPDATE', $updates) .' WHERE '. $this->getFieldName('primary') .'='. intval($uId);

        // Execute the statement.
        $this->db->sql_transaction('begin');
        if (!($dbresult = & $this->db->sql_query($sql))) {
            $this->db->sql_transaction('rollback');
            message_die(GENERAL_ERROR, 'Could not update user', '', __LINE__, __FILE__, $sql, $this->db);
            return false;
        }
        $this->db->sql_transaction('commit');

        // Everything worked out, so return true.
        return true;
    }

    function sanitisePassword($password) {
        return sha1(trim($password));
    }

    function generatePassword($uId) {
        if (!is_numeric($uId))
            return false;

        $password = $this->_randompassword();

        if ($this->_updateuser($uId, $this->getFieldName('password'), $this->sanitisePassword($password)))
            return $password;
        else
            return false;
    }

    function isBlockedEmail($email) {
      // Check whitelist
      $whitelist = $GLOBALS['email_whitelist'];
      if (!is_null($whitelist) && is_array($whitelist)) {
        if (!$this->_in_regex_array($email, $whitelist)) {
          // Not in whitelist -> blocked
          return TRUE;
        }
      }

      // Check blacklist
      $blacklist = $GLOBALS['email_blacklist'];
      if (!is_null($blacklist) && is_array($blacklist)) {
        if ($this->_in_regex_array($email, $blacklist)) {
          // In blacklist -> blocked
          return TRUE;
        }
      }

      // Not blocked
      return FALSE;
    }

    function isReserved($username) {
        if (in_array($username, $GLOBALS['reservedusers'])) {
            return true;
        } else {
            return false;
        }
    }

    function isValidEmail($email) {
        if (preg_match("/^((?:(?:(?:\w[\.\-\+_]?)*)\w)+)\@((?:(?:(?:\w[\.\-_]?){0,62})\w)+)\.(\w{2,6})$/i", $email) > 0) {
            list($emailUser, $emailDomain) = explode("@", $email);

            // Check if the email domain has a DNS record
            if ($this->_checkdns($emailDomain)) {
                return true;
            }
        }
        return false;
    }

    // Properties
    function getTableName()       { return $this->tablename; }
    function setTableName($value) { $this->tablename = $value; }

    function getFieldName($field)         { return $this->fields[$field]; }
    function setFieldName($field, $value) { $this->fields[$field] = $value; }

    function getSessionKey()       { return $this->sessionkey; }
    function setSessionKey($value) { $this->sessionkey = $value; }

    function getCookieKey()       { return $this->cookiekey; }
    function setCookieKey($value) { $this->cookiekey = $value; }
}
