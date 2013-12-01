<?php

class MsgBox extends BoxModel {

    function __construct($args) {
        parent::__construct(__CLASS__, $args);
    }

    protected function LoadContent() {
        $mode = $this->GetBoxArgument('mode');
        return $this->TransformTpl($mode == 'confirm' ? 'confirm' : 'msg', array(
                    'Message' => htmlspecialchars($this->GetBoxArgument('msg')),
                    'URL' => $this->GetBoxArgument('url'),
                    'HTML' => $this->GetBoxArgument('content')
                ));
    }

    public function After($page) {
        $page->SetTitle($this->GetBoxArgument('title'));
    }

    public function Before($page) {
        
    }

}
