<?php

class Welcome extends BoxModel {

    function __construct($args) {
        parent::__construct(__CLASS__);
    }

    protected function LoadContent() {
        $modules = array(
            'theme' => 'page_theme',
            'language' => 'page_lang',
            'configuration' => 'page_config',
            'cache' => 'page_cache'
        );
        require_once GetSysResPath('CustomList.class.php', 'modules/lists');
        $list = new CustomList('select3');
        foreach ($modules as $module => $name) {
            $list->AddItem(array(
                'url' => queryString(array(
                    'module' => $module
                )),
                'text_name' => GetLangData($name)
            ));
        }
        return $list->GetHTML();
    }

    public function After($page) {
        $page->SetTitle(GetLangData('page_home'));
    }

    public function Before($page) {
        
    }

}
