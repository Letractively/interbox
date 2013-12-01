<form method="get" data-ajax="false">
    <select name="lang" data-native-menu="false">
        <?php
        foreach ($this->langlist as $code => $name) {
            $s = ($selected == $code) ? ' selected="selected"' : '';
            $code = htmlspecialchars($code);
            $name = htmlspecialchars($name);
            echo "<option value=\"{$code}\"{$s}>{$name}</option>";
        }
        ?>
    </select>
    <input type="hidden"  name="module" value="<?php echo $this->module; ?>" />
    <input type="hidden"  name="function" value="select" />
    <input type="submit"  data-theme="b" value="<?php echo GetLangData('select'); ?>" />
</form>