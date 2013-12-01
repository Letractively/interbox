<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.user
 */
LoadIBC1Class("ServiceManager", "common.dataservice");

class UserManager extends ServiceManager {

    public function Create($ServiceName, $GradeList, $uid, $pwd, $repeat) {
        $this->GetError()->Clear();
        if (!$this->IsInstalled($this->GetError()))
            return FALSE;
        if (!eregi("[0-9a-z]", $pwd) || strlen($pwd) < 6) {
            $this->GetError()->AddItem(1, "|invalid password");
        }
        if ($pwd != $repeat) {
            $this->GetError()->AddItem(1, "|");
        }
        $c = count($GradeList);
        if ($c < 2) {
            $this->GetError()->AddItem(4, "少于2个级别|at least 2 user grade-levels");
        }
        if ($this->Exists($ServiceName)) {
            $this->GetError()->AddItem(4, "服务 '$ServiceName' 早已建立|service '$ServiceName' has already been there");
        }
        if ($this->GetError()->HasError())
            return FALSE;
        $conn = $this->GetDBConn();

        $sqlset[0][0] = $conn->CreateTableSTMT("create");
        $sqlset[0][1] = "ibc1_usr" . $ServiceName . "_user";
        $sql = &$sqlset[0][0];
        $sql->SetTable($sqlset[0][1]);
        $sql->AddField("usrUID", IBC1_DATATYPE_PURETEXT, 256, FALSE, NULL, TRUE, "", FALSE);
        $sql->AddField("usrPWD", IBC1_DATATYPE_PURETEXT, 256, FALSE);
        $sql->AddField("usrFace", IBC1_DATATYPE_PURETEXT, 256, TRUE);
        $sql->AddField("usrNickName", IBC1_DATATYPE_PURETEXT, 256, TRUE);
        $sql->AddField("usrGrade", IBC1_DATATYPE_INTEGER, 2, FALSE, 1);
        $sql->AddField("usrPoints", IBC1_DATATYPE_INTEGER, 10, FALSE, 0);
        $sql->AddField("usrLoginCount", IBC1_DATATYPE_INTEGER, 10, FALSE, 0);
        $sql->AddField("usrLoginIP", IBC1_DATATYPE_PURETEXT, 50, TRUE);
        $sql->AddField("usrLoginTime", IBC1_DATATYPE_DATETIME, 0, TRUE);
        $sql->AddField("usrVisitTime", IBC1_DATATYPE_DATETIME, 0, TRUE);
        $sql->AddField("usrRegisterTime", IBC1_DATATYPE_DATETIME, 0, FALSE);
        $sql->AddField("usrIsOnline", IBC1_DATATYPE_INTEGER, 1, FALSE, 0);
        $sql->AddField("usrIsUserAdmin", IBC1_DATATYPE_INTEGER, 1, FALSE, 0);

        /*
          $sql[0]="CREATE TABLE IBC1_usr".$ServiceName."_User(";
          $sql[0].=" usrUID VARCHAR (255) NOT NULL,";
          $sql[0].=" usrPWD VARCHAR (255) NOT NULL,";
          $sql[0].=" usrFace VARCHAR (255) NULL,";
          $sql[0].=" usrNickName VARCHAR(255) NULL,";
          $sql[0].=" usrGrade INT(2) NOT NULL DEFAULT 1,";
          $sql[0].=" usrPoints INT(10) NOT NULL DEFAULT 0,";
          $sql[0].=" usrLoginCount INT(10) NOT NULL DEFAULT 0,";
          $sql[0].=" usrLoginIP VARCHAR (50) NULL,";
          $sql[0].=" usrLoginTime TIMESTAMP(14) NULL,";
          $sql[0].=" usrVisitTime TIMESTAMP(14) NULL,";
          $sql[0].=" usrRegisterTime TIMESTAMP(14) NOT NULL,";
          $sql[0].=" usrIsOnline INT(1) NOT NULL DEFAULT 0,";
          $sql[0].=" usrIsUserAdmin INT(1) NOT NULL DEFAULT 0,";
          $sql[0].=" PRIMARY KEY (usrUID)";
          $sql[0].=") TYPE=MyISAM DEFAULT CHARSET=utf8;";
         */

        $sqlset[1][0] = $conn->CreateTableSTMT("create");
        $sqlset[1][1] = "ibc1_usr" . $ServiceName . "_grade";
        $sql = &$sqlset[1][0];
        $sql->SetTable($sqlset[1][1]);
        $sql->AddField("grdGrade", IBC1_DATATYPE_INTEGER, 2, FALSE, NULL, TRUE, "", FALSE);
        $sql->AddField("grdName", IBC1_DATATYPE_PURETEXT, 256, FALSE);

        /*
          $sql[1]="CREATE TABLE IBC1_usr".$ServiceName."_Grade(";
          $sql[1].=" grdGrade INT(2) NOT NULL,";
          $sql[1].=" grdName VARCHAR(255) NOT NULL,";
          $sql[1].=" PRIMARY KEY (grdGrade)";
          $sql[1].=") TYPE=MyISAM DEFAULT CHARSET=utf8;";
         */
        $sqlset[2][0] = $conn->CreateTableSTMT("create");
        $sqlset[2][1] = "ibc1_usr" . $ServiceName . "_groupuser";
        $sql = &$sqlset[2][0];
        $sql->SetTable($sqlset[2][1]);
        $sql->AddField("gpuID", IBC1_DATATYPE_INTEGER, 10, FALSE, NULL, TRUE, "", TRUE);
        $sql->AddField("gpuUID", IBC1_DATATYPE_PURETEXT, 256, FALSE);
        $sql->AddField("gpuGID", IBC1_DATATYPE_INTEGER, 10, FALSE);

        /*
          $sql[2]="CREATE TABLE IBC1_usr".$ServiceName."_GroupUser(";
          $sql[2].=" gpuID INT(10) NOT NULL AUTO_INCREMENT,";
          $sql[2].=" gpuUID VARCHAR(255) NOT NULL,";
          $sql[2].=" gpuGID INT(10) NOT NULL,";
          $sql[2].=" PRIMARY KEY (gpuID)";
          $sql[2].=") TYPE=MyISAM DEFAULT CHARSET=utf8;";
         */
        $sqlset[3][0] = $conn->CreateTableSTMT("create");
        $sqlset[3][1] = "ibc1_usr" . $ServiceName . "_group";
        $sql = &$sqlset[3][0];
        $sql->SetTable($sqlset[3][1]);
        $sql->AddField("grpID", IBC1_DATATYPE_INTEGER, 10, FALSE, NULL, TRUE, "", TRUE);
        $sql->AddField("grpName", IBC1_DATATYPE_PURETEXT, 256, FALSE);
        $sql->AddField("grpOwner", IBC1_DATATYPE_PURETEXT, 256, FALSE);
        $sql->AddField("grpType", IBC1_DATATYPE_INTEGER, 2, FALSE, 0);

        /*
          $sql[3]="CREATE TABLE IBC1_usr".$ServiceName."_Group(";
          $sql[3].=" grpID INT(10) NOT NULL AUTO_INCREMENT,";
          $sql[3].=" grpName VARCHAR(255) NOT NULL,";
          $sql[3].=" grpOwner VARCHAR(255) NOT NULL,";
          $sql[3].=" grpType INT(2) NOT NULL DEFAULT 0,";
          $sql[3].=" PRIMARY KEY (grpID)";
          $sql[3].=") TYPE=MyISAM DEFAULT CHARSET=utf8;";
         */
        $r = $this->CreateTables($sqlset, $conn);
        if ($r == FALSE) {
            $this->GetError()->AddItem(3, "User服务建立失败|fail to create User service");
            return FALSE;
        }

        $sql = $conn->CreateInsertSTMT();
        $sql->SetTable("ibc1_usr" . $ServiceName . "_grade");
        for ($i = 0; $i < $c; $i++) {
            $sql->AddValue("grdGrade", $i + 1);
            $sql->AddValue("grdName", $GradeList[$i], IBC1_DATATYPE_PURETEXT);

            $sql->Execute();
            $sql->ClearValues();
            $sql->CloseSTMT();
        }
        //register service
        $sql->SetTable("ibc1_dataservice");
        $sql->AddValue("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);
        $sql->AddValue("ServiceType", "usr", IBC1_DATATYPE_PURETEXT);
        $sql->Execute();
        $sql->ClearValues();
        $sql->CloseSTMT();
        //add user admin
        LoadIBC1Lib("PWDSecurity");
        $pwd = PWDEncode($pwd);

        $sql->ClearValues();
        $sql->SetTable("ibc1_usr" . $ServiceName . "_user");
        $sql->AddValue("usrUID", $uid, IBC1_DATATYPE_PURETEXT);
        $sql->AddValue("usrPWD", $pwd, IBC1_DATATYPE_PWD);
        $sql->AddValue("usrGrade", $c);
        $sql->AddValue("usrRegisterTime", "CURRENT_TIMESTAMP()", IBC1_DATATYPE_EXPRESSION);
        $sql->AddValue("usrIsUserAdmin", IBC1_DATATYPE_PURETEXT);
        $sql->Execute();
        $sql->ClearValues();
        $sql->CloseSTMT();
        if ($conn->GetError()->HasError()) {
            $this->GetError()->AddItem(7, "'" . $conn->GetError()->GetSource() . "' 存在未知错误|unknown error from '" . $conn->GetError()->GetSource() . "'");
            return FALSE;
        }
        return TRUE;
    }

    public function Delete($ServiceName) {
        $this->GetError()->Clear();
        if (!$this->Exists($ServiceName)) {
            $this->GetError()->AddItem(6, "服务'$ServiceName'不存在|cannot find service '$ServiceName'");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateTableSTMT("drop");
        $sql->SetTable("ibc1_usr" . $ServiceName . "_user");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql->SetTable("ibc1_usr" . $ServiceName . "_grade");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql->SetTable("ibc1_usr" . $ServiceName . "_groupuser");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql->SetTable("ibc1_usr" . $ServiceName . "_group");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql = $conn->CreateDeleteSTMT("ibc1_dataservice");
        $sql->AddEqual("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);
        $sql->Execute();
        $sql->CloseSTMT();
        if ($conn->GetError()->HasError()) {
            $this->GetError()->AddItem(7, "'" . $conn->GetError()->GetSource() . "' 存在未知错误|unknown error from '" . $conn->GetError()->GetSource() . "'");
            return FALSE;
        }
        return TRUE;
    }

    public function Optimize($ServiceName) {
        if (!$this->Exists($ServiceName, "usr")) {

            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateTableSTMT("optimize", "ibc1_usr" . $ServiceName . "_user");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql = $conn->CreateTableSTMT("optimize", "ibc1_usr" . $ServiceName . "_grade");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql = $conn->CreateTableSTMT("optimize", "ibc1_usr" . $ServiceName . "_groupuser");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql = $conn->CreateTableSTMT("optimize", "ibc1_usr" . $ServiceName . "_group");
        $sql->Execute();
        $sql->CloseSTMT();
        return TRUE;
    }

}

?>