<?php

LoadIBC1Class('ListModel', 'framework');

class CustomList extends ListModel {

    function __construct($mode) {
        parent::__construct($mode . '_item', __CLASS__);
        $this->parentClassName = __CLASS__;
        $this->containerTplName = $mode . '_list';
    }

    public function SetContainer(array $vars = array()) {
        parent::SetContainer($this->containerTplName, $vars);
    }

    public function After($page) {
        
    }

    public function Before($page) {
        
    }

    protected function LoadData() {
        
    }

}