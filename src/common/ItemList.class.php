<?php

/**
 * list for any items
 * @version 0.7.20110316
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common
 */
class ItemList {

    protected $_item = array();

    /**
     * return a certain item if an index is referred, or the current item;
     * if the given index does not exist, return NULL
     * @param int index
     * @return mixed
     */
    public function GetItem($index=-1) {
        $item = NULL;
        if ($index < 0) {
            $item = current();
            if ($item)
                return $item;
            $index = $this->GetIndex();
        }
        if ($index > -1 && array_key_exists($index, $this->_item))
            return $this->_item[$index];
        return NULL;
    }

    /**
     * return current index, return -1 if the index is invalid
     * @return int
     */
    public function GetIndex() {
        $index = key($this->_item);
        if (is_numeric($index))
            return intval($index);
        return -1;
    }

    /**
     * return the current item and move to the next
     * @return mixed
     */
    public function GetEach() {
        list($k, $v) = each($this->_item);
        return $v;
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
     * add 1 to current index so as to get the next item via GetItem()
     */
    public function MoveNext() {
        next($this->_item);
    }

    /**
     * check if the given index exists
     * @return bool
     */
    public function ItemExists($index) {
        return array_key_exists($index, $this->_item);
    }

    /**
     * check if the given value exists
     * @return bool
     */
    public function ValueExists($value) {
        return in_array($value, $this->_item);
    }

    /**
     * add a item to the list
     * @param mixed item
     */
    public function AddItem($item) {
        $this->_item[] = $item;
    }

    /**
     * remove an item from the list
     */
    public function Remove($index) {
        unset($this->_item[$index]);
    }

    /**
     * return the number of items in the list
     * @return int
     */
    public function Count() {
        return count($this->_item);
    }

    /**
     * remove all items from the list
     */
    public function Clear() {
        $this->_item = array();
    }

}

?>