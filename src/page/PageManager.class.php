<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.page
 */
LoadIBC1Class("ServiceManager", "common.dataservice");

class PageManager extends ServiceManager {

    public function Create($ServiceName) {
        $this->GetError()->Clear();
        if (!$this->IsInstalled($this->GetError()))
            return FALSE;
        if ($this->Exists($ServiceName)) {
            $this->GetError()->AddItem(4, "服务 '$ServiceName' 早已建立|service '$ServiceName' has already been there");
            return FALSE;
        }
        if ($this->GetError()->HasError())
            return FALSE;
        $conn = $this->GetDBConn();

        $sqlset[0][0] = $conn->CreateTableSTMT("create");
        $sqlset[0][1] = "ibc1_pag" . $ServiceName . "_theme";
        $sql = &$sqlset[0][0];
        $sql->SetTable($sqlset[0][1]);
        $sql->AddField("thmID", IBC1_DATATYPE_INTEGER, 10, FALSE, NULL, TRUE, "", TRUE);
        $sql->AddField("thmName", IBC1_DATATYPE_PURETEXT, 64, FALSE);
        $sql->AddField("thmPreview", IBC1_DATATYPE_PURETEXT, 256, TRUE);
        $sql->AddField("thmSetting", IBC1_DATATYPE_PURETEXT, 64, TRUE);

        /*
          $sql[0]="CREATE TABLE IBC1_pag".$ServiceName."_Theme(";
          $sql[0].="thmID INT(5) NOT NULL AUTO_INCREMENT,";
          $sql[0].="thmName VARCHAR(64) NOT NULL,";
          $sql[0].="thmPreview VARCHAR(255) NULL,";
          $sql[0].="thmSetting VARCHAR(64) NULL,";
          $sql[0].="PRIMARY KEY (thmID)";
          $sql[0].=") TYPE=MyISAM DEFAULT CHARSET=utf8;";
         */

        $sqlset[1][0] = $conn->CreateTableSTMT("create");
        $sqlset[1][1] = "ibc1_pag" . $ServiceName . "_template";
        $sql = &$sqlset[1][0];
        $sql->SetTable($sqlset[1][1]);
        $sql->AddField("tplID", IBC1_DATATYPE_INTEGER, 10, FALSE, NULL, TRUE, "", TRUE);
        $sql->AddField("tplName", IBC1_DATATYPE_PURETEXT, 64, FALSE);
        $sql->AddField("tplThemeID", IBC1_DATATYPE_INTEGER, 10, FALSE);
        $sql->AddField("tplFieldList", IBC1_DATATYPE_PURETEXT, 256, TRUE);
        $sql->AddField("tplType", IBC1_DATATYPE_INTEGER, 1, FALSE);
        $sql->AddField("tplContent", IBC1_DATATYPE_TEMPLATE, 0, TRUE);

        /*
          $sql[1]="CREATE TABLE IBC1_pag".$ServiceName."_Template(";
          $sql[1].=" tplID INT(5) NOT NULL AUTO_INCREMENT,";
          $sql[1].=" tplName VARCHAR(255) NOT NULL,";
          $sql[1].=" tplThemeID INT(5) NOT NULL,";
          $sql[1].=" tplFieldList VARCHAR(255) NULL,";
          $sql[1].=" tplType INT(1) NOT NULL,";
          $sql[1].=" tplContent TEXT NULL,";
          $sql[1].=" PRIMARY KEY (tplID)";
          $sql[1].=") TYPE=MyISAM DEFAULT CHARSET=utf8;";
         */

        $r = $this->CreateTables($sqlset, $conn);
        if ($r == FALSE) {
            $this->GetError()->AddItem(3, "Page服务建立失败|fail to create Page service");
            return FALSE;
        }
        $sql = $conn->CreateInsertSTMT("ibc1_dataservice");
        $sql->AddValue("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);
        $sql->AddValue("ServiceType", "pag", IBC1_DATATYPE_PURETEXT);
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
        if (!$this->Exists($ServiceName, "pag")) {
            $this->GetError()->AddItem(6, "服务'$ServiceName'不存在|cannot find service '$ServiceName'");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateTableSTMT("drop");
        $sql->SetTable("ibc1_pag" . $ServiceName . "_theme");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql->SetTable("ibc1_pag" . $ServiceName . "_template");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql = $conn->CreateDeleteSTMT("ibc1_dataservice");
        $sql->AddEqual("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);
        $sql->Execute();
        $sql->ClearConditions();
        $sql->CloseSTMT();
        if ($conn->GetError()->HasError()) {
            $this->GetError()->AddItem(7, "'" . $conn->GetError()->GetSource() . "' 存在未知错误|unknown error from '" . $conn->GetError()->GetSource() . "'");
            return FALSE;
        }
        return TRUE;
    }

    public function Optimize($ServiceName) {
        if (!$this->Exists($ServiceName, "pag")) {

            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateTableSTMT("optimize", "ibc1_pag" . $ServiceName . "_theme");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql = $conn->CreateTableSTMT("optimize", "ibc1_pag" . $ServiceName . "_template");
        $sql->Execute();
        $sql->CloseSTMT();
        return TRUE;
    }

}

?>