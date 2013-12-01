<?php

/**
 *
 * @version 0.7.20110318
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.dataservice
 */
abstract class DataList extends DataService {

    private $_PageSize = 0;
    private $_PageNumber = 0;
    private $_PageCount = 0;
    private $_TotalCount = 0;

    function __construct(ErrorList $EL=NULL) {
        parent::__construct($EL);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName, $ServiceType="") {
        $this->Clear();
        $this->_PageSize = 0;
        $this->_PageNumber = 0;
        return parent::OpenService($Conns, $ServiceName, $ServiceType);
    }

    public function CloseService() {
        parent::CloseService();
        $this->Clear();
        $this->_PageSize = 0;
        $this->_PageNumber = 0;
    }

    public function GetPageSize() {
        return $this->_PageSize;
    }

    public function SetPageSize($s) {
        $s = intval($s);
        if ($s < 1)
            $s = 0;
        $this->_PageSize = $s;
    }

    public function GetPageNumber() {
        return $this->_PageNumber;
    }

    public function SetPageNumber($n) {
        $n = intval($n);
        if ($n < 1)
            $n = 1;
        $this->_PageNumber = $n;
    }

    public function GetPageCount() {
        return $this->_PageCount;
    }

    public function GetTotalCount() {
        return $this->_TotalCount;
    }

    protected function GetCounts1(DBSQLSTMT $sql) {
        //$conn = $this->GetDBConn();
        $this->_TotalCount = intval($this->_TotalCount);
        $this->_PageSize = intval($this->_PageSize);
        if ($this->_PageSize > 0) {
            $sql->Execute();
            $a = $sql->Fetch(2);
            $sql->CloseSTMT();
            $this->_TotalCount = intval($a[0]);
            $b = $this->_TotalCount / $this->_PageSize;
            if ($b > intval($b))
                $b = 1 + intval($b);
            $this->_PageCount = $b;
        }
        else {
            $this->_PageCount = 0;
            $this->_TotalCount = 0;
        }
    }

    protected function GetCounts2() {
        if ($this->_PageSize < 1 && count($this->_item) > 0) {
            $this->_TotalCount = count($this->_item);
            $this->_PageCount = 1;
        }
    }

    abstract function LoadList();
    /* --------------------------------------------------------------------
      The following is copied from ItemList
      -------------------------------------------------------------------- */

    private $_item = array();

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
     * @param int index
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
