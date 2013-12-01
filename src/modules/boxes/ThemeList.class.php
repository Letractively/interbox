<?php

class ThemeList extends BoxModel {

    function __construct($args) {
        parent::__construct(__CLASS__);
    }

    protected function LoadContent() {
        //$selected = GetThemeID();
        $themes = GetThemes();
        require_once GetSysResPath('CustomList.class.php', 'modules/lists');
        $list = new CustomList('select2');
        foreach ($themes as $id => $name) {
            $list->AddItem(array(
                'url' => queryString(array(
                    'module' => $this->module,
                    'function' => 'select',
                    'id' => $id
                )),
                'text_name' => $name
            ));
        }
        return $list->GetHTML();
    }

    public function After($page) {
        $page->SetTitle(GetLangData('page_theme'));
        $page->ShowHomeButton();
    }

    public function Before($page) {
        
    }

}