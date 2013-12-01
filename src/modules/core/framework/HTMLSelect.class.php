<?php

/**
 * A tool for generating a select element for an HTML document dynamically .
 * 
 * @version 0.2.20121123
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.framework
 */
class HTMLSelect {

    private $id = '';
    private $name = '';
    private $css = '';
    private $js = '';
    private $value = NULL;
    private $items = array();
    private $optionsonly = FALSE;

    /**
     * @param string $name      <code>&lt;select name="[$name]"&gt;...&lt;/select&gt;</code>
     * @param string $id      <code>&lt;select id="[$id]"&gt;...&lt;/select&gt;</code>
     * @param bool $optionsonly
     * Options-only can be useful for example, in the template:
     * <code>
     * &lt;select name="s"&gt;
     * {$options}
     * &lt;/select&gt;
     * </code>
     * The "select" tag is not desired since it is already in the template.
     */
    function __construct($name = '', $id = '', $optionsonly = FALSE) {
        $this->id = htmlspecialchars($id);
        $this->name = htmlspecialchars($name);
        $this->optionsonly = $optionsonly;
    }

    /**
     * <code>&lt;select name="s" onchange="[$js]"&gt;...&lt;/select&gt;</code>
     * @param string $js
     */
    public function SetOnChange($js) {
        $this->js = htmlspecialchars($js);
    }

    /**
     * <code>&lt;select name="s" class="[$classname]"&gt;...&lt;/select&gt;</code>
     * @param string $classname
     */
    public function SetCSSClass($classname) {
        $this->css = htmlspecialchars($classname);
    }

    public function Select($value) {
        $this->value = $value;
    }

    public function AddItems(array $items) {
        foreach ($items as $value => $name) {
            $this->items[$value] = $name;
        }
    }

    public function AddItem($name, $value) {
        $this->items[$value] = $name;
    }

    public function GetHTML() {
        $html = '';
        if (!$this->optionsonly) {
            $html = '<select';
            if (!empty($this->id))
                $html.=" id=\"{$this->id}\"";
            if (!empty($this->name))
                $html.=" name=\"{$this->name}\"";
            if (!empty($this->css))
                $html.=" class=\"{$this->css}\"";
            if (!empty($this->js))
                $html.=" onchange=\"{$this->js}\"";
            $html.=">\n";
        }
        foreach ($this->items as $value => $name) {
            $html.='<option value="' . htmlspecialchars($value) . '"';
            if ($value === $this->value)
                $html.=' selected="selected"';
            $html.='>' . htmlspecialchars($name) . "</option>\n";
        }
        if (!$this->optionsonly)
            $html.="</select>\n";
        return $html;
    }

}
