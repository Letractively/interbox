<?php

class CachingController extends BoxModel {

    private $ms;

    function __construct($args) {
        parent::__construct(__CLASS__, $args);
    }

    protected function LoadContent() {
        switch ($this->GetBoxArgument('mode')) {
            case 'versioning':
            case 'timing':
                $tpl = $this->GetBoxArgument('mode');
                break;
            default:
                $tpl = 'nocache';
                break;
        }
        return $this->TransformTpl($tpl, array(
                    'elapsed' => $this->ms,
                    'version' => time() + 1
                ));
    }

    public function After($page) {
        $page->SetTitle(GetLangData('page_cache'));
        $page->ShowHomeButton();
    }

    public function Before($page) {
        $this->ms = $page->GetElapsedMillis();
    }

}