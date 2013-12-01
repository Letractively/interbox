<?php

class CachedBox extends BoxModel {

    function __construct($args) {
        parent::__construct(__CLASS__);
        switch (isset($args['mode']) ? $args['mode'] : '') {
            case 'versioning':
                $this->cacheGroup = 'caching_versioning';
                $this->cacheKey = __CLASS__;
                $this->cacheTimeout = 0;
                $this->cacheVersion = isset($_GET['version']) ? intval($_GET['version']) : time();
                break;
            case 'timing':
                $this->cacheGroup = 'caching_timing';
                $this->cacheKey = __CLASS__;
                $this->cacheTimeout = 5;
                $this->cacheVersion = 0;
                break;
            default:
                $this->cacheGroup = '';
                $this->cacheKey = '';
                $this->cacheTimeout = 0;
                $this->cacheVersion = 0;
                break;
        }
    }

    protected function LoadContent() {
        usleep(500000);
        return $this->TransformTpl('box', array(
                    'Content' => time()
                ));
    }

    public function After($page) {
        
    }

    public function Before($page) {
        $page->StartStopwatch();
    }

}