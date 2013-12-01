<?php

class SaveConfig extends ProcessModel {

    public function Process() {
        $key = strPost('conf_key');
        $value = strPost('conf_value');
        try {
            LoadIBC1Class('ConfigDataGroupEditor', 'framework');
            $editor = new ConfigDataGroupEditor();
            $editor->SetValue($key, $value);
            $editor->Persist();
            return $this->OutputBox('MsgBox', array(
                        'msg' => GetLangData('config_save_finish'),
                        'url' => '?module=configuration',
                        'title' => GetLangData('page_config')
                    ));
        } catch (Exception $ex) {
            return $this->OutputBox('MsgBox', array(
                        'msg' => GetLangData('config_save_fail'),
                        'url' => '?module=configuration',
                        'title' => GetLangData('page_config')
                    ));
        }
    }

    public function Auth($page) {
        return TRUE;
    }

}