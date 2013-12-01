<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.catalog
 */
LoadIBC1Class("ServiceManager", "common.dataservice");

class CatalogManager extends ServiceManager {

    private function GetTableSQL($middlename, $conn) {
        $sqlset[0][0] = $conn->CreateTableSTMT("create");
        $sqlset[0][1] = "ibc1_" . $middlename . "_content";
        $sql = &$sqlset[0][0];
        $sql->SetTable($sqlset[0][1]);
        $sql->AddField("cntID", IBC1_DATATYPE_INTEGER, 10, FALSE, NULL, TRUE, "", TRUE);
        $sql->AddField("cntOrdinal", IBC1_DATATYPE_INTEGER, 10, TRUE, 0);
        $sql->AddField("cntName", IBC1_DATATYPE_PURETEXT, 256, FALSE);
        $sql->AddField("cntCatalogID", IBC1_DATATYPE_INTEGER, 10, FALSE, NULL, FALSE, "parent");
        $sql->AddField("cntAuthor", IBC1_DATATYPE_PURETEXT, 256, TRUE);
        $sql->AddField("cntKeywords", IBC1_DATATYPE_WORDLIST, 255, TRUE);
        $sql->AddField("cntTimeCreated", IBC1_DATATYPE_DATETIME, 0, FALSE);
        $sql->AddField("cntTimeUpdated", IBC1_DATATYPE_DATETIME, 0, TRUE);
        $sql->AddField("cntTimeVisited", IBC1_DATATYPE_DATETIME, 0, TRUE);
        $sql->AddField("cntPointValue", IBC1_DATATYPE_INTEGER, 10, FALSE, 0);
        $sql->AddField("cntUID", IBC1_DATATYPE_PURETEXT, 256, FALSE);
        $sql->AddField("cntVisitCount", IBC1_DATATYPE_INTEGER, 10, FALSE, 0);
        $sql->AddField("cntAdminGrade", IBC1_DATATYPE_INTEGER, 10, TRUE);
        $sql->AddField("cntVisitGrade", IBC1_DATATYPE_INTEGER, 10, FALSE, 0);

        /*
          $sql[0]="CREATE TABLE IBC1_".$middlename."_Content(";
          $sql[0].=" cntID INT(10) NOT NULL AUTO_INCREMENT,";
          $sql[0].=" cntOrdinal INT(10) NULL,";
          $sql[0].=" cntName VARCHAR(255) NOT NULL,";
          $sql[0].=" cntCatalogID INT(10) NOT NULL,";
          $sql[0].=" cntAuthor VARCHAR(255) NULL,";
          $sql[0].=" cntKeywords VARCHAR(255) NULL,";
          $sql[0].=" cntTimeCreated TIMESTAMP(14) NOT NULL,";
          $sql[0].=" cntTimeUpdated TIMESTAMP(14) NULL,";
          $sql[0].=" cntTimeVisited TIMESTAMP(14) NULL,";
          $sql[0].=" cntPointValue INT(10) NOT NULL DEFAULT 0,";
          $sql[0].=" cntUID VARCHAR(255) NULL,";
          $sql[0].=" cntVisitCount INT(10) NOT NULL DEFAULT 0,";
          $sql[0].=" cntAdminGrade INT(10) NULL,";
          $sql[0].=" cntVisitGrade INT(10) NOT NULL DEFAULT 0,";
          $sql[0].=" PRIMARY KEY (cntID),";
          $sql[0].=" KEY parent (cntCatalogID)";
          $sql[0].=") TYPE=MyISAM DEFAULT CHARSET=utf8;";
         */
        $sqlset[1][0] = $conn->CreateTableSTMT("create");
        $sqlset[1][1] = "ibc1_" . $middlename . "_catalog";
        $sql = &$sqlset[1][0];
        $sql->SetTable($sqlset[1][1]);
        $sql->AddField("clgID", IBC1_DATATYPE_INTEGER, 10, FALSE, NULL, TRUE, "", TRUE);
        $sql->AddField("clgName", IBC1_DATATYPE_PURETEXT, 256, FALSE);
        $sql->AddField("clgOrdinal", IBC1_DATATYPE_INTEGER, 10, TRUE);
        $sql->AddField("clgUID", IBC1_DATATYPE_PURETEXT, 256, TRUE);
        $sql->AddField("clgParentID", IBC1_DATATYPE_INTEGER, 10, FALSE);
        $sql->AddField("clgGID", IBC1_DATATYPE_INTEGER, 10, FALSE, 0);
        $sql->AddField("clgAdminGrade", IBC1_DATATYPE_INTEGER, 10, FALSE);

        /*
          $sql[1]="CREATE TABLE IBC1_".$middlename."_Catalog(";
          $sql[1].=" clgID INT(10) NOT NULL AUTO_INCREMENT,";
          $sql[1].=" clgName VARCHAR(255) NOT NULL,";
          $sql[1].=" clgOrdinal INT(10) NULL,";
          $sql[1].=" clgParentID INT(10) NOT NULL,";
          $sql[1].=" clgGID INT(10) NULL,";
          $sql[1].=" clgVisitGrade INT(10) NOT NULL DEFAULT 0,";
          $sql[1].=" PRIMARY KEY (clgID)";
          $sql[1].=") TYPE=MyISAM DEFAULT CHARSET=utf8;";
         */
        $sqlset[2][0] = $conn->CreateTableSTMT("create");
        $sqlset[2][1] = "ibc1_" . $middlename . "_admin";
        $sql = &$sqlset[2][0];
        $sql->SetTable($sqlset[2][1]);
        $sql->AddField("admID", IBC1_DATATYPE_INTEGER, 10, FALSE, NULL, TRUE, "", TRUE);
        $sql->AddField("admCatalogID", IBC1_DATATYPE_INTEGER, 10, FALSE);
        $sql->AddField("admUID", IBC1_DATATYPE_PURETEXT, 256, FALSE);

        /*
          $sql[2]="CREATE TABLE IBC1_".$middlename."_Admin(";
          $sql[2].="admID INT(10) NOT NULL AUTO_INCREMENT,";
          $sql[2].="admCatalogID INT(10) NOT NULL,";
          $sql[2].="admUID VARCHAR(255) NOT NULL,";
          $sql[2].="PRIMARY KEY (admID)";
          $sql[2].=") TYPE=MyISAM DEFAULT CHARSET=utf8;";
         */
        return $sqlset;
    }

