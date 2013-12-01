<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.page
 */
class ThemeItemEditor extends DataItem {

    private $id = 0;
    private $isnew = TRUE;

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
        //$this->SetValue("thmName","",IBC1_DATATYPE_PURETEXT);
        //$this->SetValue("thmPreview","",IBC1_DATATYPE_PURETEXT);
        //$this->SetValue("thmSetting","",IBC1_DATATYPE_PURETEXT);
    }

    public function Open($id) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        /*
          $conn=$this->GetDBConn();
          $sql=$conn->CreateSelectSTMT("ibc1_pag".$this->GetServiceName()."_theme");
          $sql->AddEqual("thmID",$id);
          $sql->Execute();
          $r=$sql->Fetch();
          $sql->CloseSTMT();
          if($r)
          {
          $this->id=$r->thmID;
          $this->SetValue("thmName",$r->thmName,IBC1_DATATYPE_PURETEXT);
          $this->SetValue("thmPreview",$r->thmContent,IBC1_DATATYPE_PURETEXT);
          $this->SetValue("thmSetting",$r->thmSetting,IBC1_DATATYPE_PURETEXT);
          $this->isnew=FALSE;
          return TRUE;
          }

          return FALSE;
         */
        $this->id = intval($id);
        $this->isnew = FALSE;
    }

    public function OpenByName($name) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_pag" . $this->GetServiceName() . "_theme");
        $sql->AddEqual("thmName", $name, IBC1_DATATYPE_PURETEXT);

        $sql->Execute();
        $r = $sql->Fetch();
        $sql->CloseSTMT();
        if ($r) {
            $this->id = $r->thmID;
            //$this->SetValue("thmName",$r->thmName,IBC1_DATATYPE_PURETEXT);
            //$this->SetValue("thmPreview",$r->thmContent,IBC1_DATATYPE_PURETEXT);
            //$this->SetValue("thmSetting",$r->thmSetting,IBC1_DATATYPE_PURETEXT);
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
        $essential = 1;
        $conn = $this->GetDBConn();
        if ($this->isnew) {
            if ($this->Count() < $essential) {
                $this->GetError()->AddItem(1, "some fields have not been set");
                return FALSE;
            }
            $sql = $conn->CreateInsertSTMT("ibc1_pag" . $this->GetServiceName() . "_theme");
        } else {
            if ($this->Count() == 0) {
                $this->GetError()->AddItem(1, "no fields have not been set");
                return FALSE;
            }
            $sql = $conn->CreateUpdateSTMT("ibc1_pag" . $this->GetServiceName() . "_theme");
            $sql->AddEqual("thmID", $this->id);
        }
        $this->MoveFirst();
        while (list($key, $item) = $this->GetEach()) {
            $sql->AddValue($key, $item[0], $item[1]);
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
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_pag" . $this->GetServiceName() . "_theme");
        $sql->AddEqual("thmName", $name, IBC1_DATATYPE_PURETEXT);
        $sql->AddField("thmID");
        $sql->Execute();
        $r = $sql->Fetch();
        $sql->CloseSTMT();
        if ($r) {
            $this->GetError()->AddItem(2, "name exists");
            return FALSE;
        }

        $this->SetValue("thmName", $name, IBC1_DATATYPE_PURETEXT);
        return TRUE;
    }

    public function SetPreview($preview) {
        $this->SetValue("thmPreview", $preview, IBC1_DATATYPE_PURETEXT);
    }

    public function SetSettingService($ServiceName="") {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        if ($ServiceName == "") {
            $this->SetValue("thmSetting", "", IBC1_DATATYPE_PURETEXT);
            return TRUE;
        } else {
            $sm = GetServiceManager($this->GetDBConns());
            if ($sm->Exists($ServiceName)) {
                $this->SetValue("thmSetting", $ServiceName, IBC1_DATATYPE_PURETEXT);
                return TRUE;
            }
        }
        return FALSE;
    }

    public function Delete() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateDeleteSTMT("ibc1_pag" . $this->GetServiceName() . "_theme");
        $sql->AddEqual("thmID", $this->id);
        $sql->Execute();
        $sql->CloseSTMT();
        $sql->ClearConditions();
        $sql->SetTable("ibc1_pag" . $this->GetServiceName() . "_template");
        $sql->AddEqual("tplThemeID", $this->id);
        $sql->Execute();
        $sql->CloseSTMT();
        return TRUE;
    }

}

?>
