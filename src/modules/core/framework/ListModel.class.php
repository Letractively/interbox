<?php

/**
 * A template model for a list, based on a simple php string template.
 * 
 * @version 0.6.20130118
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.framework
 */
abstract class ListModel extends BoxModel {

    /**
     * stores HTML for all items in the list
     * @var string
     */
    protected $_items;

    /**
     * content of a template for an item
     * @var string
     */
    protected $_itemtpl;

    /**
     * content to fill in the first item when the list is empty
     * @var string
     */
    protected $_itemempty;

    /**
     * number of items
     * @var int
     */
    protected $_count;

    /**
     * constructor
     * @param string $itemTplName   name of a template for an item
     * @param string $classname     optional, name of the extended class 
     * (using __CLASS__); if not specified, use "ListModel" 
     * @see $_itemtpl
     */
    function __construct($itemTplName, $classname = NULL) {
        parent::__construct(empty($classname) ? __CLASS__ : $classname);
        $this->parentClassName = __CLASS__;
        $this->_itemtpl = GetTemplate($itemTplName, $this->className);
        $this->_itemempty = '';
        $this->_items = '';
        $this->_count = 0;
        $this->contentFieldName = 'ListItems';
    }

    /**
     * set a template for the list container
     * @param string $tplname   template name
     * @param array $vars   optional
     */
    public function SetContainer($tplname, array $vars = array()) {
        $this->containerTplName = $tplname;
        foreach ($vars as $field => $content)
            $this->SetField($field, $content);
    }

    /**
     * set a template for the first item when the list is empty
     * @param type $tplname   template name
     * @param array $vars   optional
     * @see Tpl2HTML()
     * @see $_itemempty
     */
    public function SetEmptyItem($tplname, array $vars = array()) {
        $this->_itemempty = $this->TransformTpl($tplname, $vars);
    }

    /**
     * add an array of items by assigning their associated variables
     * @param array $items 
     * @see TableModel::AddItems()
     */
    public function AddItems(array $items) {
        foreach ($items as $item) {
            $this->AddItem($item);
        }
    }

    /**
     * assign variables associated with the new item to the item template
     * and append the result to the attribute $_items
     * @param array $vars 
     * @see Tpl2HTML()
     * @see $_items
     */
    public function AddItem(array $vars) {
        $this->_items .= $this->Tpl2HTML($this->_itemtpl, $vars);
        $this->_count++;
    }

    /**
     * clear all items
     */
    public function Clear() {
        $this->_count = 0;
        $this->_items = '';
    }

    /**
     * get the number of items in the list
     * @return int
     * @see $_count
     */
    public function ItemCount() {
        return $this->_count;
    }

    abstract protected function LoadData();

    final protected function LoadContent() {
        $this->LoadData();
        return $this->_items;
    }

    public function GetHTML() {
        if ($this->_count == 0)
            $this->_items = $this->_itemempty;
        return parent::GetHTML();
    }

}
