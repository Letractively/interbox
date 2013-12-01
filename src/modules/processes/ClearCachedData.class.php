<?php

class ClearCachedData extends ProcessModel {

    public function Process() {
        ClearCache();
        return $this->OutputBox('MsgBox', array(
                    'msg' => GetLangData('cache_clear_finish'),
                    'url' => '?module=cache',
                    'title' => GetLangData('page_cache')
                ));
    }

    public function Auth($page) {
        return TRUE;
    }

}