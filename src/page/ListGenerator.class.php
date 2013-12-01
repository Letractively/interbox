<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.page
 */
LoadIBC1Class("PanelGenerator", "page");

class ListGenerator extends PanelGenerator {

    private $item = "";
    private $itemlist = "";
    private $header = "";
    private $footer = "";

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($Conns, $ServiceName, $EL);
        $this->tplType = IBC1_TEMPLATETYPE_LIST;
        $this->GetError()->SetSource(__CLASS__);
    }

    public function SetItemField($fieldName, $value) {
        if ($fieldName == "")
            return FALSE;
        $this->item = eregi_replace("\{ *IBC1_Template_ItemField *= *$fieldName *\}", $value, $this->item);

        return TRUE;
    }

    public function SetContent($c, $m=0) {
        $a = split("\{ *IBC1_Template_Separator *\}", $c);
        $this->header = "";
        $this->footer = "";

        if ($m == 0) {
            switch (count($a)) {
                case 3:
                    $this->footer = $a[2];
                case 2:
                    $this->header = $a[0];
                    $this->content = $a[1];
                    break;
                default:
                    $this->content = $a[0];
            }
        } else {
            $this->content = "{IBC1_Template_ItemField=$c}";
        }
        $this->item = $this->content;
    }

    public function AddItem() {
        $this->itemlist.=$this->item;
        $this->item = $this->content;
    }

    public function GetResult() {
        $this->MoveFirst();
        while (list($key, $item) = $this->GetEach()) {
            $this->header = eregi_replace("\{ *IBC1_Template_Field *= *" . $key . " *\}", $item[0], $this->header);
            $this->itemlist = eregi_replace("\{ *IBC1_Template_Field *= *" . $key . " *\}", $item[0], $this->itemlist);
            $this->footer = eregi_replace("\{ *IBC1_Template_Field *= *" . $key . " *\}", $item[0], $this->footer);
        }
        return $this->header . $this->itemlist . $this->footer;
    }

}

/*
  class ListGenerator extends PanelGenerator
  {
  private $items="";
  function __construct(DBConnProvider $Conns,$ServiceName,ErrorList $EL=NULL)
  {
  parent::__construct($Conn,$ServiceName,$EL);
  $this->tplType=IBC1_TEMPLATETYPE_LIST;
  }
  public function AddItem($item)
  {
  $this->items.=$item;
  }
  public function GetResult()
  {
  $c=parent::GetResult();
  $c=eregi_replace("\{ *IBC1_Template_ListItems *\}",$this->items,$c);
  return $c;
  }
  }
 */
?>
