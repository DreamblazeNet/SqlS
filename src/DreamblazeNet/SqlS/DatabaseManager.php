<?php

/**
 * 
 */

namespace DreamblazeNet\SqlS;

class DatabaseManager {

    static private $configs = array();
    static private $connections = array();

    public static function init($log){
        self::$log = $log;
    }

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private static $log;

    /*
     * @param string An unique identifier for this db
     * @param DatabaseConfig
     */
    public static function add_database($info, $name, $id = null){
        if(is_array($info)){
            foreach($info as $key=>$val){
                self::check_config($val);
                self::$configs[$name][$key] = $val;
            }
        } else {
            self::check_config($info);
            if(is_null($id)){
                self::$configs[$name] = $info;
            } else {
                if(!array_key_exists($name,self::$configs)){
                    self::$configs[$name] = array();
                } elseif(!is_array(self::$configs[$name])){
                    throw new Exception("Wrong Database-Config format! Setting connection-string over multi-db field is not allowed");
                }
                self::$configs[$name][$id] = $info;
            }
        }
    }
    
    private static function check_config($config){
        if(!($config instanceof \DreamblazeNet\SqlS\DatabaseConfig))
            throw new Exception('Wrong Database-Config format! Have to be an DatabaseConfig-Object');
    }


    /**
     * @param $name
     * @param $id
     * @return DatabaseConnection
     * @throws DatabaseException
     */
    public static function get_database($name,$id = null) {
        if (!isset(self::$connections[$name]) || (!is_null($id) && !isset(self::$connections[$name][$id]))) {  
            self::connect_database($name, $id);
        }
        
        if(is_null($id)){
            if(is_object(self::$connections[$name]))
                return self::$connections[$name];
        } else {
            if(is_object(self::$connections[$name][$id]))
                return self::$connections[$name][$id];
        }
        throw new DatabaseException("DB-Connection $name $id not found");
    }
    
    private static function connect_database($name, $id) {
        if(is_null($id) && isset(self::$configs[$name])){
            $info = self::$configs[$name];
        } elseif(isset(self::$configs[$name][$id]) && is_array(self::$configs[$name])) {
            $info = self::$configs[$name][$id];
        } else {
            throw new DatabaseException("No DB with name $name $id available!");
        }
        
        self::$log->debug("Connecting to DATABASE [<b>$name</b>] dns: " . var_export($info,true));
        
        $connection = self::load_adapter_class($info, self::$log);

        if(is_null($id)){
            self::$connections[$name] = $connection;
        } else {
            self::$connections[$name][$id] = $connection;
        }
    }

    /**
     * @param $adapter
     * @return DatabaseConnection
     */
    private static function load_adapter_class($info) {
        $classname = 'DreamblazeNet\\SqlS\\Adapters\\' . ucwords($info->protocol) . 'Adapter';

        try {
            /***
             * @var DatabaseConnection
             */
            $connection = new $classname($info, self::$log);
            $connection->protocol = $info->protocol;

            if (isset($info->charset))
                $connection->set_encoding($info->charset);

            $connection->set_timezone();
        } catch (PDOException $e) {
            throw new DatabaseException(null,null,$e);
        }
        return $connection;
    }
    
    public static function disconnect_database($name) {
        if (isset(self::$connections[$name])) {
            unset(self::$connections[$name]);
        }
    }
}
