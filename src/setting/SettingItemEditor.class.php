<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.setting
 */
class SettingItemEditor extends DataItem {

    private $MatchService = "";
    //private $MatchTable="";
    //private $MatchField="";
    private $MatchType = -1;
    private $ValueType = -1;
    //private $ValueLength=0;
    private $TimeIncluded = FALSE;
    private $IsNew = TRUE;
    private $ID = 0;

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        parent::OpenService($Conns, $ServiceName, "set");
        $c = $this->GetDBConn();
        $sql = $c->CreateSelectSTMT("ibc1_dataservice_setting");
        $sql->AddField("*");
        $sql->AddEqual("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);

        $sql->Execute();
        if ($r = $sql->Fetch(1)) {
            $this->IsNew = FALSE;
            $this->MatchService = $r->MatchService;
            //$this->MatchTable=$r->MatchTable;
            //$this->MatchField=$r->MatchField;
            $this->MatchType = $r->MatchType;
            $this->ValueType = $r->ValueType;
            //$this->ValueLength=$r->ValueLength;
            if ($r->TimeIncluded == 0)
                $this->TimeIncluded = FALSE;
            else
                $this->TimeIncluded = TRUE;
        }
        $sql->CloseSTMT();
    }

    public function CloseService() {
        parent::CloseService();
        //$this->MatchService="";
        //$this->MatchTable="";
        //$this->MatchField="";
        $this->MatchType = -1;
        $this->ValueType = -1;
        //$this->ValueLength=0;
        $this->TimeIncluded = FALSE;
        $this->ID = 0;
        $this->IsNew = TRUE;
        $this->Clear();
    }

    public function Create() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $this->IsNew = TRUE;
        $this->ID = 0;
        /*
          switch($this->ValueType)
          {
          case IBC1_DATATYPE_INTEGER:
          $this->SetValue("setValue",0,IBC1_DATATYPE_INTEGER);
          break;
          case IBC1_DATATYPE_PURETEXT:
          $this->SetValue("setValue","",IBC1_DATATYPE_PURETEXT);
          break;
          }
          switch($this->MatchType)
          {
          case IBC1_DATATYPE_INTEGER:
          $this->SetValue("setMatchValue",0,IBC1_DATATYPE_INTEGER);
          break;
          case IBC1_DATATYPE_PURETEXT:
          $this->SetValue("setMatchValue","",IBC1_DATATYPE_PURETEXT);
          break;
          }
         */
    }

    public function OpenByName($MatchValue, $Name) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $this->IsNew = FALSE;
        //ONLY OPEN THE FIRST ONE
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_set" . $this->GetServiceName() . "_settinglist");
        $sql->AddEqual("setMatchValue", $MatchValue, $this->MatchType);
        $sql->AddEqual("setName", $Name, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        $sql->AddField("*");
        $sql->Execute();
        if ($r = $sql->Fetch(1)) {
            $this->ID = $r->setID;
            $this->SetValue("setName", $r->setName, IBC1_DATATYPE_PURETEXT);
            $this->SetValue("setValue", $r->setValue, $this->ValueType);
            $this->SetValue("setMatchValue", $r->setMatchValue, $this->MatchType);
        }
        $sql->CloseSTMT();
    }

    public function OpenByID($id) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $this->IsNew = FALSE;
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_set" . $this->GetServiceName() . "_settinglist");
        $sql->AddEqual("setID", $id);
        $sql->AddField("*");
        $sql->Execute();
        if ($r = $sql->Fetch(1)) {
            $this->ID = $r->setID;
            $this->SetValue("setName", $r->setName, IBC1_DATATYPE_PURETEXT);
            $this->SetValue("setValue", $r->setValue, $this->ValueType);
            $this->SetValue("setMatchValue", $r->setMatchValue, $this->MatchType);
        }
        $sql->CloseSTMT();
    }

    public function GetID() {
        return $this->ID;
    }

    public function SetSettingName($name) {
        //check...
        $this->SetValue("setName", $name, IBC1_DATATYPE_PURETEXT);
    }

    public function SetSettingValue($value, $type=-1) {
        if ($type < IBC1_DATATYPE_PURETEXT)
            $type = $this->ValueType;
        $this->SetValue("setValue", $value, $type);
    }

    public function SetMatchValue($v) {
        $this->SetValue("setMatchValue", $v, $this->MatchType);
    }

    public function Save($isNameUnique=FALSE) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $essential = 1;
        $conn = $this->GetDBConn();
        $settingname = $this->GetValue("setName");
        if ($settingname == "") {

            return FALSE;
        }
        if ($this->IsNew) {
            if ($this->Count() < $essential) {
                $this->GetError()->AddItem(1, "some fields have not been set");
                return FALSE;
            }
            if ($isNameUnique) {
                $sql = $conn->CreateSelectSTMT("ibc1_set" . $this->GetServiceName() . "_settinglist");
                $sql->AddField("setID");
                $sql->AddEqual("setName", $settingname, IBC1_DATATYPE_PURETEXT);
                $sql->Execute();
                $r = $sql->Fetch(1);
                $sql->CloseSTMT();
                if ($r) {

                    return FALSE;
                }
            }
            $sql = $conn->CreateInsertSTMT("ibc1_set" . $this->GetServiceName() . "_settinglist");
            if ($this->TimeIncluded) {
                $sql->AddValue("setTimeCreated", "CURRENT_TIMESTAMP()", IBC1_DATATYPE_EXPRESSION);
            }
        } else {
            $sql = $conn->CreateUpdateSTMT("ibc1_set" . $this->GetServiceName() . "_settinglist");
            if ($this->TimeIncluded) {
                $sql->AddValue("setTimeUpdated", "CURRENT_TIMESTAMP()", IBC1_DATATYPE_EXPRESSION);
            }
            $sql->AddEqual("setID", $this->ID);
        }

        $sql->AddValue("setName", $settingname, IBC1_DATATYPE_PURETEXT);
        $sql->AddValue("setValue", $this->GetValue("setValue"), $this->ValueType);
        if ($this->MatchService != "") {
            $sql->AddValue("setMatchValue", $this->GetValue("setMatchValue"), $this->MatchType);
        }

        $sql->Execute();
        if ($this->IsNew) {
            $this->ID = $sql->GetLastInsertID();
            $this->IsNew = FALSE;
        }
        $sql->CloseSTMT();
        return TRUE;
    }

    public function Delete($id=0) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        if ($id == 0)
            $id = $this->ID;
        $conn = $this->GetDBConn();
        $sql = $conn->CreateDeleteSTMT("ibc1_set" . $this->GetServiceName() . "_settinglist");
        $sql->AddEqual("setID", $id);
        $sql->Execute();
        $sql->CloseSTMT();

        return TRUE;
    }

}

?>
