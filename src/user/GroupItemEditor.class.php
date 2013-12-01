<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.user
 */
class GroupItemEditor extends DataItem {

    private $ID = 0;
    private $IsNew = TRUE;

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        parent::OpenService($Conns, $ServiceName, "usr");
    }

    public function Create() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $this->ID = 0;
        $this->IsNew = TRUE;
    }

    public function Open($id) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        //$conn=$this->GetDBConn();
        //$sql=$conn->CreateSelectSTMT("ibc1_usr".$this->GetServiceName()."_group");
        //$sql->AddField("*");
        //$sql->AddEqual("grpID",$id);
        //$sql->Execute();
        //$r=$sql->Fetch(1);
        //if($r)
        //{
        $this->IsNew = FALSE;
        $this->ID = intval($id);
        //$this->SetValue("grpName",$r->grpName,IBC1_DATATYPE_PURETEXT);
        //$this->SetValue("grpOwner",$r->grpOwner,IBC1_DATATYPE_PURETEXT);
        //$this->SetValue("grpType",$r->grpType,IBC1_DATATYPE_INTEGER);
        //	return TRUE;
        //}
        //$this->GetError()->AddItem(1,"|");
        //return FALSE;
    }

    public function GetID() {
        return $this->ID;
    }

    public function SetName($name) {
        $this->SetValue("grpName", $name, IBC1_DATATYPE_PURETEXT);
    }

    public function SetOwner($uid) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        if ($uid == "") {
            $this->SetValue("grpOwner", "", IBC1_DATATYPE_PURETEXT);
            return TRUE;
        }
        $conn = $this->GetDBConn();
        if ($this->IsNew) {
            $sql = $conn->CreateSelectSTMT("ibc1_usr" . $this->GetServiceName() . "_user");
            $sql->AddField("usrUID");
            $sql->AddEqual("usrUID", $uid, IBC1_DATATYPE_PURETEXT);
        } else {
            $sql = $conn->CreateSelectSTMT("ibc1_usr" . $this->GetServiceName() . "_groupuser");
            $sql->AddField("gpuUID");
            $sql->AddEqual("gpuUID", $uid, IBC1_DATATYPE_PURETEXT);
            $sql->AddEqual("gpuGID", $this->ID, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);
        }
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if ($r) {
            $this->SetValue("grpOwner", $uid, IBC1_DATATYPE_PURETEXT);
            return TRUE;
        }
        return FALSE;
    }

    public function SetPrivate($p) {
        if ($p)
            $this->SetValue("grpType", 0, IBC1_DATATYPE_INTEGER);
        else
            $this->SetValue("grpType", 1, IBC1_DATATYPE_INTEGER);
    }

    public function SaveGroup() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $essential = 3;
        $conn = $this->GetDBConn();
        if ($this->IsNew) {
            if ($this->Count() < $essential) {
                $this->GetError()->AddItem(1, "some fields have not been set");
                return FALSE;
            }
            $sql = $conn->CreateInsertSTMT("ibc1_usr" . $this->GetServiceName() . "_group");
            while (list($key, $item) = $this->GetEach()) {
                $sql->AddValue($key, $item[0], $item[1]);
            }
            $r = $sql->Execute();
            if (!$r) {
                $sql->CloseSTMT();
                $this->GetError()->AddItem(1, "|");
                return FALSE;
            }
            $this->ID = $sql->GetLastInsertID();
            $sql->CloseSTMT();
            $this->IsNew = FALSE;
            if ($this->Owner != "") {
                $sql = $conn->CreateInsertSTMT("ibc1_usr" . $this->GetServiceName() . "_groupuser");
                $sql->AddValue("gpuUID", $this->GetValue("grpOwner"), IBC1_DATATYPE_PURETEXT);
                $sql->AddValue("gpuGID", $this->ID);
                $r = $sql->Execute();
                $sql->CloseSTMT();
                if (!$r) {
                    $this->GetError()->AddItem(1, "|");
                    return FALSE;
                }
            }
            return TRUE;
        } else {
            if ($this->Count() == 0) {
                $this->GetError()->AddItem(1, "no fields have not been set");
                return FALSE;
            }
            $sql = $conn->CreateUpdateSTMT("ibc1_usr" . $this->GetServiceName() . "_group");
            while (list($key, $item) = $this->GetEach()) {
                $sql->AddValue($key, $item[0], $item[1]);
            }
            $sql->AddEqual("grpID", $this->ID);
            $r = $sql->Execute();
            $sql->CloseSTMT();
            if (!$r) {
                $this->GetError()->AddItem(1, "|");
                return FALSE;
            }
            return TRUE;
        }
    }

    public function AddUser($uid) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        if ($this->ID <= 0 || $this->IsNew) {

            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_usr" . $this->GetServiceName() . "_groupuser");
        $sql->AddField("gpuUID");
        $sql->AddEqual("gpuUID", $uid, IBC1_DATATYPE_PURETEXT);
        $sql->AddEqual("gpuGID", $this->ID, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if ($r) {

            return FALSE;
        }
        $sql = $conn->CreateSelectSTMT("ibc1_usr" . $this->GetServiceName() . "_user");
        $sql->AddField("usrUID");
        $sql->AddEqual("usrUID", $uid, IBC1_DATATYPE_PURETEXT);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if ($r) {
            $sql = $conn->CreateInsertSTMT("ibc1_usr" . $this->GetServiceName() . "_groupuser");
            $sql->AddValue("gpuUID", $uid, IBC1_DATATYPE_PURETEXT);
            $sql->AddValue("gpuGID", $this->ID);
            $r = $sql->Execute();
            $sql->CloseSTMT();
            if ($r)
                return TRUE;
        }
        return FALSE;
    }

    public function RemoveUser($uid) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        if ($this->ID <= 0 || $this->IsNew) {

            return FALSE;
        }
        if ($uid == "") {

            return FALSE;
        }
        /*
          The aim of this step is to avoid removing the owner.
          But this.owner could be changed before updated in the database.
          If the changed group object was not saved to the database,
          the owner would be removed illegally.
          if(strtolower($this->Owner)==strtolower($uid))
          {

          return FALSE;
          }
         */
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_usr" . $this->GetServiceName() . "_group");
        $sql->AddField("grpOwner");
        $sql->AddEqual("grpID", $this->ID);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if ($r) {
            if (strtolower($r->grpOwner) == strtolower($uid)) {

                return FALSE;
            }
        }
        $sql = $conn->CreateDeleteSTMT("ibc1_usr" . $this->GetServiceName() . "_groupuser");
        $sql->AddEqual("gpuUID", $uid, IBC1_DATATYPE_PURETEXT);
        $sql->AddEqual("gpuGID", $this->ID, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);
        $r = $sql->Execute();
        $sql->CloseSTMT();
        if ($r)
            return TRUE;

        return FALSE;
    }

}

?>