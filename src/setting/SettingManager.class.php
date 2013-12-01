<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.setting
 */
LoadIBC1Class("ServiceManager", "common.dataservice");

class SettingManager extends ServiceManager {

    public function Create($ServiceName, $MatchService="", $MatchTable="", $MatchField="", $MatchType=IBC1_DATATYPE_INTEGER, $ValueType=IBC1_DATATYPE_INTEGER, $ValueLength=10, $TimeIncluded=FALSE) {
        $conn = $this->GetDBConn();
        $this->GetError()->Clear();
        if (!$this->IsInstalled($this->GetError()))
            return FALSE;
        if ($this->Exists($ServiceName)) {
            $this->GetError()->AddItem(4, "服务 '$ServiceName' 早已建立|service '$ServiceName' has already been there");
            return FALSE;
        }
        if ($this->GetError()->HasError())
            return FALSE;
        if ($MatchService != "") {
            if (!$this->Exists($MatchService, "", TRUE)) {
                $this->GetError()->AddItem(6, "服务'$MatchService'不存在|cannot find service '$MatchService'");
                return FALSE;
            }

            if (!$conn->TableExists($MatchTable)) {

                return FALSE;
            }

            if (!$conn->FieldExists($MatchTable, $MatchField)) {

                return FALSE;
            }
            $ValueLength = intval($ValueLength);
            $ValueType = intval($ValueType);
            $MatchType = intval($MatchType);
        }
        if (!$conn->TableExists("ibc1_dataservice_setting")) {
            $sqlset[0][0] = $conn->CreateTableSTMT("create", "ibc1_dataservice_setting");
            $sqlset[0][1] = "ibc1_dataservice_setting";
            $sql = &$sqlset[0][0];
            $sql->AddField("ServiceName", IBC1_DATATYPE_PURETEXT, 64, FALSE, NULL, TRUE, "", FALSE);
            $sql->AddField("MatchService", IBC1_DATATYPE_PURETEXT, 64, FALSE);
            $sql->AddField("MatchTable", IBC1_DATATYPE_PURETEXT, 64, FALSE);
            $sql->AddField("MatchField", IBC1_DATATYPE_PURETEXT, 64, FALSE);
            $sql->AddField("MatchType", IBC1_DATATYPE_INTEGER, 1, FALSE);
            $sql->AddField("ValueType", IBC1_DATATYPE_INTEGER, 1, FALSE);
            $sql->AddField("ValueLength", IBC1_DATATYPE_INTEGER, 5, FALSE);
            $sql->AddField("TimeIncluded", IBC1_DATATYPE_INTEGER, 1, FALSE, 0);

            /*
              $sql[0]="CREATE TABLE ibc1_dataservice_setting(";
              $sql[0].="ServiceName VARCHAR(64) NOT NULL,";
              $sql[0].="MatchService VARCHAR(64) NOT NULL,";
              $sql[0].="MatchTable VARCHAR(64) NOT NULL,";
              $sql[0].="MatchField VARCHAR(255) NOT NULL,";
              $sql[0].="MatchType INT(1) NOT NULL,";
              $sql[0].="ValueType INT(1) NOT NULL,";
              $sql[0].="ValueLength INT(5) NOT NULL,";
              $sql[0].="TimeIncluded INT(1) NOT NULL DEFAULT 0,";
              $sql[0].="PRIMARY KEY(ServiceName)";
              $sql[0].=")TYPE=MyISAM DEFAULT CHARSET=utf8;";
             */
            if (!$this->CreateTables($sqlset, $conn)) {
                $this->GetError()->AddItem(3, "Setting服务建立失败|fail to create a Setting service");
                return FALSE;
            }
        }


        $sqlset[0][0] = $conn->CreateTableSTMT("create", "ibc1_set" . $ServiceName . "_settinglist");
        $sqlset[0][1] = "ibc1_set" . $ServiceName . "_settinglist";
        $sql = &$sqlset[0][0];
        $sql->AddField("setID", IBC1_DATATYPE_INTEGER, 10, FALSE, NULL, TRUE, "", TRUE);
        if ($MatchType == IBC1_DATATYPE_INTEGER) {
            $sql->AddField("setMatchValue", IBC1_DATATYPE_INTEGER, 10, TRUE);
        } else {
            $sql->AddField("setMatchValue", IBC1_DATATYPE_PURETEXT, 256, TRUE);
            $MatchType == IBC1_DATATYPE_PURETEXT;
        }
        $sql->AddField("setName", IBC1_DATATYPE_PURETEXT, 256, FALSE);
        $sql->AddField("setValue", $ValueType, $ValueLength, TRUE);
        if ($TimeIncluded) {
            $sql->AddField("setTimeCreated", IBC1_DATATYPE_DATETIME, 0, TRUE);
            $sql->AddField("setTimeUpdated", IBC1_DATATYPE_DATETIME, 0, TRUE);
            $TimeIncluded = 1;
        } else {
            $TimeIncluded = 0;
        }
        $r = $this->CreateTables($sqlset, $conn);

        /*
          $sql="CREATE TABLE IBC1_set".$ServiceName."_SettingList(";
          $sql.="setID INT(10) NOT NULL AUTO_INCREMENT,";
          $sql.="setMatchValue ";

          //MatchType can only be INTEGER or PURETEXT
          if($MatchType==IBC1_DATATYPE_INTEGER)
          {
          $sql.="INT(10) NULL,";
          }
          else
          {
          $sql.="VARCHAR(255) NULL,";
          $MatchType==IBC1_DATATYPE_PURETEXT;
          }

          $sql.="setName VARCHAR(255) NOT NULL,";
          $sql.="setValue ";
          switch($ValueType)
          {
          case IBC1_DATATYPE_PURETEXT:
          case IBC1_DATATYPE_RICHTEXT:
          case IBC1_DATATYPE_URL:
          case IBC1_DATATYPE_EMAIL:
          case IBC1_DATATYPE_PWD:
          $sql.="VARCHAR";
          if($ValueLength>0) $sql.="($ValueLength)";
          if($ValueType > IBC1_DATATYPE_PURETEXT) $ValueType=IBC1_DATATYPE_PURETEXT;
          break;
          case IBC1_DATATYPE_DECIMAL:
          $sql.="DOUBLE";
          break;
          case IBC1_DATATYPE_DATETIME:
          $sql.="DATETIME";
          break;
          case IBC1_DATATYPE_TIME:
          $sql.="TIME";
          break;
          case IBC1_DATATYPE_DATE:
          $sql.="DATE";
          break;

          default:
          $sql.="INT";
          if($ValueLength>0) $sql.="($ValueLength)";
          }

          $sql.=" NULL, ";
          if($TimeIncluded)
          {
          $sql.="setTimeCreated TIMESTAMP(14) NULL,";
          $sql.="setTimeUpdated TIMESTAMP(14) NULL,";
          $TimeIncluded=1;

          }
          else
          {
          $TimeIncluded=0;
          }
          $sql.="PRIMARY KEY (setID)";
          $sql.=") TYPE=MyISAM DEFAULT CHARSET=utf8;";


          //$r=mysql_query($sql);
          $r=$sql->Execute($sql);
         */

        if ($r == FALSE) {
            $this->GetError()->AddItem(3, "Setting服务建立失败|fail to create Setting service");
            return FALSE;
        }
        $sql = $conn->CreateInsertSTMT();
        $sql->SetTable("ibc1_dataservice");
        $sql->AddValue("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);
        $sql->AddValue("ServiceType", "set", IBC1_DATATYPE_PURETEXT);
        $sql->Execute();
        $sql->CloseSTMT();
        $sql->ClearValues();
        $sql->SetTable("ibc1_dataservice_setting");
        $sql->AddValue("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);
        $sql->AddValue("MatchService", $MatchService, IBC1_DATATYPE_PURETEXT);
        $sql->AddValue("MatchTable", $MatchTable, IBC1_DATATYPE_PURETEXT);
        $sql->AddValue("MatchField", $MatchField, IBC1_DATATYPE_PURETEXT);
        $sql->AddValue("MatchType", $MatchType, IBC1_DATATYPE_INTEGER);
        $sql->AddValue("ValueType", $ValueType, IBC1_DATATYPE_INTEGER);
        $sql->AddValue("ValueLength", $ValueType, IBC1_DATATYPE_INTEGER);
        $sql->AddValue("TimeIncluded", $TimeIncluded, IBC1_DATATYPE_INTEGER);
        $sql->Execute();
        $sql->CloseSTMT();
        $sql->ClearValues();
        if ($conn->GetError()->HasError()) {
            //$this->GetError()->AddItem(7,"'".$conn->GetError()->GetSource()."' 存在未知错误|unknown error from '".$conn->GetError()->GetSource()."'");
            return FALSE;
        }
        return TRUE;
    }

    public function Delete($ServiceName) {

        $this->GetError()->Clear();
        if (!$this->Exists($ServiceName, "set")) {
            $this->GetError()->AddItem(6, "服务'$ServiceName'不存在|cannot find service '$ServiceName'");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateTableSTMT("drop", "ibc1_set" . $ServiceName . "_settinglist");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql = $conn->CreateDeleteSTMT();
        $sql->AddEqual("ServiceName", $ServiceName, IBC1_DATATYPE_PURETEXT);
        $sql->SetTable("ibc1_dataservice_setting");
        $sql->Execute();
        $sql->CloseSTMT();
        $sql->SetTable("ibc1_dataservice");
        $sql->Execute();
        $sql->CloseSTMT();
        if ($conn->GetError()->HasError()) {
            $this->GetError()->AddItem(7, "'" . $conn->GetError()->GetSource() . "' 存在未知错误|unknown error from '" . $conn->GetError()->GetSource() . "'");
            return FALSE;
        }
        return TRUE;
    }

    public function Optimize($ServiceName) {
        if (!$this->Exists($ServiceName, "set")) {

            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateTableSTMT("optimize", "ibc1_set" . $ServiceName . "_settinglist");
        $sql->Execute();
        $sql->CloseSTMT();
        return TRUE;
    }

}

?>