<?php

/**
 * list for properties with keys and values
 * @version 0.7.20110316
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common
 */
class PropertyList {

    protected $_item = array();

    /**
     * set a value to its key
     *
     * replace the old value if the key exists;
     * add a new key in the list together with its value and type
     * @param string $key
     * @param mixed $value
     * @param int $type
     */
    public function SetValue($key, $value, $type=IBC1_DATATYPE_INTEGER) {
        $this->_item[$key] = array($value, intval($type));
    }

    /**
     * append a string to the end of a value specified by the given key
     * (this method only works if the original type is IBC1_DATATYPE_PURETEXT)
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function AppendValue($key, $value) {
        $v = $this->GetValue($key, IBC1_VALUEMODE_ALL);
        if ($v[1] != IBC1_DATATYPE_PURETEXT)
            return FALSE;
        $v[0].=$value;
        $this->SetValue($key, $v[0], $v[1]);
        return TRUE;
    }

    /**
     * get the current key name
     * @return string
     */
    public function GetKey() {
        return key($this->_item);
    }

    /**
     * get the value for a given key or get
     * @param string $key
     * @param int $mode
     * <ul>
     * <li>0=IBC1_VALUEMODE_VALUEONLY</li>
     * <li>1=IBC1_VALUEMODE_TYPEONLY</li>
     * <li>2=IBC1_VALUEMODE_ALL</li>
     * </ul>
     * @return mixed
     */
    public function GetValue($key=NULL, $mode=IBC1_VALUEMODE_VALUEONLY) {
        if (is_null($key)) {
            $key = key($this->_item);
            if (is_null($key))
                return NULL;
        }else if (!array_key_exists($key, $this->_item)) {
            return NULL;
        }
        switch ($mode) {
            case IBC1_VALUEMODE_VALUEONLY:
            case IBC1_VALUEMODE_TYPEONLY:
                return $this->_item[$key][$mode];
            case IBC1_VALUEMODE_ALL:
                return $this->_item[$key];
        }
        return NULL;
    }

    /**
     * return the current item and move to the next
     * @return mixed
     */
    public function GetEach() {
        return each($this->_item);
    }

    /**
     * set index 0
     *
     * strongly recommend you involk this method before using GetEach() in a WHILE loop
     */
    public function MoveFirst() {
        reset($this->_item);
    }

    /**
     * add 1 to current index so as to get the next via GetValue() or GetKey()
     */
    public function MoveNext() {
        next($this->_item);
    }

    /**
     * check if the given key exists
     * @return bool
     */
    public function KeyExists($key) {
        return array_key_exists($key, $this->_item);
    }

    /**
     * check if the given value exists
     * @return bool
     */
    public function ValueExists($value) {
        return in_array($value, $this->_item);
    }

    /**
     * remove an item from the list
     */
    public function Remove($key) {
        if (array_key_exists($key, $this->_item)) {
            unset($this->_item[$key]);
        }
    }

    /**
     * remove all items from the list
     */
    public function Clear() {
        $this->_item = array();
    }

    /**
     * return the number of items in the list
     * @return int
     */
    public function Count() {
        return count($this->_item);
    }

}

?>