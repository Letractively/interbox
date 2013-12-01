<?php

/**
 * the chief manager of all data services:<br />
 * <ul>
 * <li>setup up necessary tables for the core</li>
 * <li>parent class for managers of all components in the core, like Catalog, User, etc.</li>
 * </ul>
 * @version 0.6.20110412
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.dataservice
 */
class ServiceManager {

    private $cprovider;
    private $errors;

    /**
     * the constructor
     * @param DBConnProvider $conn
     * At least one connection should be established
     * before the object is passed to the constructor,
     * or an error will show in the error list.
     * @param ErrorList $el
     * Optional;if an ErrorList object is not given, another will be created.
     */
    function __construct(DBConnProvider &$conn, ErrorList &$el=NULL) {
        if ($el == NULL)
            $this->errors = new ErrorList(__CLASS__);
        else
            $this->errors = &$el;
        if ($conn->ConnCount() == 0) {
            $this->errors->AddItem(1, "no connection");
        }
        $this->cprovider = &$conn;
    }

    /**
     * It creates multiple tables in batch and ensure no failures happen,
     * otherwise drop all of them even if some of tables can be created successfully;
     * some errors may appear in the error list even though this function returns TRUE,
     * because there are some tables that exists before creation.
     * @param array $sqlset
     * Each element in the array contains a DBSQLSTMT object
     * to create a table and the corresponding table name for dropping it
     * if it fails to be created.
     * @param DBConn $conn
     * optional, a database connection;
     * if not provided, a new connection will be established
     * @return bool
     */
    protected function CreateTables(&$sqlset, DBConn &$conn=NULL) {
        if ($conn == NULL)
            $conn = &$this->GetDBConn();
        if ($conn == NULL)
            return FALSE;
        $c = count($sqlset);
        for ($i = 0; $i < $c; $i++) {
            $sql = $sqlset[$i];
            if (!$conn->TableExists($sql[1])) {
                $r = $sql[0]->Execute();
                if ($r == FALSE) {
                    for (; $i >= 0; $i--) {
                        $stmt = $conn->CreateTableSTMT("drop", $sql[1]);
                        $stmt->Execute();
                        $stmt->CloseSTMT();
                    }
                    return FALSE;
                }
            } else {
                $this->errors->AddItem(1, "Table `" . $sql[1] . "` exists.");
            }
        }
        return TRUE;
    }

    /**
     * get an errors object where errors are recorded
     * @return ErrorList
     */
    public function GetError() {
        return $this->errors;
    }

    /**
     * get a database connection (DBConn) object
     * @return DBConn
     * return NULL if the connection object is inavailable
     */
    public function GetDBConn() {
        if ($this->cprovider == NULL) {
            $errors->AddItem(1, "Database is not properly connected.");
            return NULL;
        }
        return $this->cprovider->GetConn();
    }

    /**
     * see if the fundamental table for the core is created
     * @param ErrorList $errors
     * If an errors object is given and the core is not installed,
     * an error message is pushed into the list.
     * @return bool
     * If your database is not properly connected, it also returns FALSE.
     */
    public function IsInstalled(ErrorList &$errors=NULL) {
        $conn = &$this->GetDBConn();
        if ($conn == NULL)
            return FALSE;
        if (!$conn->TableExists("ibc1_dataservice")) {
            if ($errors != NULL)
                $errors->AddItem(1, "InterBox Core 1 has not been installed!");
            return FALSE;
        }
        return TRUE;
    }

    /**
     * create fundamental tables for the core
     * @return bool TRUE if no error occurs
     */
    public function Install() {
        if ($this->IsInstalled()) {
            $this->errors->AddItem(2, "already installed");
            return FALSE;
        }
        $conn = &$this->GetDBConn();
        if ($conn == NULL)
            return FALSE;
        $sqlset[0][0] = $conn->CreateTableSTMT("create", "ibc1_dataservice");
        $sqlset[0][1] = "ibc1_dataservice";
        $sql = &$sqlset[0][0];
        $sql->AddField("ServiceName", IBC1_DATATYPE_PURETEXT, 64, FALSE, NULL, TRUE);
        $sql->AddField("ServiceType", IBC1_DATATYPE_PURETEXT, 5, FALSE);

        if (!$this->CreateTables($sqlset, $conn)) {
            $this->errors->AddItem(3, "fail to install");
            return FALSE;
        }

        return TRUE;
    }

