<?php

/**
 * An abstract class for programmatically editing data such as locale data and configurations.
 * 
 * @version 0.1.20130110
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.framework
 */
abstract class AbstractDataGroupEditor {

    protected $editor;

    public function Persist() {
        $this->editor->Save();
    }

    public function SetValue($key, $value) {
        $this->editor->SetValue($key, $value);
    }

    public function RemoveValue($key) {
        $this->editor->Remove($key);
    }

    public function RemoveAll() {
        $this->editor->RemoveAll();
    }

}