    public function Create($ServiceName, $UserService="") {
        $this->GetError()->Clear();
        if (!$this->IsInstalled($this->GetError()))
            return FALSE;
        if ($this->Exists($ServiceName)) {
            $this->GetError()->AddItem(4, "服务 '$ServiceName' 早已建立|service '$ServiceName' has already been there");
            return FALSE;
        }
        /*
          if($UserService!=""&&!$this->Exists($UserService,"usr"))
          {
          $this->GetError()->AddItem(4,"用户服务 '$UserService' 不存在|user service '$ServiceName' does not exist");
          return FALSE;
          }
         */
        $conn = $this->GetDBConn();
        /*
          if(!$conn->TableExists("ibc1_dataservice_Catalog"))
          {
          $stmt=$conn->CreateTableSTMT("create","ibc1_dataservice_Catalog");

          $sql->AddField("ServiceName",IBC1_DATATYPE_PURETEXT,63,FALSE,NULL,TRUE,"",FALSE);
          $sql->AddField("UserService",IBC1_DATATYPE_PURETEXT,63,TRUE);
          $sql->AddField("AdminGrade",IBC1_DATATYPE_INTEGER,2,TRUE);//for top catalog(whose parentid=0)
          $sql->AddField("VisitGrade",IBC1_DATATYPE_INTEGER,2,TRUE);//for top catalog(whose parentid=0)

          $sql[0]=$stmt->GetSTMT();

          if(!$this->CreateTables($sql,$conn))
          {
          $this->GetError()->AddItem(3,"Catalog服务建立失败|fail to create a Catalog service");
          return FALSE;
          }
          }
         */
        if ($this->GetError()->HasError())
            return FALSE;

        $sql = $this->GetTableSQL("clg" . $ServiceName, $conn);
        $r = $this->CreateTables($sql, $conn);
        if ($r == FALSE) {
            $this->GetError()->AddItem(3, "Catalog服务建立失败|fail to create Catalog service");
            return FALSE;
        }
        $sql = $conn->CreateInsertSTMT();
        $sql->SetTable("ibc1_dataservice");
        $sql->AddValue("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);
        $sql->AddValue("ServiceType", "clg", IBC1_DATATYPE_PURETEXT);
        $sql->Execute();
        $sql->ClearValues();
        $sql->CloseSTMT();
        /*
          $sql->ClearValues();
          $sql->SetTable("ibc1_dataservice_Catalog");
          $sql->AddValue("ServiceName",$ServiceName,IBC1_DATATYPE_PURETEXT);
          $sql->AddValue("UserService",$UserService,IBC1_DATATYPE_PURETEXT);
          $sql->Execute();
         */
        //$sql->Execute("INSERT INTO ibc1_dataservice (ServiceName,ServiceType) VALUES (\"$ServiceName\",\"clg\")");
        if ($conn->GetError()->HasError()) {
            $this->GetError()->AddItem(7, "'" . $conn->GetError()->GetSource() . "' 存在未知错误|unknown error from '" . $conn->GetError()->GetSource() . "'");
            return FALSE;
        }
        return TRUE;
    }

    public function Delete($ServiceName) {
        $this->GetError()->Clear();
        if (!$this->Exists($ServiceName, "clg")) {
            $this->GetError()->AddItem(6, "服务'$ServiceName'不存在|cannot find service '$ServiceName'");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateTableSTMT("drop");
        $sql->SetTable("ibc1_clg" . $ServiceName . "_content");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql->SetTable("ibc1_clg" . $ServiceName . "_catalog");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql->SetTable("ibc1_clg" . $ServiceName . "_admin");
        $sql->Execute();
        $sql->CloseSTMT();
        /*
          $sql->Execute("DROP TABLE IBC1_clg".$ServiceName."_content");
          $sql->Execute("DROP TABLE IBC1_clg".$ServiceName."_catalog");
          $sql->Execute("DROP TABLE IBC1_clg".$ServiceName."_admin");
         */
        $sql = $conn->CreateDeleteSTMT();
        $sql->AddEqual("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);
        $sql->SetTable("ibc1_dataservice");
        $sql->Execute();
        $sql->CloseSTMT();
        /*
          $sql->SetTable("ibc1_dataservice_Catalog");
          $sql->Execute();
         */
        //$sql->Execute("DELETE FROM ibc1_dataservice WHERE ServiceName='$ServiceName'");
        if ($conn->GetError()->HasError()) {
            $this->GetError()->AddItem(7, "'" . $conn->GetError()->GetSource() . "' 存在未知错误|unknown error from '" . $conn->GetError()->GetSource() . "'");
            return FALSE;
        }
        return TRUE;
    }

    public function Optimize($ServiceName) {
        if (!$this->Exists($ServiceName, "clg")) {

            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateTableSTMT("optimize");
        $sql->SetTable("ibc1_clg" . $ServiceName . "_content");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql->SetTable("ibc1_clg" . $ServiceName . "_catalog");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql->SetTable("ibc1_clg" . $ServiceName . "_admin");
        $sql->Execute();
        $sql->CloseSTMT();
        if ($conn->GetError()->HasError()) {

            return FALSE;
        }
        return TRUE;
    }

    public function Backup($ServiceName) {
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_dataservice");
        $sql->AddField("ServiceType");
        $sql->AddEqual("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);
        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if (!$r) {

            return FALSE;
        }
        $sql = $conn->CreateInsertSTMT("ibc1_dataservice_Backup");
        $sql->AddValue("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);
        $sql->AddValue("ServiceName", $r->ServiceType, IBC1_DATATYPE_PURETEXT);
        $sql->AddValue("BackupTime", "CURRENT_TIMESTAMP()", IBC1_DATATYPE_EXPRESSION);
        $r = $sql->Execute();
        if ($r) {
            $backupid = $sql->GetLastInsertID();
            $sql->CloseSTMT();
            $sql = $this->GetTableSQL("Backup_" . $backupid, $conn);
            if ($this->CreateTables($sql)) {
                $sql2 = $conn->CreateSTMT("INSERT INTO IBC1_Backup_" . $backupid . "_Content SELECT * FROM IBC1_clg" . $ServiceName . "_content");
                $sql2->Execute();
                $sql2->CloseSTMT();
                $sql2 = $conn->CreateSTMT("INSERT INTO IBC1_Backup_" . $backupid . "_Catalog SELECT * FROM IBC1_clg" . $ServiceName . "_catalog");
                $sql2->Execute();
                $sql2->CloseSTMT();
                $sql2 = $conn->CreateSTMT("INSERT INTO IBC1_Backup_" . $backupid . "_Admin SELECT * FROM IBC1_clg" . $ServiceName . "_admin");
                $sql2->Execute();
                $sql2->CloseSTMT();
                if ($conn->GetError()->HasError()) {

                    return FALSE;
                }
                return TRUE;
            }

            return FALSE;
        }
        $sql->CloseSTMT();
        return FALSE;
    }

    public function Restore($ID) {
        $ID = intval($ID);
        $conn = $this->GetDBConn();
        //backup the current first
        $sql = $conn->CreateSelectSTMT("ibc1_dataservice_Backup");
        $sql->AddField("ServiceName");
        //$sql->AddField("ServiceType");
        $sql->AddEqual("ID", $ID);

        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if (!$r) {

            return FALSE;
        }
        $ServiceName = $r->ServiceName;
        //$ServiceType=$r->ServiceType;
        $r = $this->Backup($ServiceName);
        if (!$r) {

            return FALSE;
        }
        $sql = $conn->CreateSTMT("DELETE FROM IBC1_clg" . $ServiceName . "_content");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql = $conn->CreateSTMT("DELETE FROM IBC1_clg" . $ServiceName . "_catalog");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql = $conn->CreateSTMT("DELETE FROM IBC1_clg" . $ServiceName . "_admin");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql = $conn->CreateSTMT("INSERT INTO IBC1_clg" . $ServiceName . "_Content SELECT * FROM IBC1_Backup_" . $ID . "_content");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql = $conn->CreateSTMT("INSERT INTO IBC1_clg" . $ServiceName . "_Catalog SELECT * FROM IBC1_Backup_" . $ID . "_catalog");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql = $conn->CreateSTMT("INSERT INTO IBC1_clg" . $ServiceName . "_Admin SELECT * FROM IBC1_Backup_" . $ID . "_admin");
        $sql->Execute();
        $sql->CloseSTMT();
        if ($conn->GetError()->HasError()) {

            return FALSE;
        }
        return TRUE;
    }

}

?>