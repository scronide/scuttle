<?php
class ServiceFactory {
    function ServiceFactory(&$db, $serviceoverrules = array()) {
    }

    function &getServiceInstance($name, $servicedir = NULL) {
        global $dbhost, $dbuser, $dbpass, $dbname, $dbport, $dbpersist, $dbtype;
        static $instances = array();
        static $db;
        if (!isset($db)) {
            require_once dirname(__FILE__) .'/../includes/db/'. $dbtype .'.php';
            $db = new sql_db();
            $db->sql_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport, $dbpersist);
            if(!$db->db_connect_id) {
                message_die(CRITICAL_ERROR, "Could not connect to the database", $db);
            }
        }
        if (!isset($instances[$name])) {
            if (isset($serviceoverrules[$name])) {
                $name = $serviceoverrules[$name];
            }
            if (!class_exists($name)) {
                if (!isset($servicedir)) {
                    $servicedir = dirname(__FILE__) .'/';
                }
                require_once $servicedir . strtolower($name) .'.php';
            }
            $instances[$name] = call_user_func(array($name, 'getInstance'), $db);
        }
        return $instances[$name];
    }
}
