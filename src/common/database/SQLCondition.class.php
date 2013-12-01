<?php

/**
 * generating a part of SQL concerning conditions while selecting, like 'WHERE'
 * @version 0.2.20110314
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database
 */
class SQLCondition {

    /**
     * generated expression of conditions in SQL statement
     * @var string
     */
    private $cdt = "";
    /**
     * number of conditions
     * @var int
     */
    private $c = 0;

    /**
     * add a logical operator like 'AND' or 'OR'
     * @param int $l
     * an integer code for logical operators 'AND' and 'OR',
     * which connects the new condition and old ones;<br />
     * constants and their values are listed below:
     * <ul>
     * <li>0 - IBC1_LOGICAL_AND</li>
     * <li>1 - IBC1_LOGICAL_OR</li>
     * </lu>
     * @return bool
     * If there is no condition added when this method is involked,
     * or the parameter is of an invalid value, it returns FALSE.
     */
    private function addLogical($l) {
        if ($this->cdt == "")
            return FALSE;
        switch ($l) {
            case IBC1_LOGICAL_AND:
                $this->cdt.=" AND ";
                return TRUE;
            case IBC1_LOGICAL_OR:
                $this->cdt.=" OR ";
                return TRUE;
        }
        return FALSE;
    }

    /**
     * add a new condition connected with a logical operator like 'AND' or 'OR'
     * @param mixed $c
     * an expression in form of string or a SQLCondition object
     * @param int $l
     * an integer code for logical operators 'AND' and 'OR',
     * which connects the new condition and old ones;<br />
     * constants and their values are listed below:
     * <ul>
     * <li>0 - IBC1_LOGICAL_AND</li>
     * <li>1 - IBC1_LOGICAL_OR</li>
     * </lu>
     * @return bool
     * If there is no new condition added then it will return FALSE.
     */
    public function AddCondition($c, $l=IBC1_LOGICAL_AND) {
        if ($c == NULL)
            return FALSE;
        if ($c instanceof SQLCondition) {
            if ($this->c > 1)
                $this->cdt = "(" . $this->cdt . ")";
            $this->addLogical($l);
            $this->cdt.=$c->GetExpression();
            $this->c+=$c->Count();
        } else {
            $this->addLogical($l);
            $this->cdt.=$c;
            $this->c++;
        }
        return TRUE;
    }

    /*
      '?' may not be supported by all database connectors

      public function AddEqual($f, $v, $t=IBC1_DATATYPE_INTEGER, $r=IBC1_LOGICAL_AND) {
      $this->select->AddCondition($f . "=?", $r);
      $this->AddParam($t, $v);
      }

      public function AddLike($f, $v, $l=IBC1_LOGICAL_AND) {//data formatting
      $this->dtf->SetData($v, IBC1_DATATYPE_PURETEXT);
      if ($this->HasError())
      return FALSE;
      $this->select->AddCondition($f . " LIKE \"%" . $this->dtf->GetData() . "%\"", $l);
      }
     */

    /**
     * get the counted number of conditions
     * @return int
     */
    public function Count() {
        return $this->c;
    }

    /**
     * remove all conditions in the current object
     */
    public function Clear() {
        $this->cdt = "";
        $this->c = 0;
    }

    /**
     * get the generated expression
     * @return string
     */
    public function GetExpression() {
        return $this->cdt;
    }

}

?>