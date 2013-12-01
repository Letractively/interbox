<?php

/**
 *
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.page
 */
class PanelGenerator extends DataItem {

    protected $content = "";
    protected $tplType = IBC1_TEMPLATETYPE_PANEL;

    //private $mode=0;
    function __construct(DBConnProvider $Conns, $ServiceName, ErrorList $EL=NULL) {
        parent::__construct($EL);
        $this->OpenService($Conns, $ServiceName);
        $this->GetError()->SetSource(__CLASS__);
    }

    public function OpenService(DBConnProvider $Conns, $ServiceName) {
        return parent::OpenService($Conns, $ServiceName, "pag");
    }

    public function OpenTemplate($thmName, $tplName, $loadSetting=FALSE) {
        if (!$this->IsServiceOpen()) {
            $this->GetError()->AddItem(1, "service has not been opened");
            return FALSE;
        }
        $conn = $this->GetDBConn();
        $sql = $conn->CreateSelectSTMT("ibc1_pag" . $this->GetServiceName() . "_template");
        $sql->JoinTable("ibc1_pag" . $this->GetServiceName() . "_theme", "tplThemeID=thmID");
        $sql->AddField("tplFieldList");
        $sql->AddField("tplContent");
        $sql->AddField("thmSetting");
        $sql->AddEqual("thmName", $thmName, IBC1_DATATYPE_PURETEXT);
        $sql->AddEqual("tplName", $tplName, IBC1_DATATYPE_PURETEXT, IBC1_LOGICAL_AND);
        $sql->AddEqual("tplType", $this->tplType, IBC1_DATATYPE_INTEGER, IBC1_LOGICAL_AND);

        $sql->Execute();
        $r = $sql->Fetch(1);
        $sql->CloseSTMT();
        if ($r) {
            $wl = new WordList($r->tplFieldList);
            $c = $r->tplContent;
            $wl->MoveFirst();
            while ($item = $wl->GetEach()) {
                if ($item != "")
                    $this->SetValue($item, "", IBC1_DATATYPE_PURETEXT);
            }
            if ($loadSetting && $r->thmSetting != "") {
                LoadIBC1Class("SettingListReader", "setting");
                $slr = new SettingListReader($this->GetDBConns(), $r->thmSetting);
                $slr->LoadList();
                $a = $slr->Count();
                for ($i = 0; $i < $a; $i++) {
                    $b = $slr->GetItem($i);
                    $c = eregi_replace("\{ *IBC1_Template_Setting *= *" . $b->setName . " *\}", $b->setValue, $c);
                }
                $slr->CloseService();
            }
            $this->SetContent($c);
            return TRUE;
        }
        $this->GetError()->AddItem(1, "not exists");
        return FALSE;
    }

    public function SetField($fieldName, $value) {
        if ($fieldName == "")
            return FALSE;
        $this->SetValue($fieldName, $value, IBC1_DATATYPE_PURETEXT);
        return TRUE;
    }

    public function SetFields(PropertyList $propertylist) {
        $propertylist->MoveFirst();
        while (list($fieldName, $value) = $propertylist->GetEach()) {
            $this->SetValue($fieldName, $value[0], IBC1_DATATYPE_PURETEXT);
        }
    }

    public function SetContent($c) {
        $this->content = $c;
    }

    public function GetResult() {
        $c = $this->content;
        $this->MoveFirst();
        while (list($key, $item) = $this->GetEach()) {
            $c = eregi_replace("\{ *IBC1_Template_Field *= *" . $key . " *\}", $item[0], $c);
        }

        return $c;
    }

}

?>
