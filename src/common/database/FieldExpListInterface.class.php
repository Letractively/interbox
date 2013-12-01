<?php

/**
 * interface for a simple field list with support of expressions and alias in a SQL statement
 * (a SELECT generator has to realize this interface)
 * @version 0.1.20110314
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database
 */
interface FieldExpListInterface {

    /**
     * add a field name or an expression with its alias name if provided
     * to create a field in the list
     * @param string $exp
     * a field name or an expression
     * @param string $alias
     * alias name for the field, optional
     */
    public function AddField($exp, $alias="");

    /**
     * remove all fields or expressions in the list
     */
    public function ClearFields();

    /**
     * get the number of fields in the list
     */
    public function FieldCount();
}

?>
