<?php

/**
 * interface for a field list with values and their types
 * (an UPDATE or an INSERT generator has to realize this interface)
 * @version 0.1.20110314
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database
 */
interface FieldValListInterface {

    /**
     * add a field with its value to the list
     * @param string $f field name
     * @param mixed $v  field value
     * @param int $t    code of value type
     */
    public function AddValue($f, $v, $t=IBC1_DATATYPE_INTEGER);

    /**
     * remove all fields in the list
     */
    public function ClearValues();

    /**
     * get the number of fields in the list
     */
    public function ValueCount();
}

?>
