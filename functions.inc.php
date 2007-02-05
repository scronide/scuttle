<?php
// UTF-8 functions
require_once(dirname(__FILE__) .'/includes/utf8.php');

// Translation
require_once(dirname(__FILE__) .'/includes/php-gettext/gettext.inc');
$domain = 'messages';
T_setlocale(LC_ALL, $locale);
T_bindtextdomain($domain, dirname(__FILE__) .'/locales');
T_bind_textdomain_codeset($domain, 'UTF-8');
T_textdomain($domain);

// Converts tags:
// - direction = out: convert spaces to underscores;
// - direction = in: convert underscores to spaces.
function convertTag($tag, $direction = 'out') {
    if ($direction == 'out') {
        $tag = str_replace(' ', '_', $tag);
    } else {
        $tag = str_replace('_', ' ', $tag);
    }
    return $tag;
}

function filter($data, $type = NULL) {
    if (is_string($data)) {
        $data = trim($data);
        $data = stripslashes($data);
        switch ($type) {
            case 'url':
                $data = rawurlencode($data);
                break;
            default:
                $data = htmlspecialchars($data);
                break;
        }
    } else if (is_array($data)) {
        foreach(array_keys($data) as $key) {
            $row =& $data[$key];
            $row = filter($row, $type);
        }
    }
    return $data;
}

function getPerPageCount() {
    global $defaultPerPage;
    return $defaultPerPage;
}

function getSortOrder($override = NULL) {
    global $defaultOrderBy;

    if (isset($_GET['sort'])) {
        return $_GET['sort'];
    } else if (isset($override)) {
        return $override;
    } else {
        return $defaultOrderBy;
    }
}

function multi_array_search($needle, $haystack) {
    if (is_array($haystack)) {
        foreach(array_keys($haystack) as $key) {
            $value =& $haystack[$key];
            $result = multi_array_search($needle, $value);
            if (is_array($result)) {
                $return = $result;
                array_unshift($return, $key);
                return $return;
            } elseif ($result == true) {
                $return[] = $key;
                return $return;
            }
        }
        return false;
    } else {
        if ($needle === $haystack) {
            return true;
        } else {
            return false;
        }
    }
}

function createURL($page = '', $ending = '') {
    global $cleanurls, $root;
    if (!$cleanurls && $page != '') {
        $page .= '.php';
    }
    return $root . $page .'/'. $ending;
}

function message_die($msg_code, $msg_text = '', $msg_title = '', $err_line = '', $err_file = '', $sql = '', $db = NULL) {
    if(defined('HAS_DIED'))
        die(T_('message_die() was called multiple times.'));
    define('HAS_DIED', 1);
	
	$sql_store = $sql;
	
	// Get SQL error if we are debugging. Do this as soon as possible to prevent 
	// subsequent queries from overwriting the status of sql_error()
	if (DEBUG && ($msg_code == GENERAL_ERROR || $msg_code == CRITICAL_ERROR)) {
		$sql_error = is_null($db) ? '' : $db->sql_error();
		$debug_text = '';
		
		if ($sql_error['message'] != '')
			$debug_text .= '<br /><br />'. T_('SQL Error') .' : '. $sql_error['code'] .' '. $sql_error['message'];

		if ($sql_store != '')
			$debug_text .= '<br /><br />'. $sql_store;

		if ($err_line != '' && $err_file != '')
			$debug_text .= '</br /><br />'. T_('Line') .' : '. $err_line .'<br />'. T_('File') .' :'. $err_file;
	}

	switch($msg_code) {
		case GENERAL_MESSAGE:
			if ($msg_title == '')
				$msg_title = T_('Information');
			break;

		case CRITICAL_MESSAGE:
			if ($msg_title == '')
				$msg_title = T_('Critical Information');
			break;

		case GENERAL_ERROR:
			if ($msg_text == '')
				$msg_text = T_('An error occured');

			if ($msg_title == '')
				$msg_title = T_('General Error');
			break;

		case CRITICAL_ERROR:
			// Critical errors mean we cannot rely on _ANY_ DB information being
			// available so we're going to dump out a simple echo'd statement

			if ($msg_text == '')
				$msg_text = T_('An critical error occured');

			if ($msg_title == '')
				$msg_title = T_('Critical Error');
			break;
	}

	// Add on DEBUG info if we've enabled debug mode and this is an error. This
	// prevents debug info being output for general messages should DEBUG be
	// set TRUE by accident (preventing confusion for the end user!)
	if (DEBUG && ($msg_code == GENERAL_ERROR || $msg_code == CRITICAL_ERROR)) {
		if ($debug_text != '')
			$msg_text = $msg_text . '<br /><br /><strong>'. T_('DEBUG MODE') .'</strong>'. $debug_text;
	}

	echo "<html>\n<body>\n". $msg_title ."\n<br /><br />\n". $msg_text ."</body>\n</html>";
	exit;
}
?>
