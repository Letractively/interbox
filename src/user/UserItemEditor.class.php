<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.user
 */
class UserItemEditor extends DataItem {

    private $IsNew = TRUE;
    private $UID = "";
    private $PWD = "";
    private $newPWD = "";

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        parent::OpenService($Conns, $ServiceName, "usr");
    }

    public function Open($uid) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_usr" . $this->GetServiceName() . "_user");
        $sql->AddField("usrUID");
        $sql->AddField("usrPWD");
        $sql->AddEqual("usrUID", $uid, IBC1_DATATYPE_PURETEXT);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if ($r) {
            $this->UID = $r->usrUID;
            $this->PWD = $r->usrPWD;
            $this->IsNew = FALSE;
        }
    }

    public function Create($uid, $pwd, $repeat) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        if (!eregi("[0-9a-z]{6,}", $pwd)) {//||strlen($pwd)<6
            $this->GetError()->AddItem(1, "|invalid password");
        }
        if ($pwd != $repeat) {
            $this->GetError()->AddItem(1, "|");
        }
        if ($this->GetError()->HasError())
            return FALSE;
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_usr" . $this->GetServiceName() . "_user");
        $sql->AddField("usrUID");
        $sql->AddEqual("usrUID", $uid, IBC1_DATATYPE_PURETEXT);
        $sql->Execute();
        $r = $sql->Fetch();
        $sql->CloseSTMT();
        if ($r) {
            $this->GetError()->AddItem(1, "|UID exists");
            return FALSE;
        }

        LoadIBC1Lib("PWDSecurity");
        $this->UID = $uid;
        $this->PWD = PWDEncode($pwd);
        $this->IsNew = TRUE;
        return TRUE;
    }

    /**
     * @param string $uid UserAdmin's UID; grade=1 if by default(="")
     * @param string $pwd
     * @return bool
     */
    public function Save($uid="", $pwd="") {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        if ($uid != "") {
            $sql = $conn->CreateSelectSTMT("ibc1_usr" . $this->GetServiceName() . "_user");
            $sql->AddField("usrPWD");
            $sql->AddField("usrIsUserAdmin");
            $sql->AddEqual("usrUID", $uid, IBC1_DATATYPE_PURETEXT);
            $sql->Execute();
            $r = $sql->Fetch(1);
            $sql->CloseSTMT();
            if ($r) {
                LoadIBC1Lib("PWDSecurity");
                if (!$r->usrIsUserAdmin)
                    $this->GetError()->AddItem(1, "|");
                if (!IsPassed($pwd, $r->usrPWD))
                    $this->GetError()->AddItem(1, "|");
            }
            else {
                $this->GetError()->AddItem(1, "|");
            }
            if ($this->GetError()->HasError())
                return FALSE;
        }
        else if ($this->IsNew) {
            $this->SetValue("usrGrade", 1, IBC1_DATATYPE_INTEGER);
        } else {
            $this->GetError()->AddItem(1, "|");
            return FALSE;
        }
        if ($this->IsNew) {
            if (!DataFormatter::ValidateUID($this->UID)) {
                $this->GetError()->AddItem(1, "|");
                return FALSE;
            }
            if (!DataFormatter::ValidatePWD($this->PWD)) {
                $this->GetError()->AddItem(1, "|");
                return FALSE;
            }
            $sql = $conn->CreateInsertSTMT("ibc1_usr" . $this->GetServiceName() . "_user");
            $sql->AddValue("usrUID", $this->UID, IBC1_DATATYPE_PURETEXT);
            $sql->AddValue("usrPWD", $this->PWD, IBC1_DATATYPE_PURETEXT);
            $sql->AddValue("usrRegisterTime", "CURRENT_TIMESTAMP()", IBC1_DATATYPE_EXPRESSION);
        } else {
            $sql = $conn->CreateUpdateSTMT("ibc1_usr" . $this->GetServiceName() . "_user");
            //$sql->AddEqual("usrUID",$this->UID,1);UID cannot be changed
            if ($this->newPWD != "") {
                $sql->AddEqual("usrPWD", $this->newPWD, IBC1_DATATYPE_PURETEXT);
                $this->PWD = $this->newPWD;
            }
        }
        if ($this->Count() == 0) {
            $this->GetError()->AddItem(1, "|");
            return FALSE;
        }
        $this->MoveFirst();
        while (list($key, $item) = $this->GetEach()) {
            $sql->AddValue($key, $item[0], $item[1]);
        }
        $r = $sql->Execute();
        $sql->CloseSTMT();
        if ($r)
            return TRUE;
        $this->GetError()->AddItem(1, "|");
        return FALSE;
    }

    public function SetPWD($newpwd, $repeat, $pwd="") {
        if (!eregi("[0-9a-z]", $newpwd) || strlen($newpwd) < 6) {
            $this->GetError()->AddItem(1, "invalid password");
        }
        if ($newpwd != $repeat) {
            $this->GetError()->AddItem(1, "unsure password");
        }
        if ($this->GetError()->HasError())
            return FALSE;
        LoadIBC1Lib("PWDSecurity");

        if ($pwd != "" && IsPassed($pwd, $this->PWD)) {
            $this->newPWD = PWDEncode($newpwd);
            return TRUE;
        }
        $this->GetError()->AddItem(1, "wrong password");
        return FALSE;
    }

    /*
      public function SetPWD($newpwd,$repeat)
      {
      if(!eregi("[0-9a-z]",$newpwd)||strlen($newpwd)<6)
      {
      $this->GetError()->AddItem(1,"|invalid password");
      return FALSE;
      }
      else if($newpwd!=$repeat)
      {
      $this->GetError()->AddItem(1,"|");
      return FALSE;
      }
      require("PWDSecurity.lib.php");

      $this->newPWD=PWDEncode($newpwd);
      return TRUE;
      }
     */

    public function SetFace($f) {
        $this->SetValue("usrFace", $f, IBC1_DATATYPE_PURETEXT);
    }

    public function SetNickName($nn) {
        $this->SetValue("usrNickName", $nn, IBC1_DATATYPE_PURETEXT);
    }

    public function SetGrade($g) {
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_usr" . $this->GetServiceName() . "_grade");
        $sql->AddField("grdGrade");
        $sql->AddEqual("grdGrade", $g);
        $sql->Execute();
        $r = $sql->Fetch();
        $sql->CloseSTMT();
        if (!$r) {
            $this->GetError()->AddItem(1, "|");
            return FALSE;
        }
        $this->SetValue("usrGrade", $g, IBC1_DATATYPE_INTEGER);
        return TRUE;
    }

    public function AddPoints($p) {
        $p = intval($p);
        if ($p == 0)
            return TRUE;
        if ($p > 0)
            $e = "usrPoints+" . abs($p);
        else
            $e="usrPoints-" . abs($p);
        $this->SetValue("usrPoints", $e, IBC1_DATATYPE_EXPRESSION);
        return TRUE;
    }

    public function ClearPoints() {
        $this->SetValue("usrPoints", 0);
    }

    /**
     * @param bool $ua
     * @return void
     */
    public function SetUserAdmin($ua) {
        $this->SetValue("usrIsUserAdmin", $ua, IBC1_DATATYPE_PURETEXT);
    }

}

?>
