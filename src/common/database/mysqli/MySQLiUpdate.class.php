<?php

/**
 * a UPDATE statement for MySQL via mysqli
 * @version 0.7.20110315
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database.mysqli
 */
class MySQLiUpdate extends MySQLiSTMT implements FieldValListInterface, ConditionInterface {

    private $update;
    private $_data = array();
    private $_datafile = array();

    function __construct($t="", $conn=NULL) {
        parent::__construct($conn);
        $this->update = new SQLUpdate($t);
    }

    public function SetTable($t) {
        $this->update->SetTable($t);
    }

    public function AddCondition($c, $l=IBC1_LOGICAL_AND) {
        $this->update->AddCondition($c, $l);
    }

    public function ClearConditions() {
        $this->update->Clear();
        $this->ClearParams("condition");
    }

    public function ConditionCount() {
        return $this->update->Count();
    }

    public function AddEqual($f, $v, $t=IBC1_DATATYPE_INTEGER, $l=IBC1_LOGICAL_AND) {
        $this->update->AddCondition("$f=?", $l);
        $this->AddParam($t, $v, "condition");
    }

    public function AddLike($f, $v, $l=IBC1_LOGICAL_AND) {
        $formatter = new DataFormatter($v, IBC1_DATATYPE_PURETEXT);
        if ($this->HasError())
            return FALSE;
        $this->update->AddCondition($f . " LIKE " . $formatter->GetSQLValue(TRUE), $l);
        return TRUE;
    }

    public function AddValue($f, $v, $t=IBC1_DATATYPE_INTEGER) {
        $this->update->AddValue($f, "?", $t);
        $this->AddParam($t, $v, "value");
    }

    public function ClearValues() {
        $this->update->ClearValues();
        $this->ClearParams("value");
    }

    public function ValueCount() {
        return $this->update->ValueCount();
    }

    public function SetData($f, $data) {
        $this->_data[] = array($this->ParamCount("value"), $data);
        $this->AddValue($f, NULL, IBC1_DATATYPE_BINARY);
    }

    public function SetDataFromFile($f, $filename) {
        $this->_datafile[] = array($this->ParamCount("value"), $filename);
        $this->AddValue($f, NULL, IBC1_DATATYPE_BINARY);
    }

    public function Execute() {
        if (!$this->connObj) {
            $this->errorlist->AddItem(4, "database unconnected");
            return FALSE;
        }
        $this->sql = $this->update->GetSQL();
        if (!$this->_prepareSTMT())
            return FALSE;
        if (!$this->_bindParams())
            return FALSE;
        //send long data
        foreach ($this->_data as $item) {
            mysqli_stmt_send_long_data($this->stmtObj, $item[0], $item[1]);
        }
        //send long data from file
        foreach ($this->_datafile as $item) {
            $fp = fopen($item[1], "r");
            while (!feof($fp)) {
                mysqli_stmt_send_long_data($this->stmtObj, $item[0], fread($fp, 1024 * 8));
            }
            fclose($fp);
        }
        if (!mysqli_stmt_execute($this->stmtObj)) {
            $this->errorlist->AddItem(4, mysqli_stmt_error($this->stmtObj));
            return FALSE;
        }
        return TRUE;
    }

    public function GetAffectedRowCount() {
        return mysqli_stmt_affected_rows($this->stmtObj);
    }

}

?>
