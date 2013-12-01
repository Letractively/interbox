<?php

/**
 * simple field list supporting expressions and alias in a SQL statement, like SELECT
 * @version 0.2.20110314
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database
 */
class SQLFieldExpList extends ItemList {

    /**
     * add a field name or an expression with its alias name if provided
     * to create a field in the list
     * @param string $exp
     * a field name or an expression
     * @param string $alias
     * alias name for the field, optional
     */
    public function AddField($exp, $alias="") {
        if ($alias == "")
            $this->AddItem($exp);
        else
            $this->AddItem("$exp AS $alias");
    }

    /**
     * output every item as field list in a SELECT statement
     * @return string
     */
    public function GetAllAsString() {
        $str = "";
        foreach ($this->_item as $f) {
            if ($str != "")
                $str.=",";
            $str.=$f;
        }
        return $str;
    }

}

?>