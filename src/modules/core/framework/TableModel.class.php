<?php

/**
 * A generic template model for a table, based on a simple php string template.
 * TODO debug TableModel
 * @version 0.6.20130118
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.framework
 */
abstract class TableModel extends BoxModel {

    /**
     * number of columns
     * @var int 
     */
    protected $_coln;

    /**
     * number of rows
     * @var int
     */
    protected $_rown;

    /**
     * template content for an item
     * @var string
     */
    protected $_itemtpl;

    /**
     * template content for a row
     * @var string
     */
    protected $_rowtpl;

    /**
     * number of items added
     * @var int
     */
    protected $_itemcount;

    /**
     * content to fill in the first grid when the table is empty
     * @var string
     */
    protected $_itemempty;

    /**
     * content to fill in the rest empty grids
     * @var string
     */
    protected $_itemrest;

    /**
     * stores HTML for the table
     * @var string 
     */
    private $_table;

    /**
     * stores HTML for a new row
     * @var string
     */
    private $_row;

    /**
     * constructor
     * @param string $itemTplName   name of the template for an item
     * @param string $rowTplName    name of the template for a row
     * @param string $tableTplName  name of the template for the table
     * @param int $coln     maximum number of columns
     * @param string $classname     optional, name of the extended class 
     * (using __CLASS__); if not specified, use "TableModel"
     * @see $_itemtpl
     * @see $_rowtpl
     * @see $_coln
     */
    function __construct($itemTplName, $rowTplName, $tableTplName, $coln, $classname = NULL) {
        parent::__construct(empty($classname) ? __CLASS__ : $classname);
        $this->parentClassName = __CLASS__;
        $this->_coln = intval($coln);
        if ($this->_coln < 1)
            $this->_coln = 1;
        $this->_rown = 0;
        $this->_itemcount = 0;
        $this->_table = '';
        $this->_row = '';
        $this->_itemempty = '';
        $this->_itemrest = '';
        $this->_itemtpl = GetTemplate($itemTplName, $classname);
        $this->_rowtpl = GetTemplate($rowTplName, $classname);
        $this->containerTplName = $tableTplName;
        $this->className = $classname;
        $this->contentFieldName = 'TableContent';
    }

    /**
     * set a template for the rest empty grids
     * @param string $tplname   template name
     * @param array $vars   optional
     * @see Tpl2HTML()
     * @see $_itemrest
     */
    public function SetRestItem($tplname, array $vars = array()) {
        $this->_itemrest = $this->TransformTpl($tplname, $vars);
    }

    /**
     * set a template to fill in the first grid when the table is empty
     * @param string $tplname   template name
     * @param array $vars   optional
     * @see Tpl2HTML()
     * @see $_itemempty
     */
    public function SetEmptyItem($tplname, array $vars = array()) {
        $this->_itemempty = $this->TransformTpl($tplname, $vars);
    }

    /**
     * append content of the current row to the table and add 1 to the number of rows
     * @param string $row 
     */
    private function AddRow($row) {
        $this->_table .= $this->Tpl2HTML($this->_rowtpl, array('RowContent' => $row));
        $this->_rown++;
    }

    /**
     * add an array of items by assigning their associated variables
     *  to the item template
     * @param array $items 
     * <code>
     * array(
     *     array(
     *         [variable1 name]=>[variable1 value],
     *         [variable2 name]=>[variable2 value],
     *         ...
     *     ),
     *     array(
     *         [variable1 name]=>[variable1 value],
     *         [variable2 name]=>[variable2 value],
     *         ...
     *     ),
     *     ...
     * )
     * </code>
     */
    public function AddItems(array $items) {
        foreach ($items as $item) {
            $this->AddItem($item);
        }
    }

    /**
     * assign variables associated with the new item to the item template
     * and append the result to the attribute $_row
     * @param array $vars   variables associated with the item 
     * @see Tpl2HTML()
     */
    public function AddItem(array $vars) {
        $this->_row.=$this->Tpl2HTML($this->_itemtpl, $vars);
        $this->_itemcount++;
        $m = $this->_itemcount % $this->_coln;
        if ($m == 0) {
            $this->AddRow($this->_row);
            $this->_row = '';
        }
    }

    /**
     * get the number of items added
     * @return int
     */
    public function ItemCount() {
        return $this->_itemcount;
    }

    /**
     * get the number of columns
     * @return int
     */
    public function ColCount() {
        return $this->_coln;
    }

    /**
     * get the number of rows
     * @return int
     */
    public function RowCount() {
        return $this->_row == '' ? $this->_rown : $this->_rown + 1;
    }

    /**
     * get the size of the table, in other words, 
     * the max number of items in the table
     * @return int
     */
    public function Size() {
        return $this->RowCount() * $this->_coln;
    }

    /**
     * clear all items in the table
     */
    public function Clear() {
        $this->_row = '';
        $this->_table = '';
        $this->_itemcount = 0;
        $this->_rown = 0;
    }

    abstract protected function LoadData();

    final protected function LoadContent() {
        $this->LoadData();
        return $this->_table;
    }

    public function GetHTML() {
        if ($this->_itemcount == 0) {
            $this->AddRow($this->_itemempty);
        } else {
            $m = $this->_itemcount % $this->_coln;
            if ($m > 0) {
                while ($m < $this->_coln) {
                    $this->_row .= $this->_itemrest;
                    $m++;
                }
                $this->AddRow($this->_row);
                $this->_row = '';
            }
        }
        return parent::GetHTML();
    }

}
