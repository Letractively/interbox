<?php

/**
 * field list with values and their types
 * @version 0.3.20110327
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database
 */
class SQLFieldValList extends PropertyList {

    /**
     * add a field with its value to the list
     * @param string $f field name
     * @param mixed $v  field value
     * @param int $t    code of value type
     */
    public function AddValue($f, $v, $t=IBC1_DATATYPE_INTEGER) {
        $formatter = new DataFormatter($v, $t);
        if (!$formatter->HasError()) {
            $this->SetValue($f, $formatter->GetSQLValue(), $formatter->GetType());
        }
    }

}

?>