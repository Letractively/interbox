<?php

class SelectTheme extends ProcessModel {

    public function Process() {
        $id = strGet('id');
        $list = GetThemes();
        if (isset($list[$id])) {
            SetThemeID($id);
            return $this->OutputBox('MsgBox', array(
                        'msg' => GetLangData('theme_selected'),
                        'url' => '?module=theme',
                        'title' => GetLangData('page_theme')
                    ));
        } else {
            return $this->OutputBox('MsgBox', array(
                        'msg' => GetLangData('theme_notfound'),
                        'url' => '?module=theme',
                        'title' => GetLangData('page_theme')
                    ));
        }
    }

    public function Auth($page) {
        return TRUE;
    }

}