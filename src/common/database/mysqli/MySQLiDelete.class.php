<?php

/**
 * a DELETE statement for MySQL via mysqli
 * @version 0.7.20110315
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database.mysqli
 */
class MySQLiDelete extends MySQLiSTMT implements ConditionInterface {

    private $delete;

    function __construct($t="", $conn=NULL) {
        parent::__construct($conn);
        $this->delete = new SQLDelete($t);
    }

    public function SetTable($t) {
        $this->delete->SetTable($t);
    }

    public function AddCondition($c, $l=IBC1_LOGICAL_AND) {
        $this->delete->AddCondition($c, $l);
    }

    public function AddEqual($f, $v, $t=IBC1_DATATYPE_INTEGER, $r=IBC1_LOGICAL_AND) {
        $this->delete->AddCondition("$f=?", $r);
        $this->AddParam($t, $v);
    }

    public function AddLike($f, $v, $l=IBC1_LOGICAL_AND) {
        $formatter = new DataFormatter($v, IBC1_DATATYPE_PURETEXT);
        if ($this->HasError())
            return FALSE;
        $this->delete->AddCondition($f . " LIKE " . $formatter->GetSQLValue(TRUE), $l);
        return TRUE;
    }

    public function ClearConditions() {
        $this->delete->ClearConditions();
        $this->ClearParams();
    }

    public function ConditionCount() {
        return $this->delete->ConditionCount();
    }

    public function Execute() {
        $this->sql = $this->delete->GetSQL();
        return parent::Execute();
    }

}

?>