    /**
     * check if a service exists
     * @param string $servicename
     * @param string $servicetype
     * if ignore the parameter or give an empty string to it,
     * type of the designated service won't be checked
     * @param bool $noerror
     * If ignore the parameter or give a FALSE value to it,
     * the format of $ServiceName is not expected to be checked.
     * When it is invalid, an error is reported rather than a single FALSE value returned .
     * @return bool
     */
    public function Exists($servicename, $servicetype="", $noerror=FALSE) {
        if (!DataFormatter::ValidateServiceName($servicename)) {
            if (!$noerror) {
                $this->errors->AddItem(5, "invalid service name");
            }
            return FALSE;
        }
        if (!$this->IsInstalled())
            return FALSE;
        $conn = &$this->GetDBConn();
        if ($conn == NULL)
            return FALSE;
        $sql = $conn->CreateSelectSTMT("ibc1_dataservice");
        $sql->AddField("ServiceName");
        $sql->AddEqual("ServiceName", $servicename, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        if ($servicetype != "") {
            $sql->AddEqual("ServiceType", $servicetype, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        }
        $sql->Execute();
        $r = $sql->Fetch();
        $sql->CloseSTMT();
        if ($r)
            return TRUE;
        return FALSE;
    }

}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
/*
  $sql[1]="CREATE TABLE ibc1_dataservice_Backup (";
  $sql[1].="ID INT(10) NOT NULL AUTO_INCREMENT,";
  $sql[1].="ServiceName VARCHAR(64) NOT NULL,";
  $sql[1].="ServiceType VARCHAR(5) NOT NULL,";
  $sql[1].="BackupTime TIMESTAMP(14) NOT NULL,";
  $sql[1].="PRIMARY KEY (ID)";
  $sql[1].=") TYPE=MyISAM DEFAULT CHARSET=utf8;";

  public function DropBackup($id) {
  $conn = $this->GetDBConn();
  $nameroot = "ibc1_backup_" . intval($id) . "_";
  $l = strlen($nameroot);
  $r = mysql_list_tables($this->cprovider->GetDBName());
  $c = mysql_num_rows($r);
  for ($i = 0; $i < $c; $i++) {
  $tablename = mysql_tablename($r, $i);
  if (strtolower(substr($tablename, 0, $l)) == strtolower($nameroot)) {
  $sql->Execute("DROP TABLE $tablename");
  }
  }
  mysql_free_result($r);
  $sql = $conn->CreateDeleteSTMT("ibc1_dataservice_backup");
  $sql->AddEqual("ID", $id);
  $sql->Execute();
  if ($conn->GetError()->HasError()) {

  return FALSE;
  }
  return TRUE;
  }

  private function BackupTable($tlongname, $tshortname, $id) {
  $pri = "";
  $keys = array();
  $conn = $this->GetDBConn();
  $sql->Execute("EXPLAIN $tlongname");
  $sql = "CREATE TABLE ibc1_backup_" . $id . "_" . $tshortname . " (";
  while ($r = $sql->Fetch(1)) {
  $sql.=$r->Field . " " . $r->Type;
  if ($r->Null == "YES")
  $sql.=" NULL";
  else
  $sql.=" NOT NULL";
  if ($r->Default != "NULL") {
  $sql.=" DEFAULT ";
  if (is_numeric($r->Default))
  $sql.=" " . $r->Default;
  else
  $sql.=" \"$r->Default\"";
  }
  $sql.=$r->Extra . ",";
  if ($r->Key == "PRI")
  $pri = $r->Field;
  else if ($r->Key != "")
  $keys[] = $r->Field;
  }
  if ($pri != "")
  $sql.="PRIMARY KEY($pri),";
  foreach ($keys as $key)
  $sql.="KEY($key),";
  $sql = substr($sql, 0, strlen($sql) - 1) . ") TYPE=MyISAM DEFAULT CHARSET=utf8;";
  $r = $sql->Execute($sql);
  if (!$r) {

  return FALSE;
  }
  //LOCK TABLES real_table WRITE, insert_table WRITE;UNLOCK TABLES;
  $r = $sql->Execute("INSERT INTO ibc1_backup_" . $id . "_" . $tshortname . " SELECT * FROM $tlongname");
  if (!$r) {

  return FALSE;
  }
  return TRUE;
  }

  public function Backup2($ServiceName) {
  $conn = $this->GetDBConn();
  $sql = $conn->CreateSelectSTMT("ibc1_dataservice");
  $sql->AddField("ServiceType");
  $sql->AddEqual("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);
  $sql->Execute();
  $r = $sql->Fetch(1);
  $sql->CloseSTMT();
  if (!$r) {

  return FALSE;
  }
  $sql = $conn->CreateInsertSTMT("ibc1_dataservice_backup");
  $sql->AddValue("ServiceName", $r->ServiceName, IBC1_DATATYPE_PURETEXT);
  $sql->AddValue("ServiceName", $r->ServiceType, IBC1_DATATYPE_PURETEXT);
  $sql->AddValue("BackupTime", "CURRENT_TIMESTAMP()", IBC1_DATATYPE_EXPRESSION);
  $sql->Execute();
  $backupid = $sql->GetLastInsertID();
  $sql->CloseSTMT();
  $nameroot = "ibc1_" . $r->ServiceType . $r->ServiceName . "_";
  $l = strlen($nameroot);
  $r = mysql_list_tables($this->cprovider->GetDBName());
  $c = mysql_num_rows($r);
  for ($i = 0; $i < $c; $i++) {
  $tablename = mysql_tablename($r, $i);
  if (strtolower(substr($tablename, 0, $l)) == strtolower($nameroot)) {
  if (!$this->BackupTable($tablename, substr($tablename, $l - strlen($tablename)), $backupid)) {

  return FALSE;
  }
  }
  }

  return TRUE;
  }

  public function Restore2($ID) {
  $ID = intval($ID);
  $conn = $this->GetDBConn();
  $sql = $conn->CreateSelectSTMT("ibc1_dataservice_backup");
  $sql->AddField("ServiceName");
  $sql->AddField("ServiceType");
  $sql->AddEqual("ID", $ID);
  $sql->Execute();
  $r = $sql->Fetch(1);
  $sql->CloseSTMT();
  if (!$r) {

  return FALSE;
  }
  $ServiceName = $r->ServiceName;
  $ServiceType = $r->ServiceType;
  //$r=$this->Backup($ServiceName);
  $r = $this->Backup2($ServiceName);
  if (!$r) {

  return FALSE;
  }
  $tablelist = array();
  $nameroot = "ibc1_" . $ServiceType . $ServiceName . "_";
  $l = strlen($nameroot);
  $r = mysql_list_tables($this->cprovider->GetDBName());
  $c = mysql_num_rows($r);
  for ($i = 0; $i < $c; $i++) {
  $tablename = mysql_tablename($r, $i);
  if (strtolower(substr($tablename, 0, $l)) == strtolower($nameroot)) {
  $tablelist[] = array($tablename, substr($tablename, $l - strlen($tablename)));
  }
  }
  mysql_free_result($r);

  foreach ($tablelist as $tablename) {

  $sql->Execute("DELETE FROM " . $tablename[0]);
  $sql->Execute("INSERT INTO " . $tablename[0] . " SELECT * FROM IBC1_Backup_" . $ID . "_" . $tablename[1]);
  }
  if ($conn->GetError()->HasError()) {

  return FALSE;
  }
  return TRUE;
  }
 */
?>
