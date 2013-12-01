<?php

/**
 * interface for a field list with greater details for field definition
 * (a CREATE TABLE generator has to realize this interface)
 * @version 0.1.20110314
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database
 */
interface FieldDefListInterface {

    /**
     * add a field definition into the list
     * @param string $fieldname
     * @param int $fieldtype
     * constants defined as IBC1_DATATYPE_[type name];default value is IBC1_DATATYPE_INTEGER
     * @param int $length
     * default value is 1
     * @param bool $isnull
     * default value is TRUE
     * @param string $default
     * default value is null
     * @param bool $ispkey
     * TRUE if the field is the primary key of the table; default value is FALSE
     * @param string $keyname
     * @param bool $autoincrement
     * Only when data type of the field is int or
     * $fieldtype is IBC1_DATATYPE_INTEGER,
     * this parameter can be given the value of TRUE.
     */
    public function AddField($fieldname, $fieldtype=IBC1_DATATYPE_INTEGER, $length=1, $isnull=TRUE, $default=NULL, $ispkey=FALSE, $keyname="", $autoincrement=FALSE);

    /**
     * remove all field definitions in the list
     */
    public function ClearFields();

    /**
     * get the number of field definitions in the list
     * @return int
     */
    public function FieldCount();
}

?>
