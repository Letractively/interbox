<?php

/**
 *
 * @version 0.7.20110316
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database.mysqli
 */
class MySQLiTable extends MySQLiSTMT implements FieldDefListInterface {

    private $table;

    function __construct($m, $t="", $conn=NULL) {
        parent::__construct($conn);
        $this->table = new SQLTable($m, $t);
    }

    public function SetMode($m) {
        $this->table->SetMode($m);
    }

    public function SetTable($t) {
        $this->table->SetTable($t);
    }

    public function AddField($fieldname, $fieldtype=IBC1_DATATYPE_INTEGER, $length=1, $isnull=TRUE, $default=NULL, $ispkey=FALSE, $keyname="", $autoincrement=FALSE) {
        $this->table->AddField($fieldname, $fieldtype, $length, $isnull, $default, $ispkey, $keyname, $autoincrement);
    }

    public function ClearFields() {
        $this->table->ClearFields();
    }

    public function FieldCount() {
        return $this->table->FieldCount();
    }

    public function Execute() {
        $sql = $this->table->GetSQL();
        if (!is_null($sql)) {
            $this->sql = &$sql;
            return parent::Execute();
        }
        return FALSE;
    }

//TODO: tablelists and fieldlists
    public function GetTableList() {
        return $this->Execute("SHOW TABLES FROM " . $this->dbname);
    }

    public function GetFieldList($table) {
        return $this->Execute("SHOW COLUMNS FROM " . $this->dbname . ".$table;");
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

}

?>