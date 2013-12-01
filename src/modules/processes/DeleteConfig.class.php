<?php

class DeleteConfig extends ProcessModel {

    public function Process() {
        $key = strGet('key');
        if (isset($_GET['confirmed'])) {
            try {
                LoadIBC1Class('ConfigDataGroupEditor', 'framework');
                $editor = new ConfigDataGroupEditor();
                $editor->RemoveValue($key);
                $editor->Persist();
                return $this->OutputBox('MsgBox', array(
                            'msg' => GetLangData('config_delete_finish'),
                            'url' => '?module=configuration',
                            'title' => GetLangData('page_config')
                        ));
            } catch (Exception $ex) {
                return $this->OutputBox('MsgBox', array(
                            'msg' => GetLangData('config_delete_fail'),
                            'url' => queryString(array(
                                'module' => 'configuration/editor',
                                'key' => $key
                            )),
                            'title' => GetLangData('page_config')
                        ));
            }
        } else {
            return $this->OutputBox('MsgBox', array(
                        'mode' => 'confirm',
                        'title' => GetLangData('page_config'),
                        'msg' => GetLangData('config_delete_confirm'),
                        'url' => queryString(array(
                            'module' => 'configuration/editor',
                            'function' => 'delete',
                            'key' => $key,
                            'confirmed' => 'yes'
                        ))
                    ));
        }
    }

    public function Auth($page) {
        return TRUE;
    }

}