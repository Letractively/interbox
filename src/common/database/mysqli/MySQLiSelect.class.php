<?php

/**
 * a SELECT statement for MySQL via mysqli
 * @version 0.7.20110315
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database.mysqli
 */
class MySQLiSelect extends MySQLiSTMT implements FieldExpListInterface, ConditionInterface {

    protected $select;
    protected $limit = "";

    function __construct($t="", &$conn=NULL) {
        parent::__construct($conn);
        $this->select = new SQLSelect($t);
    }

    public function SetTable($t) {
        $this->select->SetTable($t);
    }

    public function JoinTable($t, $on) {
        $this->select->JoinTable($t, $on);
    }

    public function AddField($exp, $alias="") {
        $this->select->AddField($exp, $alias);
    }

    public function ClearFields() {
        $this->select->ClearFields();
    }

    public function FieldCount() {
        return $this->select->FieldCount();
    }

    public function AddCondition($c, $l=IBC1_LOGICAL_AND) {
        $this->select->AddCondition($c, $l);
    }

    public function AddEqual($f, $v, $t=IBC1_DATATYPE_INTEGER, $r=IBC1_LOGICAL_AND) {
        $this->select->AddCondition("$f=?", $r);
        $this->AddParam($t, $v);
    }

    public function AddLike($f, $v, $l=IBC1_LOGICAL_AND) {
        $formatter = new DataFormatter($v, IBC1_DATATYPE_PURETEXT);
        if ($this->HasError())
            return FALSE;
        $this->select->AddCondition($f . " LIKE " . $formatter->GetSQLValue(TRUE), $l);
        return TRUE;
    }

    public function ClearConditions() {
        $this->select->ClearConditions();
        $this->ClearParams();
    }

    public function ConditionCount() {
        return $this->select->ConditionCount();
    }

    public function OrderBy($field, $mode=IBC1_ORDER_ASC) {
        $this->select->OrderBy($field, $mode);
    }

    public function GroupBy($field, $having="") {
        $this->select->GroupBy($field, $having);
    }

    public function SetLimit($PageSize, $PageNumber) {
        $PageNumber = intval($PageNumber);
        if ($PageNumber < 1)
            $PageNumber = 1;
        $length = intval($PageSize);
        if ($length < 1) {
            $this->limit = "";
        } else {
            $start = ($PageNumber - 1) * $length;
            //start from 0
            $this->limit = " LIMIT $start , $length";
        }
    }

    public function Execute() {
        $sql = $this->select->GetSQL();
        if ($this->limit != "")
            $sql.=$this->limit;
        $this->sql = &$sql;
        return parent::Execute();
    }

}

?>
