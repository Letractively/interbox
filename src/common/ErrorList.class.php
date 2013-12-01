<?php

/**
 * @version 0.6.20110412
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common
 */
class ErrorList extends ItemList {

    //private $eList=array();
    private $eE = FALSE;
    private $eSource = "";

    function __construct($s="") {
        $this->SetSource($s);
    }

    public function AddItem($code, $msg, $source="") {
        if ($source == "")
            $source = $this->eSource;
        $this->eE = TRUE;
        //$this->eList[]=array($code,$msg,$source);
        parent::AddItem(array($code, $msg, $source));
        //debug
        echo "<P><font color=\"red\">error[$code,$source]:<B>$msg</B></font></P>";
    }

    public function SetSource($s) {
        $this->eSource = $s;
    }

    /*
      public function Length()
      {
      return count($this->eList);
      }
      public function Clear()
      {
      $this->eList=array();
      $this->eE=FALSE;
      }
     */

    public function HasError() {
        return $this->eE;
    }

    public function GetCode($n=-1) {
        $item = $this->GetItem($n);
        if (!$item)
            return -1;
        return $item[0];
    }

    public function GetMessage($n=-1) {
        $item = $this->GetItem($n);
        if (!$item)
            return "no message can be found";
        return $item[1];
    }

    public function GetSource($n=-1) {
        $item = $this->GetItem($n);
        if (!$item)
            return __CLASS__;
        return $item[2];
    }

    public function PrintAll() {
        die("......");
    }

    /*
      public function GetEach()
      {
      return each($this->eList);
      }
     */
}

?>
