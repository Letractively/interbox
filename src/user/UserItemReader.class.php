<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.user
 */
class UserItemReader extends DataItem {

    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        parent::OpenService($Conns, $ServiceName, "usr");
    }

    public function CloseService() {
        parent::CloseService();
        $this->Clear();
    }

    public function Open($UID) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_usr" . $this->GetServiceName() . "_user");
        $sql->AddField("usrUID");
        $sql->AddField("usrPWD");
        $sql->AddField("usrFace");
        $sql->AddField("usrNickName");
        $sql->AddField("usrGrade");
        $sql->AddField("usrPoints");
        $sql->AddField("usrLoginCount");
        $sql->AddField("usrLoginIP");
        $sql->AddField("DATE_FORMAT(usrLoginTime,\"%Y-%m-%d %H:%i:%s\")", "LoginTime");
        $sql->AddField("DATE_FORMAT(usrVisitTime,\"%Y-%m-%d %H:%i:%s\")", "VisitTime");
        $sql->AddField("DATE_FORMAT(usrRegisterTime,\"%Y-%m-%d %H:%i:%s\")", "RegisterTime");
        $sql->AddField("usrIsUserAdmin");
        $sql->AddEqual("usrUID", $UID, IBC1_DATATYPE_PURETEXT);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if ($r == FALSE) {
            $this->GetError()->AddItem(1, "|");
            return FALSE;
        }
        $this->SetValue("UID", $r->usrUID, IBC1_DATATYPE_PURETEXT);
        $this->SetValue("PWD", $r->usrPWD, IBC1_DATATYPE_PURETEXT);
        $this->SetValue("Face", $r->usrFace, IBC1_DATATYPE_PURETEXT);
        $this->SetValue("NickName", $r->usrNickName, IBC1_DATATYPE_PURETEXT);
        $this->SetValue("Grade", $r->usrGrade, IBC1_DATATYPE_INTEGER);
        $this->SetValue("Points", $r->usrPoints, IBC1_DATATYPE_INTEGER);
        $this->SetValue("LoginCount", $r->usrLoginCount, IBC1_DATATYPE_INTEGER);
        $this->SetValue("LoginIP", $r->usrLoginIP, IBC1_DATATYPE_PURETEXT);
        $this->SetValue("LoginTime", $r->LoginTime, IBC1_DATATYPE_PURETEXT);
        $this->SetValue("VisitTime", $r->VisitTime, IBC1_DATATYPE_PURETEXT);
        $this->SetValue("RegisterTime", $r->RegisterTime, IBC1_DATATYPE_PURETEXT);
        $this->SetValue("IsUserAdmin", $r->usrIsUserAdmin, IBC1_DATATYPE_INTEGER);
        return TRUE;
    }

    public function CheckPWD($PWD) {
        LoadIBC1Lib("PWDSecurity");
        return IsPassed($PWD, $this->GetValue("PWD"));
    }

    public function GetUID() {
        return $this->GetValue("UID");
    }

    public function GetGrade() {
        return $this->GetValue("Grade");
    }

    /*
      public function GetGradeName()
      {
      return $this->;
      }
     */

    public function GetPoints() {
        return $this->GetValue("Points");
    }

    public function GetFace() {
        return $this->GetValue("Face");
    }

    public function GetNickName() {
        return $this->GetValue("NickName");
    }

    public function GetLoginCount() {
        return $this->GetValue("LoginCount");
    }

    public function GetLoginIP() {
        return $this->GetValue("LoginIP");
    }

    public function GetLoginTime() {
        return $this->GetValue("LoginTime");
    }

    public function GetVisitTime() {
        return $this->GetValue("VisitTime");
    }

    public function GetRegisterTime() {
        return $this->GetValue("RegisterTime");
    }

    public function IsUserAdmin() {
        return $this->GetValue("IsUserAdmin");
    }

}

?>
