<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.page
 */
class TemplateItemEditor extends DataItem {

    private $id = 0;
    private $isnew = TRUE;
    private $isFieldListChanged = FALSE;
    //private $FieldList=array();
    private $FieldList = NULL;
    private $tplThemeID = 0;
    private $tplType = 0;

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        parent::OpenService($Conns, $ServiceName, "pag");
    }

    public function Create() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $this->id = 0;
        $this->isnew = TRUE;
        //$this->SetValue("tplName","",IBC1_DATATYPE_PURETEXT);
        //$this->SetValue("tplThemeID",0,IBC1_DATATYPE_INTEGER);
        //$this->SetValue("tplType",0,IBC1_DATATYPE_INTEGER);
        //$this->SetValue("tplContent","",IBC1_DATATYPE_PURETEXT);
        //$this->FieldList=array();
        $this->FieldList = new WordList();
    }

    public function Open($id) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_pag" . $this->GetServiceName() . "_template");
        $sql->AddField("tplThemeID");
        $sql->AddField("tplType");
        $sql->AddField("tplFieldList");
        $sql->AddEqual("tplID", $id);

        $sql->Execute();
        $r = $sql->Fetch();
        $sql->CloseSTMT();
        if ($r) {
            $this->id = intval($id);
            //$this->SetValue("tplName",$r->tplName,IBC1_DATATYPE_PURETEXT);
            $this->tplThemeID = $r->tplThemeID;
            $this->tplType = $r->tplType;
            //$this->SetValue("tplContent",$r->tplContent,IBC1_DATATYPE_PURETEXT);
            //$this->FieldList=explode("|",$r->tplFieldList);

            $this->FieldList = new WordList($r->tplFieldList);
            $this->isnew = FALSE;
            return TRUE;
        }

        return FALSE;
    }

    public function OpenByName($thmName, $tplName) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_pag" . $this->GetServiceName() . "_theme");
        $sql->JoinTable("ibc1_pag" . $this->GetServiceName() . "_Template", "thmID=tplThemeID");
        $sql->AddField("tplID");
        $sql->AddField("tplThemeID");
        $sql->AddField("tplType");
        $sql->AddField("tplFieldList");
        $sql->AddEqual("tplName", $tplName, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        $sql->AddEqual("thmName", $thmName, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);

        $sql->Execute();
        $r = $sql->Fetch();
        $sql->CloseSTMT();
        if ($r) {
            $this->id = $r->tplID;
            //$this->SetValue("tplName",$r->tplName,IBC1_DATATYPE_PURETEXT);
            $this->tplThemeID = $r->tplThemeID;
            $this->tplType = $r->tplType;
            //$this->SetValue("tplContent",$r->tplContent,IBC1_DATATYPE_PURETEXT);
            //$this->FieldList=explode("|",$r->tplFieldList);
            $this->FieldList = new WordList($r->tplFieldList);
            $this->isnew = FALSE;
            return TRUE;
        }

        return FALSE;
    }

    public function Save() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $essential = 3;
        $conn = $this->GetDBConn();
        if ($this->isnew) {
            if ($this->Count() < $essential) {
                $this->GetError()->AddItem(1, "some fields have not been set");
                return FALSE;
            }
            $sql = $conn->CreateInsertSTMT("ibc1_pag" . $this->GetServiceName() . "_template");
        } else {
            if ($this->Count() == 0) {
                $this->GetError()->AddItem(1, "no fields have not been set");
                return FALSE;
            }
            $sql = $conn->CreateUpdateSTMT("ibc1_pag" . $this->GetServiceName() . "_template");
            $sql->AddEqual("tplID", $this->id);
        }
        $this->MoveFirst();
        while (list($key, $item) = $this->GetEach()) {
            $sql->AddValue($key, $item[0], $item[1]);
        }

        if ($this->isFieldListChanged) {
            $fieldlist = $this->FieldList->GetWords();
            /*
              $fieldlist="";
              foreach($this->FieldList as $field)
              {
              if($field!="") $fieldlist.="$field|";
              }
              $fieldlist=substr($fieldlist,0,-1);
             */
            $sql->AddValue("tplFieldList", $fieldlist, IBC1_DATATYPE_PURETEXT);
        }
        $r = $sql->Execute();
        if ($r == FALSE) {
            $sql->CloseSTMT();
            $this->GetError()->AddItem(2, "数据库操作出错");
            return FALSE;
        }
        $this->id = $sql->GetLastInsertID();
        $sql->CloseSTMT();
        return TRUE;
    }

    public function GetID() {
        return $this->id;
    }

    public function SetName($name) {
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_pag" . $this->GetServiceName() . "_template");
        $sql->AddEqual("tplName", $name, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        $sql->AddEqual("tplThemeID", $this->tplThemeID, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);
        $sql->AddEqual("tplType", $this->tplType, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);
        $sql->AddField("tplID");
        $sql->Execute();
        $r = $sql->Fetch();
        $sql->CloseSTMT();
        if ($r) {
            return FALSE;
        }
        $this->SetValue("tplName", $name, IBC1_DATATYPE_PURETEXT);
        return TRUE;
    }

    public function SetTheme($id) {
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_pag" . $this->GetServiceName() . "_theme");
        $sql->AddEqual("thmID", $id, IBC1_DATATYPE_INTEGER);
        $sql->AddField("thmID");
        $sql->Execute();
        $r = $sql->Fetch();
        $sql->CloseSTMT();
        if (!$r) {
            return FALSE;
        }
        $this->SetValue("tplThemeID", $id, IBC1_DATATYPE_INTEGER);
    }

    public function SetThemeByName($name) {
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_pag" . $this->GetServiceName() . "_theme");
        $sql->AddEqual("thmName", $name, IBC1_DATATYPE_PURETEXT);
        $sql->AddField("thmID");
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if ($r) {
            $id = $r->thmID;
            //echo "found:$id";
            $this->SetValue("tplThemeID", $id, IBC1_DATATYPE_INTEGER);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function SetType($t) {
        $this->SetValue("tplType", $t, IBC1_DATATYPE_INTEGER);
    }

    public function SetContent($c) {
        $this->SetValue("tplContent", $c, IBC1_DATATYPE_TEMPLATE);
    }

    public function AddField($f) {
        //check...
        if ($f == "")
            return FALSE;
        if ($this->FieldList == NULL)
            return FALSE;
        /*
          if(strpos($f,"|")!=FALSE) return FALSE;
          foreach($this->FieldList as $field)
          {
          if(strtolower($field)==strtolower($f))
          return FALSE;
          }
          $this->FieldList[]=$f;
         */
        $this->FieldList->AddItem($f);
        $this->isFieldListChanged = TRUE;
        return TRUE;
    }

    public function RemoveField($f) {
        $c = FALSE;
        $newlist = new WordList();
        $this->FieldList->MoveFirst();
        while ($field = $this->FieldList->GetEach()) {
            if (strtolower($field) != strtolower($f))
                $newlist->AddItem($field);
            else
                $c=TRUE;
        }
        $this->FieldList = &$newlist;
        if ($c)
            $this->isFieldListChanged = TRUE;
        return $c;
        /*
          $c=FALSE;
          $newlist=array();
          foreach($this->FieldList as $field)
          {
          if($field!="")
          {
          if(strtolower($field)!=strtolower($f))
          $newlist[]=$field;
          else
          $c=TRUE;
          }
          }
          $this->FieldList=$newlist;
          if($c) $this->isFieldListChanged=TRUE;
          return $c;
         */
    }

    public function MoveTo($thmID) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_pag" . $this->GetServiceName() . "_theme");
        $sql->AddEqual("thmID", $thmID);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if (!$r) {

            return FALSE;
        }
        $sql = $conn->CreateUpdateSTMT("ibc1_pag" . $this->GetServiceName() . "_template");
        $sql->AddValue("tplThemeID", $thmID);
        $sql->AddEqual("tplID", $this->id);
        $sql->Execute();
        $sql->CloseSTMT();
        return TRUE;
    }

    public function Delete() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateDeleteSTMT("ibc1_pag" . $this->GetServiceName() . "_template");
        $sql->AddEqual("tplID", $this->id);
        $sql->Execute();
        $sql->CloseSTMT();
        return TRUE;
    }

}

?>
