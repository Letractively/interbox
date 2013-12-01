<?php

/**
 *
 * @version 0.7
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.page
 */
class PanelTplProcessor extends PropertyList {

//PanelGenerator extends DataService rather than DataItem
    protected $content = "";
    protected $tplType = IBC1_TEMPLATETYPE_PANEL;

    //private $mode=0;
    public function LoadSetting($conns, $servicename) {
        $c = $this->content;
        LoadIBC1Class("SettingListReader", "setting");
        $slr = new SettingListReader($conns, $servicename);
        $slr->LoadList();
        $a = $slr->Count();
        for ($i = 0; $i < $a; $i++) {
            $b = $slr->GetItem($i);
            $c = eregi_replace("\{ *IBC1_Template_Setting *= *" . $b->setName . " *\}", $b->setValue, $c);
        }
        $slr->CloseService();
        $this->content = $c;
    }

    public function OpenTplFile($tplfile) {
        //public function OpenTplFile($path,$tplname){$path.$tplname.".ibc1tpl"
        $this->content = file_get_contents($tplfile);
    }

    public function SetField($fieldName, $value) {
        if ($fieldName == "")
            return FALSE;
        $this->SetValue($fieldName, $value, IBC1_DATATYPE_PURETEXT);
        return TRUE;
    }

    public function SetFields(PropertyList $propertylist) {
        while (list($fieldName, $value) = $propertylist->GetEach()) {
            $this->SetValue($fieldName, $value[0], IBC1_DATATYPE_PURETEXT);
        }
    }

    public function SetContent($c) {
        $this->content = $c;
    }

    public function SaveAsTplFile($tplfile) {
        //public function SaveAsTplFile($path,$tplname){$path.$tplname.".ibc1tpl"
        $c = $this->content;
        $this->MoveFirst();
        while (list($key, $item) = $this->GetEach()) {
            $c = eregi_replace("\{ *IBC1_Template_Field *= *" . $key . " *\}", $item[0], $c);
        }
        //remain the tags that are not used
        $f = fopen($tplfile, "w");
        if ($f) {
            fwrite($f, $c);
            fclose($f);
        }
    }

    public function GetResult() {
        $c = $this->content;
        $this->MoveFirst();
        while (list($key, $item) = $this->GetEach()) {
            $c = eregi_replace("\{ *IBC1_Template_Field *= *" . $key . " *\}", $item[0], $c);
        }
        //remove the tags that are not used
        $c = eregi_replace("\{ *(IBC1_Template_Field|IBC1_Template_Setting).\}", "", $c);
        return $c;
    }

}

?>