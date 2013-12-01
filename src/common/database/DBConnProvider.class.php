<?php

/**
 * provider of all database connections
 * @version 0.6.20110413
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.dataservice
 */
class DBConnProvider {

    private $ConnIndex = -1;
    private $ConnList = array();
    private $ConnInfo = NULL;

    /**
     * the constructor
     * @see OpenDB()
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $db
     * @param string $dbsoft
     */
    function __construct($host, $user, $pass, $db, $dbsoft=IBC1_DEFAULT_DBSOFT) {
        $this->OpenDB($host, $user, $pass, $db, $dbsoft);
    }

    function __destruct() {
        $this->CloseAll();
    }

    public function GetHostName() {
        if ($this->ConnInfo == NULL)
            return "";
        return $this->ConnInfo[0];
    }

    public function GetUserName() {
        if ($this->ConnInfo == NULL)
            return "";
        return $this->ConnInfo[1];
    }

    public function GetDBName() {
        if ($this->ConnInfo == NULL)
            return "";
        return $this->ConnInfo[3];
    }

    public function GetDBSoft() {
        if ($this->ConnInfo == NULL)
            return "";
        return $this->ConnInfo[4];
    }

    /**
     * Save database connection information temporarily if available,
     * and then open one connection;
     * if a second connection to the same database is needed,
     * parameters of this method are not required again.
     * @param string $host
     * If database is running on a special port,
     * add to the end of this string with a colon separated.
     * e.g. $obj->OpenDB("localhost:10000","root","","mydb");
     * @param string $user
     * @param string $pass
     * @param string $db
     * @param string $dbsoft
     * Currently, only "mysqli" is fully supported and it is the default value.
     */
    public function OpenDB($host="", $user="", $pass="", $db="", $dbsoft=IBC1_DEFAULT_DBSOFT) {
        //no new connection information provided
        if ($host == "" && $this->ConnInfo != NULL) {
            $host = $this->ConnInfo[0];
            $user = $this->ConnInfo[1];
            $pass = $this->ConnInfo[2];
            $db = $this->ConnInfo[3];
            $dbsoft = $this->ConnInfo[4];
        }
        //find a disconnected item
        $c = -1;
        $b = count($this->ConnList);
        if ($b > 0) {
            for ($a = 0; $a < $b; $a++) {
                //if available
                if (!$this->ConnList[$a]->Connected()) {
                    //take a note of the position
                    $c = $a;
                    //connect
                    $this->ConnList[$c]->OpenDB($host, $user, $pass, $db);
                    break;
                }
            }
        }
        //not found
        if ($c == -1) {
            //position one after the last
            $c = $b;
            //establish a connection according to database software and connectors
            switch (strtolower($dbsoft)) {
                case "mysqli":
                    LoadIBC1Class("MySQLiConn", "common.database.mysqli");
                    LoadIBC1Class("MySQLiSTMT", "common.database.mysqli");
                    $this->ConnList[] = new MySQLiConn($host, $user, $pass, $db);
                    break;
                case "mysql_pdo":
                    LoadIBC1Class("MySQLConn", "common.database.mysql_pdo");
                    $this->ConnList[] = new MySQLConn($host, $user, $pass, $db);
                    break;
                default:
                    LoadIBC1Class("MySQLConn", "common.database.mysql");
                    $this->ConnList[] = new MySQLConn($host, $user, $pass, $db);
            }
        }
        if ($this->ConnList[$c]->Connected()) {
            //if no database information saved then save it
            if ($this->ConnInfo == NULL)
                $this->ConnInfo = array($host, $user, $pass, $db, $dbsoft);
            //mark the position of the new connection
            $this->ConnIndex = $c;
        }else
            throw new Exception("fail to connect to database", 1);
    }

    /**
     * close all connections opened
     */
    public function CloseAll() {
        $c = count($this->ConnList);
        for ($i = 0; $i < $c; $i++) {
            $this->ConnList[$i]->CloseDB();
        }
        $this->ConnList = array();
        $this->ConnIndex = -1;
    }

    /**
     * get a workable database connection
     * @param int $i
     * Optional;
     * if given, access the indicated connection;
     * otherwise, return the latest connection workable
     * @return DBConn
     * return NULL if no connection available
     */
    public function GetConn($i=NULL) {
        if ($this->ConnInfo == NULL) {
            return NULL; //no connection
        } else if ($i == NULL) {
            if (array_key_exists($this->ConnIndex, $this->ConnList)) {
                return $this->ConnList[$this->ConnIndex]; //latest connection
            }
        } else if (array_key_exists($i, $this->ConnList)) {
            return $this->ConnList[$i]; //indicated connection
        }
        //on connection but with connection information
        $this->OpenDB();
        return $this->ConnList[$this->ConnIndex];
    }

    /**
     * the index number of all connections created by the current object
     * (range from 0, return -1 if no connection has been created)
     * @return int
     */
    public function GetConnIndex() {
        return $this->ConnIndex;
    }

    /**
     * count the number of connections created
     * @return int
     */
    public function ConnCount() {
        return count($this->ConnList);
    }

}

?>