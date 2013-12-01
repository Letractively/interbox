<?php

class ConfigList extends BoxModel {

    function __construct($args) {
        parent::__construct(__CLASS__);
    }

    protected function LoadContent() {
        GetConfigValue(''); // initialize the reader
        $reader = $GLOBALS['IBC1_ConfigReader']['conf_main'];
        $keys = $reader->GetKeys();

        require_once GetSysResPath('CustomList.class.php', 'modules/lists');
        $list = new CustomList('admin2');
        foreach ($keys as $key) {
            $list->AddItem(array(
                'url_edit' => queryString(array(
                    'module' => $this->module . '/editor',
                    'key' => $key
                )),
                'url_link' => '#',
                'text_key' => $key,
                'text_value' => $reader->GetValue($key)
            ));
        }
        return $list->GetHTML();
    }

    public function After($page) {
        $page->SetTitle(GetLangData('page_config'));
        $page->ShowHomeButton();
        $page->ShowRightButton(array(
            'URL' => queryString(array(
                'module' => $this->module . '/editor'
            )),
            'Icon' => 'add',
            'Content' => GetLangData('add')
        ));
    }

    public function Before($page) {
        
    }

}