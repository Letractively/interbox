<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.user
 */
class UserPassport extends DataService {

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
        session_start();
        session_regenerate_id();
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        parent::OpenService($Conns, $ServiceName, "usr");
    }

    public function GetValue($name) {
        if (session_is_registered("ibc1_" . $this->GetServiceName() . "_$name")) {
            return $_SESSION["ibc1_" . $this->GetServiceName() . "_$name"];
        }
        return NULL;
    }

    protected function SetValue($name, $value) {
        if (!session_is_registered("ibc1_" . $this->GetServiceName() . "_$name")) {
            session_register("ibc1_" . $this->GetServiceName() . "_$name");
            $_SESSION["ibc1_" . $this->GetServiceName() . "_$name"] = $value;
        }
    }

    public function IsOnline() {
        if ($this->GetValue("UID") == NULL) {
            @session_destroy();
            return FALSE;
        }
        return TRUE;
    }

    public function GetUID() {
        return $this->GetValue("UID");
    }

    public function Login($UID, $PWD) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_usr" . $this->GetServiceName() . "_user");
        $sql->AddEqual("usrUID", $UID, IBC1_DATATYPE_PURETEXT);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if ($r) {
            $codedPWD = $r->usrPWD;
            $this->SetValue("Face", $r->usrFace);
            $this->SetValue("NickName", $r->usrNickName);
            $this->SetValue("Grade", $r->usrGrade);
            $this->SetValue("Points", $r->usrPoints);
            $this->SetValue("LoginCount", $r->usrLoginCount);
            $this->SetValue("RegisterTime", $r->usrRegisterTime);
        } else {
            $this->GetError()->AddItem(1, "this user id does not exist");
            return FALSE;
        }
        LoadIBC1Lib("PWDSecurity");
        if (IsPassed($PWD, $codedPWD)) {
            $t = date("Y-m-d H:i:s");
            $sql = $conn->CreateUpdateSTMT("ibc1_usr" . $this->GetServiceName() . "_user");
            $sql->AddValue("usrLoginCount", "usrLoginCount+1", IBC1_DATATYPE_EXPRESSION);
            $sql->AddValue("usrLoginTime", $t, IBC1_DATATYPE_PURETEXT);
            $sql->AddValue("usrLoginIP", $_SERVER["REMOTE_ADDR"], IBC1_DATATYPE_PURETEXT);
            $sql->AddValue("usrIsOnline", 1, IBC1_DATATYPE_INTEGER);
            $sql->AddEqual("usrUID", $UID, IBC1_DATATYPE_PURETEXT);
            $sql->Execute();
            $sql->CloseSTMT();


            //refresh properties...
            $this->SetValue("LoginTime", $t);
            $this->SetValue("LoginIP", $_SERVER["REMOTE_ADDR"]);
            $this->SetValue("UID", $UID);

            return TRUE;
        } else {
            @session_destroy();
            $this->GetError()->AddItem(1, "wrong password");
            return FALSE;
        }
    }

    public function Logout() {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateUpdateSTMT("ibc1_usr" . $this->GetServiceName() . "_user");
        $sql->AddValue("usrIsOnline", 0, IBC1_DATATYPE_INTEGER);
        $sql->AddEqual("usrUID", $this->GetUID(), IBC1_DATATYPE_PURETEXT);
        $sql->Execute();
        $sql->CloseSTMT();
        @session_destroy();
        return TRUE;
    }

}

?>