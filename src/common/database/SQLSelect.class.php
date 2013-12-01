<?php

/**
 * a SELECT statement
 * @version 0.7.20110313
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database
 */
class SQLSelect implements FieldExpListInterface, ConditionInterface {

    protected $table = "";
    protected $fieldlist;
    protected $condition;
    protected $orderby = "";
    protected $groupby = "";
    protected $having = "";

    function __construct($t="") {
        $this->fieldlist = new SQLFieldExpList();
        $this->condition = new SQLCondition();
        $this->SetTable($t);
    }

    public function SetTable($t) {
        $this->table = $t;
    }

    public function JoinTable($t, $on) {
        $this->table.=" RIGHT JOIN $t ON $on";
    }

    public function AddField($exp, $alias="") {
        $this->fieldlist->AddField($exp, $alias);
    }

    public function ClearFields() {
        $this->fieldlist->Clear();
    }

    public function FieldCount() {
        return $this->fieldlist->Count();
    }

    public function AddCondition($c, $l=IBC1_LOGICAL_AND) {
        $this->condition->AddCondition($c, $l);
    }

    public function ClearConditions() {
        $this->condition->Clear();
    }

    public function ConditionCount() {
        return $this->condition->Count();
    }

    public function OrderBy($field, $mode=IBC1_ORDER_ASC) {
        $this->orderby = $field;
        if ($mode == IBC1_ORDER_ASC)
            $this->orderby.=" ASC";
        else
            $this->orderby.=" DESC";
    }

    public function GroupBy($field, $having="") {
        $this->groupby = $field;
        $this->having = $having;
    }

    public function GetSQL() {
        $sql_f = $this->fieldlist->GetAllAsString();
        if ($sql_f == "")
            $sql_f = "*";
        $sql = "SELECT $sql_f FROM " . $this->table;

        $sql_c = $this->condition->GetExpression();
        if ($sql_c != "")
            $sql.=" WHERE $sql_c";

        if ($this->orderby != "")
            $sql.=" ORDER BY " . $this->orderby;
        if ($this->groupby != "") {
            $sql.=" GROUP BY " . $this->groupby;
            if ($this->having != "")
                $sql.=" HAVING " . $this->having;
        }

        return $sql;
    }

    public function SetRowNumber($i) {
        mysqli_stmt_data_seek($this->stmtObj, $i);
    }

    public function GetRowCount() {
        return mysqli_stmt_num_rows($this->stmtObj);
    }

}

?>
