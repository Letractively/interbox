<?php

class TestPage extends PageModel {

    private $stopwatch = NULL;

    function __construct() {
        parent::__construct('page1');
    }

    public function ShowHomeButton($url = NULL) {
        if (empty($url)) {
            $this->SetField('LeftButton', $this->TransformTpl('button_home', array(
                        'name' => GetLangData('page_home'),
                        'icon' => 'home',
                        'url' => './'
                    )));
        } else {
            $this->SetField('LeftButton', $this->TransformTpl('button_home', array(
                        'name' => GetLangData('back'),
                        'icon' => 'arrow-l',
                        'url' => $url
                    )));
        }
    }

    public function ShowRightButton($data) {
        if (!empty($data)) {
            $this->SetField('RightButton', $this->TransformTpl('button_right', $data));
        } else {
            $this->SetField('RightButton', '');
        }
    }

    public function StartStopwatch() {
        LoadIBC1Class('Stopwatch', 'util');
        $this->stopwatch = new Stopwatch();
    }

    public function GetElapsedMillis() {
        if (empty($this->stopwatch))
            return -1;
        return $this->stopwatch->elapsedMillis();
    }

    public function After($page) {
        
    }

    public function Before($page) {
        header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
        header('Pragma: no-cache'); // HTTP 1.0.
        header('Expires: 0'); // Proxies.
        $this->SetField('LeftButton', '');
        $this->SetField('RightButton', '');
        $this->SetField('Title', '');
        $this->SetField('Content', '');
        $this->SetField('TopNav', '');
    }

}