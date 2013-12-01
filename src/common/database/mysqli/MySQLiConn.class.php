<?php

/**
 * database connection via mysqli
 * @version 0.8.20110313
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database.mysqli
 */
class MySQLiConn extends DBConn {

    /**
     * create a connection to a MySQL server and open he indicated database
     * @param string $host
     * the address of MySQL server;
     * if the database is running on a special port,
     * add it to the end of the string with a colon to separate
     * @param string $user
     * @param string $pass
     * @param string $db
     */
    public function OpenDB($host, $user, $pass, $db) {
        $h = explode(":", $host);
        if (count($h) == 1) {
            $r = mysqli_connect($host, $user, $pass, $db);
        } else {
            $r = mysqli_connect($h[0], $user, $pass, $db, $h[1]);
        }
        if (!$r) {
            $this->CloseDB();
            throw new Exception("fail to connect to database", 1);
        }
        $this->connObj = &$r;
        $this->dbname = $db;
        $this->hostname = $host;
        $this->username = $user;
    }

    public function CloseDB() {
        @mysqli_close($this->connObj);
        parent::CloseDB();
    }

    public function CreateSelectSTMT($tablename="") {
        LoadIBC1Class("MySQLiSTMT", "common.database.mysqli");
        LoadIBC1Class("MySQLiSelect", "common.database.mysqli");
        $stmt = new MySQLiSelect($tablename, $this->connObj);
        return $stmt;
    }

    public function CreateInsertSTMT($tablename="") {
        LoadIBC1Class("MySQLiSTMT", "common.database.mysqli");
        LoadIBC1Class("MySQLiInsert", "common.database.mysqli");
        $stmt = new MySQLiInsert($tablename, $this->connObj);
        return $stmt;
    }

    public function CreateUpdateSTMT($tablename="") {
        LoadIBC1Class("MySQLiSTMT", "common.database.mysqli");
        LoadIBC1Class("MySQLiUpdate", "common.database.mysqli");
        $stmt = new MySQLiUpdate($tablename, $this->connObj);
        return $stmt;
    }

    public function CreateDeleteSTMT($tablename="") {
        LoadIBC1Class("MySQLiSTMT", "common.database.mysqli");
        LoadIBC1Class("MySQLiDelete", "common.database.mysqli");
        $stmt = new MySQLiDelete($tablename, $this->connObj);
        return $stmt;
    }

    public function CreateTableSTMT($mode, $tablename="") {
        LoadIBC1Class("MySQLiSTMT", "common.database.mysqli");
        LoadIBC1Class("MySQLiTable", "common.database.mysqli");
        $stmt = new MySQLiTable($mode, $tablename, $this->connObj);
        return $stmt;
    }

    public function CreateSTMT($sql=NULL) {
        LoadIBC1Class("MySQLiSTMT", "common.database.mysqli");
        $stmt = new MySQLiSTMT($this->connObj, $sql);
        return $stmt;
    }

    public function TableExists($table) {
        if (!DataFormatter::ValidateTableName($table))
            return FALSE;
        $sql = $this->CreateSTMT("SHOW TABLES FROM " . $this->GetDBName() . " LIKE \"$table\";");
        $sql->Execute();
        $r = $sql->Fetch();
        $sql->CloseSTMT();
        return (!!$r);
    }

    public function FieldExists($table, $field) {
        if (!DataFormatter::ValidateTableName($table))
            return FALSE;
        if (!DataFormatter::ValidateFieldName($field))
            return FALSE;
        $sql = $this->CreateSTMT("SHOW COLUMNS FROM " . $this->GetDBName() . ".$table LIKE \"$field\";");
        $sql->Execute();
        $r = $sql->Fetch();
        $sql->CloseSTMT();
        return (!!$r);
    }

    public function GetTableList() {
        $sql = $this->CreateSTMT("SHOW TABLES FROM " . $this->GetDBName());
        $sql->Execute();
        //TODO: generate a table list
    }

    public function GetFieldList($table) {
        if (!DataFormatter::ValidateTableName($table))
            return FALSE;
        $sql = $this->CreateSTMT("SHOW COLUMNS FROM " . $this->GetDBName() . ".$table;");
        $sql->Execute();
        //TODO: generate a field list
    }

}

/*
  public function SetRowNumber($i) {
  //return @mysqli_data_seek($this->QueryID,$i);
  mysqli_stmt_data_seek($this->dbSTMT, $i);
  }

  public function GetRowCount() {
  //return @mysqli_num_rows($this->QueryID);
  return mysqli_stmt_num_rows($this->dbSTMT);
  }

  public function GetLastInsertID() {
  //return mysqli_insert_id();
  return mysqli_stmt_insert_id($this->dbSTMT);
  }

  public function GetAffectedRowCount() {
  //return mysqli_affected_rows($this->connObj);
  return mysqli_stmt_affected_rows($this->dbSTMT);
  }
 */
?>
